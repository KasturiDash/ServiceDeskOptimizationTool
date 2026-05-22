<?php
session_start();
require_once("../config/db.php");

/* =========================
   SECURITY CHECK
========================= */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'End User') {
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* =========================
   FETCH USER DATA
========================= */
$user = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT name, email
    FROM users
    WHERE user_id='$user_id'
"));

/* =========================
   UPDATE PROFILE
========================= */
if (isset($_POST['update_profile'])) {

    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $password = trim($_POST['password']);

    if (!empty($password)) {
        mysqli_query($conn,"
            UPDATE users
            SET name='$name', password='$password'
            WHERE user_id='$user_id'
        ");
    } else {
        mysqli_query($conn,"
            UPDATE users
            SET name='$name'
            WHERE user_id='$user_id'
        ");
    }

    $_SESSION['success'] = "Profile updated successfully.";
    header("Location: manage_profile.php");
    exit();
}

/* =========================
   TEMPORARY DEACTIVATE
========================= */
if (isset($_POST['deactivate_account'])) {

    mysqli_query($conn,"
        UPDATE users
        SET status='Inactive'
        WHERE user_id='$user_id'
    ");

    mysqli_query($conn,"
        INSERT INTO audit_logs (user_id, action)
        VALUES ('$user_id', 'End User temporarily deactivated account')
    ");

    session_destroy();
    session_start();
    $_SESSION['success'] = "Your account has been temporarily deactivated.";
    header("Location: ../auth/login.php");
    exit();
}

/* ==============================
   DELETE ACCOUNT (PERMANENT)
============================== */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_account'])) {

    // 1️⃣ Delete dependent records FIRST (order matters)
    mysqli_query($conn, "DELETE FROM tickets WHERE user_id = '$user_id'");
    mysqli_query($conn, "DELETE FROM activation_requests WHERE user_id = '$user_id'");
    mysqli_query($conn, "DELETE FROM audit_logs WHERE user_id = '$user_id'");

    // 2️⃣ Delete user
    mysqli_query($conn, "DELETE FROM users WHERE user_id = '$user_id'");

    // 3️⃣ Logout cleanly
    session_unset();
    session_destroy();

    session_start();
    $_SESSION['success'] = "Your account has been deleted permanently.";

    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Profile</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="dashboard-container">

  <div class="dashboard-header">
    <h2>Manage Profile</h2>
    <p>Update your account details</p>
  </div>

  <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
      <?= $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
  <?php endif; ?>

  <div class="card" style="max-width:600px;margin:auto;">

    <!-- UPDATE PROFILE -->
    <form method="POST">
      <label>Name</label>
      <input type="text" name="name"
             value="<?= htmlspecialchars($user['name']) ?>" required>

      <label>Email (cannot be changed)</label>
      <input type="email"
             value="<?= htmlspecialchars($user['email']) ?>" disabled>

      <label>New Password (optional)</label>
      <input type="password" name="password"
             placeholder="Leave blank to keep current password">

      <button type="submit" name="update_profile" class="btn">
        Update Profile
      </button>
    </form>

    <hr style="margin:40px 0;">

        <div class="account-actions">

            <h4 class="section-title">Account Actions</h4>
            <p class="muted-text">
                Manage your account status or permanently delete it.
            </p>

            <!-- TEMP DEACTIVATE -->
            <form method="POST"
                  onsubmit="return confirm('Your account will be temporarily disabled. Continue?');">
                <button type="submit" name="deactivate_account"
                        class="btn btn-outline-warning">
                    <i class="fa-solid fa-pause"></i>
                    Temporarily Deactivate Account
                </button>
            </form>

            <!-- DELETE -->
            <form method="POST" style="margin-top:15px;"
                  onsubmit="return confirm('This will permanently delete your account. This cannot be undone!');">
                <button type="submit" name="delete_account"
                        class="btn btn-danger">
                    <i class="fa-solid fa-trash"></i>
                    Delete Account Permanently
                </button>
            </form>

        </div>

  </div>

  <div style="text-align:center;margin-top:30px;">
    <a href="dashboard.php" class="btn">
      Back to Dashboard
    </a>
  </div>

</div>

</body>
</html>