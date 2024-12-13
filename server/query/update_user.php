<?php
require_once '../db_config.php';

header('Content-Type: application/json');

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!isset($data['uid']) || !isset($data['email']) || !isset($data['role']) || 
        !isset($data['type']) || !isset($data['first_name']) || !isset($data['last_name'])) {
        throw new Exception('Missing required fields');
    }

    $conn->begin_transaction();

    // Update users_auth table
    $sql1 = "UPDATE users_auth SET email = ?, role = ? WHERE uid = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param("sss", $data['email'], $data['role'], $data['uid']);
    $stmt1->execute();

    // Update user_info table
    $sql2 = "UPDATE user_info SET first_name = ?, last_name = ?, type = ? WHERE uid = ?";
    $stmt2 = $conn->prepare($sql2);
    $stmt2->bind_param("ssss", $data['first_name'], $data['last_name'], $data['type'], $data['uid']);
    $stmt2->execute();

    $conn->commit();
    echo json_encode(['status' => 'success']);

} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
