<?php
session_start();
include("../config/db.php");

// Allow only admin
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access Denied");
}

// Handle delete or status change
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    if ($_GET['action'] == "delete") {
        $conn->query("DELETE FROM users WHERE user_id=$id AND role='manager'");
    } elseif ($_GET['action'] == "deactivate") {
        $conn->query("UPDATE users SET status='inactive' WHERE user_id=$id AND role='manager'");
    } elseif ($_GET['action'] == "activate") {
        $conn->query("UPDATE users SET status='active' WHERE user_id=$id AND role='manager'");
    }
    header("Location: view_managers.php");
    exit();
}

$result = $conn->query("SELECT * FROM users WHERE role='manager'");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Managers</title>
    <style>
        table { border-collapse: collapse; width: 70%; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
    </style>
</head>
<body>
<h2>Managers List</h2>
<a href="add_manager.php">+ Add New Manager</a><br><br>

<table>
    <tr>
        <th>ID</th><th>Name</th><th>Email</th><th>Status</th><th>Actions</th>
    </tr>
    <?php while($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['user_id'] ?></td>
        <td><?= $row['name'] ?></td>
        <td><?= $row['email'] ?></td>
        <td><?= ucfirst($row['status']) ?></td>
        <td>
            <?php if($row['status']=='active'){ ?>
                <a href="?action=deactivate&id=<?= $row['user_id'] ?>">Deactivate</a>
            <?php } else { ?>
                <a href="?action=activate&id=<?= $row['user_id'] ?>">Activate</a>
            <?php } ?>
            | <a href="?action=delete&id=<?= $row['user_id'] ?>" onclick="return confirm('Delete manager?')">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
</body>
</html>
