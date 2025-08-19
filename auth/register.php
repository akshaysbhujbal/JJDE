<?php
include("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password']; // stored as plain text
    $role = 'admin';

    // Check if email already exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $error = "Email already registered!";
    } else {
        $sql = "INSERT INTO users (name,email,password,role) VALUES ('$name','$email','$password','$role')";
        if ($conn->query($sql)) {
            $success = "Admin registered successfully. You can <a href='login.php'>login now</a>.";
        } else {
            $error = "Error: " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Register Admin - Jai Jogai Dairy Equipments</title>
<style>
    body { font-family: Arial; padding: 30px; }
    input { padding: 8px; width: 250px; margin-bottom: 10px; }
    button { padding: 10px 20px; }
    .error { color: red; margin-bottom: 10px; }
    .success { color: green; margin-bottom: 10px; }
</style>
</head>
<body>
<h2>Register Initial Admin</h2>

<?php
if (isset($error)) echo "<p class='error'>$error</p>";
if (isset($success)) echo "<p class='success'>$success</p>";
?>

<form method="post" autocomplete="on">
    <label for="name">Full Name:</label><br>
    <input type="text" name="name" id="name" placeholder="Full Name" required><br>

    <label for="email">Email:</label><br>
    <input type="email" name="email" id="email" placeholder="Email" autocomplete="username" required><br>

    <label for="password">Password:</label><br>
    <input type="password" name="password" id="password" placeholder="Password" autocomplete="new-password" required><br>

    <button type="submit">Register Admin</button>
</form>
</body>
</html>
