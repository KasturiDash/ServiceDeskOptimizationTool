<?php
session_start();
include("../config/db.php");

/* Allow BOTH Admin and Agent */
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['Admin', 'Agent'])) {
    header("Location: ../auth/login.php");
    exit();
}

/* Workload for ALL agents */
$query = mysqli_query($conn, "
    SELECT 
        u.name AS agent_name,
        SUM(CASE WHEN t.status = 'Open' THEN 1 ELSE 0 END) AS open,
        SUM(CASE WHEN t.status = 'In Progress' THEN 1 ELSE 0 END) AS in_progress,
        SUM(CASE WHEN t.status = 'Resolved' THEN 1 ELSE 0 END) AS resolved,
        COUNT(t.ticket_id) AS total
    FROM users u
    LEFT JOIN tickets t ON u.user_id = t.assigned_agent_id
    WHERE u.role = 'Agent'
    GROUP BY u.user_id
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Agent Workload</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard-container">

    <div class="dashboard-header center">
        <h2><i class="fa-solid fa-layer-group"></i> Agent Workload</h2>
        <p>Ticket distribution across all agents</p>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Agent</th>
                    <th>Open</th>
                    <th>In Progress</th>
                    <th>Resolved</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                <tr>
                    <td><?= htmlspecialchars($row['agent_name']); ?></td>
                    <td><?= $row['open']; ?></td>
                    <td><?= $row['in_progress']; ?></td>
                    <td><?= $row['resolved']; ?></td>
                    <td><strong><?= $row['total']; ?></strong></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div style="text-align:center;margin-top:25px;">
        <a href="dashboard.php" class="btn btn-primary">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

</div>

</body>
</html>