<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");

/* ======================================
   SUPER ADMIN ONLY ACCESS
====================================== */
if (
    $_SESSION['role'] !== 'Admin' ||
    empty($_SESSION['is_super_admin'])
) {
    header("Location: ../auth/super_admin_login.php");
    exit();
}

/*
|--------------------------------------------------------------------------
| AGENT WORKLOAD LOGIC (CORRECT)
|--------------------------------------------------------------------------
| Open / In Progress → assigned_agent_id
| Resolved           → assigned_agent_id (status-based)
*/

$query = mysqli_query($conn, "
    SELECT 
        u.user_id,
        u.name AS agent_name,

        SUM(CASE 
            WHEN t.assigned_agent_id = u.user_id 
             AND t.status = 'Open'
            THEN 1 ELSE 0 END) AS open_count,

        SUM(CASE 
            WHEN t.assigned_agent_id = u.user_id 
             AND t.status = 'In Progress'
            THEN 1 ELSE 0 END) AS progress_count,

        SUM(CASE 
            WHEN t.assigned_agent_id = u.user_id 
             AND t.status = 'Resolved'
            THEN 1 ELSE 0 END) AS resolved_count

    FROM users u
    LEFT JOIN tickets t 
        ON t.assigned_agent_id = u.user_id

    WHERE u.role = 'Agent'
    GROUP BY u.user_id
");

/* ============================
   BUILD AGENTS ARRAY (FIX)
============================ */
$agents = [];

while ($row = mysqli_fetch_assoc($query)) {
    $row['total'] =
        $row['open_count'] +
        $row['progress_count'] +
        $row['resolved_count'];

    $agents[] = $row;
}
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

    <!-- HEADER -->
    <div class="dashboard-header center">
        <h2>
            <i class="fa-solid fa-layer-group"></i> Agent Workload
        </h2>
        <p>Overview of ticket distribution among agents</p>
    </div>

    <!-- TABLE -->
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
            <?php if (empty($agents)) { ?>
                <tr>
                    <td colspan="5" style="text-align:center;">
                        No agent workload data available.
                    </td>
                </tr>
            <?php } else { ?>
                <?php foreach ($agents as $ag) { ?>
                <tr>
                    <td><?= htmlspecialchars($ag['agent_name']); ?></td>
                    <td><?= $ag['open_count']; ?></td>
                    <td><?= $ag['progress_count']; ?></td>
                    <td>
                        <span class="badge badge-resolved">
                            <?= $ag['resolved_count']; ?>
                        </span>
                    </td>
                    <td><strong><?= $ag['total']; ?></strong></td>
                </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>

    </div>

    <!-- BACK BUTTON -->
    <div style="text-align:center; margin-top:30px;">
        <a href="../super_admin/dashboard.php">
            <button class="btn btn-primary">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </button>
        </a>
    </div>

</div>

</body>
</html>