<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");

/* ======================================
   SUPER ADMIN ONLY
====================================== */
if (
    $_SESSION['role'] !== 'Admin' ||
    empty($_SESSION['is_super_admin'])
) {
    header("Location: ../auth/super_admin_login.php");
    exit();
}

/* ======================================
   SLA CONFIG
====================================== */
define('SLA_LIMIT', 72);              // hours
define('SLA_WARNING_RATIO', 0.8);     // 80%

/* ======================================
   FETCH TICKETS
====================================== */
$query = mysqli_query($conn, "
    SELECT 
        t.ticket_id,
        t.title,
        u.name AS user_name,
        t.priority,
        t.status,
        t.created_date,
        TIMESTAMPDIFF(HOUR, t.created_date, NOW()) AS hours_passed
    FROM tickets t
    JOIN users u ON t.user_id = u.user_id
    ORDER BY t.created_date DESC
");

/* ======================================
   SLA COUNTERS
====================================== */
$slaWithin   = 0;
$slaRisk     = 0;
$slaBreached = 0;

$tickets = [];

while ($row = mysqli_fetch_assoc($query)) {

    $warningLimit = SLA_LIMIT * SLA_WARNING_RATIO;

    /* ======================================
       SLA LOGIC (FIXED)
    ====================================== */
    if ($row['status'] === 'Resolved') {

        // SLA STOPS once resolved
        $sla = 'Within SLA';
        $slaWithin++;

    } else {

        if ($row['hours_passed'] <= $warningLimit) {
            $sla = 'Within SLA';
            $slaWithin++;
        }
        elseif ($row['hours_passed'] <= SLA_LIMIT) {
            $sla = 'At Risk';
            $slaRisk++;
        }
        else {
            $sla = 'Breached';
            $slaBreached++;
        }
    }

    $row['sla_status'] = $sla;
    $tickets[] = $row;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>SLA Monitoring</title>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="dashboard-container">

<!-- HEADER -->
<div class="dashboard-header center">
    <h2><i class="fa-solid fa-clock"></i> SLA Monitoring</h2>
    <p>System-wide SLA compliance overview</p>
</div>

<!-- CHART -->
<div class="card" style="margin-bottom:30px;">
    <h3 style="text-align:center;margin-bottom:20px;">
        SLA Status Overview
    </h3>
    <canvas id="slaChart" height="120"></canvas>
</div>

<!-- TABLE -->
<div class="card">

<div style="text-align:right;margin-bottom:15px;">
    <div class="export-dropdown">
        <button class="btn btn-primary btn-sm export-toggle">
            Export SLA Report ▼
        </button>
        <div class="export-menu">
            <a href="../super_admin/export_sla_pdf.php">Export as PDF</a>
            <a href="../super_admin/export_sla_csv.php">Export as CSV</a>
        </div>
    </div>
</div>

<table class="table">
<thead>
<tr>
    <th>ID</th>
    <th>Title</th>
    <th>User</th>
    <th>Priority</th>
    <th>Status</th>
    <th>Hours Passed</th>
    <th>SLA Status</th>
</tr>
</thead>

<tbody>
<?php foreach ($tickets as $t): ?>
<tr>

<td>#<?= $t['ticket_id'] ?></td>

<td><?= htmlspecialchars($t['title']) ?></td>

<td><?= htmlspecialchars($t['user_name']) ?></td>

<td><?= htmlspecialchars($t['priority']) ?></td>

<!-- TICKET STATUS -->
<td>
<?php
$statusClass = match ($t['status']) {
    'Resolved' => 'badge-success',
    'Pending'  => 'badge-warning',
    default    => 'badge-open'
};
?>
<span class="badge <?= $statusClass ?>">
    <?= htmlspecialchars($t['status']) ?>
</span>
</td>

<td><?= (int)$t['hours_passed'] ?> hrs</td>

<!-- SLA STATUS -->
<td>
<?php
if ($t['sla_status'] === 'Within SLA') {
    echo '<span class="badge badge-success">Within SLA</span>';
}
elseif ($t['sla_status'] === 'At Risk') {
    echo '<span class="badge badge-warning">At Risk</span>';
}
else {
    echo '<span class="badge badge-danger">Breached</span>';
}
?>
</td>

</tr>
<?php endforeach; ?>
</tbody>
</table>

</div>

<!-- BACK -->
<div style="text-align:center;margin-top:30px;">
    <a href="dashboard.php" class="btn btn-primary">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

</div>

<!-- SLA CHART -->
<script>
new Chart(document.getElementById('slaChart'), {
    type: 'bar',
    data: {
        labels: ['Within SLA', 'At Risk', 'Breached'],
        datasets: [{
            data: [<?= $slaWithin ?>, <?= $slaRisk ?>, <?= $slaBreached ?>],
            backgroundColor: ['#22c55e', '#facc15', '#ef4444']
        }]
    },
    options: {
        plugins: { legend: { display: false } },
        scales: { y: { beginAtZero: true } }
    }
});
</script>

<!-- EXPORT DROPDOWN -->
<script>
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