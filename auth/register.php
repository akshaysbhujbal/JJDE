<?php
include("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // secure hash
    $role = 'admin';

    $conn->query("INSERT INTO users (name,email,password,role) VALUES ('$name','$email','$password','$role')");
    echo "Admin registered. You can <a href='login.php'>login</a> now.";
    exit;
}
?>
<!DOCTYPE html>
<html>
<head><title>Register Admin</title></head>
<body>
<h2>Register Initial Admin</h2>
<form method="post">
    <input type="text" name="name" placeholder="Full Name" required><br><br>
    <input type="email" name="email" placeholder="Email" required><br><br>
    <input type="password" name="password" placeholder="Password" required><br><br>
    <button type="submit">Register Admin</button>
</form>
</body>
</html>
