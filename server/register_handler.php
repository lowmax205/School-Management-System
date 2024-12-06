
<?php
header('Content-Type: application/json');
include '../includes/db_config.php';

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    $password = $data['password'];
    
    if (!$email) {
        $response['message'] = 'Invalid email format';
    } elseif (strlen($password) < 6) {
        $response['message'] = 'Password must be at least 6 characters long';
    } else {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $response['message'] = 'Email already exists';
        } else {
            // Hash password and insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (username, user_password, email, phone_number) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ss", $email, $hashed_password);
            
            if ($stmt->execute()) {
                $response['success'] = true;
                $response['message'] = 'Registration successful';
            } else {
                $response['message'] = 'Database error occurred';
            }
        }
        $stmt->close();
    }
}

echo json_encode($response);