<?php
if (!isset($_SESSION['role'])) {
    header("Location: ../../index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>

<body>
    <div class="sidebar">

        <a href="../auth/base_dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'base_dashboard.php' ? 'active' : ''; ?>">
            <i class="fas fa-home"></i> <span>Dashboard</span>
        </a>

        <?php if ($_SESSION['role'] === 'Admin'): ?>
            <a href="../admin/admin_profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user-circle"></i> <span>My Profile</span>
            </a>
            <a href="../admin/users_management.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users_management.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> <span>User Management</span>
            </a>
            <a href="../admin/system_reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'system_reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i> <span>Reports</span>
            </a>
            <a href="../admin/system_logs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'system_logs.php' ? 'active' : ''; ?>">
                <i class="fas fa-tasks"></i> <span>System Logs</span>
            </a>
        <?php else: ?>
            <a href="../users/profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                <i class="fas fa-user"></i> <span>My Profile</span>
            </a>
        <?php endif; ?>

        <div class="mt-auto">
            <a href="../../server/logout.php" class="text-red-600">
                <i class="fas fa-sign-out-alt"></i> <span>Logout</span>
            </a>
        </div>
    </div>
</body>

</html>