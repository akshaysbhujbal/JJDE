<?php
session_start();

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'admin') {
        header("Location: admin/dashboard.php");
        exit();
    } elseif ($_SESSION['role'] == 'manager') {
        header("Location: manager/dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Jai Jogai Dairy Equipments</title>
</head>
<body>
    <h1>Welcome to Jai Jogai Dairy Equipments</h1>
    <p><a href="auth/login.php">Login Here</a></p>
</body>
</html>
