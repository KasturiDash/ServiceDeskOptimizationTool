<?php
require_once("../includes/auth_check.php");
require_once("../includes/log_action.php");
require_once("../config/db.php");

if ($_SESSION['role'] !== 'Admin' || empty($_SESSION['is_super_admin'])) {
    header("Location: ../auth/admin_login.php");
    exit();
}

$name  = trim($_POST['name']);
$email = trim($_POST['email']);
$pass  = trim($_POST['password']);

$stmt = $conn->prepare("
    INSERT INTO users (name, email, password, role, status, is_super_admin)
    VALUES (?, ?, ?, 'Admin', 'Active', 0)
");
$stmt->bind_param("sss", $name, $email, $pass);
$stmt->execute();

/* AUDIT */
logAction($conn, $_SESSION['user_id'], "Created admin account ($email)");

header("Location: manage_admins.php");
exit();