<?php
session_start();
include("../config/db.php");

// Only manager
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    die("Access Denied");
}

// Fetch categories for dropdown
$cat_result = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = $_POST['description'];
    $created_by = $_SESSION['user_id'];

    // Image upload
    $targetDir = "../uploads/products/";
    $fileName = time() . "_" . basename($_FILES["product_image"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile)) {
        $sql = "INSERT INTO products (category_id, product_name, description, price, stock, created_by) 
                VALUES ($category_id, '$name', '$desc', $price, $stock, $created_by)";
        if ($conn->query($sql)) {
            $msg = "Product added successfully!";
        } else {
            $msg = "Error: " . $conn->error;
        }
    } else {
        $msg = "Failed to upload image!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Add Product</title></head>
<body>
<h2>Add New Product</h2>
<?php if(isset($msg)) echo "<p>$msg</p>"; ?>
<form method="post" enctype="multipart/form-data">
    <input type="text" name="product_name" placeholder="Product Name" required><br><br>
    
    <select name="category_id" required>
        <option value="">--Select Category--</option>
        <?php while($row = $cat_result->fetch_assoc()) { ?>
            <option value="<?= $row['category_id'] ?>"><?= $row['category_name'] ?></option>
        <?php } ?>
    </select><br><br>

    <input type="number" step="0.01" name="price" placeholder="Price" required><br><br>
    <input type="number" name="stock" placeholder="Stock Quantity" required><br><br>
    <textarea name="description" placeholder="Description"></textarea><br><br>
    <input type="file" name="product_image" required><br><br>
    <button type="submit">Add Product</button>
</form>
</body>
</html>
