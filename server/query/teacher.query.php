<?php
require_once __DIR__ . '/../db_config.php';

function getTeachers()
{
    global $conn;
    $sql = "SELECT * FROM teacher";
    $result = $conn->query($sql);
    return $result;
}

function addTeacher($uid, $department, $contact, $status)
{
    global $conn;
    $sql = "INSERT INTO teacher (uid, department, contact, status) 
            VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $uid, $department, $contact, $status);
    return $stmt->execute();
}
