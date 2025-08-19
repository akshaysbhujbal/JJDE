<?php
session_start();
include("../config/db.php");

// Only manager
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    die("Access Denied");
}

// Fetch products
$products = $conn->query("SELECT * FROM products ORDER BY product_name ASC");

// Fetch services
$services = $conn->query("SELECT * FROM services ORDER BY service_name ASC");

// Fetch QR codes
$qr_codes = $conn->query("SELECT * FROM qr_codes ORDER BY uploaded_at DESC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $customer_name = $_POST['customer_name'];
    $customer_phone = $_POST['customer_phone'];
    $payment_method = $_POST['payment_method'];
    $created_by = $_SESSION['user_id'];

    $total_amount = 0;

    // Calculate total for products
    $products_selected = $_POST['products'] ?? [];
    $quantities = $_POST['quantity'] ?? [];
    $product_totals = [];
    foreach ($products_selected as $pid => $q) {
        $res = $conn->query("SELECT price FROM products WHERE product_id=$pid")->fetch_assoc();
        $total = $res['price'] * $q;
        $total_amount += $total;
        $product_totals[$pid] = $total;
    }

    // Calculate total for services
    $services_selected = $_POST['services'] ?? [];
    $service_totals = [];
    foreach ($services_selected as $sid) {
        $res = $conn->query("SELECT price FROM services WHERE service_id=$sid")->fetch_assoc();
        $total_amount += $res['price'];
        $service_totals[$sid] = $res['price'];
    }

    // Insert into bills table
    $bill_no = "BILL" . time();
    $conn->query("INSERT INTO bills (bill_no, customer_name, customer_phone, total_amount, payment_method, created_by)
                  VALUES ('$bill_no', '$customer_name', '$customer_phone', $total_amount, '$payment_method', $created_by)");
    $bill_id = $conn->insert_id;

    // Insert bill items (products)
    foreach ($products_selected as $pid => $q) {
        $conn->query("INSERT INTO bill_items (bill_id, product_id, quantity, price, total)
                      VALUES ($bill_id, $pid, $q, ".$product_totals[$pid]."/$q, ".$product_totals[$pid].")");
    }

    // Insert bill items (services)
    foreach ($services_selected as $sid) {
        $conn->query("INSERT INTO bill_items (bill_id, service_id, quantity, price, total)
                      VALUES ($bill_id, $sid, 1, ".$service_totals[$sid].", ".$service_totals[$sid].")");
    }

    $msg = "Bill created successfully! Total: ₹" . number_format($total_amount,2);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Create Bill</title>
    <style>
        table { border-collapse: collapse; width: 80%; margin-bottom: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
        input[type=number] { width: 60px; }
    </style>
</head>
<body>
<h2>Create New Bill</h2>
<?php if(isset($msg)) echo "<p style='color:green;'>$msg</p>"; ?>

<form method="post">
    <input type="text" name="customer_name" placeholder="Customer Name" required><br><br>
    <input type="text" name="customer_phone" placeholder="Customer Phone" required><br><br>

    <h3>Select Products</h3>
    <table>
        <tr><th>Select</th><th>Product</th><th>Price</th><th>Quantity</th></tr>
        <?php while($p = $products->fetch_assoc()){ ?>
        <tr>
            <td><input type="checkbox" name="products[<?= $p['product_id'] ?>]"></td>
            <td><?= $p['product_name'] ?></td>
            <td><?= number_format($p['price'],2) ?></td>
            <td><input type="number" name="quantity[<?= $p['product_id'] ?>]" value="1" min="1"></td>
        </tr>
        <?php } ?>
    </table>

    <h3>Select Services</h3>
    <table>
        <tr><th>Select</th><th>Service</th><th>Price</th></tr>
        <?php while($s = $services->fetch_assoc()){ ?>
        <tr>
            <td><input type="checkbox" name="services[]" value="<?= $s['service_id'] ?>"></td>
            <td><?= $s['service_name'] ?></td>
            <td><?= number_format($s['price'],2) ?></td>
        </tr>
        <?php } ?>
    </table>

    <h3>Payment Method</h3>
    <select name="payment_method" required>
        <option value="cash">Cash</option>
        <option value="qr">QR Payment</option>
        <option value="card">Card</option>
    </select><br><br>

    <?php if ($qr_codes->num_rows > 0){ ?>
        <div id="qr-images">
        <h4>Available QR Codes</h4>
        <?php while($qr=$qr_codes->fetch_assoc()){ ?>
            <img src="../uploads/qr/<?= $qr['qr_image'] ?>" width="100" alt="<?= $qr['title'] ?>" title="<?= $qr['title'] ?>">
        <?php } ?>
        </div>
    <?php } ?>

    <button type="submit">Create Bill</button>
</form>
</body>
</html>
