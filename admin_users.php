<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);

include 'db_connect.php';
session_start();

// Allowed roles for filtering
$allowed_roles = ['admin', 'nanny', 'client'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['userid'])) {
    $userid = intval($_POST['userid']);

    if ($userid > 0) {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userid);

        if ($stmt->execute()) {
            $_SESSION['status_message'] = [
            'text' => "User ID {$userid} and all related records deleted.",
            'type' => 'success'
          ];
        } else {
            $_SESSION['status_message'] = [
            'text' => "Error deleting user: " . $stmt->error,
            'type' => 'error'
          ];
        }

        $stmt->close();
    }

    header("Location: admin_users.php");
    exit;
}

// Get filter from query param safely
$filter_role = isset($_GET['role']) && in_array($_GET['role'], $allowed_roles) ? $_GET['role'] : '';

// Prepare SQL with optional filtering
if ($filter_role) {
    $stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, u.phone, u.address, u.date_of_birth, u.picture_path, u.created_at, u.username, 
    u.work_at_branch, u.role, u.status, u.suspended_until, sb.city, sb.address AS branch_address
    FROM users u
    LEFT JOIN shop_branches sb ON sb.id = u.work_at_branch
    WHERE u.role = ?
    ORDER BY u.role");
    $stmt->bind_param("s", $filter_role);
} else {
    $stmt = $conn->prepare("
    SELECT u.id, u.name, u.email, u.phone, u.address, u.date_of_birth, u.picture_path, u.created_at, u.username, 
    u.work_at_branch, u.role, u.status, u.suspended_until, sb.city, sb.address AS branch_address
    FROM users u
    LEFT JOIN shop_branches sb ON sb.id = u.work_at_branch
    ORDER BY u.role, u.id");
}

$stmt->execute();
$result = $stmt->get_result();

$users = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
}

$stmt->close();
$conn->close();

?>



<!DOCTYPE html>
<html lang="en">

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="adminNavbarStyle.css">
<link rel="stylesheet" href="footerStyle.css">
<title>Manage Users</title>
<style>
  html, body {
  margin: 0;
  padding: 0;
  min-height: 100%;
  width: 100%;
  overflow-x: hidden;
  
}

.page-content {
  padding-top: 60px; /* or margin-top: 60px; */
}


footer {
  position: relative;
  background-color: chocolate;
  color: white;
  text-align: center;
  padding: 20px 0;
  left: 0;
  min-width: 100%; /* this is the key */
}

.footer-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

.footer-links a, .social-icons a {
  color: white;
  margin: 0 10px;
  text-decoration: none;
}

.footer-links {
  margin: 10px 0;
}

.social-icons i {
  font-size: 20px;
}

  .content {
    flex: 1; /* Fills available vertical space */
    
  }


body > *:not(footer) {
  flex: 1;
}

    table {
  border-collapse: separate;
  border-spacing: 0;
  min-width: 1000px;
  font-family: Arial, sans-serif;
  background-color: bisque;
  border: 1px solid chocolate;
  border-radius: 10px;
}


th, td {
  border: 1px solid chocolate;
  padding: 8px 12px;
  text-align: center;

}
th {
  background-color: bisque;
  color: brown;
  text-align: center;
}
td{
  color:brown;
  
}
tbody tr:nth-child(even) {
  background-color: #fdf6e3;
}

  .na-cell {
    color: #999;
    font-style: italic;
  }
  form {
    max-width: 900px;
    margin: 20px auto;
    font-family: Arial, sans-serif;
    margin-top: 80px; /* pushes the filter form down */
  }
  label {
    font-weight: bold;
  }
  select {
    padding: 5px 10px;
    margin-left: 10px;
  }
  input[type="submit"] {
    padding: 5px 15px;
    margin-left: 10px;
  }
  .status-btn {
  padding: 6px 12px;
  border: none;
  border-radius: 5px;
  color: white;
  cursor: pointer;
  font-weight: bold;
}
.status-btn.suspend {
  background-color: #e74c3c;
}
.status-btn.reactivate {
  background-color: #2ecc71;
}

.export-btn {
  padding: 8px 16px;
  background-color: red;
  color: white;
  border: none;
  border-radius: 6px;
  cursor: pointer;
  font-weight: bold;
  margin-bottom: 10px;
  
}
.export-btn:hover {
  background-color: #A52A2A;
}
thead tr:first-child th:first-child {
  border-top-left-radius: 10px;
}
thead tr:first-child th:last-child {
  border-top-right-radius: 10px;
}
tbody tr:last-child td:first-child {
  border-bottom-left-radius: 10px;
}
tbody tr:last-child td:last-child {
  border-bottom-right-radius: 10px;
}
label{
  color: chocolate;
}
.table-scroll {
  overflow-x: auto;
  width: 100%;
  padding: 0 20px; /* adds space left and right like margin */
  
  box-sizing: border-box;
  margin-top: 20px;

  max-height: 500px; /* control vertical size */
  max-width: 100%;   /* allow horizontal scroll */
  overflow: auto;    /* enables both directions */

}

