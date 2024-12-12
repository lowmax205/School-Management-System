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
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>

    </style>
</head>

<body>
    <div class="sidebar">
        <a href="../auth/base_dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <?php if ($_SESSION['role'] === 'Admin'): ?>
            <a href="../admin/admin_profile.php"><i class="fas fa-user-circle"></i> My Profile</a>
            <a href="../admin/users_management.php"><i class="fas fa-user-graduate"></i> User Management</a>
            <a href="../admin/student_list.php"><i class="fas fa-user-graduate"></i> Students</a>
            <a href="../admin/teacher_list.php"><i class="fas fa-chalkboard-teacher"></i> Teachers</a>
            <!-- <a href="../admin/course_list.php"><i class="fas fa-book-open"></i> Courses</a>
            <a href="../admin/admin_list.php"><i class="fas fa-users-cog"></i> Admins</a>
            <a href="../admin/reports.php"><i class="fas fa-chart-bar"></i> Reports</a>
            <a href="../admin/system_log.php"><i class="fas fa-history"></i> System Logs</a>
            <a href="../admin/admin_settings.php"><i class="fas fa-cog"></i> Settings</a> -->
        <?php else: ?>
            <a href="../users/profile.php"><i class="fas fa-user"></i> My Profile</a>
            <!-- <a href="../users/my_courses.php"><i class="fas fa-graduation-cap"></i> My Courses</a>
            <a href="../users/schedule.php"><i class="fas fa-calendar-alt"></i> Schedule</a>
            <a href="../users/subject_list.php"><i class="fas fa-book"></i> My Subjects</a>
            <a href="../users/student_grade.php"><i class="fas fa-chart-line"></i> My Grades</a>
            <a href="../users/user_settings.php"><i class="fas fa-cog"></i> Settings</a> -->
        <?php endif; ?>
        <a href="../../server/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</body>

</html>