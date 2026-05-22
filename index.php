<?php
session_start();

/* Redirect if already logged in */
if (isset($_SESSION['role'])) {

    // 🔐 Super Admin
    if ($_SESSION['role'] === 'Admin' && !empty($_SESSION['is_super_admin'])) {
        header("Location: ../super_admin/dashboard.php");
        exit();
    }

    // 🔐 Normal Admin
    if ($_SESSION['role'] === 'Admin') {
        header("Location: dashboard.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Admin Panel</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="center-box">
  <div class="card">

    <h2>
      <i class="fa-solid fa-user-shield"></i> Admin Panel
    </h2>
    <p class="subtitle">Authorized administrators only</p>

    <!-- ADMIN LOGIN -->
    <a href="../auth/admin_login.php">
      <button class="btn">
        <i class="fa-solid fa-right-to-bracket"></i> Login as Admin
      </button>
    </a>

    <!-- SUPER ADMIN LOGIN -->
    <a href="../auth/super_admin_login.php">
      <button class="btn">
        <i class="fa-solid fa-crown"></i> Login as Super Admin
      </button>
    </a>

    <!-- INFO -->
    <div class="link">
      Not registered?<br>
      <strong>Contact Super Admin for access</strong>
    </div>

  </div>
</div>

</body>
</html>