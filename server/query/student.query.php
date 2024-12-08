<?php
require_once __DIR__ . '/../db_config.php';

function getStudents()
{
    global $conn;
    $sql = "SELECT * FROM student";
    $result = $conn->query($sql);
    return $result;
}

function addStudent($uid, $year, $section, $status, $program, $major, $id_no)
{
    global $conn;
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
