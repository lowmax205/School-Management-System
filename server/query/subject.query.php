<?php
require_once __DIR__ . '/../db_config.php';

function getSubjects()
{
    global $conn;
    $sql = "SELECT * FROM subject";
    $result = $conn->query($sql);
    return $result;
}

function addSubject($subject_code, $subject_name, $units, $department, $status)
{
    global $conn;
    $sql = "INSERT INTO subject (subject_code, subject_name, units, department, status) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssiss", $subject_code, $subject_name, $units, $department, $status);
    return $stmt->execute();
}

function enrollSubject($student_uid, $subject_code)
{
    global $conn;
    $sql = "INSERT INTO student_subject (student_uid, subject_code) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $student_uid, $subject_code);
    return $stmt->execute();
}

function getSubjectByCode($subject_code)
{
    global $conn;
    $sql = "SELECT * FROM subject WHERE subject_code = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $subject_code);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result->fetch_assoc();
}
