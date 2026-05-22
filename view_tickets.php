<?php
session_start();
include "../config/db.php";

$user_id = $_SESSION['user_id'];

$query = "SELECT ticket_id, title, status, created_date 
          FROM tickets 
          WHERE user_id = $user_id 
          ORDER BY created_date DESC";

$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html>
<head>
<title>My Tickets</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="dashboard-container">

  <!-- HEADER -->
  <div class="dashboard-header">
    <h2><i class="fa-solid fa-ticket"></i> My Tickets</h2>
    <p>View all your submitted service requests</p>
  </div>
  
</head>
<body>



<table class="ticket-table">
  <tr>
    <th>Ticket ID</th>
    <th>Title</th>
    <th>Status</th>
    <th>Created</th>
  </tr>

  <?php while($row = mysqli_fetch_assoc($result)): ?>
  <tr>
    <td>#<?= $row['ticket_id'] ?></td>
    <td><?= $row['title'] ?></td>
    <td>
      <span class="status <?= strtolower(str_replace(' ','_',$row['status'])) ?>">
        <?= $row['status'] ?>
      </span>
    </td>
    <td><?= date("d M Y", strtotime($row['created_date'])) ?></td>
  </tr>
  <?php endwhile; ?>
  
  
</table>
<!-- BACK BUTTON -->
  <a href="dashboard.php" class="btn back-btn">
    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
  </a>

</body>
</html>