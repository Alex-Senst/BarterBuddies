<?php
session_start();
require 'config.php'; // DB connection

// Check admin session

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get is_admin from DB
$stmt = $conn->prepare("SELECT is_admin FROM users WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$stmt->bind_result($is_admin);
$stmt->fetch();
$stmt->close();

if (!$is_admin) {
    header("Location: login.php");
    exit();
}


// Handle actions
if (isset($_GET['suspend'])) {
    $id = intval($_GET['suspend']);
    $conn->query("UPDATE users SET suspended = 1 WHERE user_id = $id");
} elseif (isset($_GET['unsuspend'])) {
    $id = intval($_GET['unsuspend']);
    $conn->query("UPDATE users SET suspended = 0 WHERE user_id = $id");
} elseif (isset($_GET['promote'])) {
    $id = intval($_GET['promote']);
    $conn->query("UPDATE users SET is_admin = 1 WHERE user_id = $id");
  } elseif (isset($_GET['delete_match'])) {
    $match_id = intval($_GET['delete_match']);
    $conn->query("DELETE FROM trade_match WHERE match_id = $match_id");
  } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $match_id = intval($_POST['match_id']);
    $new_status = $conn->real_escape_string($_POST['new_status']);
    $conn->query("UPDATE trade_match SET status = '$new_status' WHERE match_id = $match_id");
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
  $txn_id = intval($_POST['transaction_id']);
  $new_status = $conn->real_escape_string($_POST['new_status']);
  $conn->query("UPDATE transactions SET status = '$new_status' WHERE transaction_id = $txn_id");
}



// Fetch users
$all_users = $conn->query("SELECT * FROM users ORDER BY user_id ASC");

$all_transactions = $conn->query("
  SELECT
    tm.match_id,
    tm.status AS match_status,
    tm.hash_code,
    
    u1.full_name AS user1_name,
    u2.full_name AS user2_name,
    
    i1.item_name AS user1_item_offered,
    td1.quantity_offered AS user1_qty_offered,
    i2.item_name AS user1_item_desired,
    td1.quantity_desired AS user1_qty_desired,
    
    i3.item_name AS user2_item_offered,
    td2.quantity_offered AS user2_qty_offered,
    i4.item_name AS user2_item_desired,
    td2.quantity_desired AS user2_qty_desired,
    
    GREATEST(p1.created_at, p2.created_at) AS trade_time
    
  FROM trade_match tm
  JOIN posts p1 ON tm.post1_id = p1.post_id
  JOIN posts p2 ON tm.post2_id = p2.post_id
  
  JOIN trade_members tm1 ON tm1.post_id = p1.post_id
  JOIN trade_members tm2 ON tm2.post_id = p2.post_id
  
  JOIN users u1 ON tm1.created_by = u1.user_id
  JOIN users u2 ON tm2.created_by = u2.user_id
  
  JOIN trade_details td1 ON td1.post_id = p1.post_id
  JOIN trade_details td2 ON td2.post_id = p2.post_id
  
  JOIN items i1 ON td1.item_offered = i1.item_id
  JOIN items i2 ON td1.item_desired = i2.item_id
  JOIN items i3 ON td2.item_offered = i3.item_id
  JOIN items i4 ON td2.item_desired = i4.item_id
  
  ORDER BY trade_time DESC
");


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_match_status'])) {
  $match_id = intval($_POST['match_id']);
  $new_status = $conn->real_escape_string($_POST['new_status']);
  $conn->query("UPDATE trade_match SET status = '$new_status' WHERE match_id = $match_id");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
  <style>
    .small-btn {
      padding: 5px 10px;
      font-size: 14px;
      margin-left: 5px;
    }
  </style>
</head>
<body>

  <div class="navbar">
    <div class="navbar-left">
      <div class="logo">BarterBuddies</div>
    </div>
    <div class="navbar-right">
      <a href="approvals.php" class="nav-button">Approvals</a>
      <a href="admin_dashboard.php" class="nav-button active">Admin Panel</a>
      <a href="logout.php" class="nav-button">Log Out</a>
    </div>
  </div>

  <div style="display: flex; gap: 40px; padding: 20px; align-items: flex-start;">

<!-- Transactions Column -->
<div style="flex: 1;">
  <h2>Transactions</h2>
  <table border="1" cellpadding="6" cellspacing="0" style="width: 100%;">
    <tr>
      <th>ID</th>
      <th>User A</th>
      <th>User B</th>
      <th>Item A → B</th>
      <th>Item B → A</th>
      <th>Status</th>
      <th>Date</th>
      <th>Actions</th>
    </tr>
    <?php while ($row = $all_transactions->fetch_assoc()): ?>
    <tr>
      <td><?= $row['match_id'] ?></td>
      <td><?= htmlspecialchars($row['user1_name']) ?></td>
      <td><?= htmlspecialchars($row['user2_name']) ?></td>
      
      <td><?= $row['user1_qty_offered'] ?> × <?= htmlspecialchars($row['user1_item_offered']) ?></td>
      <td><?= $row['user1_qty_desired'] ?> × <?= htmlspecialchars($row['user1_item_desired']) ?></td>
      
      <td><?= $row['user2_qty_offered'] ?> × <?= htmlspecialchars($row['user2_item_offered']) ?></td>
      <td><?= $row['user2_qty_desired'] ?> × <?= htmlspecialchars($row['user2_item_desired']) ?></td>
      
      <td>
        <form method="POST" style="display: flex; align-items: center;">
          <input type="hidden" name="match_id" value="<?= $row['match_id'] ?>">
          <select name="new_status">
            <?php
              $statuses = ['in progress', 'completed', 'cancelled'];
              foreach ($statuses as $status_option) {
                $selected = ($row['match_status'] === $status_option) ? 'selected' : '';
                echo "<option value='$status_option' $selected>$status_option</option>";
              }
            ?>
          </select>
          <button type="submit" name="update_match_status" class="small-btn">Update</button>
        </form>
      </td>
      <td><?= htmlspecialchars($row['trade_time']) ?></td>
    </tr>
  <?php endwhile; ?>

  </table>
</div>



    <!-- Users Column (Dynamic) -->
    <div style="flex: 2;">
      <h2>All Users</h2>
      <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; margin-bottom: 30px;">
        <tr>
          <th>ID</th><th>Name</th><th>Email</th><th>Approved?</th><th>Partner ID</th><th>Suspended?</th><th>Actions</th>
        </tr>
        <?php while ($user = $all_users->fetch_assoc()): ?>
          <tr>
            <td><?= $user['user_id'] ?></td>
            <td><?= htmlspecialchars($user['full_name']) ?></td>
            <td><?= htmlspecialchars($user['email']) ?></td>
            <td><?= $user['status'] === 'approved' ? 'Yes' : 'No' ?></td> <!-- Replaces is_approved -->
            <td><?= $user['partner_id'] ?? 'None' ?></td>
            <td><?= $user['status'] === 'suspended' ? 'Yes' : 'No' ?></td> <!-- Replaces suspended -->
            <td>

            <?php if (!$user['status'] !== 'suspended'): ?>
              <a href="?suspend=<?= $user['user_id'] ?>" class="small-btn">Suspend</a>
            <?php else: ?>
              <a href="?unsuspend=<?= $user['user_id'] ?>" class="small-btn">Unsuspend</a>
            <?php endif; ?>
            <?php if (!$user['is_admin']): ?>
              <a href="?promote=<?= $user['user_id'] ?>" class="small-btn" onclick="return confirm('Promote this user to admin?')">Promote</a>
            <?php else: ?>
              <span style="font-weight: bold;">Admin</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </div>

</body>
</html>
