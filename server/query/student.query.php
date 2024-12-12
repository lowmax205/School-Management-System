<?php
require_once __DIR__ . '/../db_config.php';

function getStudents()
{
    global $conn;
    $sql = "SELECT s.*, 
            CONCAT(ui.first_name, ' ', ui.last_name) as name,
            ui.type,
            ui.gender,
            ui.birth_date,
            ui.address,
            ui.phone 
            FROM student s
            JOIN user_info ui ON s.uid = ui.uid
            JOIN users_auth ua ON s.uid = ua.uid
            WHERE ui.type = 'Student'
            ORDER BY s.id_no";
    $result = $conn->query($sql);
    return $result;
}

function generateStudentId()
{
    global $conn;
    $year = date('Y');

    // Get current count of students for this year
    $sql = "SELECT COUNT(*) as count FROM student WHERE LEFT(id_no, 4) = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $count = $result->fetch_assoc()['count'] + 1;

    // Format: YYYY-000001
    return $year . '-' . str_pad($count, 6, '0', STR_PAD_LEFT);
}

function addStudent($uid, $year, $section, $status, $program, $major)
{
    global $conn;
    $id_no = generateStudentId();

    $sql = "INSERT INTO student (uid, year, section, status, program, major, id_no) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sisssss", $uid, $year, $section, $status, $program, $major, $id_no);
    return $stmt->execute();
}

function getTotalUnits($uid)
{
    global $conn;
    $sql = "SELECT SUM(s.units) as total_units 
            FROM student_subject ss 
            JOIN subject s ON ss.subject_code = s.subject_code 
            WHERE ss.student_uid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['total_units'] ?? 0;
}
