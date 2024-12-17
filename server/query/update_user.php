<?php
session_start();
error_reporting(E_ALL); // Enable error reporting for debugging
ini_set('display_errors', 1);
header('Content-Type: application/json');

require_once '../db_config.php';
require_once 'user.query.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

try {
    $input = file_get_contents('php://input');
    if (empty($input)) {
        throw new Exception('No data received');
    }

    $data = json_decode($input, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception('Invalid JSON: ' . json_last_error_msg());
    }

    if (empty($data['uid'])) {
        throw new Exception('User ID is required');
    }

    // Get original user data for comparison
    $stmt = $conn->prepare("SELECT email, role FROM users_auth WHERE uid = ?");
    $stmt->bind_param("s", $data['uid']);
    $stmt->execute();
    $oldData = $stmt->get_result()->fetch_assoc();

    $conn->begin_transaction();

    // Update users_auth table
    $sql1 = "UPDATE users_auth SET email = ?, role = ? WHERE uid = ?";
    $stmt1 = $conn->prepare($sql1);
    if (!$stmt1) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt1->bind_param("sss", $data['email'], $data['role'], $data['uid']);
    if (!$stmt1->execute()) {
        throw new Exception("Execute failed: " . $stmt1->error);
    }

    // Update user_info table with additional fields
    $sql2 = "UPDATE user_info SET 
             first_name = ?, 
             last_name = ?, 
             type = ?,
             birth_date = NULLIF(?, ''),
             gender = NULLIF(?, ''),
             address = NULLIF(?, ''),
             phone = NULLIF(?, '')
             WHERE uid = ?";

    $stmt2 = $conn->prepare($sql2);
    if (!$stmt2) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    $stmt2->bind_param(
        "ssssssss",
        $data['first_name'],
        $data['last_name'],
        $data['type'],
        $data['birth_date'],
        $data['gender'],
        $data['address'],
        $data['phone'],
        $data['uid']
    );

    if (!$stmt2->execute()) {
        throw new Exception("Execute failed: " . $stmt2->error);
    }

    // Update status if provided
    if (isset($data['status'])) {
        $status = $data['status'];
        $type = $data['type'];
        $uid = $data['uid'];

        $table = strtolower($type); // student, teacher, or staff
        if (in_array($table, ['student', 'teacher', 'staff'])) {
            $sql3 = "INSERT INTO {$table} (uid, status) VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE status = ?";
            $stmt3 = $conn->prepare($sql3);
            if (!$stmt3) {
                throw new Exception("Prepare failed: " . $conn->error);
            }
            $stmt3->bind_param("sss", $uid, $status, $status);
            if (!$stmt3->execute()) {
                throw new Exception("Execute failed: " . $stmt3->error);
            }
        }
    }

    // Update password if provided
    if (!empty($data['password'])) {
        $hashed_password = password_hash($data['password'], PASSWORD_DEFAULT);
        $sql3 = "UPDATE users_auth SET pwd = ? WHERE uid = ?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("ss", $hashed_password, $data['uid']);
        $stmt3->execute();
    }

    // Log the changes
    $adminUid = $_SESSION['uid'];
    $changes = [];

    if ($oldData['email'] != $data['email']) {
        $changes[] = "email: {$oldData['email']} → {$data['email']}";
    }
    if ($oldData['role'] != $data['role']) {
        $changes[] = "role: {$oldData['role']} → {$data['role']}";
    }

    // Log in user_logs
    logUserActivity(
        $adminUid,
        'success',
        "Updated user (UID: {$data['uid']}) - Changes: " . implode(", ", $changes)
    );

    // Log in system_logs
    addSystemLog(
        $adminUid,
        'user_update',
        "Admin updated user information - UID: {$data['uid']}, Changes: " . implode(", ", $changes),
        'success'
    );

    $conn->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    if (isset($conn) && !$conn->connect_error) {
        $conn->rollback();
    }
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage(),
        'trace' => debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS)
    ]);
}

$conn->close();