.table-scroll thead th{
  position: sticky;
  top: 0;
  background-color: #fdf6e3;
  z-index: 2;
}
.table-scroll th {
  white-space: nowrap; /* prevents breaking text */
}


h1{
  color: chocolate;
  text-align: center;
}
.status-btn{
  margin-top: 20px;
  margin-bottom: 20px;
}
.status-active {
  background-color: #2ecc71;
  color: white;
  padding: 4px 8px;
  border-radius: 5px;
  font-weight: bold;
}
.status-suspended {
  background-color: #e74c3c;
  color: white;
  padding: 4px 8px;
  border-radius: 5px;
  font-weight: bold;
}
.table-vertical-scroll {
  max-height: 500px; /* adjust height as needed */
  overflow-y: auto;
  overflow-x: auto;       /* horizontal scroll if needed */
  margin: 20px;
  border: 1px solid chocolate;
  border-radius: 10px;
}
.table-vertical-scroll table {
  width: 100%;
  border-collapse: collapse;
}

.table-vertical-scroll thead {
  position: sticky;
  top: 0;
  background-color: #fdf6e3;
  z-index: 2;
}

.table-vertical-scroll th, 
.table-vertical-scroll td {
  padding: 8px 12px;
  border: 1px solid chocolate;
  text-align: center;
  white-space: nowrap;
}
.status-alert {
  background-color: #dff0d8;
  color: #3c763d;
  padding: 12px;
  margin: 10px auto;
  border: 1px solid #d6e9c6;
  width: 80%;
  text-align: center;
  border-radius: 5px;
   margin-top: 20px;
}
select {
  padding: 5px 10px;
  margin-left: 10px;
  background-color: burlywood;
  border: 1px solid bisque;
  border-radius: 10px;
  color: white;
  font-weight: bold;
}

button.delete-btn {
  background-color: #e74c3c; /* red */
  color: white;
  border: none;
  border-radius: 5px;
  padding: 6px 12px;
  cursor: pointer;
  font-weight: bold;
  transition: background-color 0.2s;
}

button.delete-btn:hover {
  background-color: #c0392b; /* darker red on hover */
}

