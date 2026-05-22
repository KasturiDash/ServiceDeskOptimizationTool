<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");

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
$query = mysqli_query($conn, "
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
");

/* ===============================
   CSV HEADERS
=============================== */
header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=agent_performance_report.csv");

$output = fopen("php://output", "w");
fputcsv($output, ["Agent Name", "Total Assigned", "Resolved", "Pending"]);

while ($row = mysqli_fetch_assoc($query)) {
    fputcsv($output, $row);
}

fclose($output);

/* ===============================
   AUDIT LOG
=============================== */
mysqli_query($conn, "
    INSERT INTO audit_logs (user_id, action)
    VALUES ('{$_SESSION['user_id']}', 'Exported Agent Performance CSV')
");

exit();