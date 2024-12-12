<?php
session_start();
require_once '../db_config.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
    exit();
}

$student_id = $_POST['student_id'] ?? '';
$year = $_POST['year'] ?? '';
$section = $_POST['section'] ?? '';
$program = $_POST['program'] ?? '';
$major = !empty($_POST['major']) ? $_POST['major'] : 'Undeclared';

if (empty($student_id) || empty($year) || empty($section) || empty($program)) {
    echo json_encode(['status' => 'error', 'message' => 'Required fields are missing']);
    exit();
}

try {
    // Verify that the program exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM programs WHERE program_code = ? AND status = 'Active'");
    $stmt->bind_param("s", $program);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->fetch_row()[0] == 0) {
        throw new Exception('Invalid program selected');
    }

    // Update student information
    $sql = "UPDATE student SET 
            year = ?, 
            section = ?, 
            program = ?, 
            major = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssi", $year, $section, $program, $major, $student_id);

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        throw new Exception($stmt->error);
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
