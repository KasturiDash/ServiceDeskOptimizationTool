<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");

if (
    $_SESSION['role'] !== 'Admin' ||
    empty($_SESSION['is_super_admin'])
) {
    exit();
}

define('SLA_LIMIT', 72);

header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=sla_report.csv");

$out = fopen("php://output","w");
fputcsv($out, ['Ticket ID','Title','Status','Hours Passed','SLA Status']);

$q = mysqli_query($conn,"
    SELECT ticket_id,title,status,
    TIMESTAMPDIFF(HOUR, created_date, NOW()) AS hours
    FROM tickets
");

while($r=mysqli_fetch_assoc($q)){
    $sla = ($r['status']=='Resolved' && $r['hours']<=SLA_LIMIT)
        ? 'Within SLA'
        : (($r['hours']>SLA_LIMIT) ? 'Breached' : 'At Risk');

    fputcsv($out, [$r['ticket_id'],$r['title'],$r['status'],$r['hours'],$sla]);
}

fclose($out);
exit();