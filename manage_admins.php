<?php
require_once("../includes/auth_check.php");
require_once("../config/db.php");
require_once("../includes/log_action.php");

/* SUPER ADMIN ONLY */
if (
    $_SESSION['role'] !== 'Admin' ||
    empty($_SESSION['is_super_admin'])
) {
    header("Location: ../auth/admin_login.php");
    exit();
}

/* =========================
   HANDLE ACTIONS (POST ONLY)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $admin_id = (int)$_POST['admin_id'];
    $action   = $_POST['action'];
    $sa_id    = $_SESSION['user_id'];

    /* Prevent self action */
    if ($admin_id === $sa_id) {
        header("Location: manage_admins.php");
        exit();
    }

    /* Fetch admin */
    $res = mysqli_query($conn, "
        SELECT role, is_super_admin
        FROM users
        WHERE user_id = $admin_id
          AND role = 'Admin'
        LIMIT 1
    ");

    if ($res && mysqli_num_rows($res) === 1) {

        $admin = mysqli_fetch_assoc($res);

        /* Protect Super Admin */
        if ($admin['is_super_admin']) {
            header("Location: manage_admins.php");
            exit();
        }

        if ($action === 'activate') {
            mysqli_query($conn,"
                UPDATE users SET status='Active'
                WHERE user_id=$admin_id
            ");
            logAction($conn, $sa_id, "Activated admin ID $admin_id");
        }

        if ($action === 'deactivate') {
            mysqli_query($conn,"
                UPDATE users SET status='Inactive'
                WHERE user_id=$admin_id
            ");
            logAction($conn, $sa_id, "Deactivated admin ID $admin_id");
        }
    }

    header("Location: manage_admins.php");
    exit();
}

/* =========================
   CREATE ADMIN
========================= */
if (isset($_POST['create_admin'])) {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    mysqli_query($conn,"
        INSERT INTO users (name, email, password, role, status, is_super_admin)
        VALUES ('$name','$email','$pass','Admin','Active',0)
    ");

    logAction($conn, $_SESSION['user_id'], "Created admin: $email");

    header("Location: manage_admins.php");
    exit();
}

/* =========================
   FETCH ADMINS
========================= */
$query = mysqli_query($conn,"
    SELECT user_id, name, email, status, is_super_admin
    FROM users
    WHERE role='Admin'
    ORDER BY is_super_admin DESC, created_at DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Manage Admins</title>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.badge-active { background:#dcfce7; color:#166534; }
.badge-inactive { background:#fee2e2; color:#991b1b; }
.badge-protected { background:#e0e7ff; color:#3730a3; }

.action-btn {
    padding:6px 14px;
    border-radius:8px;
    font-size:13px;
    font-weight:600;
    border:none;
}
</style>
</head>

<body>

<div class="dashboard-container">

<div class="dashboard-header">
    <h2><i class="fa-solid fa-user-shield"></i> Manage Admins</h2>
    <p>Super Admin Control Panel</p>
</div>

<!-- CREATE ADMIN -->
<div class="card">
<h3>➕ Create Admin</h3>

<form method="POST">
    <input type="text" name="name" placeholder="Admin Name" required>
    <input type="email" name="email" placeholder="Admin Email" required>
    <input type="password" name="password" placeholder="Temporary Password" required>

    <button class="btn" name="create_admin">
        <i class="fa-solid fa-user-plus"></i> Create Admin
    </button>
</form>
</div>

<!-- ADMIN TABLE -->
<div class="card" style="margin-top:30px;">
<table class="table">
<thead>
<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Role</th>
    <th>Status</th>
    <th>Actions</th>
</tr>
</thead>

<tbody>
<?php while ($a = mysqli_fetch_assoc($query)) { ?>
<tr>

<td><?= htmlspecialchars($a['name']) ?></td>
<td><?= htmlspecialchars($a['email']) ?></td>

<td>
<?= $a['is_super_admin']
    ? '<span class="badge badge-protected">Super Admin</span>'
    : '<span class="badge badge-dark">Admin</span>' ?>
</td>

<td>
<?= $a['status'] === 'Active'
    ? '<span class="badge badge-active">Active</span>'
    : '<span class="badge badge-inactive">Inactive</span>' ?>
</td>

<td>
<?php if ($a['is_super_admin']) { ?>
    <span class="badge badge-protected">Protected</span>
<?php } else { ?>
<form method="POST" style="display:inline;">
<input type="hidden" name="admin_id" value="<?= $a['user_id'] ?>">

<?php if ($a['status'] === 'Active') { ?>
    <button class="action-btn btn-warning" name="action" value="deactivate">
        Deactivate
    </button>
<?php } else { ?>
    <button class="action-btn btn-success" name="action" value="activate">
        Activate
    </button>
<?php } ?>

</form>
<?php } ?>
</td>

</tr>
<?php } ?>
</tbody>
</table>
</div>

<div style="text-align:center;margin-top:30px;">
    <a href="dashboard.php" class="btn">
        <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
</div>

</div>
</body>
</html>