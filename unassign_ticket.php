<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");

/* ALLOW ADMIN + SUPER ADMIN */
if ($_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/admin_login.php");
    exit();
}

if (!isset($_POST['ticket_id'])) {
    header("Location: all_tickets.php");
    exit();
}

$ticket_id = (int)$_POST['ticket_id'];

/* UNASSIGN */
mysqli_query($conn, "
    UPDATE tickets
    SET assigned_agent_id = NULL, status='Open'
    WHERE ticket_id = $ticket_id
");

/* AUDIT LOG */
$actor = !empty($_SESSION['is_super_admin']) ? 'Super Admin' : 'Admin';

mysqli_query($conn, "
    INSERT INTO audit_logs (user_id, action)
    VALUES (
        '{$_SESSION['user_id']}',
        '$actor unassigned agent from ticket ID $ticket_id'
    )
");

/* REDIRECT */
if (!empty($_SESSION['is_super_admin'])) {
    header("Location: ../super_admin/all_tickets.php");
} else {
    header("Location: all_tickets.php");
}
exit();