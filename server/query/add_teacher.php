
<?php
require_once 'teacher.query.php';
header('Content-Type: application/json');

try {
    // Validate required fields
    $required_fields = ['uid', 'name', 'department', 'contact', 'status'];
    foreach ($required_fields as $field) {
        if (!isset($_POST[$field]) || empty($_POST[$field])) {
            throw new Exception("Missing required field: $field");
        }
    }

    // Get form data
    $uid = $_POST['uid'];
    $name = $_POST['name'];
    $department = $_POST['department'];
    $contact = $_POST['contact'];
    $status = $_POST['status'];

    // Add teacher to database
    $result = addTeacher($email, $password, $firstName, $lastName, $department, $contact, $status);

    if ($result) {
        echo json_encode(['status' => 'success', 'message' => 'Teacher added successfully']);
    } else {
        throw new Exception("Failed to add teacher");
    }
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
