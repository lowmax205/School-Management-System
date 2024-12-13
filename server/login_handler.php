<?php
session_start();
include 'db_config.php'; // Include your database configuration file

$response = ['status' => '', 'message' => ''];

// Check if the login form was submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email)) {
        $response = ['status' => 'error', 'message' => 'Email is required'];
    } elseif (empty($password)) {
        $response = ['status' => 'error', 'message' => 'Password should not be empty'];
    } else {
        // Query the database to check if the user exists
        $stmt = $conn->prepare("SELECT * FROM users_auth WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            // Fetch user data
            $user = $result->fetch_assoc();
            
            // Verify password with the stored hash
            if ($password == $user['pwd']) {
                // Set session variables and redirect to the main page
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $user['role'];
                $_SESSION['uid'] = $user['uid'];
                $response = ['status' => 'success', 'message' => 'Login successful'];
            } else {
                $response = ['status' => 'error', 'message' => 'Invalid password'];
            }
        } else {
            $response = ['status' => 'error', 'message' => 'Email not found'];
        }
        $stmt->close();
    }
    $conn->close();
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
?>