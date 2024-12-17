<?php
include 'server/db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, message) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $message);

    if ($stmt->execute()) {
        echo "<script>alert('Message sent successfully!');</script>";

        // Log the contact form submission
        $log_stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details, status) VALUES (?, ?, ?, ?)");
        $log_user_id = NULL; // Set to NULL for unauthenticated actions
        $log_action = 'Contact Form Submission';
        $log_details = "Name: $name, Email: $email, Message: $message";
        $log_status = 'success';
        $log_stmt->bind_param("ssss", $log_user_id, $log_action, $log_details, $log_status);
        $log_stmt->execute();
        $log_stmt->close();
    } else {
        echo "Error: " . $stmt->error;

        // Log the error
        $log_stmt = $conn->prepare("INSERT INTO system_logs (user_id, action, details, status) VALUES (?, ?, ?, ?)");
        $log_user_id = NULL; // Set to NULL for unauthenticated actions
        $log_action = 'Contact Form Submission';
        $log_details = "Error: " . $stmt->error;
        $log_status = 'error';
        $log_stmt->bind_param("ssss", $log_user_id, $log_action, $log_details, $log_status);
        $log_stmt->execute();
        $log_stmt->close();
    }

    $stmt->close();
}
?>

<div class="container py-5 mt-5">
    <h1 class="text-center mb-5">Contact Us</h1>
    <div class="row">
        <div class="col-md-6">
            <form class="contact-form" method="POST" action="">
                <div class="form-group">
                    <input type="text" name="name" class="form-control" placeholder="Your Name" required>
                </div>
                <div class="form-group">
                    <input type="email" name="email" class="form-control" placeholder="Your Email" required>
                </div>
                <div class="form-group">
                    <textarea name="message" class="form-control" rows="5" placeholder="Your Message" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Send Message</button>
            </form>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Get in Touch</h3>
                    <p class="card-text">Email: snsu@snsu.edu.ph</p>
                    <p class="card-text">Phone: (086) 826-1349</p>
                    <p class="card-text">Address: Surigao City, Surigao del Norte, Philippines 8400</p>
                </div>
            </div>
        </div>
    </div>
</div>