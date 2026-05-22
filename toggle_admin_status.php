<?php
require_once("../includes/auth_check.php");
require_once("../includes/log_action.php");
require_once("../config/db.php");

if ($_SESSION['role'] !== 'Admin' || empty($_SESSION['is_super_admin'])) {
    header("Location: ../auth/admin_login.php");
    exit();
}

$id = (int)$_GET['id'];

/* Prevent Super Admin lockout */
$check = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT is_super_admin, status FROM users WHERE user_id=$id")
);

if ($check['is_super_admin']) {
    header("Location: manage_admins.php");
    exit();
}

$newStatus = $check['status'] === 'Active' ? 'Inactive' : 'Active';

mysqli_query($conn, "
    UPDATE users SET status='$newStatus' WHERE user_id=$id
");

logAction(
    $conn,
    $_SESSION['user_id'],
    "Changed admin status (ID $id → $newStatus)"
);

header("Location: manage_admins.php");
exit();