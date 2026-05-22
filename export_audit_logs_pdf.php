<?php
session_start();
require_once("../config/db.php");
require_once("../lib/fpdf/fpdf.php");

/* SUPER ADMIN ONLY */
if (
    !isset($_SESSION['user_id']) ||
    $_SESSION['role'] !== 'Admin' ||
    empty($_SESSION['is_super_admin'])
) {
    header("Location: ../auth/super_admin_login.php");
    exit();
}

/* FILTER VALUES */
$from   = $_GET['from']   ?? date('Y-m-d');
$to     = $_GET['to']     ?? date('Y-m-d');
$user   = $_GET['user']   ?? '';
$action = $_GET['action'] ?? '';

/* BUILD QUERY */
$sql = "
    SELECT 
        u.name AS user,
        a.action,
        a.created_at
    FROM audit_logs a
    LEFT JOIN users u ON a.user_id = u.user_id
    WHERE DATE(a.created_at) BETWEEN ? AND ?
";

$params = [$from, $to];
$types  = "ss";

if (!empty($user)) {
    $sql .= " AND u.name = ?";
    $params[] = $user;
    $types   .= "s";
}

if (!empty($action)) {
    $sql .= " AND a.action LIKE ?";
    $params[] = "%$action%";
    $types   .= "s";
}

$sql .= " ORDER BY a.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

/* CREATE PDF */
$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,"Audit Logs Report ($from to $to)",0,1,'C');
$pdf->Ln(5);

/* TABLE HEADER */
$pdf->SetFont('Arial','B',10);
$pdf->Cell(50,8,'User',1);
$pdf->Cell(90,8,'Action',1);
$pdf->Cell(40,8,'Date & Time',1);
$pdf->Ln();

/* TABLE BODY */
$pdf->SetFont('Arial','',9);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(50,8,$row['user'] ?? 'System',1);
    $pdf->Cell(90,8,$row['action'],1);
    $pdf->Cell(
        40,
        8,
        date("d M Y, h:i A", strtotime($row['created_at'])),
        1
    );
    $pdf->Ln();
}

/* DOWNLOAD */
$pdf->Output("D","audit_logs_{$from}_to_{$to}.pdf");
exit;