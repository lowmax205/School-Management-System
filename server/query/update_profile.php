<?php
session_start();
require_once '../db_config.php';
require_once 'user.query.php'; // Add this line

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

if (!isset($_SESSION['uid'])) {
    echo json_encode(['status' => 'error', 'message' => 'User not authenticated']);
    exit();
}

$uid = $_SESSION['uid'];
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$birth_date = $_POST['birth_date'] ?: null;
$gender = $_POST['gender'] ?: null;
$address = $_POST['address'] ?: null;
$phone = $_POST['phone'] ?: null;

if (empty($first_name) || empty($last_name)) {
    echo json_encode(['status' => 'error', 'message' => 'First name and last name are required']);
    exit();
}

try {
    $sql = "UPDATE user_info SET 
            first_name = ?,
            last_name = ?,
            birth_date = ?,
            gender = ?,
            address = ?,
            phone = ?
            WHERE uid = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssss",
        $first_name,
        $last_name,
        $birth_date,
        $gender,
        $address,
        $phone,
        $uid
    );

    if ($stmt->execute()) {
        // Log the profile update
        logUserActivity(
            $uid,
            'success',
            "Updated profile information"
        );

        // Add system log
        addSystemLog(
            $uid,
            'update profile',
            "User updated their profile information - Name: $first_name $last_name",
            'success'
        );

        // Update session data
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;

        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception('Failed to update profile');
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
