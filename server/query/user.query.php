
<?php
require_once __DIR__ . '/../db_config.php';

function getAllUsers()
{
    global $conn;
    $sql = "SELECT ua.*, ui.first_name, ui.last_name, ui.type, ui.birth_date, 
                   ui.gender, ui.address, ui.phone 
            FROM users_auth ua
            LEFT JOIN user_info ui ON ua.uid = ui.uid
            ORDER BY ua.created_at DESC";
    return $conn->query($sql);
}

function updateUserRole($uid, $role, $type)
{
    global $conn;
    $conn->begin_transaction();

    try {
        // Update users_auth role
        $sql1 = "UPDATE users_auth SET role = ? WHERE uid = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bind_param("ss", $role, $uid);
        $stmt1->execute();

        // Update user_info type
        $sql2 = "UPDATE user_info SET type = ? WHERE uid = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("ss", $type, $uid);
        $stmt2->execute();

        $conn->commit();
        return true;
    } catch (Exception $e) {
        $conn->rollback();
        throw $e;
    }
}

function getUserDetails($uid)
{
    global $conn;
    $sql = "SELECT ua.*, ui.* 
            FROM users_auth ua
            LEFT JOIN user_info ui ON ua.uid = ui.uid
            WHERE ua.uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}
