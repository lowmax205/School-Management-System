
<?php
function checkAuth($requiredRole = null)
{
    if (!isset($_SESSION['role'])) {
        echo "<script>
            alert('Please login to access this page.');
            window.location.href = '../../index.php';
        </script>";
        exit();
    }

    if ($requiredRole && $_SESSION['role'] !== $requiredRole) {
        echo "<script>
            alert('You are not authorized to access this page.');
            window.history.back();
        </script>";
        exit();
    }
}
