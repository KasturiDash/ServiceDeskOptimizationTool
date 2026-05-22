<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

if(!isset($_GET['id'])){
    header("Location: view_tickets.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$ticket_id = $_GET['id'];

/* Fetch ticket details (security check included) */
$query = mysqli_query($conn,"
    SELECT * FROM tickets
    WHERE ticket_id = '$ticket_id'
    AND user_id = '$user_id'
");

if(mysqli_num_rows($query) == 0){
    header("Location: view_tickets.php");
    exit();
}

$ticket = mysqli_fetch_assoc($query);
?>
<!DOCTYPE html>
<html>
<head>
<title>Ticket Details</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="dashboard-container">

  <!-- HEADER -->
  <div class="dashboard-header">
    <h2><i class="fa-solid fa-ticket"></i> Ticket Details</h2>
    <p>View complete information about your service request</p>
  </div>

  <!-- DETAILS CARD -->
  <div class="card">

    <p><strong>Ticket ID:</strong> #<?php echo $ticket['ticket_id']; ?></p>

    <p style="margin-top:12px;">
      <strong>Title:</strong><br>
      <?php echo htmlspecialchars($ticket['title']); ?>
    </p>

    <p style="margin-top:12px;">
      <strong>Description:</strong><br>
      <?php echo nl2br(htmlspecialchars($ticket['description'])); ?>
    </p>

    <p style="margin-top:12px;">
      <strong>Priority:</strong>
      <?php echo $ticket['priority']; ?>
    </p>

    <div style="margin-top:25px;">
  <strong>Ticket Progress</strong>

  <div class="status-tracker">

    <div class="status-step <?php if($ticket['status']=="Open" || $ticket['status']=="In Progress" || $ticket['status']=="Resolved") echo "status-active"; ?>">
      <div class="status-circle">1</div>
      <div class="status-label">Open</div>
    </div>

    <div class="status-step <?php if($ticket['status']=="In Progress" || $ticket['status']=="Resolved") echo "status-active"; ?>">
      <div class="status-circle">2</div>
      <div class="status-label">In Progress</div>
    </div>

    <div class="status-step <?php if($ticket['status']=="Resolved") echo "status-active"; ?>">
      <div class="status-circle">3</div>
      <div class="status-label">Resolved</div>
    </div>

  </div>
</div>


    <p style="margin-top:12px;">
      <strong>Created On:</strong>
      <?php echo date("d M Y, h:i A", strtotime($ticket['created_date'])); ?>
    </p>

  </div>

  <!-- BACK BUTTON -->
  <a href="view_tickets.php">
    <button class="btn" style="margin-top:25px;">
      <i class="fa-solid fa-arrow-left"></i> Back to My Tickets
    </button>
  </a>

</div>

</body>
</html>
