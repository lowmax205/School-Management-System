<?php
include 'db_config.php';

$response = ['status' => '', 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirmPassword = trim($_POST['confirmPassword']);

    // Validation
    if (empty($email)) {
        $response = ['status' => 'error', 'message' => 'Email is required'];
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response = ['status' => 'error', 'message' => 'Please enter a valid email address'];
    } elseif (empty($password)) {
        $response = ['status' => 'error', 'message' => 'Password is required'];
    } elseif (strlen($password) < 6) {
        $response = ['status' => 'error', 'message' => 'Password must be at least 6 characters long'];
    } elseif ($password !== $confirmPassword) {
        $response = ['status' => 'error', 'message' => 'Passwords do not match'];
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users_auth WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $response = ['status' => 'error', 'message' => 'Email is already registered'];
        } else {
            $hashedPassword =  $password; // password_hash($password, PASSWORD_DEFAULT);
            $createdAt = date('Y-m-d H:i:s');
            $role_user = 'User';
            $uidRandom = uniqid($role_user.'_', true);

            $stmt = $conn->prepare("INSERT INTO users_auth (email, pwd, uid, role, created_at) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $email, $hashedPassword, $uidRandom, $role_user, $createdAt);
            
            if ($stmt->execute()) {
                $stmt = $conn->prepare("SELECT id, email, uid FROM users_auth WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $response = [
                    'status' => 'success', 
                    'message' => 'Registration successful! You can now login.',
                    'user' => [
                        'id' => $user['id'],
                        'email' => $user['email'],
                        'uid' => $user['uid']
                    ]
                ];
            } else {
                $response = ['status' => 'error', 'message' => 'Registration failed. Please try again.'];
            }
        }
    }
    $stmt->close();
    $conn->close();

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}
?>

