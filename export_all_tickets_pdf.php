<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");
require_once("../lib/fpdf/fpdf.php");

/* ADMIN + SUPER ADMIN */
if ($_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/admin_login.php");
    exit();
}

/* DATE FILTERS */
$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

$where = "";
if ($from && $to) {
    $from = mysqli_real_escape_string($conn, $from);
    $to   = mysqli_real_escape_string($conn, $to);
    $where = "WHERE DATE(created_date) BETWEEN '$from' AND '$to'";
}

$sql = "
    SELECT 
        t.ticket_id,
        t.title,
        t.priority,
        t.status,
        t.created_date,
        u.name AS user_name
    FROM tickets t
    JOIN users u ON t.user_id = u.user_id
    $where
    ORDER BY t.created_date DESC
";

$result = mysqli_query($conn, $sql);

/* PDF */
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Service Desk - All Tickets Report',0,1,'C');
$pdf->Ln(5);

/* HEADER */
$pdf->SetFont('Arial','B',10);
$pdf->Cell(15,8,'ID',1);
$pdf->Cell(50,8,'Title',1);
$pdf->Cell(30,8,'User',1);
$pdf->Cell(25,8,'Priority',1);
$pdf->Cell(25,8,'Status',1);
$pdf->Cell(40,8,'Created',1);
$pdf->Ln();

/* DATA */
$pdf->SetFont('Arial','',9);
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(15,8,$row['ticket_id'],1);
    $pdf->Cell(50,8,substr($row['title'],0,30),1);
    $pdf->Cell(30,8,$row['user_name'],1);
    $pdf->Cell(25,8,$row['priority'],1);
    $pdf->Cell(25,8,$row['status'],1);
    $pdf->Cell(40,8,$row['created_date'],1);
    $pdf->Ln();
}

/* FOOTER */
$pdf->Ln(5);
$pdf->SetFont('Arial','I',8);
$pdf->Cell(0,10,'Generated on '.date('d M Y'),0,0,'R');

/* AUDIT LOG */
$actor = !empty($_SESSION['is_super_admin']) ? 'Super Admin' : 'Admin';

mysqli_query($conn, "
    INSERT INTO audit_logs (user_id, action)
    VALUES (
        '{$_SESSION['user_id']}',
        '$actor exported tickets as PDF'
    )
");

$pdf->Output("D","all_tickets_report.pdf");
exit();