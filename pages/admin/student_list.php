<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}
include '../../includes/header.php';
?>

<link href="../../assets/css/dashboard-styles.css" rel="stylesheet">

<div class="dashboard-container">
    <?php include '../auth/side_navbar_dashboard.php'; ?>
    
    <div class="content">
        <h2>Student Management</h2>
        <div class="card">
            <div class="card-body">
                <h5>Student List</h5>
                <!-- Add student list table here -->
                <p>Student management interface coming soon...</p>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
