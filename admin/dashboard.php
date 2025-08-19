<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit();
}

include("../includes/header.php");
include("../includes/navbar.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Jai Jogai Dairy Equipments</title>
    <style>
        body { font-family: Arial; padding: 20px; }
        h1 { margin-bottom: 20px; }
        a { display: block; margin-bottom: 10px; }
        .welcome { margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="welcome">
        <h1>Welcome, <?php echo $_SESSION['name']; ?> (Admin)</h1>
    </div>

    <h2>Admin Actions</h2>
    <a href="add_manager.php">Add Manager</a>
    <a href="manage_qr.php">Upload/Manage QR Codes</a>
    <a href="../auth/logout.php">Logout</a>
</body>
</html>

<?php
include("../includes/footer.php");
?>
