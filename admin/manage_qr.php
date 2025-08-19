<?php
session_start();
include("../config/db.php");

// Only admin can upload QR
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    die("Access Denied");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $range = $_POST['amount_range'];

    // File upload
    $targetDir = "../uploads/qr/";
    $fileName = time() . "_" . basename($_FILES["qr_image"]["name"]);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($_FILES["qr_image"]["tmp_name"], $targetFile)) {
        $sql = "INSERT INTO qr_codes (title, qr_image, amount_range, uploaded_by) 
                VALUES ('$title', '$fileName', '$range', ".$_SESSION['user_id'].")";
        $conn->query($sql);
        $msg = "QR uploaded successfully!";
    } else {
        $msg = "Failed to upload QR!";
    }
}

$result = $conn->query("SELECT * FROM qr_codes ORDER BY uploaded_at DESC");
?>
<!DOCTYPE html>
<html>
<head><title>Manage QR Codes</title></head>
<body>
<h2>Upload New QR</h2>
<?php if (isset($msg)) echo "<p>$msg</p>"; ?>
<form method="post" enctype="multipart/form-data">
    <input type="text" name="title" placeholder="QR Title" required><br><br>
    <input type="text" name="amount_range" placeholder="Amount Range (e.g. 0-500)" required><br><br>
    <input type="file" name="qr_image" required><br><br>
    <button type="submit">Upload</button>
</form>

<h2>Uploaded QRs</h2>
<table border="1" cellpadding="5">
<tr><th>Title</th><th>Amount Range</th><th>Image</th><th>Uploaded At</th></tr>
<?php while($row = $result->fetch_assoc()) { ?>
<tr>
    <td><?= $row['title'] ?></td>
    <td><?= $row['amount_range'] ?></td>
    <td><img src="../uploads/qr/<?= $row['qr_image'] ?>" width="100"></td>
    <td><?= $row['uploaded_at'] ?></td>
</tr>
<?php } ?>
</table>
</body>
</html>
