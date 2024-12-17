<?php
session_start();
require_once '../db_config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if (!isset($_GET['uid'])) {
    echo json_encode(['status' => 'error', 'message' => 'User ID not provided']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT * FROM user_details_view WHERE uid = ?");
    $stmt->bind_param("s", $_GET['uid']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        echo json_encode($user);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'User not found']);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

$conn->close();
