<?php
session_start();
require 'config.php'; // DB connection

// Redirect non-logged-in users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT is_admin FROM users WHERE user_id = $user_id");
$row = $result->fetch_assoc();

if (!$row || !$row['is_admin']) {
    echo "Access denied.";
    exit();
}

// Handle actions
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $conn->query("UPDATE users SET status = 'approved' WHERE user_id = $id");
    //$conn->query("UPDATE users SET is_approved = 1 WHERE user_id = $id");
} elseif (isset($_GET['deny'])) {
    $id = intval($_GET['deny']);
    $conn->query("DELETE FROM users WHERE user_id = $id");
  } elseif (isset($_GET['suspend'])) {
    $id = intval($_GET['suspend']);
    $conn->query("UPDATE users SET status = 'suspended' WHERE user_id = $id");
} elseif (isset($_GET['unsuspend'])) {
    $id = intval($_GET['unsuspend']);
    $conn->query("UPDATE users SET status = 'approved' WHERE user_id = $id");
} elseif (isset($_GET['promote'])) {
    $id = intval($_GET['promote']);
    $conn->query("UPDATE users SET is_admin = 1 WHERE user_id = $id");
}

// Handle new admin creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_admin'])) {
    $new_name = trim($_POST['full_name']);
    $new_email = trim($_POST['email']);
    $new_password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($new_name && $new_email && $_POST['password']) {
        //$stmt = $conn->prepare("INSERT INTO users (full_name, email, password, is_admin, is_approved) VALUES (?, ?, ?, 1, 1)");
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, is_admin, status) VALUES (?, ?, ?, 1, 'approved')");
        $stmt->bind_param("sss", $new_name, $new_email, $new_password);
        $stmt->execute();
        $stmt->close();
        $admin_msg = "<p style='color: green;'>New admin added successfully.</p>";
    } else {
        $admin_msg = "<p style='color: red;'>Please fill out all fields.</p>";
    }
}

// Fetch user lists
//$pending_users = $conn->query("SELECT user_id, full_name, email FROM users WHERE is_approved = 0");
$pending_users = $conn->query("SELECT user_id, full_name, email FROM users WHERE status = 'pending'");
//$all_users = $conn->query("SELECT user_id, full_name, email, is_approved, partner_id, suspended, is_admin FROM users ORDER BY user_id DESC");
$all_users = $conn->query("SELECT user_id, full_name, email, status, is_admin FROM users ORDER BY user_id DESC");

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
</head>
<body>

<div class="navbar">
  <div class="navbar-left">
    <div class="logo">BarterBuddies</div>
  </div>
  <div class="navbar-right">
    <a href="approvals.php" class="nav-button active">Approvals</a>
    <a href="admin_dashboard.php" class="nav-button">Admin Panel</a>
    <a href="logout.php" class="nav-button">Log Out</a>
  </div>
</div>

<div style="padding: 20px;">
  <h1>Welcome Admin</h1>

  <h2>Pending Approvals</h2>
  <?php if ($pending_users->num_rows > 0): ?>
    <table border="1" style="margin-bottom: 30px;">
      <tr><th>Name</th><th>Email</th><th>Actions</th></tr>
      <?php while ($user = $pending_users->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($user['full_name']) ?></td>
          <td><?= htmlspecialchars($user['email']) ?></td>
          <td>
            <a href="?approve=<?= $user['user_id'] ?>">Approve</a> |
            <a href="?deny=<?= $user['user_id'] ?>" onclick="return confirm('Deny and delete this user?')">Deny</a>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  <?php else: ?>
    <p>No users pending approval.</p>
  <?php endif; ?>

  <h2>Create New Admin</h2>
  <?php if (isset($admin_msg)) echo $admin_msg; ?>
  <form method="post">
    <label for="full_name">Full Name:</label><br>
    <input type="text" name="full_name" required><br><br>

    <label for="email">Email:</label><br>
    <input type="email" name="email" required><br><br>

    <label for="password">Password:</label><br>
    <input type="password" name="password" required><br><br>

    <input type="submit" name="add_admin" value="Add Admin">
  </form>
</div>

</body>
</html>
