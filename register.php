<?php
session_start();
include("../config/db.php");
// ✅ Clear old flash messages when opening Register page
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    unset($_SESSION['success']);
    unset($_SESSION['error']);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name  = $_POST['name'];
    $email = $_POST['email'];
    $pass  = $_POST['password'];
    $role  = $_POST['role'];

    $check = mysqli_query($conn, "SELECT user_id FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) > 0) {

        $_SESSION['error'] = "Email already registered.";

    } else {

        mysqli_query($conn, "
            INSERT INTO users (name, email, password, role, created_at)
            VALUES ('$name', '$email', '$pass', '$role', NOW())
        ");

        $_SESSION['success'] = "Registration successful. Please login.";
        header("Location: login.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="center-box">
  <div class="card">

    <h2><i class="fa-solid fa-user-plus"></i> Register</h2>
    <p class="subtitle">Create your account</p>

    <!-- ✅ SESSION MESSAGES -->
    <?php
    if (isset($_SESSION['error'])) {
        echo "<div class='alert alert-error'>" . $_SESSION['error'] . "</div>";
        unset($_SESSION['error']);
    }

    if (isset($_SESSION['success'])) {
        echo "<div class='alert alert-success'>" . $_SESSION['success'] . "</div>";
        unset($_SESSION['success']);
    }
    ?>

    <form method="POST">
        <input type="text" name="name" placeholder="👤 Full Name" required>
        <input type="email" name="email" placeholder="📧 Email" required>
        <input type="password" name="password" placeholder="🔒 Password" required>

        <select name="role" required>
            <option value="">Select Role</option>
            <option value="End User">End User</option>
            <option value="Agent">Service Desk Agent</option>
        </select>

        <button class="btn">Register</button>
    </form>

    <div class="link">
        Already registered? <a href="login.php">Login</a>
    </div>

  </div>
</div>

</body>
</html>