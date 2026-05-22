<?php
session_start();
require_once("../config/db.php");

if (!isset($_POST['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = (int)$_POST['user_id'];

/* prevent duplicate pending request */
$check = $conn->prepare("
    SELECT request_id FROM activation_requests
    WHERE user_id = ? AND status = 'Pending'
");
$check->bind_param("i", $user_id);
$check->execute();

if ($check->get_result()->num_rows === 0) {

    $stmt = $conn->prepare("
        INSERT INTO activation_requests (user_id, reason)
        VALUES (?, 'User requested activation')
    ");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // audit log
    $log = $conn->prepare("
        INSERT INTO audit_logs (user_id, action)
        VALUES (?, 'Requested account activation')
    ");
    $log->bind_param("i", $user_id);
    $log->execute();
}

$_SESSION['error'] = "Activation request submitted successfully.";
header("Location: login.php");
exit();