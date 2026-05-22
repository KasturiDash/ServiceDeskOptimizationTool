<?php
session_start();
include("../config/db.php");

/* ADMIN REGISTRATION KEY */
define("ADMIN_REGISTER_KEY", "ALLOW_ADMIN_2026");

/* BLOCK UNAUTHORIZED ACCESS */
if (!isset($_GET['key']) || $_GET['key'] !== ADMIN_REGISTER_KEY) {
    die("❌ Unauthorized access.");
}

/* HANDLE FORM */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    $check = mysqli_query($conn, "
        SELECT user_id FROM users WHERE email='$email'
    ");

    if (mysqli_num_rows($check) > 0) {
        $error = "Admin already exists";
    } else {

        mysqli_query($conn, "
            INSERT INTO users (name, email, password, role)
            VALUES ('$name', '$email', '$pass', 'Admin')
        ");

        $_SESSION['success'] = "Admin registered successfully. Please login.";
        header("Location: admin_login.php");
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Registration</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="center-box">
  <div class="card">
    <h2>🛡 Admin Registration</h2>
    <p class="subtitle">Authorized personnel only</p>

    <?php if(!empty($error)): ?>
      <div class="alert alert-error"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
      <input type="text" name="name" placeholder="Admin Name" required>
      <input type="email" name="email" placeholder="Admin Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button class="btn">Register Admin</button>
    </form>

    <div class="link">
      Already admin?
      <a href="admin_login.php">Login</a>
    </div>

  </div>
</div>

</body>
</html>