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
} elseif (isset($_GET['delete_txn'])) {
  $txn_id = intval($_GET['delete_txn']);
  $conn->query("DELETE FROM transactions WHERE transaction_id = $txn_id");
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
  $txn_id = intval($_POST['transaction_id']);
  $new_status = $conn->real_escape_string($_POST['new_status']);
  $conn->query("UPDATE transactions SET status = '$new_status' WHERE transaction_id = $txn_id");
}



// Fetch users
$all_users = $conn->query("SELECT * FROM users ORDER BY user_id ASC");

$all_transactions = $conn->query("
    SELECT 
        t.transaction_id,
        t.user_a_id,
        t.user_b_id,
        ua.full_name AS user_a_name,
        ub.full_name AS user_b_name,
        t.item_from_a,
        t.item_from_b,
        t.qty_from_a,
        t.qty_from_b,
        t.status,
        t.created_at
    FROM transactions t
    JOIN users ua ON t.user_a_id = ua.user_id
    JOIN users ub ON t.user_b_id = ub.user_id
    ORDER BY t.created_at DESC
");
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
    <?php while ($txn = $all_transactions->fetch_assoc()): ?>
    <tr>
      <td><?= $txn['transaction_id'] ?></td>
      <td><?= htmlspecialchars($txn['user_a_name']) ?></td>
      <td><?= htmlspecialchars($txn['user_b_name']) ?></td>
      <td><?= htmlspecialchars($txn['qty_from_a']) ?> × <?= htmlspecialchars($txn['item_from_a']) ?></td>
      <td><?= htmlspecialchars($txn['qty_from_b']) ?> × <?= htmlspecialchars($txn['item_from_b']) ?></td>
      <td>
      <form method="POST" style="display: flex; align-items: center;">
        <input type="hidden" name="transaction_id" value="<?= $txn['transaction_id'] ?>">
        <select name="new_status">
          <?php
            $statuses = ['pending', 'awaiting-confirmation', 'completed', 'failed'];
            foreach ($statuses as $status_option) {
              $selected = ($txn['status'] === $status_option) ? 'selected' : '';
              echo "<option value='$status_option' $selected>$status_option</option>";
            }
          ?>
        </select>
        <button type="submit" name="update_status" class="small-btn">Update</button>
      </form>
    </td>

      <td><?= htmlspecialchars($txn['created_at']) ?></td>
      <td>
        <a href="?delete_txn=<?= $txn['transaction_id'] ?>" onclick="return confirm('Delete this transaction?')">Delete</a>
        |
        <a href="edit_transaction.php?id=<?= $txn['transaction_id'] ?>">Edit</a>
      </td>
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
