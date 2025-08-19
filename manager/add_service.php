<?php
session_start();
include("../config/db.php");

// Only manager
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    die("Access Denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['service_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];
    $created_by = $_SESSION['user_id'];

    $sql = "INSERT INTO services (service_name, description, price, created_by) 
            VALUES ('$name', '$desc', $price, $created_by)";
    if ($conn->query($sql)) {
        $msg = "Service added successfully!";
    } else {
        $msg = "Error: " . $conn->error;
    }
}

$result = $conn->query("SELECT * FROM services ORDER BY service_id DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Add Service</title></head>
<body>
<h2>Add New Service</h2>
<?php if(isset($msg)) echo "<p>$msg</p>"; ?>
<form method="post">
    <input type="text" name="service_name" placeholder="Service Name" required><br><br>
    <textarea name="description" placeholder="Description"></textarea><br><br>
    <input type="number" step="0.01" name="price" placeholder="Price" required><br><br>
    <button type="submit">Add Service</button>
</form>

<h2>Existing Services</h2>
<table border="1" cellpadding="5">
<tr><th>ID</th><th>Name</th><th>Description</th><th>Price</th></tr>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['service_id'] ?></td>
    <td><?= $row['service_name'] ?></td>
    <td><?= $row['description'] ?></td>
    <td><?= number_format($row['price'], 2) ?></td>
</tr>
<?php } ?>
</table>
</body>
</html>
