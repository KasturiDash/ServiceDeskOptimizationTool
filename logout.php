<?php
session_start();
require_once("../config/db.php");

/* AUDIT LOG */
if (isset($_SESSION['user_id'])) {
    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, action) VALUES (?, 'User logged out')"
    );
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
}

/* DESTROY SESSION COMPLETELY */
session_unset();
session_destroy();

/* REDIRECT WITH QUERY PARAM */
header("Location: login.php?logged_out=1");
exit();