<?php
require_once '../db_config.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['uid'])) {
        throw new Exception('User ID is required');
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

    $conn->commit();
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
