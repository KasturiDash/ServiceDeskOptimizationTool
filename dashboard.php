<?php
session_start();

/* Security check */
if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>User Dashboard</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="dashboard-container">

  <!-- HEADER -->
  <div class="dashboard-header">
    <h2><i class="fa-solid fa-user"></i> User Dashboard</h2>
    <p>Manage your service requests and track their status</p>
  </div>
  <?php
if(isset($_SESSION['success'])){
  echo "<div class='alert alert-success'>".$_SESSION['success']."</div>";
  unset($_SESSION['success']);
}
if(isset($_SESSION['error'])){
  echo "<div class='alert alert-error'>".$_SESSION['error']."</div>";
  unset($_SESSION['error']);
}
?>


  <!-- GRID -->
  <div class="dashboard-grid">

    <a href="create_ticket.php" style="text-decoration:none;color:inherit;">
      <div class="dashboard-card">
        <i class="fa-solid fa-plus"></i>
        <h4>Create Ticket</h4>
        <p>Raise a new service request</p>
      </div>
    </a>

    <a href="view_tickets.php" class="dashboard-link">
  <div class="dashboard-card">
    <i class="fa-solid fa-ticket"></i>
    <h4>My Tickets</h4>
    <p>View all your submitted tickets</p>
  </div>
</a>

<a href="track_status.php" class="dashboard-link">
  <div class="dashboard-card">
    <i class="fa-solid fa-clock"></i>
    <h4>Track Status</h4>
    <p>Monitor progress of your requests</p>
  </div>
</a>

    <a href="profile.php" style="text-decoration:none;color:inherit;">
  <div class="dashboard-card">
    <i class="fa-solid fa-user-gear"></i>
    <h4>My Profile</h4>
    <p>View and manage your account</p>
  </div>
</a>


    </div>

  </div>

  <!-- LOGOUT -->
  <div class="logout-wrapper">
    <a href="../auth/logout.php" class="logout-link">
        <i class="fa-solid fa-right-from-bracket"></i> Logout
    </a>
</div>


</div>

</body>
</html>
