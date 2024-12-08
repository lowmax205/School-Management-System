
<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] === 'Admin') {
    header("Location: ../../index.php");
    exit();
}
include '../../includes/header.php';
?>

<link href="../../assets/css/dashboard-styles.css" rel="stylesheet">

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>
    
    <div class="content">
        <h2>My Courses</h2>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Enrolled Courses</h5>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Name</th>
                                        <th>Instructor</th>
                                        <th>Schedule</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="5" class="text-center">Course list coming soon...</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>