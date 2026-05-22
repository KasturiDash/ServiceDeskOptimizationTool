<?php
require_once("../includes/auth_check.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/admin_login.php");
    exit();
}

require_once("../config/db.php");

/* ===============================
   CORRECT AGENT PERFORMANCE QUERY
   =============================== */
$query = mysqli_query($conn, "
    SELECT 
        u.user_id,
        u.name AS agent_name,
        COUNT(t.ticket_id) AS total_assigned,
        SUM(CASE WHEN t.status = 'Resolved' THEN 1 ELSE 0 END) AS resolved,
        SUM(CASE WHEN t.status != 'Resolved' AND t.ticket_id IS NOT NULL THEN 1 ELSE 0 END) AS pending
    FROM users u
    LEFT JOIN tickets t 
        ON u.user_id = t.assigned_agent_id
    WHERE u.role = 'Agent'
    GROUP BY u.user_id
");

/* ===============================
   SUMMARY METRICS (FIXED LOGIC)
   =============================== */
$totalResolved = 0;
$totalAssigned = 0;

$bestAgent     = "N/A";
$bestRate      = 0;
$bestResolved  = 0;
$bestAssigned  = 0;

$agents = [];
$chartLabels   = [];
$chartResolved = [];
$chartPending  = [];

while ($row = mysqli_fetch_assoc($query)) {

    $resolutionRate = ($row['total_assigned'] > 0)
        ? round(($row['resolved'] / $row['total_assigned']) * 100)
        : 0;

    /* ✅ PROFESSIONAL TOP PERFORMER LOGIC */
    if (
        $resolutionRate > $bestRate ||
        (
            $resolutionRate == $bestRate &&
            $row['resolved'] > $bestResolved
        ) ||
        (
            $resolutionRate == $bestRate &&
            $row['resolved'] == $bestResolved &&
            $row['total_assigned'] > $bestAssigned
        )
    ) {
        $bestRate     = $resolutionRate;
        $bestAgent    = $row['agent_name'];
        $bestResolved = $row['resolved'];
        $bestAssigned = $row['total_assigned'];
    }

    $totalResolved += $row['resolved'];
    $totalAssigned += $row['total_assigned'];

    $agents[] = [
        'agent_name'       => $row['agent_name'],
        'total_assigned'   => $row['total_assigned'],
        'resolved'         => $row['resolved'],
        'pending'          => $row['pending'],
        'resolution_rate'  => $resolutionRate
    ];

    $chartLabels[]   = $row['agent_name'];
    $chartResolved[] = $row['resolved'];
    $chartPending[]  = $row['pending'];
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Agent Performance</title>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="dashboard-container">

    <!-- HEADER -->
    <div class="dashboard-header center">
        <h2><i class="fa-solid fa-chart-line"></i> Agent Performance Metrics</h2>
        <p>Measure agent efficiency and resolution accuracy</p>

        <div style="text-align:right;margin-bottom:15px;">
            <div class="export-dropdown">
                <button type="button" class="btn btn-primary btn-sm export-toggle">
                    Export Agent Performance ▼
                </button>
                <div class="export-menu">
                    <a href="../super_admin/export_agent_performance.php">Export as PDF</a>
                    <a href="../super_admin/export_agent_performance_csv.php">Export as CSV</a>
                </div>
            </div>
        </div>
    </div>

    <!-- SUMMARY -->
    <div class="stats-grid">

        <div class="stat-card">
            <i class="fa-solid fa-user-check"></i>
            <h3><?= htmlspecialchars($bestAgent) ?></h3>
            <p>Top Performing Agent</p>
        </div>

        <div class="stat-card">
            <i class="fa-solid fa-percent"></i>
            <h3><?= $bestRate ?>%</h3>
            <p>Highest Resolution Rate</p>
        </div>

        <div class="stat-card">
            <i class="fa-solid fa-check-circle"></i>
            <h3><?= $totalResolved ?></h3>
            <p>Total Resolved Tickets</p>
        </div>

        <div class="stat-card">
            <i class="fa-solid fa-ticket"></i>
            <h3><?= $totalAssigned ?></h3>
            <p>Total Assigned Tickets</p>
        </div>

    </div>

    <!-- CHART -->
    <div class="card" style="margin-top:30px;">
        <h3 style="text-align:center;margin-bottom:20px;">
            Agent Resolution Overview
        </h3>
        <canvas id="performanceChart" height="120"></canvas>
    </div>

    <!-- TABLE -->
    <div class="card">
        <table class="table">
            <thead>
                <tr>
                    <th>Agent</th>
                    <th>Total Assigned</th>
                    <th>Resolved</th>
                    <th>Pending</th>
                    <th>Resolution Rate</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($agents as $ag): ?>
                <tr>
                    <td><?= htmlspecialchars($ag['agent_name']) ?></td>
                    <td><?= $ag['total_assigned'] ?></td>
                    <td><span class="badge badge-resolved"><?= $ag['resolved'] ?></span></td>
                    <td><span class="badge badge-open"><?= $ag['pending'] ?></span></td>
                    <td><strong><?= $ag['resolution_rate'] ?>%</strong></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- BACK -->
    <div style="text-align:center;margin-top:30px;">
        <a href="dashboard.php" class="btn back-btn">
            <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
        </a>
    </div>

</div>

<script>
const ctx = document.getElementById('performanceChart').getContext('2d');

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [
            {
                label: 'Resolved',
                data: <?= json_encode($chartResolved) ?>,
                backgroundColor: '#22c55e'
            },
            {
                label: 'Pending',
                data: <?= json_encode($chartPending) ?>,
                backgroundColor: '#ef4444'
            }
        ]
    },
    options: {
        responsive: true,
        scales: { y: { beginAtZero: true } }
    }
});

/* EXPORT DROPDOWN FIX */
document.addEventListener('click', e => {
    document.querySelectorAll('.export-dropdown').forEach(d => {
        if (!d.contains(e.target)) d.classList.remove('open');
    });
    const btn = e.target.closest('.export-toggle');
    if (btn) btn.parentElement.classList.toggle('open');
});
</script>

</body>
</html>