<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] === 'User') {
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
        <h2>My Grade</h2>

    </div>
</div>

<?php include '../../includes/footer.php'; ?>