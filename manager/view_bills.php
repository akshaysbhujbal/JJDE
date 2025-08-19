<?php
session_start();
include("../config/db.php");

// Only manager
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'manager') {
    die("Access Denied");
}

// Fetch all bills
$sql = "SELECT b.*, u.name AS manager_name 
        FROM bills b 
        LEFT JOIN users u ON b.created_by=u.user_id
        ORDER BY b.bill_id DESC";
$bills = $conn->query($sql);

// View bill details if bill_id is set
$bill_details = null;
if (isset($_GET['view_id'])) {
    $bill_id = intval($_GET['view_id']);
    $res = $conn->query("SELECT * FROM bills WHERE bill_id=$bill_id");
    if ($res->num_rows) {
        $bill_details = $res->fetch_assoc();
        $items = $conn->query("SELECT bi.*, p.product_name, s.service_name
                               FROM bill_items bi
                               LEFT JOIN products p ON bi.product_id=p.product_id
                               LEFT JOIN services s ON bi.service_id=s.service_id
                               WHERE bi.bill_id=$bill_id");
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Bills</title>
    <style>
        table { border-collapse: collapse; width: 90%; margin-bottom: 20px; }
        th, td { border: 1px solid #aaa; padding: 8px; text-align: center; }
    </style>
</head>
<body>
<h2>All Bills</h2>
<table>
<tr>
    <th>Bill No</th><th>Customer</th><th>Phone</th><th>Total Amount</th><th>Payment Method</th><th>Created By</th><th>Date</th><th>Actions</th>
</tr>
<?php while($b = $bills->fetch_assoc()){ ?>
<tr>
    <td><?= $b['bill_no'] ?></td>
    <td><?= $b['customer_name'] ?></td>
    <td><?= $b['customer_phone'] ?></td>
    <td>₹<?= number_format($b['total_amount'],2) ?></td>
    <td><?= ucfirst($b['payment_method']) ?></td>
    <td><?= $b['manager_name'] ?></td>
    <td><?= $b['created_at'] ?></td>
    <td><a href="?view_id=<?= $b['bill_id'] ?>">View</a></td>
</tr>
<?php } ?>
</table>

<?php if($bill_details) { ?>
<h2>Bill Details: <?= $bill_details['bill_no'] ?></h2>
<p>Customer: <?= $bill_details['customer_name'] ?> | Phone: <?= $bill_details['customer_phone'] ?></p>
<p>Payment Method: <?= ucfirst($bill_details['payment_method']) ?></p>
<p>Total Amount: ₹<?= number_format($bill_details['total_amount'],2) ?></p>

<h3>Items</h3>
<table>
<tr><th>Type</th><th>Name</th><th>Quantity</th><th>Price</th><th>Total</th></tr>
<?php while($item = $items->fetch_assoc()) { ?>
<tr>
    <td><?= $item['product_id'] ? 'Product' : 'Service' ?></td>
    <td><?= $item['product_name'] ?? $item['service_name'] ?></td>
    <td><?= $item['quantity'] ?></td>
    <td>₹<?= number_format($item['price'],2) ?></td>
    <td>₹<?= number_format($item['total'],2) ?></td>
</tr>
<?php } ?>
</table>
<?php } ?>
</body>
</html>
