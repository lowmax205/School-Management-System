<?php
// Add error handling configuration
error_reporting(0);
ini_set('display_errors', 0);
header('Content-Type: application/json');
include '../includes/db_config.php';

$response = ['success' => false, 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if ($data === null) {
            throw new Exception('Invalid JSON data received');
        }
        
        $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
        $password = $data['password'];
        
        if (!$email) {
            $response['message'] = 'Invalid email format';
        } elseif (strlen($password) < 6) {
            $response['message'] = 'Password must be at least 6 characters long';
        } else {
            // Check if email already exists
            $stmt = $conn->prepare("SELECT id FROM users_auth WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $response['message'] = 'Email already exists';
            } else {
                // Hash password and insert new user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $conn->prepare("INSERT INTO users_auth (email, pwd) VALUES (?, ?)");
                $stmt->bind_param("ss", $email, $hashed_password);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Registration successful';
                } else {
                    throw new Exception('Database error occurred');
                }
            }
            $stmt->close();
        }
    }
} catch (Exception $e) {
    $response['success'] = false;
    $response['message'] = 'Server error: ' . $e->getMessage();
}

echo json_encode($response);
exit;