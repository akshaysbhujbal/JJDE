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
    // Delete product image first
    $res = $conn->query("SELECT product_id, product_name FROM products WHERE product_id=$id");
    if($res->num_rows){
        $conn->query("DELETE FROM products WHERE product_id=$id");
    }
    header("Location: manage_products.php");
    exit();
}

// Fetch products with category
$sql = "SELECT p.*, c.category_name FROM products p 
        LEFT JOIN categories c ON p.category_id=c.category_id
        ORDER BY p.product_id DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Products</title>
    <style>
        table { border-collapse: collapse; width: 90%; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
        img { width: 80px; }
    </style>
</head>
<body>
<h2>Manage Products</h2>
<a href="add_product.php">+ Add New Product</a><br><br>

<table>
<tr>
    <th>ID</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Image</th><th>Actions</th>
</tr>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['product_id'] ?></td>
    <td><?= $row['product_name'] ?></td>
    <td><?= $row['category_name'] ?></td>
    <td><?= number_format($row['price'],2) ?></td>
    <td><?= $row['stock'] ?></td>
    <td><img src="../uploads/products/<?= $row['product_image'] ?>" alt="<?= $row['product_name'] ?>"></td>
    <td>
        <a href="edit_product.php?id=<?= $row['product_id'] ?>">Edit</a> | 
        <a href="?action=delete&id=<?= $row['product_id'] ?>" onclick="return confirm('Delete this product?')">Delete</a>
    </td>
</tr>
<?php } ?>
</table>
</body>
</html>
    