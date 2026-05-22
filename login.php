<?php
session_start();
require_once("../config/db.php");

/* =========================
   HANDLE LOGIN SUBMISSION
   ========================= */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $login_as = $_POST['login_as'] ?? '';
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);

    /* ROLE CHECK */
    if (!in_array($login_as, ['End User', 'Agent'])) {
        $_SESSION['error'] = "Invalid login type.";
        header("Location: login.php");
        exit();
    }

    /* USER / AGENT LOGIN */
    $stmt = $conn->prepare("
        SELECT user_id, role, status
        FROM users
        WHERE email = ?
          AND password = ?
          AND role = ?
          AND is_super_admin = 0
        LIMIT 1
    ");
    $stmt->bind_param("sss", $email, $password, $login_as);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        /* =========================
           INACTIVE ACCOUNT LOGIC
           ========================= */
        if ($user['status'] === 'Inactive') {

            $check = $conn->prepare("
                SELECT status
                FROM activation_requests
                WHERE user_id = ?
                ORDER BY requested_at DESC
                LIMIT 1
            ");
            $check->bind_param("i", $user['user_id']);
            $check->execute();
            $req = $check->get_result()->fetch_assoc();

            if ($req && $req['status'] === 'Pending') {
                $_SESSION['error'] =
                    "Your activation request is already pending approval.";
            } else {
                $_SESSION['error'] =
                    "Your account is inactive. You may request activation below.";
                $_SESSION['activation_user'] = $user['user_id'];
            }

            header("Location: login.php");
            exit();
        }

        /* =========================
           LOGIN SUCCESS
           ========================= */
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role']    = $user['role'];
        $_SESSION['success'] = "Login successful. Welcome back!";

        mysqli_query($conn, "
            INSERT INTO audit_logs (user_id, action)
            VALUES ('{$user['user_id']}', 'User logged in')
        ");

        if ($user['role'] === 'End User') {
            header("Location: ../user/dashboard.php");
        } else {
            header("Location: ../agent/dashboard.php");
        }
        exit();

    } else {
        $_SESSION['error'] = "Invalid email or password.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="center-box">
    <div class="card">

        <!-- SUCCESS MESSAGE (FROM REGISTER / LOGOUT) -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?= htmlspecialchars($_SESSION['success']); ?>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <h2><i class="fa-solid fa-right-to-bracket"></i> Login</h2>
        <p class="subtitle">Access your account</p>

        <!-- ERROR MESSAGE -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?= htmlspecialchars($_SESSION['error']); ?>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <form method="POST">

            <select name="login_as" required>
                <option value="">Login As</option>
                <option value="End User">End User</option>
                <option value="Agent">Agent</option>
            </select>

            <input type="email" name="email" placeholder="📧 Email" required>
            <input type="password" name="password" placeholder="🔒 Password" required>

            <button class="btn">Login</button>
        </form>

        <!-- 🔴 ACTIVATION REQUEST BUTTON (DO NOT UNSET HERE) -->
        <?php if (isset($_SESSION['activation_user'])): ?>
            <form method="POST" action="request_activation.php" style="margin-top:15px;">
                <input type="hidden" name="user_id"
                       value="<?= $_SESSION['activation_user']; ?>">
                <button class="btn btn-warning">
                    Request Activation
                </button>
            </form>
        <?php endif; ?>

        <div class="link">
            New user? <a href="register.php">Register</a>
        </div>

    </div>
</div>

</body>
</html>