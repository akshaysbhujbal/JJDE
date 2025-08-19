<?php
session_start();
include("../config/db.php");

// Only manager
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    die("Access Denied");
}

if (!isset($_GET['id'])) {
    die("Product ID is required.");
}

$id = intval($_GET['id']);

// Fetch existing product
$res = $conn->query("SELECT * FROM products WHERE product_id=$id");
if ($res->num_rows == 0) die("Product not found.");
$product = $res->fetch_assoc();

// Fetch categories for dropdown
$cat_result = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['product_name'];
    $category_id = $_POST['category_id'];
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $desc = $_POST['description'];

    // Image upload if new image provided
    if (!empty($_FILES['product_image']['name'])) {
        $targetDir = "../uploads/products/";
        $fileName = time() . "_" . basename($_FILES["product_image"]["name"]);
        $targetFile = $targetDir . $fileName;

        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $targetFile)) {
            $conn->query("UPDATE products SET product_image='$fileName' WHERE product_id=$id");
        }
    }

    $sql = "UPDATE products SET 
            product_name='$name',
            category_id=$category_id,
            price=$price,
            stock=$stock,
            description='$desc'
            WHERE product_id=$id";

    if ($conn->query($sql)) {
        $msg = "Product updated successfully!";
        // Refresh product info
        $res = $conn->query("SELECT * FROM products WHERE product_id=$id");
        $product = $res->fetch_assoc();
    } else {
        $msg = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Edit Product</title></head>
<body>
<h2>Edit Product</h2>
<?php if(isset($msg)) echo "<p>$msg</p>"; ?>
<form method="post" enctype="multipart/form-data">
    <input type="text" name="product_name" value="<?= $product['product_name'] ?>" placeholder="Product Name" required><br><br>
    
    <select name="category_id" required>
        <option value="">--Select Category--</option>
        <?php while($row = $cat_result->fetch_assoc()) { ?>
            <option value="<?= $row['category_id'] ?>" <?= $row['category_id']==$product['category_id']?'selected':'' ?>>
                <?= $row['category_name'] ?>
            </option>
        <?php } ?>
    </select><br><br>

    <input type="number" step="0.01" name="price" value="<?= $product['price'] ?>" placeholder="Price" required><br><br>
    <input type="number" name="stock" value="<?= $product['stock'] ?>" placeholder="Stock Quantity" required><br><br>
    <textarea name="description" placeholder="Description"><?= $product['description'] ?></textarea><br><br>

    <label>Current Image:</label><br>
    <img src="../uploads/products/<?= $product['product_image'] ?>" width="100"><br><br>

    <input type="file" name="product_image"><br><small>Upload new image to replace existing</small><br><br>
    <button type="submit">Update Product</button>
</form>
</body>
</html>
