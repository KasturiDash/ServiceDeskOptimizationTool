<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");

/* SUPER ADMIN ONLY */
if (
    $_SESSION['role'] !== 'Admin' ||
    empty($_SESSION['is_super_admin'])
) {
    header("Location: ../auth/super_admin_login.php");
    exit();
}

/* DATE FILTER (TODAY DEFAULT) */
$today = date('Y-m-d');
$from  = $_GET['from'] ?? $today;
$to    = $_GET['to']   ?? $today;

/* ACTION BADGE */
function actionBadge(string $action): string
{
    $t = strtolower($action);

    /* =========================
       ACCOUNT STATUS ACTIONS
    ========================= */

    // ❌ Deactivated (check FIRST)
    if (str_contains($t, 'deactivated')) {
        return '<span class="badge badge-danger">DEACTIVATED</span>';
    }

    // ✅ Activated
    if (str_contains($t, 'activated')) {
        return '<span class="badge badge-success">ACTIVATED</span>';
    }

    // 🗑️ Deleted
    if (str_contains($t, 'deleted')) {
        return '<span class="badge badge-danger">DELETED</span>';
    }

    /* =========================
       AUTHENTICATION
    ========================= */

    // 🔐 Login
    if (str_contains($t, 'logged in')) {
        return '<span class="badge badge-success">LOGIN</span>';
    }

    // 🔓 Logout
    if (str_contains($t, 'logged out')) {
        return '<span class="badge badge-secondary">LOGOUT</span>';
    }

    /* =========================
       ACTIVATION FLOW
    ========================= */

    // 📩 Activation request
    if (
        str_contains($t, 'requested activation') ||
        str_contains($t, 'request activation')
    ) {
        return '<span class="badge badge-warning">REQUEST</span>';
    }

    /* =========================
       PROFILE / ADMIN ACTIONS
    ========================= */

    // ✏️ Profile updated
    if (str_contains($t, 'updated profile')) {
        return '<span class="badge badge-info">UPDATED</span>';
    }

    // 👑 Admin created
    if (str_contains($t, 'created admin')) {
        return '<span class="badge badge-primary">ADMIN</span>';
    }

    // 👑 Admin managed
    if (str_contains($t, 'admin')) {
        return '<span class="badge badge-primary">ADMIN</span>';
    }

    /* =========================
       TICKET ACTIONS
    ========================= */

    // 🎫 Ticket created
    if (str_contains($t, 'ticket created')) {
        return '<span class="badge badge-primary">TICKET</span>';
    }

    // 🛠️ Ticket assigned
    if (str_contains($t, 'assigned')) {
        return '<span class="badge badge-info">ASSIGNED</span>';
    }

    // ✅ Ticket resolved
    if (str_contains($t, 'resolved')) {
        return '<span class="badge badge-success">RESOLVED</span>';
    }

    // ⏰ SLA breached
    if (str_contains($t, 'sla')) {
        return '<span class="badge badge-danger">SLA</span>';
    }

    /* =========================
       FALLBACK
    ========================= */

    return '<span class="badge badge-dark">ACTION</span>';
}
/* FETCH LOGS */
$stmt = $conn->prepare("
    SELECT a.action, a.created_at, u.name
    FROM audit_logs a
    LEFT JOIN users u ON a.user_id = u.user_id
    WHERE DATE(a.created_at) BETWEEN ? AND ?
    ORDER BY a.created_at DESC
");
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$logs = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Audit Logs</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="dashboard-container">

    <div class="dashboard-header">
        <h2>🔐 Audit Logs</h2>
        <p>System activity tracking (Super Admin)</p>
    </div>

    <div class="card">

        <!-- FILTER BAR -->
        <form method="GET" class="audit-filter-row">

            <div class="audit-filters">
                <div class="filter-group">
                    <label>From</label>
                    <input type="date" name="from" value="<?= $from ?>">
                </div>

                <div class="filter-group">
                    <label>To</label>
                    <input type="date" name="to" value="<?= $to ?>">
                </div>
            </div>

            <div class="audit-actions">
                <button class="btn btn-primary btn-sm">Filter</button>

                <div class="export-dropdown">
                    <button type="button" class="btn btn-primary btn-sm export-toggle">
                        Export ▼
                    </button>
                    <div class="export-menu">
                        <a href="../super_admin/export_audit_logs_pdf.php?from=<?= $from ?>&to=<?= $to ?>">Export PDF</a>
                        <a href="../super_admin/export_audit_logs_csv.php?from=<?= $from ?>&to=<?= $to ?>">Export CSV</a>
                    </div>
                </div>
            </div>

        </form>

        <!-- TABLE -->
        <table class="table">
            <tr>
                <th>User</th>
                <th>Action</th>
                <th>Date & Time</th>
            </tr>

            <?php if ($logs->num_rows === 0): ?>
                <tr>
                    <td colspan="3" style="text-align:center;color:#64748b;">
                        No activity found for selected date
                    </td>
                </tr>
            <?php endif; ?>

            <?php while($r = $logs->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($r['name'] ?? 'System') ?></td>
                    <td>
                        <?= actionBadge($r['action']) ?>
                        <div class="muted-text"><?= htmlspecialchars($r['action']) ?></div>
                    </td>
                    <td><?= date("d M Y, h:i A", strtotime($r['created_at'])) ?></td>
                </tr>
            <?php endwhile; ?>
        </table>

    </div>

    <div class="back-btn">
        <a href="../super_admin/dashboard.php">
            <button class="btn">
                <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
            </button>
        </a>
    </div>

</div>

<script>
document.addEventListener('click', e => {
    const dropdown = document.querySelector('.export-dropdown');
    const toggle   = document.querySelector('.export-toggle');

    if (toggle.contains(e.target)) {
        dropdown.classList.toggle('open');
        return;
    }
    if (!dropdown.contains(e.target)) {
        dropdown.classList.remove('open');
    }
});
</script>

</body>
</html>