.status-message {
  padding: 12px 20px;
  margin: 15px auto;
  width: 80%;
  text-align: center;
  border-radius: 6px;
  font-weight: bold;
  box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

/* Success */
.status-message.success {
  background-color: #d4edda; /* light green */
  color: #155724;             /* dark green */
  border: 1px solid #c3e6cb;
}

/* Error */
.status-message.error {
  background-color: #f8d7da; /* light red */
  color: #721c24;             /* dark red */
  border: 1px solid #f5c6cb;
}

 /* Hamburger Button */
    .menu-toggle {
    display: none;  /* hidden on desktop */
    
    }
    
/* Mobile Responsive */
@media screen and (max-width: 480px) {
    .AdminNavbar {
        flex-direction: column;
        display: none; /* hidden by default */
        position: fixed;     /* also fixed */
        top: 50px;           /* push below menu-toggle */
        left: 0;
        right: 0;
        background: chocolate;
        z-index: 1000;
    }
    .AdminNavbar a {
            display: block;
            color: white;
            text-align: center;
            padding: 14px 130px;
            text-decoration: none;
            font-weight: bold;
            flex:1;/* 🔥 Make all items take equal space */
    }
    .AdminNavbar.active {
        display: flex; /* shown when toggled */
    }
    .menu-toggle {
    display: block; /* show only on mobile */
    position: fixed;     /* 🧲 stick to top */
    top: 0;              /* align top */
    left: 0;             /* align left */
    width: 100%;         /* full width bar */
    cursor: pointer;
    font-size: 22px;
    text-decoration: none;
    font-weight: bold;
    padding: 10px;
    background: chocolate;
    z-index: 1100;       /* higher than navbar */
    color: white;
    text-align: left;
    }
    body {
        padding-top: 50px; /* prevent overlap */
    }

    .export-btn {
    padding: 4px 8px;
    background-color: red;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
    font-weight: bold;
    margin-bottom: 10px;
    margin-right: 5px;
    font-size: 12px;
  
  }

    .footer-links a {
        display: inline-block;
        margin: 5px 8px;
    }

}

</style>
</head>

<body>
   
<div class="page-content">
 <?php include 'admin_navbar.php'; ?>
 


    <h1>Manage Users</h1>
    
<?php if(isset($_SESSION['status_message']) && is_array($_SESSION['status_message'])): 
    $msg = $_SESSION['status_message'];
?>
  <div class="status-message <?= htmlspecialchars($msg['type']) ?>">
    <?= htmlspecialchars($msg['text']) ?>
  </div>
  <?php unset($_SESSION['status_message']); ?>
<?php endif; ?>



    <div style="display:flex; justify-content:space-between; align-items:center; max-width: 900px; margin: 0 auto; margin-top:20px;">
<form method="get" action="" style="display:flex; align-items:center; margin-top:10px;">
  <label for="role">Filter by role:</label>
  <select name="role" id="role">  
  <option value="">All</option>
    <?php foreach ($allowed_roles as $role): ?>
      <option value="<?= htmlspecialchars($role) ?>" <?= $filter_role === $role ? 'selected' : '' ?>>
        <?= ucfirst($role) ?>
      </option>
    <?php endforeach; ?>
  </select>
  <input style="background-color:chocolate;color:white; border-radius:10px;margin-left: 10px;border: 1px solid burlywood;font-weight:bold;" type="submit" value="Filter" />
</form>
<a href="export_users_pdf.php?role=<?= urlencode($filter_role) ?>" class="export-btn" style="text-decoration:none;display:inline-block; ">
  <i class="fas fa-file-pdf"></i> Export to PDF
</a>

    </div>
<!-- Mass Update Form -->

<div class="table-vertical-scroll">
  <table>
    <thead>
      <tr>
        <th><input type="checkbox" id="selectAll" /></th>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Date of Birth</th>
        <th>Picture path</th>
        <th>Created_at</th>
        <th>Username</th>
        <th>Work At branch</th>
        <th>Role</th>
        <th>Status</th>
        <th>Suspended Until</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user): ?>
        <tr>
          <td><input type="checkbox" class="user-checkbox" name="selected_users_checkbox" value="<?= $user['id'] ?>"></td>
          <td><?= htmlspecialchars($user['id']) ?></td>
          <td><?= htmlspecialchars($user['name'] ?? '') ?></td>
          <td><?= htmlspecialchars($user['email'] ?? '') ?></td>
          <td class="<?= empty($user['phone']) ? 'na-cell' : '' ?>">
            <?= !empty($user['phone']) ? htmlspecialchars($user['phone']) : 'N/A' ?>
          </td>
          <td class="<?= empty($user['address']) ? 'na-cell' : '' ?>">
            <?= !empty($user['address']) ? htmlspecialchars($user['address']) : 'N/A' ?>
          </td>
          <td><?= htmlspecialchars($user['date_of_birth'] ?: 'N/A') ?></td>
          <td><?= htmlspecialchars($user['picture_path'] ?? '') ?></td>
          <td><?= htmlspecialchars($user['created_at']) ?></td>
          <td><?= htmlspecialchars($user['username']) ?></td>
          <td><?= htmlspecialchars($user['city'] ?? '') . ' ' . htmlspecialchars($user['branch_address'] ?? '') ?></td>
          <td><?= htmlspecialchars($user['role']) ?></td>
          <td>
            <span class="status-<?= $user['status'] ?>">
              <?= ucfirst($user['status']) ?>
            </span>
          </td>
          <td><?= htmlspecialchars($user['suspended_until'] ?? '') ?></td>
          <td>
      <form method="post" action="" style="display:inline;">
        <input type="hidden" name="userid" value="<?= htmlspecialchars($user['id']) ?>">
        <button type="submit" class="delete-btn" onclick="return confirm('Are you sure you want to delete <?= htmlspecialchars($user['username']) ?> user?');">
            Delete
        </button>
      </form>
        </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<!-- MASS ACTION PANEL -->
<div class="mass-actions-panel" style="text-align:center; margin: 15px;">
  <form method="post" action="mass_update_status.php" id="massActionForm">
    <input type="hidden" name="selected_users" id="selectedUsers">
    <button type="submit" name="action" value="suspend" class="status-btn suspend">Mass Suspend</button>
    <button type="submit" name="action" value="reactivate" class="status-btn reactivate">Mass Reactivate</button>
  </form>
</div>


  <script>
  // Select All toggle
  document.getElementById('selectAll').addEventListener('change', function() {
    document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = this.checked);
  });

  // Before submitting mass action, collect selected user IDs
 document.getElementById('massActionForm').addEventListener('submit', function(e) {
    const selected = [];
    document.querySelectorAll("input[name='selected_users_checkbox']:checked").forEach(cb => {
        selected.push(cb.value);
    });
    if (selected.length === 0) {
        alert("Please select at least one user.");
        e.preventDefault();
        return;
    }
    // Set the hidden input
    document.getElementById('selectedUsers').value = selected.join(',');
});
</script>

</div>



<script>
  document.addEventListener("DOMContentLoaded", function() {
    document.querySelector('.menu-toggle').addEventListener('click', function() {
      document.querySelector('.AdminNavbar').classList.toggle('active');
    });
  });
</script>

<?php include 'footer.php'; ?>

</body>

</html>