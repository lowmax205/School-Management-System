<?php
include 'db_config.php'; 

if(isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Email validation (contains '@')
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format. Please enter a valid email.";
    } 
    // Phone number validation (must be 11 digits)
    elseif (strlen($phone) != 11 || !ctype_digit($phone)) {
        echo "Phone number must be exactly 11 digits.";
    }
    // Password confirmation validation
    elseif ($password !== $confirm_password) {
        echo "Passwords do not match.";
    } else {
        // Hash the password using password_hash()
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepare the SQL statement
        $stmt = $conn->prepare("INSERT INTO users (username, user_password, email, phone_number) VALUES (?, ?, ?, ?)");
        
        // Bind parameters to the SQL query
        $stmt->bind_param("ssss", $username, $hashed_password, $email, $phone);

        // Execute the query
        if($stmt->execute()) {
            echo "Account created successfully. You can now <a href='index.php'>login</a>.";
        } else {
            echo "Error creating account: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Create Account</title>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var confirmPasswordField = document.getElementById("confirm_password");
            var passwordFieldType = passwordField.type;
            if (passwordFieldType === "password") {
                passwordField.type = "text";
                confirmPasswordField.type = "text";
            } else {
                passwordField.type = "password";
                confirmPasswordField.type = "password";
            }
        }
    </script>
</head>
<body>
    <form action="register.php" method="POST">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>

        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>

        <label for="phone">Phone Number (11 digits):</label><br>
        <input type="text" id="phone" name="phone" maxlength="11" required><br><br>

        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="confirm_password">Confirm Password:</label><br>
        <input type="password" id="confirm_password" name="confirm_password" required><br><br>

        <label for="show_password">
            <input type="checkbox" id="show_password" onclick="togglePassword()"> Show Password
        </label><br><br>

        <button type="submit" name="register">Create Account</button>
    </form>
</body>
</html>
