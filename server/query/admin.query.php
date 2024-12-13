<?php
require_once __DIR__ . '/../db_config.php';

function getAdmins()
{
    global $conn;
    $sql = "SELECT a.*, u.email, ui.first_name, ui.last_name 
            FROM admin a 
            JOIN users_auth u ON a.uid = u.uid
            JOIN user_info ui ON a.uid = ui.uid";
    $result = $conn->query($sql);
    return $result;
}

function addAdmin($email, $password, $firstName, $lastName, $role, $status, $position)
{
    global $conn;

    $conn->begin_transaction();

    try {
        // 1. Insert into users_auth
        $authRole = 'Admin';
        $hashedPwd = password_hash($password, PASSWORD_DEFAULT);
        $sql1 = "INSERT INTO users_auth (email, pwd, role) VALUES (?, ?, ?)";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("sss", $email, $hashedPwd, $authRole);
        $stmt1->execute();

        // Get the generated uid
        $uid = $conn->query("SELECT uid FROM users_auth WHERE email = '$email'")->fetch_assoc()['uid'];

        // 2. Update user_info
        $type = 'Staff';
        $sql2 = "UPDATE user_info SET first_name=?, last_name=?, type=? WHERE uid=?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ssss", $firstName, $lastName, $type, $uid);
        $stmt2->execute();

        // 3. Insert into admin table
        $sql3 = "INSERT INTO admin (uid, role, status, position) VALUES (?, ?, ?, ?)";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bind_param("ssss", $uid, $role, $status, $position);
        $stmt3->execute();

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

function getAdminById($id)
{
    global $conn;
    $sql = "SELECT a.*, u.email, ui.first_name, ui.last_name 
            FROM admin a 
            JOIN users_auth u ON a.uid = u.uid 
            JOIN user_info ui ON a.uid = ui.uid
            WHERE a.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function changeUserRole($uid, $newType, $newRole)
{
    global $conn;

    $conn->begin_transaction();
    try {
        // Update user_info type
        $sql1 = "UPDATE user_info SET type = ? WHERE uid = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("ss", $newType, $uid);
        $stmt1->execute();

        // Update users_auth role
        $sql2 = "UPDATE users_auth SET role = ? WHERE uid = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ss", $newRole, $uid);
        $stmt2->execute();

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}
