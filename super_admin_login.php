<?php
session_start();
require_once("../config/db.php");

/* Already logged in */
if (
    isset($_SESSION['role']) &&
    $_SESSION['role'] === 'Admin' &&
    !empty($_SESSION['is_super_admin'])
) {
    header("Location: ../super_admin/dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST['email']);
    $pass  = trim($_POST['password']);

    $stmt = $conn->prepare("
        SELECT user_id, password, status
        FROM users
        WHERE email = ?
          AND role = 'Admin'
          AND is_super_admin = 1
        LIMIT 1
    ");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $sa = $result->fetch_assoc();

        /* PASSWORD CHECK (UNCHANGED STYLE) */
        if ($pass !== $sa['password']) {
            $error = "Invalid Super Admin credentials.";
        }
        elseif ($sa['status'] === 'Inactive') {
            $error = "Your Super Admin account is inactive.";
        }
        else {
            $_SESSION['user_id']        = $sa['user_id'];
            $_SESSION['role']           = 'Admin';
            $_SESSION['is_super_admin'] = 1;

            $_SESSION['success'] = "Welcome Super Admin! Login successful.";

            header("Location: ../super_admin/dashboard.php");
            exit();
        }

    } else {
        $error = "Invalid Super Admin credentials.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Super Admin Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

<div class="center-box">
  <div class="card">

    <h2>👑 Super Admin Login</h2>
    <p class="subtitle">Restricted Access</p>

    <?php if (!empty($error)): ?>
      <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <?php
    if (isset($_SESSION['sa_logout_success'])) {
        echo "<div class='alert alert-success'>{$_SESSION['sa_logout_success']}</div>";
        unset($_SESSION['sa_logout_success']);
    }
    ?>

    <form method="POST">
      <input type="email" name="email" placeholder="Super Admin Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button class="btn">Login</button>
    </form>

    <div class="link">
      <strong>No registration allowed</strong><br><br>
      <a href="../admin/index.php">← Back to Admin Panel</a>
    </div>

  </div>
</div>

</body>
</html>