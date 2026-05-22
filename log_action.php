<?php
function logAction(mysqli $conn, int $user_id, string $action) {

    $role = $_SESSION['role'] ?? 'System';

    // Normalize action text
    $action = trim($action);

    // Prefix role for clarity
    if ($role === 'Admin' && !empty($_SESSION['is_super_admin'])) {
        $action = "SUPER ADMIN: " . $action;
    } elseif ($role === 'Admin') {
        $action = "ADMIN: " . $action;
    } elseif ($role === 'Agent') {
        $action = "AGENT: " . $action;
    } elseif ($role === 'End User') {
        $action = "USER: " . $action;
    }

    $stmt = $conn->prepare(
        "INSERT INTO audit_logs (user_id, action) VALUES (?, ?)"
    );
    $stmt->bind_param("is", $user_id, $action);
    $stmt->execute();
}