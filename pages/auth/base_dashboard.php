<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['role'])) {
    header("Location: ../../index.php");
    exit();
}

include '../../includes/header.php';
?>

<link href="../../assets/css/dashboard-styles.css" rel="stylesheet">

<main>
    <?php
    // Load appropriate dashboard based on role
    if ($_SESSION['role'] === 'Admin') {
        include 'admin_dashboard.php';
    } else {
        include 'user_dashboard.php';
    }
    ?>
</main>

<?php include '../../includes/footer.php'; ?>