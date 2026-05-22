<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");

/* SUPER ADMIN ONLY */
if (
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

/* CSV HEADERS */
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=audit_logs_{$from}_to_{$to}.csv");

$output = fopen("php://output", "w");

/* COLUMN TITLES */
fputcsv($output, ["User", "Action", "Date & Time"]);

/* ROWS */
while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['user'] ?? 'System',
        $row['action'],
        date("d M Y, h:i A", strtotime($row['created_at']))
    ]);
}

fclose($output);
exit();