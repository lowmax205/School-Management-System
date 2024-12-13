<?php
require_once __DIR__ . '/../db_config.php';

function getAllUsers($start = 0, $limit = 10)
{
    global $conn;
    $query = "SELECT 
                ua.uid, 
                ua.email, 
                ua.role, 
                ui.first_name, 
                ui.last_name, 
                ui.type,
                CASE 
                    WHEN ui.type = 'Student' THEN s.status
                    WHEN ui.type = 'Teacher' THEN t.status
                    WHEN ui.type = 'Staff' THEN st.status
                    WHEN ua.role = 'Admin' THEN a.status
                    ELSE 'Active'
                END as status
              FROM users_auth ua 
              LEFT JOIN user_info ui ON ua.uid = ui.uid 
              LEFT JOIN student s ON ui.uid = s.uid AND ui.type = 'Student'
              LEFT JOIN teacher t ON ui.uid = t.uid AND ui.type = 'Teacher'
              LEFT JOIN staff st ON ui.uid = st.uid AND ui.type = 'Staff'
              LEFT JOIN admin a ON ui.uid = a.uid AND ua.role = 'Admin'
              LIMIT ?, ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $start, $limit);
    $stmt->execute();
    return $stmt->get_result();
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

function getTotalUsersCount()
{
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM users_auth";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return $row['total'];
}
