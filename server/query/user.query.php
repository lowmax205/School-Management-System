<?php
require_once __DIR__ . '/../db_config.php';

function getAllUsers($start = 0, $limit = 10, $search = '')
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
              LEFT JOIN admin a ON ui.uid = a.uid AND ua.role = 'Admin'";
    
    if (!empty($search)) {
        $search = "%$search%";
        $query .= " WHERE CONCAT(ui.first_name, ' ', ui.last_name) LIKE ? OR ua.email LIKE ?";
    }
    
    $query .= " LIMIT ?, ?";
    
    $stmt = $conn->prepare($query);
    
    if (!empty($search)) {
        $stmt->bind_param("ssii", $search, $search, $start, $limit);
    } else {
        $stmt->bind_param("ii", $start, $limit);
    }
    
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

function getTotalUsersCount($search = '')
{
    global $conn;
    $sql = "SELECT COUNT(*) as total FROM users_auth ua LEFT JOIN user_info ui ON ua.uid = ui.uid";
    
    if (!empty($search)) {
        $search = "%$search%";
        $sql .= " WHERE CONCAT(ui.first_name, ' ', ui.last_name) LIKE ? OR ua.email LIKE ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $search, $search);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }
    
    $row = $result->fetch_assoc();
    return $row['total'];
}

function getUserStatistics() {
    global $conn;
    $stats = array();
    
    // Get total registered users
    $query = "SELECT COUNT(*) as total FROM users_auth";
    $result = mysqli_query($conn, $query);
    $stats['total_users'] = mysqli_fetch_assoc($result)['total'];
    
    // Get users by type from user_info table
    $query = "SELECT ui.type, COUNT(*) as count 
              FROM user_info ui 
              WHERE ui.type IS NOT NULL 
              GROUP BY ui.type";
    $result = mysqli_query($conn, $query);
    $stats['users_by_role'] = array();
    while($row = mysqli_fetch_assoc($result)) {
        $stats['users_by_role'][$row['type']] = $row['count'];
    }
    
    // Modify login activity query to count actual logins from user_logs
    $query = "SELECT 
                DATE(log_time) as date, 
                COUNT(*) as count 
              FROM user_logs 
              WHERE log_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                AND status = 'success'
                AND description LIKE '%logged in%'
              GROUP BY DATE(log_time)
              ORDER BY date ASC";
    $result = mysqli_query($conn, $query);
    $stats['login_activity'] = array();
    
    // Initialize all 7 days with 0 count
    for($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $stats['login_activity'][$date] = 0;
    }
    
    // Fill in actual login counts
    while($row = mysqli_fetch_assoc($result)) {
        $stats['login_activity'][$row['date']] = (int)$row['count'];
    }
    
    // Add log statistics
    $logStats = getUserLogStatistics();
    $stats['log_activity'] = $logStats['log_activity'];
    
    // Add system log statistics
    $log_stats = "SELECT DATE(created_at) as date,
                  SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END) as success,
                  SUM(CASE WHEN status = 'warning' THEN 1 ELSE 0 END) as warning,
                  SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END) as error
                  FROM system_logs 
                  WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
                  GROUP BY DATE(created_at)";
    $result = $conn->query($log_stats);
    $logs_by_date = [];
    while ($row = $result->fetch_assoc()) {
        $logs_by_date[$row['date']] = [
            'success' => (int)$row['success'],
            'warning' => (int)$row['warning'],
            'error' => (int)$row['error']
        ];
    }
    
    $stats['system_logs'] = $logs_by_date;
    
    return $stats;
}

function logUserActivity($uid, $status, $description) {
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $query = "INSERT INTO user_logs (uid, status, description, ip_address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $uid, $status, $description, $ip_address);
    return $stmt->execute();
}

function getUserLogStatistics() {
    global $conn;
    $stats = array();
    
    // Get logs by status for the last 7 days
    $query = "SELECT 
                DATE(log_time) as date,
                status,
                COUNT(*) as count 
              FROM user_logs 
              WHERE log_time >= DATE_SUB(NOW(), INTERVAL 7 DAY)
              GROUP BY DATE(log_time), status
              ORDER BY date ASC, status";
    
    $result = mysqli_query($conn, $query);
    $stats['log_activity'] = array();
    
    // Initialize all 7 days with 0 counts for each status
    for($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));
        $stats['log_activity'][$date] = array(
            'success' => 0,
            'error' => 0,
            'warning' => 0
        );
    }
    
    // Fill in actual counts
    while($row = mysqli_fetch_assoc($result)) {
        $stats['log_activity'][$row['date']][$row['status']] = (int)$row['count'];
    }
    
    return $stats;
}

function addSystemLog($userId, $action, $details, $status = 'success') {
    global $conn;
    $query = "INSERT INTO system_logs (user_id, action, details, status, created_at) 
              VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $userId, $action, $details, $status);
    return $stmt->execute();
}

function getSystemLogs($limit = 100) {
    global $conn;
    $query = "SELECT l.*, ua.email as username, ua.role 
              FROM system_logs l 
              JOIN users_auth ua ON l.user_id = ua.uid 
              ORDER BY l.created_at DESC 
              LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}