<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
// header("Location: ../../server/maintenance.php");
// exit();
include '../../includes/header.php';
include '../../server/query/system_log.query.php';
$logs = getSystemLogs();
?>

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>

    <div class="content flex-grow-1">
        <div class="card shadow-sm h-100">
            <div class="card-header bg-primary text-white">
                <h3 class="card-title mb-0"><i class="fas fa-history me-2"></i>System Log Entries</h3>
            </div>
            <div class="card-body d-flex flex-column">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <input type="text" class="form-control" placeholder="Search logs...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 text-md-end">
                        <select class="form-select w-auto d-inline-block">
                            <option value="All">All Role Type</option>
                            <option value="Admin">Admin</option>
                            <option value="Teacher">Teacher</option>
                            <option value="Student">Student</option>
                            <option value="Staff">Staff</option>
                        </select>
                    </div>
                </div>

                <div class="table-responsive flex-grow-1">
                    <table class="table table-hover table-striped">
                        <thead class="table-light">
                            <tr>
                                <th scope="col"><i class="fas fa-id-badge me-2"></i>User ID</th>
                                <th scope="col"><i class="fas fa-calendar me-2"></i>Date</th>
                                <th scope="col"><i class="fas fa-clock me-2"></i>Time</th>
                                <th scope="col"><i class="fas fa-user me-2"></i>User Type</th>
                                <th scope="col"><i class="fas fa-cog me-2"></i>Action</th>
                                <th scope="col"><i class="fas fa-info-circle me-2"></i>Details</th>
                                <th scope="col"><i class="fas fa-user-tag me-2"></i>Role Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $logs->fetch_assoc()): ?>
                                <tr>
                                    <td><span class="badge bg-secondary"><?php echo $row['user_id']; ?></span></td>
                                    <td><?php echo $row['date']; ?></td>
                                    <td><?php echo $row['time']; ?></td>
                                    <td><span class="badge bg-<?php echo $row['user_type'] == 'Admin' ? 'primary' : ($row['user_type'] == 'Teacher' ? 'info' : ($row['user_type'] == 'Student' ? 'warning' : 'secondary')); ?>"><?php echo $row['user_type']; ?></span></td>
                                    <td><span class="badge bg-<?php echo $row['action'] == 'Login' ? 'success' : ($row['action'] == 'Logout' ? 'warning' : 'info'); ?>"><?php echo $row['action']; ?></span></td>
                                    <td><?php echo $row['details']; ?></td>
                                    <td><span class="badge bg-secondary"><?php echo $row['role_type']; ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>

                <nav aria-label="Page navigation" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#" tabindex="-1">Previous</a>
                        </li>
                        <li class="page-item active"><a class="page-link" href="#">1</a></li>
                        <li class="page-item"><a class="page-link" href="#">2</a></li>
                        <li class="page-item"><a class="page-link" href="#">3</a></li>
                        <li class="page-item">
                            <a class="page-link" href="#">Next</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>