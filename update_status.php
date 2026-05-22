<?php
session_start();
include("../config/db.php");

// 🔐 Agent-only access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Agent') {
    header("Location: ../auth/login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $ticket_id = intval($_POST['ticket_id']);
    $status    = mysqli_real_escape_string($conn, $_POST['status']);
    $note      = mysqli_real_escape_string($conn, $_POST['resolution_note']);
    $agent_id  = $_SESSION['user_id'];

    // ✅ Update ONLY tickets assigned to this agent
    $update = mysqli_query($conn, "
        UPDATE tickets
        SET 
            status = '$status',
            resolution_note = '$note',
            resolved_by = '$agent_id'
        WHERE ticket_id = $ticket_id
          AND assigned_agent_id = $agent_id
    ");

    if ($update && mysqli_affected_rows($conn) > 0) {

        // 🧾 AUDIT LOG
        mysqli_query($conn, "
            INSERT INTO audit_logs (user_id, action)
            VALUES (
                '$agent_id',
                'Resolved ticket ID $ticket_id'
            )
        ");

        $_SESSION['success'] = "Ticket resolved successfully.";
    } else {
        $_SESSION['error'] = "You are not allowed to update this ticket.";
    }

    header("Location: tickets.php");
    exit();
}