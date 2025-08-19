<?php
session_start();
include("../config/db.php");

// Only manager can access
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    die("Access Denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['category_name'];
    $desc = $_POST['description'];
    $created_by = $_SESSION['user_id'];

    $sql = "INSERT INTO categories (category_name, description, created_by) 
            VALUES ('$name', '$desc', $created_by)";
    if ($conn->query($sql)) {
        $msg = "Category added successfully!";
    } else {
        $msg = "Error: " . $conn->error;
    }
}

$result = $conn->query("SELECT * FROM categories ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Add Category</title></head>
<body>
<h2>Add New Category</h2>
<?php if(isset($msg)) echo "<p>$msg</p>"; ?>
<form method="post">
    <input type="text" name="category_name" placeholder="Category Name" required><br><br>
    <textarea name="description" placeholder="Description"></textarea><br><br>
    <button type="submit">Add Category</button>
</form>

<h2>Existing Categories</h2>
<table border="1" cellpadding="5">
<tr><th>ID</th><th>Name</th><th>Description</th></tr>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['category_id'] ?></td>
    <td><?= $row['category_name'] ?></td>
    <td><?= $row['description'] ?></td>
</tr>
<?php } ?>
</table>
</body>
</html>
