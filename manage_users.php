<?php
require_once("../includes/auth_check.php");
include("../config/db.php");

/* ONLY SUPER ADMIN */
if (
    $_SESSION['role'] !== 'Admin' ||
    empty($_SESSION['is_super_admin'])
) {
    header("Location: ../auth/super_admin_login.php");
    exit();
}

/* HANDLE ACTIONS */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $user_id = (int)$_POST['user_id'];
    $action  = $_POST['action'];
    $sa_id   = $_SESSION['user_id'];

    /* Fetch ONLY End User / Agent */
    $res = mysqli_query($conn,"
        SELECT role
        FROM users
        WHERE user_id = $user_id
          AND role IN ('End User', 'Agent')
        LIMIT 1
    ");

    if ($res && mysqli_num_rows($res) === 1) {

        if ($action === 'activate') {
            mysqli_query($conn,"
                UPDATE users SET status='Active'
                WHERE user_id=$user_id
            ");
            mysqli_query($conn,"
                INSERT INTO audit_logs (user_id, action)
                VALUES ($sa_id, 'Activated user ID $user_id')
            ");
        }

        if ($action === 'deactivate') {
            mysqli_query($conn,"
                UPDATE users SET status='Inactive'
                WHERE user_id=$user_id
            ");
            mysqli_query($conn,"
                INSERT INTO audit_logs (user_id, action)
                VALUES ($sa_id, 'Deactivated user ID $user_id')
            ");
        }

        if ($action === 'delete') {
            mysqli_query($conn,"
                DELETE FROM users WHERE user_id=$user_id
            ");
            mysqli_query($conn,"
                INSERT INTO audit_logs (user_id, action)
                VALUES ($sa_id, 'Deleted user ID $user_id')
            ");
        }
    }

    header("Location: manage_users.php");
    exit();
}

/* FETCH ONLY USERS & AGENTS */
$query = mysqli_query($conn,"
    SELECT user_id, name, email, role, status
    FROM users
    WHERE role IN ('End User', 'Agent')
    ORDER BY created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Users</title>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.action-btn{
    padding:6px 12px;
    border-radius:8px;
    font-size:13px;
    font-weight:600;
    border:none;
    cursor:pointer;
    margin-right:6px;
}
.activate{ background:#dcfce7; color:#166534; }
.deactivate{ background:#fee2e2; color:#991b1b; }
.delete{ background:#111827; color:#fff; }

.badge-active{ background:#dcfce7; color:#166534; }
.badge-inactive{ background:#fee2e2; color:#991b1b; }
</style>
</head>

<body>

<div class="dashboard-container">

<div class="dashboard-header">
    <h2><i class="fa-solid fa-users-gear"></i> Manage Users</h2>
    <p>End Users & Agents Only</p>
</div>

<div class="card">

<table class="table">
<thead>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>
<?php while($u = mysqli_fetch_assoc($query)) { ?>
<tr>

<td>#<?= $u['user_id'] ?></td>
<td><?= htmlspecialchars($u['name']) ?></td>
<td><?= htmlspecialchars($u['email']) ?></td>
<td><?= $u['role'] ?></td>

<td>
<?php if ($u['status'] === 'Active') { ?>
    <span class="badge badge-active">Active</span>
<?php } else { ?>
    <span class="badge badge-inactive">Inactive</span>
<?php } ?>
</td>

<td>
<form method="POST" style="display:inline;">
<input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">

<?php if ($u['status'] === 'Inactive') { ?>
    <button class="action-btn activate" name="action" value="activate">
        Activate
    </button>
<?php } else { ?>
    <button class="action-btn deactivate" name="action" value="deactivate">
        Deactivate
    </button>
<?php } ?>

<button class="action-btn delete"
        name="action"
        value="delete"
        onclick="return confirm('Delete this user permanently?')">
    Delete
</button>
</form>
</td>

</tr>
<?php } ?>
</tbody>
</table>

</div>

<div style="text-align:center;margin-top:30px;">
    <a href="../super_admin/dashboard.php" class="btn">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

</div>

</body>
</html>