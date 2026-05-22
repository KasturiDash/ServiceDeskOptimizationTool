<?php
session_start();
include("../config/db.php");

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Agent') {
    header("Location: ../auth/login.php");
    exit();
}

$agent_id = $_SESSION['user_id'];

/* Fetch tickets assigned to this agent */
$query = mysqli_query($conn, "
    SELECT ticket_id, title, priority, status
    FROM tickets
    WHERE assigned_agent_id = $agent_id
    ORDER BY ticket_id DESC
");

if (!$query) {
    die("Query Error: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Assigned Tickets</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
<div class="dashboard-container">

    <div class="dashboard-header center">
        <h2>My Assigned Tickets</h2>
        <p>Manage your tickets</p>
    </div>

    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
            <?php if (mysqli_num_rows($query) == 0) { ?>
                <tr>
                    <td colspan="5" style="text-align:center;">No tickets assigned</td>
                </tr>
            <?php } else { ?>
                <?php while ($row = mysqli_fetch_assoc($query)) { ?>
                    <tr>
                        <td>#<?= $row['ticket_id']; ?></td>
                        <td><?= htmlspecialchars($row['title']); ?></td>
                        <td><?= $row['priority']; ?></td>
                        <td>
                            <?php
                            if ($row['status'] == 'Open')
                                echo "<span class='badge badge-open'>Open</span>";
                            elseif ($row['status'] == 'In Progress')
                                echo "<span class='badge badge-progress'>In Progress</span>";
                            else
                                echo "<span class='badge badge-resolved'>Resolved</span>";
                            ?>
                        </td>
                        <td>
                            <a href="ticket_details.php?id=<?= $row['ticket_id']; ?>" class="btn btn-sm btn-primary">
                                Update
                            </a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } ?>
            </tbody>
        </table>
    </div>

    <div style="text-align:center; margin-top:25px;">
        <a href="dashboard.php" class="btn btn-primary">← Back to Dashboard</a>
    </div>

</div>

</body>
</html>