<?php
// Include database config
require_once '../../server/db_config.php';

// Count total students
$student_query = "SELECT COUNT(*) as total FROM user_info WHERE type = 'Student'";
$student_result = $conn->query($student_query);
$student_count = $student_result->fetch_assoc()['total'];

// Count total teachers  
$teacher_query = "SELECT COUNT(*) as total FROM user_info WHERE type = 'Teacher'";
$teacher_result = $conn->query($teacher_query);
$teacher_count = $teacher_result->fetch_assoc()['total'];
?>

<div class="dashboard-container">
    <?php include 'side_navbar_dashboard.php'; ?>

    <div class="content">
        <h2>Admin Dashboard</h2>
        <div class="row mt-4">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5>Total Students</h5>
                        <h3><?php echo $student_count; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5>Total Teachers</h5>
                        <h3><?php echo $teacher_count; ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5>Active Courses</h5>
                        <h3>N/A</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5>Total Classes</h5>
                        <h3>N/A</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>