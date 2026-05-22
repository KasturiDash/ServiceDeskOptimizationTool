<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");

/* =========================
   SUPER ADMIN ONLY
========================= */
if (
    $_SESSION['role'] !== 'Admin' ||
    empty($_SESSION['is_super_admin'])
) {
    header("Location: ../auth/super_admin_login.php");
    exit();
}

/* =========================
   HANDLE APPROVE / REJECT (POST)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $request_id = (int)$_POST['request_id'];
    $action     = $_POST['action'];
    $sa_id      = $_SESSION['user_id'];

    if ($action === 'approve') {

        $conn->query("
            UPDATE users u
            JOIN activation_requests r ON u.user_id = r.user_id
            SET 
                u.status = 'Active',
                r.status = 'Approved',
                r.reviewed_at = NOW(),
                r.reviewed_by = $sa_id
            WHERE r.request_id = $request_id
        ");

    }

    if ($action === 'reject') {

        $conn->query("
            UPDATE activation_requests
            SET 
                status = 'Rejected',
                reviewed_at = NOW(),
                reviewed_by = $sa_id
            WHERE request_id = $request_id
        ");
    }

    header("Location: activation_requests.php");
    exit();
}

/* =========================
   FETCH PENDING REQUESTS
========================= */
$pending = $conn->query("
    SELECT 
        r.request_id,
        u.name,
        u.email,
        u.role,
        r.requested_at
    FROM activation_requests r
    JOIN users u ON r.user_id = u.user_id
    WHERE 
        r.status = 'Pending'
        AND u.role IN ('End User','Agent')
    ORDER BY r.requested_at DESC
");

/* =========================
   FETCH HISTORY
========================= */
$history = $conn->query("
    SELECT 
        u.name,
        u.email,
        u.role,
        r.status,
        r.requested_at,
        r.reviewed_at
    FROM activation_requests r
    JOIN users u ON r.user_id = u.user_id
    WHERE 
        r.status IN ('Approved','Rejected')
        AND u.role IN ('End User','Agent')
    ORDER BY r.reviewed_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Activation Requests</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="dashboard-container">

<div class="dashboard-header">
    <h2>🔑 Activation Requests</h2>
    <p>Approve or reject user & agent reactivation requests</p>
</div>

<!-- ================= PENDING REQUESTS ================= -->
<div class="card">
<h3>Pending Requests</h3>

<table class="table">
<tr>
    <th>User</th>
    <th>Email</th>
    <th>Role</th>
    <th>Requested At</th>
    <th>Action</th>
</tr>

<?php if ($pending->num_rows === 0): ?>
<tr>
    <td colspan="5" style="text-align:center;color:#64748b;">
        No pending activation requests
    </td>
</tr>
<?php endif; ?>

<?php while($r = $pending->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($r['name']) ?></td>
    <td><?= htmlspecialchars($r['email']) ?></td>
    <td><?= $r['role'] ?></td>
    <td><?= date("d M Y, h:i A", strtotime($r['requested_at'])) ?></td>
    <td>
        <form method="POST" style="display:inline;">
            <input type="hidden" name="request_id" value="<?= $r['request_id'] ?>">
            <button name="action" value="approve"
                    class="btn btn-success btn-sm">
                Approve
            </button>
        </form>

        <form method="POST" style="display:inline;">
            <input type="hidden" name="request_id" value="<?= $r['request_id'] ?>">
            <button name="action" value="reject"
                    class="btn btn-danger btn-sm">
                Reject
            </button>
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
</div>

<!-- ================= REQUEST HISTORY ================= -->
<div class="card" style="margin-top:30px;">
<h3>Request History</h3>

<table class="table">
<tr>
    <th>User</th>
    <th>Email</th>
    <th>Role</th>
    <th>Status</th>
    <th>Requested At</th>
    <th>Reviewed At</th>
</tr>

<?php if ($history->num_rows === 0): ?>
<tr>
    <td colspan="6" style="text-align:center;color:#64748b;">
        No activation history found
    </td>
</tr>
<?php endif; ?>

<?php while($h = $history->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($h['name']) ?></td>
    <td><?= htmlspecialchars($h['email']) ?></td>
    <td><?= $h['role'] ?></td>
    <td>
        <?php if ($h['status'] === 'Approved'): ?>
            <span class="badge badge-success">Approved</span>
        <?php else: ?>
            <span class="badge badge-danger">Rejected</span>
        <?php endif; ?>
    </td>
    <td><?= date("d M Y, h:i A", strtotime($h['requested_at'])) ?></td>
    <td><?= date("d M Y, h:i A", strtotime($h['reviewed_at'])) ?></td>
</tr>
<?php endwhile; ?>
</table>
</div>

<div style="text-align:center;margin-top:30px;">
    <a href="dashboard.php" class="btn">
        ← Back to Dashboard
    </a>
</div>

</div>
</body>
</html>