<?php
require_once __DIR__ . '/../db_config.php';

function getTeachers()
{
    global $conn;
    $sql = "SELECT t.*, CONCAT(ui.first_name, ' ', ui.last_name) as name 
            FROM teacher t
            JOIN user_info ui ON t.uid = ui.uid";
    $result = $conn->query($sql);
    return $result;
}

function addTeacher($email, $password, $firstName, $lastName, $department, $contact, $status)
{
    global $conn;

    // Start transaction
    $conn->begin_transaction();

    try {
        // 1. Insert into users_auth
        $role = 'User';
        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
        $sql1 = "INSERT INTO users_auth (email, pwd, role) VALUES (?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("sss", $email, $hashedPwd, $role);
        $stmt1->execute();

        // Get the generated uid
        $uid = $conn->query("SELECT uid FROM users_auth WHERE email = '$email'")->fetch_assoc()['uid'];

        // 2. Update user_info
        $type = 'Teacher';
        $sql2 = "UPDATE user_info SET first_name=?, last_name=?, type=? WHERE uid=?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ssss", $firstName, $lastName, $type, $uid);
        $stmt2->execute();

        // 3. Insert into teacher table
        $sql3 = "INSERT INTO teacher (uid, department, contact, status) VALUES (?, ?, ?, ?)";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("ssss", $uid, $department, $contact, $status);
        $stmt3->execute();

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

function getTeacherById($id)
{
    global $conn;
    $sql = "SELECT * FROM teacher WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}

function updateTeacher($id, $uid, $department, $contact, $status)
{
    global $conn;
    $sql = "UPDATE teacher SET uid=?, department=?, contact=?, status=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $uid, $department, $contact, $status, $id);
    return $stmt->execute();
}

function deleteTeacher($id)
{
    global $conn;
    $sql = "DELETE FROM teacher WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    return $stmt->execute();
}

function generateTeacherUID()
{
    global $conn;
    do {
        $uid = 'TCH-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
        $sql = "SELECT uid FROM teacher WHERE uid = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $uid);
        $stmt->execute();
        $result = $stmt->get_result();
    } while ($result->num_rows > 0);

    return $uid;
}
