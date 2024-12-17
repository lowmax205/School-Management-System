<?php
session_start();
require_once '../db_config.php';
require_once 'user.query.php';

header('Content-Type: application/json');

// Check if user is logged in and is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Unauthorized access'
    ]);
    exit();
}

try {
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['uid'])) {
        throw new Exception('User ID is required');
    }

    // Get user details before deletion for logging
    $stmt = $conn->prepare("SELECT ua.email, ui.first_name, ui.last_name, ui.type 
                           FROM users_auth ua 
                           LEFT JOIN user_info ui ON ua.uid = ui.uid 
                           WHERE ua.uid = ?");
    $stmt->bind_param("s", $data['uid']);
    $stmt->execute();
    $userResult = $stmt->get_result()->fetch_assoc();

    if (!$userResult) {
        throw new Exception('User not found');
    }

    $conn->begin_transaction();

    // Delete from user_info first (due to foreign key constraint)
    $sql1 = "DELETE FROM user_info WHERE uid = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("s", $data['uid']);
    $stmt1->execute();

    // Then delete from users_auth
    $sql2 = "DELETE FROM users_auth WHERE uid = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("s", $data['uid']);
    $stmt2->execute();

    // Log the deletion in user_logs
    logUserActivity(
        $_SESSION['uid'],
        'warning',
        sprintf(
            "Deleted user: %s (%s %s) - Type: %s",
            $userResult['email'],
            $userResult['first_name'],
            $userResult['last_name'],
            $userResult['type']
        )
    );

    // Log the deletion in system_logs
    addSystemLog(
        $_SESSION['uid'],
        'user_delete',
        sprintf(
            "Admin deleted user - Email: %s, Name: %s %s, Type: %s",
            $userResult['email'],
            $userResult['first_name'],
            $userResult['last_name'],
            $userResult['type']
        ),
        'warning'
    );

    $conn->commit();
    echo json_encode(['status' => 'success']);
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
