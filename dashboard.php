<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    if (isset($_SESSION['flash_message'])) {
        echo "<div style='padding: 10px; background-color: #ddffdd; border: 1px solid green; color: green; margin-bottom: 15px; border-radius: 5px; font-weight: bold;'>";
        echo $_SESSION['flash_message'];
        echo "</div>";
        unset($_SESSION['flash_message']);
    }
    header("Location: login.php");
    exit();
}

require_once 'config.php';

$user_id = $_SESSION['user_id'];

// Get user info
/*$stmt = $conn->prepare("SELECT full_name, partner_id FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $partner_id);
$stmt->fetch();
$stmt->close();*/
$stmt = $conn->prepare("
    SELECT u.full_name, p.user1_id, p.user2_id
    FROM users u
    LEFT JOIN partners p ON u.user_id = p.user1_id OR u.user_id = p.user2_id
    WHERE u.user_id = ?
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($full_name, $user1_id, $user2_id);
$stmt->fetch();
$stmt->close();

$has_partner = ($user1_id || $user2_id);


// Load trades by status
/*$current_posted = $conn->query("
    SELECT 
        t.trade_id,
        t.quantity_offered,
        t.quantity_desired,
        io.item_name AS offered_item_name,
        id.item_name AS desired_item_name
    FROM trades t
    JOIN items io ON t.item_offered = io.item_id
    JOIN items id ON t.item_desired = id.item_id
    WHERE t.sender_id = $user_id AND t.status = 'open'
");*/
/*$current_posted = $conn->prepare("
    SELECT 
        p.post_id,
        io.item_name AS offered_item_name,
        id.item_name AS desired_item_name,
        td.quantity_offered,
        td.quantity_desired
    FROM posts p
    JOIN trade_details td ON p.post_id = td.post_id
    JOIN items io ON td.item_offered = io.item_id
    JOIN items id ON td.item_desired = id.item_id
    WHERE td.created_by = ? AND p.status = 'open'
");
$current_posted->bind_param("i", $user_id);*/
$sql = "
    SELECT p.post_id,
           p.status,
           td.item_offered,
           td.item_desired,
           tm.created_by
    FROM posts AS p
    JOIN trade_details AS td ON td.post_id = p.post_id
    JOIN trade_members AS tm ON tm.post_id = p.post_id
    LEFT JOIN partners AS pt ON 
        (pt.user1_id = tm.created_by OR pt.user2_id = tm.created_by)
    WHERE p.status = 'open'
      AND (
            tm.created_by = ?
         OR (pt.user1_id = ? AND pt.user2_id = tm.created_by)
         OR (pt.user2_id = ? AND pt.user1_id = tm.created_by)
      )
    ORDER BY p.created_at DESC
";

$current_posted = $conn->prepare($sql);
$current_posted->bind_param('iii', $user_id, $user_id, $user_id);
$current_posted->execute();
$result = $current_posted->get_result();

// Query to find matched trades (both user's own matches and their partner's)
$matched = $conn->prepare("
    SELECT tm.match_id, tm.post1_id, tm.post2_id
    FROM trade_match tm
    JOIN trade_members tm1 ON tm.post1_id = tm1.post_id
    JOIN trade_members tm2 ON tm.post2_id = tm2.post_id
    LEFT JOIN partners p1 
      ON (p1.user1_id = tm1.created_by OR p1.user2_id = tm1.created_by)
    LEFT JOIN partners p2 
      ON (p2.user1_id = tm2.created_by OR p2.user2_id = tm2.created_by)
    WHERE(
      (
        tm1.created_by = ? 
        OR (p1.user1_id = ? AND p1.user2_id = tm1.created_by)
        OR (p1.user2_id = ? AND p1.user1_id = tm1.created_by)
      )
    OR (
        tm2.created_by = ? 
        OR (p2.user1_id = ? AND p2.user2_id = tm2.created_by)
        OR (p2.user2_id = ? AND p2.user1_id = tm2.created_by)
      )
        )
    AND tm.status = 'in progress'
");
$matched->bind_param("iiiiii", $user_id, $user_id, $user_id, $user_id, $user_id, $user_id);  // Include both the user's ID and partner's IDs
$matched->execute();
$matched_result = $matched->get_result();


//$completed = $conn->query("SELECT * FROM trades WHERE (sender_id = $user_id OR receiver_id = $user_id) AND status = 'completed'");
$completed = $conn->prepare("
    SELECT tm.match_id, tm.post1_id, tm.post2_id
    FROM trade_match tm
    JOIN trade_members tm1 ON tm.post1_id = tm1.post_id
    JOIN trade_members tm2 ON tm.post2_id = tm2.post_id
    LEFT JOIN partners p1 
      ON (p1.user1_id = tm1.created_by OR p1.user2_id = tm1.created_by)
    LEFT JOIN partners p2 
      ON (p2.user1_id = tm2.created_by OR p2.user2_id = tm2.created_by)
    WHERE(
      (
        tm1.created_by = ? 
        OR (p1.user1_id = ? AND p1.user2_id = tm1.created_by)
        OR (p1.user2_id = ? AND p1.user1_id = tm1.created_by)
      )
    OR (
        tm2.created_by = ? 
        OR (p2.user1_id = ? AND p2.user2_id = tm2.created_by)
        OR (p2.user2_id = ? AND p2.user1_id = tm2.created_by)
      )
        )
    AND tm.status = 'completed'
");
$completed->bind_param("iiiiii", $user_id, $user_id, $user_id, $user_id, $user_id, $user_id);
$completed->execute();
$completed_result = $completed->get_result();

// Dummy check for action-needed trades
//$action_needed = $conn->query("SELECT * FROM trades WHERE (sender_id = $user_id OR receiver_id = $user_id) AND status = 'matched' AND action_required_by = $user_id");
$action_needed = $conn->prepare("
    SELECT tc.match_id
    FROM trade_confirmation tc
    WHERE tc.user_id = ? AND (tc.submitted_hash = 0 OR tc.submitted_item = 0)
");
$action_needed->bind_param("i", $user_id);
$action_needed->execute();
$action_result = $action_needed->get_result();

//$has_partner = !empty($partner_id);
// Example: Check if this user has any matched partner posts
//$has_partner = false;

//$query = "SELECT 1 FROM posts WHERE created_by = ? AND matched_user_id IS NOT NULL LIMIT 1";
$query = "
  SELECT 1
  FROM trade_match tm
  JOIN trade_members tmem ON tm.post1_id = tmem.post_id OR tm.post2_id = tmem.post_id
  WHERE tmem.created_by = ?
  LIMIT 1
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id); // assume $user_id is the logged-in user's ID
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    $has_partner = true;
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarterBuddies Dashboard</title>
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="navbar">
        <div class="navbar-left">
          <div class="logo">BarterBuddies</div>
        </div>
        <div class="navbar-right">
            <?php if ($has_partner): ?>
                <a href="create_trade.php" class="nav-button">Create Trade</a>
            <?php else: ?>
                <a href="find_partner.php" class="nav-button">Find Partner</a>
            <?php endif; ?>
            <a href="dashboard.php" class="nav-button active">User Dashboard</a>
            <a href="logout.php" class="nav-button">Log Out</a>
        </div>

    </div>
    
    <div class="title">Welcome, <?= htmlspecialchars($full_name) ?>!</div>

    <div class="section-header">
        <h2>Current Posted Trades</h2>
        <?php if ($result->num_rows == 0): ?>
            <p>No posted trades.</p>
        <?php else: ?>
            <div class="trade-card">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <div class="trade-row">
                        <div class="trade-left">
                            <span class="trade-name">
                                Trade: <?= htmlspecialchars($row['item_offered']) ?> for <?= htmlspecialchars($row['item_desired']) ?>
                            </span>
                            <a href="trade_details.php?post_id=<?= $row['post_id'] ?>" class="small-btn">View Details</a>
                            <button class="small-btn">Edit</button>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>

    </div>
    
    <div class="section-header">
        <h2>In-Progress Trades</h2>
        <?php if ($matched_result->num_rows == 0): ?>
            <p>No trades in progress.</p>
        <?php else: ?>
            <div class="trade-card">
                <?php while ($row = $matched_result->fetch_assoc()): ?>
                    <div class="trade-info">
                        <span class="trade-name">Trade #<?= $row['match_id'] ?> in progress</span>
                        <a href="in_progress.php?match_id=<?= $row['match_id'] ?>" class="small-btn">Actions</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="section-header">
        <h2>Completed Trades</h2>
        <?php if ($completed_result->num_rows == 0): ?>
            <p>No completed trades.</p>
        <?php else: ?>
            <div class="trade-card">
                <?php while ($row = $completed_result->fetch_assoc()): ?>
                    <div class="trade-info">
                        <span class="trade-name">Completed Trade ID: <?= htmlspecialchars($row['match_id']) ?></span>
                        <a href="trade_details.php?match_id=<?= $row['match_id'] ?>" class="small-btn">View Details</a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
