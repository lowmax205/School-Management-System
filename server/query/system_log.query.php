<?php
require_once __DIR__ . '/../db_config.php';

function getSystemLogs()
{
    global $conn;
    $sql = "SELECT * FROM system_log";
    $result = $conn->query($sql);
    return $result;
}
