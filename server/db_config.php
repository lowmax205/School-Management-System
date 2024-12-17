<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'snsu_management';

$conn = new mysqli($host, $username, $password, $database);

if ($conn->connect_error) {
    error_log("Connection failed: " . $conn->connect_error);
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to ensure proper character handling
$conn->set_charset("utf8mb4");
