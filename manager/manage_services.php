<?php
session_start();
include("../config/db.php");

// Only manager
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    die("Access Denied");
}

// Handle delete
if (isset($_GET['action']) && $_GET['action']=='delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $conn->query("DELETE FROM services WHERE service_id=$id");
    header("Location: manage_services.php");
    exit();
}

// Fetch all services
$result = $conn->query("SELECT * FROM services ORDER BY service_id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Services</title>
    <style>
        table { border-collapse: collapse; width: 70%; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
    </style>
</head>
<body>
<h2>Manage Services</h2>
<a href="add_service.php">+ Add New Service</a><br><br>

<table>
<tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th><th>Actions</th></tr>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['service_id'] ?></td>
    <td><?= $row['service_name'] ?></td>
    <td><?= $row['description'] ?></td>
    <td>₹<?= number_format($row['price'],2) ?></td>
    <td>
        <a href="edit_service.php?id=<?= $row['service_id'] ?>">Edit</a> |
        <a href="?action=delete&id=<?= $row['service_id'] ?>" onclick="return confirm('Delete this service?')">Delete</a>
    </td>
</tr>
<?php } ?>
</table>
</body>
</html>
