<?php
require_once("../includes/auth_check.php");

if ($_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/admin_login.php");
    exit();
}

include("../config/db.php");

$admin_id = $_SESSION['user_id'];

/* =========================
   FETCH ADMIN DETAILS
========================= */
$admin = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM users WHERE user_id='$admin_id'")
);

/* =========================
   UPDATE PROFILE
========================= */
if (isset($_POST['update_profile'])) {

    $name  = trim($_POST['name']);
    $pass  = trim($_POST['password']);

    if (!empty($pass)) {
        mysqli_query($conn, "
            UPDATE users 
            SET name='$name', password='$pass'
            WHERE user_id='$admin_id'
        ");
    } else {
        mysqli_query($conn, "
            UPDATE users 
            SET name='$name'
            WHERE user_id='$admin_id'
        ");
    }

    mysqli_query($conn, "
        INSERT INTO audit_logs (user_id, action)
        VALUES ('$admin_id', 'Admin updated profile')
    ");

    $_SESSION['success'] = "Profile updated successfully.";
    header("Location: manage_profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Profile</title>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="dashboard-container">

  <!-- HEADER -->
  <div class="dashboard-header">
    <h2><i class="fa-solid fa-id-badge"></i> Manage Profile</h2>
    <p>Update your admin account details</p>
  </div>

  <!-- CARD -->
  <div class="card">

    <?php
    if (isset($_SESSION['success'])) {
        echo "<div class='alert alert-success'>".$_SESSION['success']."</div>";
        unset($_SESSION['success']);
    }
    ?>

    <form method="POST">

      <label>Name</label>
      <input type="text"
             name="name"
             value="<?= htmlspecialchars($admin['name']) ?>"
             required>

      <label>Email (cannot be changed)</label>
      <input type="email"
             value="<?= htmlspecialchars($admin['email']) ?>"
             readonly>

      <label>New Password <span class="muted-text">(optional)</span></label>
      <input type="password"
             name="password"
             placeholder="Leave blank to keep current password">

      <button type="submit" name="update_profile" class="btn">
        <i class="fa-solid fa-save"></i> Update Profile
      </button>

    </form>

    <!-- INFO NOTE -->
    <div style="margin-top:25px; font-size:14px; color:#64748b;">
        <i class="fa-solid fa-circle-info"></i>
        Account deletion is managed by the <strong>Super Admin</strong>.
    </div>

  </div>

  <!-- BACK -->
  <div style="text-align:center;margin-top:30px;">
    <a href="dashboard.php" class="btn">
      <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
  </div>

</div>

</body>
</html>