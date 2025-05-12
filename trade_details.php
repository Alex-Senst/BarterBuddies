<?php
session_start();
require 'config.php'; // Your DB connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$trade_id = isset($_GET['trade_id']) ? intval($_GET['trade_id']) : 0;

// Fetch trade details, including item names
$query = "SELECT t.trade_id, t.sender_id, t.receiver_id, 
                 t.item_offered, t.quantity_offered, 
                 t.item_desired, t.quantity_desired, 
                 t.status, t.transaction_hash, 
                 u1.full_name AS sender_name, 
                 u2.full_name AS receiver_name, 
                 i1.item_name AS item_offered_name, 
                 i2.item_name AS item_desired_name
          FROM trades t 
          JOIN users u1 ON u1.user_id = t.sender_id
          JOIN users u2 ON u2.user_id = t.receiver_id
          JOIN items i1 ON i1.item_id = t.item_offered
          JOIN items i2 ON i2.item_id = t.item_desired
          WHERE t.trade_id = $trade_id";
$result = $conn->query($query);

if ($result->num_rows == 0) {
    echo "Trade not found.";
    exit();
}

$trade = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>View Trade Details</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="navbar">
        <div class="navbar-left">
          <div class="logo">BarterBuddies</div>
        </div>
        <div class="navbar-right">
          <a href="create_trade.php" class="nav-button">Create Trade</a>
          <a href="dashboard.php" class="nav-button">User Dashboard</a>
          <a href="logout.php" class="nav-button">Log Out</a>
        </div>
    </div>

    <div class="section-header">
        <h2>Trade #<?php echo htmlspecialchars($trade['trade_id']); ?> Details</h2>
        <ul>
            <li style="font-size: 17px;">
                <p><strong>Item Offered:</strong> <?php echo htmlspecialchars($trade['item_offered_name']); ?></p>
                <p><strong>Quantity Offered:</strong> <?php echo htmlspecialchars($trade['quantity_offered']); ?></p>
                <p><strong>Item Desired:</strong> <?php echo htmlspecialchars($trade['item_desired_name']); ?></p>
                <p><strong>Quantity Desired:</strong> <?php echo htmlspecialchars($trade['quantity_desired']); ?></p>
                <p><strong>Sender:</strong> <?php echo htmlspecialchars($trade['sender_name']); ?></p>
                <p><strong>Receiver:</strong> <?php echo htmlspecialchars($trade['receiver_name']); ?></p>
                <p><strong>Status:</strong> <?php echo htmlspecialchars($trade['status']); ?></p>
            </li>
        </ul>
    </div>

</body>
</html>
