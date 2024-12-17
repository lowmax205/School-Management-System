<?php
require_once __DIR__ . '/../db_config.php';

// Set timezone to Manila/Philippines
date_default_timezone_set('Asia/Manila');

function getAllUsers($start = 0, $limit = 10, $search = '')
{
    global $conn;
    $query = "SELECT * FROM user_details_view";

    if (!empty($search)) {
        $search = "%$search%";
        $query .= " WHERE CONCAT(first_name, ' ', last_name) LIKE ? OR email LIKE ?";
    }

    $query .= " ORDER BY account_created DESC LIMIT ?, ?";

    $stmt = $conn->prepare($query);

    if (!empty($search)) {
        $stmt->bind_param("ssii", $search, $search, $start, $limit);
    } else {
        $stmt->bind_param("ii", $start, $limit);
    }

    $stmt->execute();
    return $stmt->get_result();
}

function getUserDetails($uid)
{
    global $conn;
    $sql = "SELECT * FROM user_details_view WHERE uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
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

function getUserStatistics()
{
    global $conn;
    $stats = array();

    // Initialize arrays
    $stats['users_by_role'] = array();
    $stats['login_activity'] = array();
    $stats['system_logs'] = array();
    $stats['total_users'] = 0;

    // Get total registered users
    $query = "SELECT COUNT(*) as total FROM users_auth";
    $result = mysqli_query($conn, $query);
    if ($result && $row = mysqli_fetch_assoc($result)) {
        $stats['total_users'] = (int)$row['total'];
    }

    // Modified query to only get Teacher, Staff, and Student counts
    $query = "SELECT type, COUNT(*) as count 
              FROM user_info 
              WHERE type IN ('Teacher', 'Staff', 'Student')
              GROUP BY type
              ORDER BY FIELD(type, 'Teacher', 'Staff', 'Student')";

    if ($result = mysqli_query($conn, $query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            if ($row['type'] !== null) {
                $stats['users_by_role'][$row['type']] = (int)$row['count'];
            }
        }
    }

    // Initialize dates for the last 7 days
    $dates = array();
    $currentDate = date('Y-m-d', strtotime('today')); // Ensure we get today's date
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("$currentDate -$i days"));
        $dates[$date] = 0;
    }

    // Modified login activity query to count both successful and failed logins
    $login_query = "SELECT 
                    DATE(log_time) as date,
                    status,
                    COUNT(*) as count 
                    FROM user_logs 
                    WHERE log_time >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 DAY)
                    AND (description LIKE '%logged in successfully%' 
                         OR description LIKE '%Failed login attempt%')
                    GROUP BY DATE(log_time), status
                    ORDER BY date ASC";

    // Initialize login activity with zeros for all dates
    $stats['login_activity'] = array();
    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("$currentDate -$i days"));
        $stats['login_activity'][$date] = [
            'success' => 0,
            'failed' => 0
        ];
    }

    // Fill in actual login counts
    if ($result = mysqli_query($conn, $login_query)) {
        while ($row = mysqli_fetch_assoc($result)) {
            if (isset($stats['login_activity'][$row['date']])) {
                $type = $row['status'] === 'error' ? 'failed' : 'success';
                $stats['login_activity'][$row['date']][$type] = (int)$row['count'];
            }
        }
    }

    // Add system log statistics with error handling
    $log_stats = "SELECT DATE(created_at) as date,
                  COALESCE(SUM(CASE WHEN status = 'success' THEN 1 ELSE 0 END), 0) as success,
                  COALESCE(SUM(CASE WHEN status = 'warning' THEN 1 ELSE 0 END), 0) as warning,
                  COALESCE(SUM(CASE WHEN status = 'error' THEN 1 ELSE 0 END), 0) as error
                  FROM system_logs 
                  WHERE created_at >= DATE_SUB(CURRENT_DATE(), INTERVAL 6 DAY)
                  GROUP BY DATE(created_at)
                  ORDER BY date ASC";

    // Initialize system logs with zeros for all dates
    $stats['system_logs'] = array();
    foreach ($dates as $date => $value) {
        $stats['system_logs'][$date] = [
            'success' => 0,
            'warning' => 0,
            'error' => 0
        ];
    }

    // Fill in actual system log counts
    if ($result = $conn->query($log_stats)) {
        while ($row = $result->fetch_assoc()) {
            if (isset($stats['system_logs'][$row['date']])) {
                $stats['system_logs'][$row['date']] = [
                    'success' => (int)$row['success'],
                    'warning' => (int)$row['warning'],
                    'error' => (int)$row['error']
                ];
            }
        }
    }

    return $stats;
}

function logUserActivity($uid, $status, $description)
{
    global $conn;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $query = "INSERT INTO user_logs (uid, status, description, ip_address) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $uid, $status, $description, $ip_address);
    return $stmt->execute();
}

function addSystemLog($userId, $action, $details, $status = 'success')
{
    global $conn;
    $query = "INSERT INTO system_logs (user_id, action, details, status, created_at) 
              VALUES (?, ?, ?, ?, NOW())";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssss", $userId, $action, $details, $status);
    return $stmt->execute();
}

function getSystemLogs($limit = 100)
{
    global $conn;
    $query = "SELECT l.*, ua.email as username, ua.role 
              FROM system_logs l 
              LEFT JOIN users_auth ua ON l.user_id = ua.uid 
              ORDER BY l.created_at DESC 
              LIMIT ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $limit);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
