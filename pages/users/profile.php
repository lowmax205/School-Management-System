
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
        <h2>My Profile</h2>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Personal Information</h5>
                        <form>
                            <div class="mb-3">
                                <label class="form-label">Full Name</label>
                                <input type="text" class="form-control" placeholder="Enter your full name">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="Enter your email">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Student ID</label>
                                <input type="text" class="form-control" readonly>
                            </div>
                            <button type="submit" class="btn btn-primary">Update Profile</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>