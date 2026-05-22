<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");
require_once("../lib/fpdf/fpdf.php");

/* ======================================
   SUPER ADMIN ONLY ACCESS
====================================== */
if (
    $_SESSION['role'] !== 'Admin' ||
    empty($_SESSION['is_super_admin'])
) {
    header("Location: ../auth/super_admin_login.php");
    exit();
}

/* ===============================
   AGENT PERFORMANCE DATA
=============================== */
$sql = "
SELECT 
    u.name AS agent_name,
    COUNT(t.ticket_id) AS total_assigned,
    SUM(CASE WHEN t.status = 'Resolved' THEN 1 ELSE 0 END) AS resolved,
    SUM(CASE WHEN t.status != 'Resolved' THEN 1 ELSE 0 END) AS pending
FROM users u
LEFT JOIN tickets t 
    ON t.assigned_agent_id = u.user_id
WHERE u.role = 'Agent'
GROUP BY u.user_id
";

$result = mysqli_query($conn, $sql);

/* ===============================
   PDF GENERATION
=============================== */
$pdf = new FPDF();
$pdf->AddPage();

$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Agent Performance Report',0,1,'C');
$pdf->Ln(6);

$pdf->SetFont('Arial','B',10);
$pdf->Cell(50,8,'Agent Name',1);
$pdf->Cell(35,8,'Total Assigned',1);
$pdf->Cell(35,8,'Resolved',1);
$pdf->Cell(35,8,'Pending',1);
$pdf->Ln();

$pdf->SetFont('Arial','',10);

while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(50,8,$row['agent_name'],1);
    $pdf->Cell(35,8,$row['total_assigned'],1);
    $pdf->Cell(35,8,$row['resolved'],1);
    $pdf->Cell(35,8,$row['pending'],1);
    $pdf->Ln();
}

/* ===============================
   AUDIT LOG
=============================== */
mysqli_query($conn, "
    INSERT INTO audit_logs (user_id, action)
    VALUES ('{$_SESSION['user_id']}', 'Exported Agent Performance PDF')
");

$pdf->Output("D", "agent_performance_report.pdf");
exit();