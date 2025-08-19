<?php
require('../fpdf/fpdf.php');
include("../config/db.php");

if (!isset($_GET['bill_id'])) die("Bill ID required");

$bill_id = intval($_GET['bill_id']);

// Fetch bill
$bill = $conn->query("SELECT * FROM bills WHERE bill_id=$bill_id")->fetch_assoc();
$items = $conn->query("SELECT bi.*, p.product_name, s.service_name
                       FROM bill_items bi
                       LEFT JOIN products p ON bi.product_id=p.product_id
                       LEFT JOIN services s ON bi.service_id=s.service_id
                       WHERE bi.bill_id=$bill_id");

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);

$pdf->Cell(0,10,'Jai Jogai Dairy Equipments',0,1,'C');
$pdf->SetFont('Arial','',12);
$pdf->Cell(0,8,'Invoice No: '.$bill['bill_no'],0,1);
$pdf->Cell(0,8,'Customer: '.$bill['customer_name'].' | Phone: '.$bill['customer_phone'],0,1);
$pdf->Cell(0,8,'Payment Method: '.$bill['payment_method'],0,1);
$pdf->Ln(5);

// Table header
$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,8,'Item',1);
$pdf->Cell(20,8,'Qty',1);
$pdf->Cell(30,8,'Price',1);
$pdf->Cell(30,8,'Total',1);
$pdf->Ln();

// Table rows
$pdf->SetFont('Arial','',12);
while($item=$items->fetch_assoc()){
    $name = $item['product_name'] ?? $item['service_name'];
    $pdf->Cell(50,8,$name,1);
    $pdf->Cell(20,8,$item['quantity'],1);
    $pdf->Cell(30,8,number_format($item['price'],2),1);
    $pdf->Cell(30,8,number_format($item['total'],2),1);
    $pdf->Ln();
}

// Total
$pdf->SetFont('Arial','B',12);
$pdf->Cell(100,8,'Total Amount',1);
$pdf->Cell(30,8,'₹'.number_format($bill['total_amount'],2),1);

$pdf->Output();
