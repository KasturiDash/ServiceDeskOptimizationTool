<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Fetch user details */
$query = mysqli_query($conn,"
    SELECT name, email, role, created_at
    FROM users
    WHERE user_id = '$user_id'
");

$user = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html>
<head>
<title>My Profile</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="dashboard-container">

  <!-- HEADER -->
  <div class="dashboard-header">
    <h2><i class="fa-solid fa-user"></i> My Profile</h2>
    <p>View your account details</p>
  </div>

  <!-- PROFILE CARD -->
  <div class="card" style="max-width:600px;margin:auto;">

    <p><strong>Name:</strong><br>
      <?php echo htmlspecialchars($user['name']); ?>
    </p>

    <p style="margin-top:15px;"><strong>Email:</strong><br>
      <?php echo htmlspecialchars($user['email']); ?>
    </p>

    <p style="margin-top:15px;"><strong>Role:</strong><br>
      <?php echo $user['role']; ?>
    </p>

    <p style="margin-top:15px;"><strong>Account Created On:</strong><br>
      <?php echo date("d M Y", strtotime($user['created_at'])); ?>
    </p>
	<a href="edit_profile.php">
  <button class="btn" style="margin-top:20px;">
    Edit Profile
  </button>
</a>


  </div>

  <!-- BACK BUTTON -->
  <a href="dashboard.php" class="btn back-btn">
    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
  </a>

</div>

</body>
</html>
