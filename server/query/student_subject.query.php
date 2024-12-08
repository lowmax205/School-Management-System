<?php
require_once __DIR__ . '/../db_config.php';

function getSubjects()
{
    global $conn;
    $sql = "SELECT * FROM subject";
    $result = $conn->query($sql);
    return $result;
}
