<?php
session_start();
require_once '../db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$email = trim($_POST['email'] ?? '');
$password = trim($_POST['password'] ?? '');
$type = $_POST['type'] ?? '';
$first_name = trim($_POST['first_name'] ?? '');
$last_name = trim($_POST['last_name'] ?? '');

// Validation
if (empty($email) || empty($password) || empty($type) || empty($first_name) || empty($last_name)) {
    echo json_encode(['status' => 'error', 'message' => 'All fields are required']);
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(['status' => 'error', 'message' => 'Password must be at least 6 characters']);
    exit();
}

try {
    $conn->begin_transaction();

    // Check if email exists
    $stmt = $conn->prepare("SELECT id FROM users_auth WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        throw new Exception('Email already exists');
    }

    // Create user auth record
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $role = 'User';
    $uid = uniqid('USER_', true);

    $stmt = $conn->prepare("INSERT INTO users_auth (email, pwd, uid, role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $email, $hashedPassword, $uid, $role);
    $stmt->execute();

    // Update user info record with name and type
    $stmt = $conn->prepare("UPDATE user_info SET type = ?, first_name = ?, last_name = ? WHERE uid = ?");
    $stmt->bind_param("ssss", $type, $first_name, $last_name, $uid);
    $stmt->execute();

    $conn->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
