<?php
session_start();
include("../config/db.php");

if(!isset($_SESSION['user_id'])){
    header("Location: ../auth/login.php");
    exit();
}
?>
<?php
if($_SERVER["REQUEST_METHOD"] == "POST"){

    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $description = mysqli_real_escape_string($conn, $_POST['description']);
    $priority = $_POST['priority'];
    $user_id = $_SESSION['user_id'];

    $query = "
        INSERT INTO tickets
        (title, description, priority, status, created_date, user_id)
        VALUES
        ('$title', '$description', '$priority', 'Open', NOW(), '$user_id')
    ";

    if(mysqli_query($conn, $query)){

  // ✅ AUDIT LOG: ticket creation
  mysqli_query($conn, "
    INSERT INTO audit_logs (user_id, action)
    VALUES ('{$_SESSION['user_id']}', 'Created a new support ticket')
  ");

  $_SESSION['success'] = "Ticket submitted successfully.";
  header("Location: dashboard.php");
  exit();

}else{
  $_SESSION['error'] = "Failed to submit ticket. Try again.";
}
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Ticket</title>
<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
<div style="position:absolute;top:20px;right:30px;">
  <a href="../auth/logout.php" style="color:#ef4444;font-weight:500;text-decoration:none;">
    <i class="fa-solid fa-right-from-bracket"></i> Logout
  </a>
</div>


<div class="dashboard-container">

  <div class="dashboard-header">
    <h2><i class="fa-solid fa-plus"></i> Create Service Ticket</h2>
    <p>Describe your issue and submit a service request</p>
  </div>

  <div class="card" style="max-width:720px;margin:auto;">

    <form method="POST">

  <label>Issue Title</label>
  <input type="text" name="title"
         placeholder="Short summary of the issue" required>

  <label>Description</label>
  <textarea name="description"
            placeholder="Explain the issue in detail"
            required></textarea>

  <label>Priority</label>
  <select name="priority" required>
    <option value="">Select Priority</option>
    <option value="Low">Low – Minor issue</option>
    <option value="Medium">Medium – Affects work</option>
    <option value="High">High – Critical issue</option>
  </select>

  <button class="btn">
    <i class="fa-solid fa-paper-plane"></i> Submit Ticket
  </button>
  <!-- BACK BUTTON -->
  <a href="dashboard.php" class="btn back-btn">
    <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
  </a>

</form>


  </div>

</div>

</body>
</html>
