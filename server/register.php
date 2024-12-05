<?php
include 'db_config.php';

$response = ['status' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = ['status' => 'error', 'message' => 'Invalid email format'];
    }
    // Validate password length
    elseif (strlen($password) < 6) {
        $response = ['status' => 'error', 'message' => 'Password must be at least 6 characters'];
    }
    // Check password match
    elseif ($password !== $confirmPassword) {
        $response = ['status' => 'error', 'message' => 'Passwords do not match'];
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users_auth WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $response = ['status' => 'error', 'message' => 'Email already registered'];
        } else {
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $createdAt = date('Y-m-d H:i:s');
            $role_user = 'User';
            $uidRandom = uniqid($role_user.'_', true);

            $stmt = $conn->prepare("INSERT INTO users_auth (email, pwd, uid, role, created_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $email, $hashedPassword, $uidRandom, $role_user, $createdAt);

            if ($stmt->execute()) {
                $response = ['status' => 'success', 'message' => 'Registration successful! You can now login.'];
            } else {
                $response = ['status' => 'error', 'message' => 'Registration failed. Please try again.'];
            }
        }
        $stmt->close();
    }
    $conn->close();

    session_start();
    $_SESSION['auth_response'] = $response;
    header("Location: ../index.php");
    exit();
}
?>

