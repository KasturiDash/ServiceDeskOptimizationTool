<?php
require_once("../includes/auth_check.php");

/* ALLOW ADMIN & SUPER ADMIN */
if ($_SESSION['role'] !== 'Admin') {
    header("Location: ../auth/admin_login.php");
    exit();
}

$isSuperAdmin = !empty($_SESSION['is_super_admin']);

include("../config/db.php");

/* Fetch tickets with users & agents */
$query = mysqli_query($conn, "
    SELECT 
        t.ticket_id,
        t.title,
        t.priority,
        t.status,
        t.created_date,
        u.name AS user_name,
        a.name AS agent_name,
        t.assigned_agent_id
    FROM tickets t
    JOIN users u ON t.user_id = u.user_id
    LEFT JOIN users a ON t.assigned_agent_id = a.user_id
    ORDER BY t.created_date DESC
");

/* Fetch all agents once */
$agents = mysqli_query($conn, "SELECT user_id, name FROM users WHERE role='Agent'");
$agentList = [];
while ($ag = mysqli_fetch_assoc($agents)) {
    $agentList[] = $ag;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>All Tickets</title>

<link rel="stylesheet" href="../assets/css/style.css">
<link rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
.assign-wrapper{display:flex;flex-direction:column;gap:8px;}
.assign-form{display:flex;gap:8px;align-items:center;}
.agent-badge{
  background:#e0e7ff;color:#3730a3;
  padding:6px 14px;border-radius:999px;font-weight:600;
}
.muted-text{font-size:12px;color:#555;}
.inline-form{display:inline-block;}
.btn-danger{background:#fee2e2;color:#991b1b;border:none;}
.badge{font-weight:600;}
</style>
</head>

<body>

<div class="dashboard-container">

  <!-- HEADER -->
  <div class="dashboard-header center">
    <h2>
      <i class="fa-solid fa-clipboard-list"></i> All Tickets
    </h2>
    <p>System-wide ticket overview</p>
  </div>

  <!-- CARD -->
  <div class="card">

    <!-- EXPORT -->
    <div style="text-align:right;margin-bottom:15px;">
      <div class="export-dropdown">
        <button type="button" class="btn btn-primary btn-sm export-toggle">
          Export Ticket Details ▼
        </button>
        <div class="export-menu">
          <a href="export_all_tickets_pdf.php">Export as PDF</a>
          <a href="export_all_tickets_csv.php">Export as CSV</a>
        </div>
      </div>
    </div>

    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Title</th>
          <th>User</th>
          <th>Priority</th>
          <th>Status</th>
          <th>Created</th>
          <th>Assign Agent</th>
        </tr>
      </thead>

      <tbody>
      <?php while ($row = mysqli_fetch_assoc($query)) { ?>
        <tr>

          <td>#<?= $row['ticket_id'] ?></td>
          <td><?= htmlspecialchars($row['title']) ?></td>
          <td><?= htmlspecialchars($row['user_name']) ?></td>
          <td><?= $row['priority'] ?></td>

          <td>
            <?php
              if ($row['status']==="Open")
                echo "<span class='badge badge-open'>Open</span>";
              elseif ($row['status']==="In Progress")
                echo "<span class='badge badge-progress'>In Progress</span>";
              else
                echo "<span class='badge badge-resolved'>Resolved</span>";
            ?>
          </td>

          <td><?= date("d M Y", strtotime($row['created_date'])) ?></td>

          <td>
            <div class="assign-wrapper">

            <?php if ($row['assigned_agent_id']) { ?>
              <span class="agent-badge">
                Assigned to <?= htmlspecialchars($row['agent_name']) ?>
              </span>
            <?php } ?>

            <?php if ($row['status']==="Resolved") { ?>
              <span class="muted-text">Assignment locked</span>

            <?php } else { ?>

              <form method="POST" action="assign_ticket.php" class="assign-form">
                <input type="hidden" name="ticket_id" value="<?= $row['ticket_id'] ?>">

                <select name="agent_id" required>
                  <option value="">Select Agent</option>
                  <?php foreach ($agentList as $ag) { ?>
                    <option value="<?= $ag['user_id'] ?>"
                      <?= $row['assigned_agent_id']==$ag['user_id']?'selected':'' ?>>
                      <?= htmlspecialchars($ag['name']) ?>
                    </option>
                  <?php } ?>
                </select>

                <button class="btn btn-sm btn-primary">
                  <?= $row['assigned_agent_id'] ? "Reassign" : "Assign" ?>
                </button>
              </form>

              <?php if ($row['assigned_agent_id']) { ?>
                <form method="POST" action="unassign_ticket.php" class="inline-form">
                  <input type="hidden" name="ticket_id" value="<?= $row['ticket_id'] ?>">
                  <button class="btn btn-sm btn-danger">Unassign</button>
                </form>
              <?php } ?>

            <?php } ?>

            </div>
          </td>
        </tr>
      <?php } ?>
      </tbody>
    </table>

  </div>

  <!-- BACK BUTTON (SMART) -->
  <div style="text-align:center;margin-top:30px;">
    <a href="<?= $isSuperAdmin ? '../super_admin/dashboard.php' : 'dashboard.php' ?>" class="btn btn-primary">
      <i class="fa-solid fa-arrow-left"></i> Back to Dashboard
    </a>
  </div>

</div>

<script>
document.addEventListener('click', e => {
  document.querySelectorAll('.export-dropdown').forEach(d=>{
    if(!d.contains(e.target)) d.classList.remove('open');
  });
  const btn=e.target.closest('.export-toggle');
  if(btn){btn.parentElement.classList.toggle('open');}
});
</script>

</body>
</html>