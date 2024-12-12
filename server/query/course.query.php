
<?php
require_once __DIR__ . '/../db_config.php';

function getAllPrograms()
{
    global $conn;
    $sql = "SELECT DISTINCT program_code, program_name FROM programs WHERE status = 'Active'";
    $result = $conn->query($sql);
    $programs = array();

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $programs[] = $row;
        }
    }
    return $programs;
}
