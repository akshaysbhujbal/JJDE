<?php
session_start();
include("../config/db.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Match these names with form 'name' attributes
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from database
    $res = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($res->num_rows > 0) {
        $user = $res->fetch_assoc();
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Redirect based on role
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../manager/dashboard.php");
            }
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Email not found!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - Jai Jogai Dairy Equipments</title>
<style>
    body { font-family: Arial; padding: 30px; }
    input { padding: 8px; width: 250px; margin-bottom: 10px; }
    button { padding: 10px 20px; }
    .error { color: red; margin-bottom: 10px; }
</style>
</head>
<body>
<h2>Login</h2>
<?php if(isset($error)) echo "<p class='error'>$error</p>"; ?>
<form method="post" autocomplete="on">
    <label for="email">Email:</label><br>
    <input type="email" name="email" id="email" placeholder="Email" autocomplete="username" required><br>

    <label for="password">Password:</label><br>
    <input type="password" name="password" id="password" placeholder="Password" autocomplete="current-password" required><br>

    <button type="submit">Login</button>
</form>
</body>
</html>
