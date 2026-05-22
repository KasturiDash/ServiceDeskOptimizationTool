<?php
session_start();

/* ✅ SECURITY CHECK – ONLY SUPER ADMIN CAN LOG OUT HERE */
if (
    empty($_SESSION['role']) ||
    $_SESSION['role'] !== 'Admin' ||
    empty($_SESSION['is_super_admin'])
) {
    header("Location: ../auth/super_admin_login.php");
    exit();
}

/* ===============================
   AUDIT LOG (OPTIONAL – SAFE)
   =============================== */
if (!empty($_SESSION['user_id'])) {
    require_once("../config/db.php");

    $stmt = $conn->prepare("
        INSERT INTO audit_logs (user_id, action)
        VALUES (?, ?)
    ");
    $action = 'Super Admin logged out';
    $stmt->bind_param("is", $_SESSION['user_id'], $action);
    $stmt->execute();
}

/* ===============================
   DESTROY SESSION CLEANLY
   =============================== */
session_unset();
session_destroy();

/* ===============================
   START NEW SESSION FOR MESSAGE
   =============================== */
session_start();
$_SESSION['sa_logout_success'] = "You have been logged out successfully.";

/* ===============================
   REDIRECT TO LOGIN
   =============================== */
header("Location: ../auth/super_admin_login.php");
exit();