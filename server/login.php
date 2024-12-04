<?php
session_start();
include 'db_config.php'; // Include your database configuration file

// Check if the login form was submitted
if(isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Query the database to check if the user exists
    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($conn, $query);

    if(mysqli_num_rows($result) == 1) {
        // Fetch user data
        $user = mysqli_fetch_assoc($result);
        
        // Verify password with the stored hash
        if(password_verify($password, $user['user_password'])) {
            // Set session variable and redirect to the main page
            $_SESSION['username'] = $username;
            header("Location: index.php");
        } else {
            echo "Invalid username or password";
        }
    } else {
        echo "Invalid username or password";
    }
}
?>
