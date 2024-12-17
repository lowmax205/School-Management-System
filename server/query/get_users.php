<?php
require_once '../db_config.php';
require_once 'user.query.php';

// Get current page from query string or default to 1
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Items per page
$offset = ($page - 1) * $limit;

// Get search term if any
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Get users with pagination
$result = getAllUsers($offset, $limit, $search);

// Include your users table template here
include '../../includes/users_table.php';
