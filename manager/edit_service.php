<?php
session_start();
include("../config/db.php");

// Only manager
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    die("Access Denied");
}

if (!isset($_GET['id'])) die("Service ID is required");

$id = intval($_GET['id']);
$res = $conn->query("SELECT * FROM services WHERE service_id=$id");
if ($res->num_rows == 0) die("Service not found");
$service = $res->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['service_name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    $sql = "UPDATE services SET service_name='$name', description='$desc', price=$price WHERE service_id=$id";
    if ($conn->query($sql)) {
        $msg = "Service updated successfully!";
        $res = $conn->query("SELECT * FROM services WHERE service_id=$id");
        $service = $res->fetch_assoc();
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Service</title></head>
<body>
<h2>Edit Service</h2>
<?php if(isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>
<form method="post">
    <input type="text" name="service_name" value="<?= $service['service_name'] ?>" placeholder="Service Name" required><br><br>
    <textarea name="description" placeholder="Description"><?= $service['description'] ?></textarea><br><br>
    <input type="number" step="0.01" name="price" value="<?= $service['price'] ?>" placeholder="Price" required><br><br>
    <button type="submit">Update Service</button>
</form>
</body>
</html>
