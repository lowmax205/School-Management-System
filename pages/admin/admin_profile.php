<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../../index.php");
    exit();
}

include '../../includes/header.php';
include '../../server/query/user.query.php';

// Get uid from users_auth table if not in session
if (!isset($_SESSION['uid'])) {
    $user_id = $_SESSION['user_id'];
    $sql = "SELECT uid FROM users_auth WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $_SESSION['uid'] = $row['uid'];
    } else {
        echo "<script>
            alert('Error retrieving user information. Please login again.');
            window.location.href = '../../index.php';
        </script>";
        exit();
    }
}

$userDetails = getUserDetails($_SESSION['uid']);
?>

<link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

<div class="container-fluid">
    <div class="row">
        <?php include '../auth/side_navbar_dashboard.php'; ?>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 mb-5">
            <div class="card-body p-5">
                <h1 class="h2">My Profile</h1>
                <p>Manage your personal information</p>
                <form id="profileForm" method="POST">
                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="first_name">First Name</label>
                            <input type="text" class="form-control" id="first_name" name="first_name" value="<?php echo htmlspecialchars($userDetails['first_name'] ?? ''); ?>" required>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="last_name">Last Name</label>
                            <input type="text" class="form-control" id="last_name" name="last_name" value="<?php echo htmlspecialchars($userDetails['last_name'] ?? ''); ?>" required>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($userDetails['email'] ?? ''); ?>" readonly>
                        </div>
                        <div class="form-group col-md-6">
                            <label for="uid">User ID</label>
                            <input type="text" class="form-control" id="uid" value="<?php echo htmlspecialchars($userDetails['uid'] ?? ''); ?>" readonly>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                            <label for="birth_date">Birth Date</label>
                            <input type="date" class="form-control" id="birth_date" name="birth_date" value="<?php echo htmlspecialchars($userDetails['birth_date'] ?? ''); ?>">
                        </div>
                        <div class="form-group col-md-6">
                            <label for="gender">Gender</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="">Select Gender</option>
                                <option value="M" <?php echo ($userDetails['gender'] ?? '') === 'M' ? 'selected' : ''; ?>>Male</option>
                                <option value="F" <?php echo ($userDetails['gender'] ?? '') === 'F' ? 'selected' : ''; ?>>Female</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Address</label>
                        <textarea class="form-control" id="address" name="address" rows="2"><?php echo htmlspecialchars($userDetails['address'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="phone">Phone Number</label>
                        <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($userDetails['phone'] ?? ''); ?>">
                    </div>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i> Update Profile
                    </button>
                </form>
            </div>
    </div>
    </main>
</div>
</div>

<script>
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = 'Updating...';

        fetch('../../server/query/update_profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Profile updated successfully');
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('An error occurred while updating profile');
                console.error('Error:', error);
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.innerHTML = 'Update Profile';
            });
    });
</script>

<?php include '../../includes/footer.php'; ?>