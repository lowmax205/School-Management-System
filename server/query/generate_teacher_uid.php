
<?php
require_once __DIR__ . '/../db_config.php';

function generateTeacherUID()
{
    global $conn;
    $sql = "SELECT MAX(id) as max_id FROM teacher";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    $maxId = $row['max_id'] + 1;
    return 'T' . str_pad($maxId, 5, '0', STR_PAD_LEFT);
}
