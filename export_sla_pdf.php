<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");
require_once("../lib/fpdf/fpdf.php");

if (
    $_SESSION['role'] !== 'Admin' ||
    empty($_SESSION['is_super_admin'])
) {
    exit();
}

define('SLA_LIMIT',72);

$pdf=new FPDF();
$pdf->AddPage();
$pdf->SetFont("Arial","B",14);
$pdf->Cell(0,10,"SLA Monitoring Report",0,1,"C");
$pdf->Ln(5);

$pdf->SetFont("Arial","B",10);
$pdf->Cell(15,8,"ID",1);
$pdf->Cell(55,8,"Title",1);
$pdf->Cell(30,8,"Status",1);
$pdf->Cell(25,8,"Hours",1);
$pdf->Cell(30,8,"SLA",1);
$pdf->Ln();

$pdf->SetFont("Arial","",9);

$q=mysqli_query($conn,"
    SELECT ticket_id,title,status,
    TIMESTAMPDIFF(HOUR, created_date, NOW()) AS hours
    FROM tickets
");

while($r=mysqli_fetch_assoc($q)){
    $sla = ($r['status']=='Resolved' && $r['hours']<=SLA_LIMIT)
        ? 'Within SLA'
        : (($r['hours']>SLA_LIMIT) ? 'Breached' : 'At Risk');

    $pdf->Cell(15,8,$r['ticket_id'],1);
    $pdf->Cell(55,8,substr($r['title'],0,30),1);
    $pdf->Cell(30,8,$r['status'],1);
    $pdf->Cell(25,8,$r['hours'],1);
    $pdf->Cell(30,8,$sla,1);
    $pdf->Ln();
}

$pdf->Output("D","sla_report.pdf");
exit();