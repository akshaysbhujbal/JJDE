<?php
$host = "localhost";
$user = "root";     // default for XAMPP
$pass = "";         // default empty
$db   = "jai_jogai_dairy_equipment";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
