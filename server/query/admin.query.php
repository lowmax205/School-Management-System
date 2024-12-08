<?php
require_once __DIR__ . '/../db_config.php';

function getAdmins()
{
    global $conn;
    $sql = "SELECT * FROM admin";
    $result = $conn->query($sql);
    return $result;
}
