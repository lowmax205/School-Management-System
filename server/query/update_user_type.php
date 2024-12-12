<?php
require_once '../db_config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $uid = $_POST['uid'] ?? '';
    $type = $_POST['type'] ?? '';

    if (empty($uid) || empty($type)) {
        echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        exit;
    }

    // Start transaction
    $conn->begin_transaction();

    try {
        // First get current user type
        $check_type = $conn->prepare("SELECT type FROM user_info WHERE uid = ?");
        $check_type->bind_param("s", $uid);
        $check_type->execute();
        $current_type = $check_type->get_result()->fetch_assoc()['type'];

        // Update user type
        $sql = "UPDATE user_info SET type = ? WHERE uid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $type, $uid);
        $stmt->execute();

        // Handle different type cases
        switch ($type) {
            case 'Student':
                // Clear existing roles
                $stmt = $conn->prepare("DELETE FROM teacher WHERE uid = ?");
                $stmt->bind_param("s", $uid);
                $stmt->execute();
                $stmt = $conn->prepare("DELETE FROM staff WHERE uid = ?");
                $stmt->bind_param("s", $uid);
                $stmt->execute();
                $stmt = $conn->prepare("DELETE FROM student WHERE uid = ?");
                $stmt->bind_param("s", $uid);
                $stmt->execute();

                // Insert new student record
                $id_no = date('Y') . '-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
                $sql = "INSERT INTO student (uid, year, section, status, program, major, id_no) 
                        VALUES (?, 1, 'A', 'Active', 'Unassigned', 'Undeclared', ?)";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ss", $uid, $id_no);
                $stmt->execute();
                break;

            case 'Teacher':
                // Remove other role records
                $stmt = $conn->prepare("DELETE FROM student WHERE uid = ?");
                $stmt->bind_param("s", $uid);
                $stmt->execute();
                $stmt = $conn->prepare("DELETE FROM staff WHERE uid = ?");
                $stmt->bind_param("s", $uid);
                $stmt->execute();

                // Check if teacher record exists
                $check = $conn->prepare("SELECT uid FROM teacher WHERE uid = ?");
                $check->bind_param("s", $uid);
                $check->execute();
                if ($check->get_result()->num_rows === 0) {
                    $sql = "INSERT INTO teacher (uid, department, status) VALUES (?, 'Unassigned', 'Active')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $uid);
                    $stmt->execute();
                }
                break;

            case 'Staff':
                // Remove other role records
                $stmt = $conn->prepare("DELETE FROM student WHERE uid = ?");
                $stmt->bind_param("s", $uid);
                $stmt->execute();
                $stmt = $conn->prepare("DELETE FROM teacher WHERE uid = ?");
                $stmt->bind_param("s", $uid);
                $stmt->execute();

                // Check if staff record exists
                $check = $conn->prepare("SELECT uid FROM staff WHERE uid = ?");
                $check->bind_param("s", $uid);
                $check->execute();
                if ($check->get_result()->num_rows === 0) {
                    $sql = "INSERT INTO staff (uid, department, position) VALUES (?, 'Unassigned', 'General Staff')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $uid);
                    $stmt->execute();
                }
                break;

            default:
                throw new Exception("Invalid user type specified");
        }

        $conn->commit();
        echo json_encode(['status' => 'success']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

    $conn->close();
}
