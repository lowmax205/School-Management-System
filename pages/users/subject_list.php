<?php
session_start();
if (!isset($_SESSION['role'])) {
    echo "<script>
        alert('Not authorized');
        window.history.back();
    </script>";
    exit();
}

include '../../includes/header.php';
include '../../server/query/subject.query.php';
include '../../server/query/student.query.php';

$MAX_UNITS = 21;
$current_units = getTotalUnits($_SESSION['uid']);
$available_units = $MAX_UNITS - $current_units;
$subjects = getSubjects();

// Handle POST request for subject enrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_code = $_POST['subject_code'];
    $subject = getSubjectByCode($subject_code);

    if ($subject['units'] <= $available_units) {
        if (enrollSubject($_SESSION['uid'], $subject_code)) {
            $_SESSION['message'] = "Subject enrolled successfully";
        }
    } else {
        $_SESSION['error'] = "Exceeds maximum allowed units of " . $MAX_UNITS;
    }
}
?>

<link href="../../assets/css/dashboard-styles.css" rel="stylesheet">

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>

    <div class="content">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3>Available Subjects</h3>
                <div>
                    <span class="badge bg-info">Available Units: <?php echo $available_units; ?>/<?php echo $MAX_UNITS; ?></span>
                </div>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5>Subject List</h5>
                    <button class="btn btn-primary"><i class="fas fa-plus"></i> Add Subject</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Subject Code</th>
                                <th>Subject Name</th>
                                <th>Units</th>
                                <th>Department</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $subjects->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['subject_code']; ?></td>
                                    <td><?php echo $row['subject_name']; ?></td>
                                    <td><?php echo $row['units']; ?></td>
                                    <td><?php echo $row['department']; ?></td>
                                    <td><span class="badge bg-<?php echo $row['status'] == 'Active' ? 'success' : 'secondary'; ?>"><?php echo $row['status']; ?></span></td>
                                    <td>
                                        <?php if ($available_units >= $row['units']): ?>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="subject_code" value="<?php echo $row['subject_code']; ?>">
                                                <button type="submit" class="btn btn-sm btn-success">Enroll</button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-secondary" disabled>Not Available</button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <?php include '../../includes/footer.php'; ?>