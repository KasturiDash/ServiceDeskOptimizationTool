<?php
session_start();
require_once("../config/db.php");

/* AUTH CHECK */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Admin') {
    header("Location: admin_login.php");
    exit();
}

/* AUDIT LOG */
$admin_id = $_SESSION['user_id'];
$action   = "Admin logged out";

$stmt = $conn->prepare("
    INSERT INTO audit_logs (user_id, action)
    VALUES (?, ?)
");
$stmt->bind_param("is", $admin_id, $action);
$stmt->execute();
$stmt->close();

/* SUCCESS MESSAGE */
$_SESSION['success'] = "You have been logged out successfully.";

/* CLEAR SESSION */
unset($_SESSION['user_id']);
unset($_SESSION['role']);

/* REDIRECT */
header("Location: admin_login.php");
exit();