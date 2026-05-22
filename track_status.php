<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/* Fetch tickets of logged-in user */
$query = mysqli_query($conn, "
    SELECT * FROM tickets
    WHERE user_id = '$user_id'
    ORDER BY created_date DESC
");
?>
<!DOCTYPE html>
<html>
<head>
<title>Track Ticket Status</title>
  <link rel="stylesheet" href="../assets/css/style.css">
  </head>
<body>

<div class="dashboard-container">

  <!-- HEADER -->
  <div class="dashboard-header">
    <h2><i class="fa-solid fa-ticket"></i> Track Status Of Your Tickets</h2>
    <p>Track all your submitted service requests</p>
  </div>

  <!-- CARD -->
  <div class="card">

    <?php
    if(mysqli_num_rows($query) == 0){
        echo "<p>No tickets found.</p>";
    } else {
    ?>
    <table class="table">
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Priority</th>
        <th>Status</th>
        <th>Created On</th>
      </tr>

      <?php while($row = mysqli_fetch_assoc($query)){ ?>
      <tr>
        <td>#<?php echo $row['ticket_id']; ?></td>

        <td>
          <a href="ticket_details.php?id=<?php echo $row['ticket_id']; ?>"
             class="ticket-link">
             <?php echo htmlspecialchars($row['title']); ?>
          </a>
        </td>

        <td><?php echo $row['priority']; ?></td>

        <!-- STATUS (SUBTLE) -->
        <td>
          <?php
          if($row['status'] == "Open"){
            echo "<span class='status small open'>Open</span>";
          }elseif($row['status'] == "In Progress"){
            echo "<span class='status small in_progress'>In Progress</span>";
          }else{
            echo "<span class='status small resolved'>Resolved</span>";
          }
          ?>
        </td>

        <td><?php echo date("d M Y", strtotime($row['created_date'])); ?></td>
      </tr>
      <?php } ?>
    </table>
    <?php } ?>

  </div>

  <!-- BACK BUTTON -->
  <a href="dashboard.php" class="btn back-btn">
    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
  </a>

</div>

</body>
</html>
