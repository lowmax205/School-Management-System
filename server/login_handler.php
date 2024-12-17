<?php
session_start();
include 'db_config.php';
require_once 'query/user.query.php';

$response = ['status' => '', 'message' => ''];

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
            if (password_verify($password, $user['pwd']) || $password == $user['pwd']) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $user['role'];
                $_SESSION['uid'] = $user['uid'];

                // Log successful login
                logUserActivity(
                    $user['uid'],
                    'success',
                    'User logged in successfully'
                );

                // Add system log for successful login
                addSystemLog(
                    $user['uid'],
                    'login',
                    'Successful login attempt',
                    'success'
                );

                $response = ['status' => 'success', 'message' => 'Login successful'];
            } else {
                // Log failed login attempt - wrong password
                logUserActivity(
                    $user['uid'],
                    'error',
                    'Failed login attempt - Invalid password'
                );

                // Add system log for failed login
                addSystemLog(
                    $user['uid'],
                    'login',
                    'Failed login attempt - Invalid password',
                    'error'
                );

                $response = ['status' => 'error', 'message' => 'Invalid password'];
            }
        } else {
            // Log failed login attempt - email not found
            // Note: We can't log to user_logs since there's no uid for non-existent user
            // But we can log to system_logs with a null user_id
            addSystemLog(
                null,
                'login',
                'Failed login attempt - Email not found: ' . $email,
                'warning'
            );

            $response = ['status' => 'error', 'message' => 'Email not found'];
        }
        $stmt->close();
    }
    $conn->close();
}

header('Content-Type: application/json');
echo json_encode($response);
exit();
