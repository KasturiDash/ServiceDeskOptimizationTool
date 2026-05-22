<?php
session_start();
require_once("../config/db.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    /* 🔐 ADMIN (NORMAL + SUPER) */
    $stmt = $conn->prepare("
        SELECT user_id, role, status, is_super_admin
        FROM users
        WHERE email = ?
          AND password = ?
          AND role = 'Admin'
        LIMIT 1
    ");
    $stmt->bind_param("ss", $email, $pass);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $admin = $result->fetch_assoc();

        /* 🚫 ACCOUNT DISABLED */
        if ($admin['status'] === 'Inactive') {
            $error = "Your account is temporarily disabled. Please contact the Super Admin.";
        } else {

            /* ✅ SESSION */
            $_SESSION['user_id']        = $admin['user_id'];
            $_SESSION['role']           = 'Admin';
            $_SESSION['is_super_admin'] = (int)$admin['is_super_admin'];

            /* 📜 AUDIT LOG */
            $logText = $admin['is_super_admin']
                ? 'Super Admin logged in'
                : 'Admin logged in';

            $log = $conn->prepare("
                INSERT INTO audit_logs (user_id, action)
                VALUES (?, ?)
            ");
            $log->bind_param("is", $admin['user_id'], $logText);
            $log->execute();

            /* 🔁 REDIRECT */
            if ($admin['is_super_admin']) {
                header("Location: ../super_admin/dashboard.php");
            } else {
                header("Location: ../admin/dashboard.php");
            }
            exit();
        }

    } else {
        $error = "Invalid admin credentials.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="center-box">
  <div class="card">

    <h2>🔐 Admin Login</h2>
    <p class="subtitle">Admin Panel Access</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error">
        <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form method="POST">
      <input type="email" name="email" placeholder="Admin Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button class="btn">Login</button>
    </form>

    <div class="link">
      Not registered?<br>
      <strong>Contact Super Admin</strong><br><br>
      <a href="../admin/index.php">← Back to Admin Panel</a>
    </div>

  </div>
</div>

</body>
</html>