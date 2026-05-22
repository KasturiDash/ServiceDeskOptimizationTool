<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");

/* ADMIN + SUPER ADMIN */
if ($_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/admin_login.php");
    exit();
}

/* DATE FILTERS */
$from = $_POST['from'] ?? '';
$to   = $_POST['to'] ?? '';

$where = "";
if ($from && $to) {
    $from = mysqli_real_escape_string($conn, $from);
    $to   = mysqli_real_escape_string($conn, $to);
    $where = "WHERE DATE(created_date) BETWEEN '$from' AND '$to'";
}

/* FETCH DATA */
$query = mysqli_query($conn, "
    SELECT ticket_id, title, priority, status, created_date
    FROM tickets
    $where
    ORDER BY created_date DESC
");

/* CSV HEADERS */
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="tickets_report.csv"');

$output = fopen("php://output", "w");
fputcsv($output, ['Ticket ID', 'Title', 'Priority', 'Status', 'Created Date']);

while ($row = mysqli_fetch_assoc($query)) {
    fputcsv($output, $row);
}
fclose($output);

/* AUDIT LOG */
$actor = !empty($_SESSION['is_super_admin']) ? 'Super Admin' : 'Admin';

mysqli_query($conn, "
    INSERT INTO audit_logs (user_id, action)
    VALUES (
        '{$_SESSION['user_id']}',
        '$actor exported tickets as CSV'
    )
");

exit();