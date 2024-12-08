<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
header("Location: ../../server/maintenance.php");
exit();
include '../../includes/header.php';
?>

<link href="../../assets/css/dashboard-styles.css" rel="stylesheet">

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>
    
    <div class="content">
        <h2>Course Management</h2>
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5>Course List</h5>
                    <button class="btn btn-primary"><i class="fas fa-plus"></i> Add Course</button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Course Code</th>
                                <th>Course Name</th>
                                <th>Department</th>
                                <th>Units</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="6" class="text-center">Course list coming soon...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>