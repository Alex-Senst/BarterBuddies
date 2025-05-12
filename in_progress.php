<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$match_id = isset($_GET['match_id']) ? intval($_GET['match_id']) : 0;

// Assuming the current user is stored in the session as 'user_id'
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;  // replace with your session logic

// Fetch trade match details
$query = "SELECT 
            tm.match_id, 
            tm.hash_user1, 
            tm.hash_user2, 
            tm.status,
            tc.sent_hash,
            tc.submitted_item,
            tc.submitted_hash,
            tc.user_id AS tc_user_id,
            tm.post1_id,
            tm.post2_id,
            u1.full_name AS user1_name,
            u2.full_name AS user2_name,
            m1.poster_has_items AS poster1_has_items,
            m2.poster_has_items AS poster2_has_items
          FROM trade_match tm
          JOIN posts p1 ON p1.post_id = tm.post1_id
          JOIN posts p2 ON p2.post_id = tm.post2_id
          JOIN trade_members m1 ON m1.post_id = p1.post_id
          JOIN trade_members m2 ON m2.post_id = p2.post_id
          JOIN users u1 ON u1.user_id = m1.created_by
          JOIN users u2 ON u2.user_id = m2.created_by
          JOIN trade_confirmation tc ON tc.match_id = tm.match_id
          WHERE tm.match_id = ? AND tm.status = 'in progress'
          AND (tc.user_id = ?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $match_id, $current_user_id);
$stmt->execute();
$result = $stmt->get_result();



// Check if the match is found
if ($result->num_rows == 0) {
    echo "No active match found or match is not in progress.";
    exit();
}

$row = $result->fetch_assoc();
//var_dump($row);  // This will show the entire array returned by fetch_assoc
//Store poster_has_items values
$poster1_has_items = $row['poster1_has_items'];
$poster2_has_items = $row['poster2_has_items'];


// Get creators of post1 and post2
$query_get_creators = "
    SELECT post_id, created_by
    FROM trade_members
    WHERE post_id IN (?, ?)
";

$stmt_get_creators = $conn->prepare($query_get_creators);
$stmt_get_creators->bind_param("ii", $row['post1_id'], $row['post2_id']);
$stmt_get_creators->execute();
$result_creators = $stmt_get_creators->get_result();

$creators = [];
while ($r = $result_creators->fetch_assoc()) {
    $creators[$r['post_id']] = $r['created_by'];
}

$post1_creator = $creators[$row['post1_id']] ?? null;
$post2_creator = $creators[$row['post2_id']] ?? null;

// Now check if current user is a partner of either creator
$query_check_partners = "
    SELECT user1_id, user2_id
    FROM partners
    WHERE 
        (user1_id = ? AND user2_id IN (?, ?)) OR
        (user2_id = ? AND user1_id IN (?, ?))
    LIMIT 1
";

$stmt_check = $conn->prepare($query_check_partners);
$stmt_check->bind_param(
    "iiiiii",
    $current_user_id,
    $post1_creator,
    $post2_creator,
    $current_user_id,
    $post1_creator,
    $post2_creator
);

$stmt_check->execute();
$check_result = $stmt_check->get_result();

$partner_to_post1 = null;
$partner_to_post2 = null;

while ($r = $check_result->fetch_assoc()) {
    // Check if the current user is a partner of post1 or post2
    if ($r['user1_id'] == $post1_creator) {
        $partner_to_post1 = $r['user2_id'];
    } elseif ($r['user2_id'] == $post1_creator) {
        $partner_to_post1 = $r['user1_id'];
    }

    if ($r['user1_id'] == $post2_creator) {
        $partner_to_post2 = $r['user2_id'];
    } elseif ($r['user2_id'] == $post2_creator) {
        $partner_to_post2 = $r['user1_id'];
    }
}
var_dump($partner_to_post1);
var_dump($partner_to_post2);

// Also check if user is directly the creator
$is_creator = ($current_user_id == $post1_creator || $current_user_id == $post2_creator);

if ($is_creator || $check_result->num_rows > 0) {
} else {
    echo "You are not a participant in this match.";
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_hash'])) {
  $match_id = $row['match_id'];
  $current_user_id = $_SESSION['user_id'];

  // Step 1: Update current user's sent_hash
  $stmt = $conn->prepare("UPDATE trade_confirmation SET sent_hash = 1 WHERE match_id = ? AND user_id = ?");
  $stmt->bind_param("ii", $match_id, $current_user_id);
  $stmt->execute();
  $stmt->close();

  // Step 2: Get partner's user_id from partners table
  $stmt = $conn->prepare("
      SELECT 
        CASE 
          WHEN user1_id = ? THEN user2_id 
          WHEN user2_id = ? THEN user1_id 
        END AS partner_id
      FROM partners
      WHERE (user1_id = ? OR user2_id = ?)
      LIMIT 1
  ");
  $stmt->bind_param("iiii", $current_user_id, $current_user_id, $current_user_id, $current_user_id);
  $stmt->execute();
  $stmt->bind_result($partner_id);
  $stmt->fetch();
  $stmt->close();

  if (!empty($partner_id)) {
    // Step 3: Update partner's sent_hash
    $stmt = $conn->prepare("UPDATE trade_confirmation SET sent_hash = 1 WHERE match_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $match_id, $partner_id);
    $stmt->execute();
    $stmt->close();
  }


  $stmt = $conn->prepare("SELECT 
            tm.match_id, 
            tm.hash_user1, 
            tm.hash_user2, 
            tm.status,
            tc.sent_hash,
            tc.submitted_item,
            tc.submitted_hash,
            tc.user_id AS tc_user_id,
            tm.post1_id,
            tm.post2_id,
            u1.full_name AS user1_name,
            u2.full_name AS user2_name,
            m1.poster_has_items AS poster1_has_items,
            m2.poster_has_items AS poster2_has_items
          FROM trade_match tm
          JOIN posts p1 ON p1.post_id = tm.post1_id
          JOIN posts p2 ON p2.post_id = tm.post2_id
          JOIN trade_members m1 ON m1.post_id = p1.post_id
          JOIN trade_members m2 ON m2.post_id = p2.post_id
          JOIN users u1 ON u1.user_id = m1.created_by
          JOIN users u2 ON u2.user_id = m2.created_by
          JOIN trade_confirmation tc ON tc.match_id = tm.match_id
          WHERE tm.match_id = ? AND tm.status = 'in progress'
          AND (tc.user_id = ?)"
  );
  $stmt->bind_param("ii", $match_id, $current_user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_item'])) {
  $match_id = $row['match_id'];
  $current_user_id = $_SESSION['user_id'];

  // Step 1: Update current user's sent_hash
  $stmt = $conn->prepare("UPDATE trade_confirmation SET submitted_item = 1 WHERE match_id = ? AND user_id = ?");
  $stmt->bind_param("ii", $match_id, $current_user_id);
  $stmt->execute();
  $stmt->close();

  // Step 2: Get partner's user_id from partners table
  $stmt = $conn->prepare("
      SELECT 
        CASE 
          WHEN user1_id = ? THEN user2_id 
          WHEN user2_id = ? THEN user1_id 
        END AS partner_id
      FROM partners
      WHERE (user1_id = ? OR user2_id = ?)
      LIMIT 1
  ");
  $stmt->bind_param("iiii", $current_user_id, $current_user_id, $current_user_id, $current_user_id);
  $stmt->execute();
  $stmt->bind_result($partner_id);
  $stmt->fetch();
  $stmt->close();

  if (!empty($partner_id)) {
    // Step 3: Update partner's submitted_item
    $stmt = $conn->prepare("UPDATE trade_confirmation SET submitted_item = 1 WHERE match_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $match_id, $partner_id);
    $stmt->execute();
    $stmt->close();
  }

  $stmt = $conn->prepare("SELECT 
            tm.match_id, 
            tm.hash_user1, 
            tm.hash_user2, 
            tm.status,
            tc.sent_hash,
            tc.submitted_item,
            tc.submitted_hash,
            tc.user_id AS tc_user_id,
            tm.post1_id,
            tm.post2_id,
            u1.full_name AS user1_name,
            u2.full_name AS user2_name,
            m1.poster_has_items AS poster1_has_items,
            m2.poster_has_items AS poster2_has_items
          FROM trade_match tm
          JOIN posts p1 ON p1.post_id = tm.post1_id
          JOIN posts p2 ON p2.post_id = tm.post2_id
          JOIN trade_members m1 ON m1.post_id = p1.post_id
          JOIN trade_members m2 ON m2.post_id = p2.post_id
          JOIN users u1 ON u1.user_id = m1.created_by
          JOIN users u2 ON u2.user_id = m2.created_by
          JOIN trade_confirmation tc ON tc.match_id = tm.match_id
          WHERE tm.match_id = ? AND tm.status = 'in progress'
          AND (tc.user_id = ?)"
  );
  $stmt->bind_param("ii", $match_id, $current_user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_hash'])) {
  $match_id = $row['match_id'];
  $current_user_id = $_SESSION['user_id'];

  // Step 1: Update current user's submitted_hash
  $stmt = $conn->prepare("UPDATE trade_confirmation SET submitted_hash = 1 WHERE match_id = ? AND user_id = ?");
  $stmt->bind_param("ii", $match_id, $current_user_id);
  $stmt->execute();
  $stmt->close();

  $stmt = $conn->prepare("SELECT 
            tm.match_id, 
            tm.hash_user1, 
            tm.hash_user2, 
            tm.status,
            tc.sent_hash,
            tc.submitted_item,
            tc.submitted_hash,
            tc.user_id AS tc_user_id,
            tm.post1_id,
            tm.post2_id,
            u1.full_name AS user1_name,
            u2.full_name AS user2_name,
            m1.poster_has_items AS poster1_has_items,
            m2.poster_has_items AS poster2_has_items
          FROM trade_match tm
          JOIN posts p1 ON p1.post_id = tm.post1_id
          JOIN posts p2 ON p2.post_id = tm.post2_id
          JOIN trade_members m1 ON m1.post_id = p1.post_id
          JOIN trade_members m2 ON m2.post_id = p2.post_id
          JOIN users u1 ON u1.user_id = m1.created_by
          JOIN users u2 ON u2.user_id = m2.created_by
          JOIN trade_confirmation tc ON tc.match_id = tm.match_id
          WHERE tm.match_id = ? AND tm.status = 'in progress'
          AND (tc.user_id = ?)"
  );
  $stmt->bind_param("ii", $match_id, $current_user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
}




?>
<!DOCTYPE html>
<html>
<head>
    <title>Trade In Progress</title>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="styles.css">
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
</head>
<body>
<div class="navbar">
  <div class="navbar-left">
    <div class="logo">BarterBuddies</div>
  </div>
  <div class="navbar-right">
    <a href="dashboard.php" class="nav-button">User Dashboard</a>
    <a href="logout.php" class="nav-button">Log Out</a>
  </div>
</div>

<div class="section-header">
  <h2>In Progress Trade</h2>
</div>

<div class="trade-container" style="padding: 20px;">
  <h3>Match ID: <?= $row['match_id'] ?></h3>

  <form method="post">
    <?php
    if($is_creator){
      if($current_user_id == $post1_creator){
        if($row['submitted_hash'] != 1){
          echo '<p>Your half of the code:</p>';
          echo htmlspecialchars($row['hash_user1']);
          echo '<button type="submit" class="send-btn" name="submit_hash">Submit Your Hash</button>';
        }
        if ($row['sent_hash'] != 1) {
          echo "<p>Give this to your partner.</p>";
          echo '<button type="submit" class="send-btn" name="send_hash">Send hash to your partner</button>';
        }
        if($poster1_has_items == 1 && $row['submitted_item'] != 1){
           echo '<button type="submit" class="send-btn" name="send_item">Send your item</button>';      
        }
        if($row['submitted_item'] == 1 && $row['submitted_hash'] == 1 && $row['sent_hash'] == 1){
          echo 'Your work is done! We are now waiting for others in your trade to complete their necessary tasks';
        }
      }
      elseif($current_user_id == $post2_creator){
        if($row['submitted_hash'] != 1){
          echo '<p>Your half of the code:</p>';
          echo htmlspecialchars($row['hash_user2']);
          echo '<button type="submit" class="send-btn" name="submit_hash">Submit Your Hash</button>';
        }
        if ($row['sent_hash'] != 1) {
          echo "<p>Give this to your partner.</p>";
          echo '<button type="submit" class="send-btn" name="send_hash">Send hash to your partner</button>';
        }
        if($poster2_has_items == 1 && $row['submitted_item'] != 1){
          echo '<button type="submit" class="send-btn" name="send_item">Send your item</button>';
        }
        if($row['submitted_item'] == 1 && $row['submitted_hash'] == 1 && $row['sent_hash'] == 1){
          echo 'Your work is done! We are now waiting for others in your trade to complete their necessary tasks';
        }
      }
    }
    elseif($current_user_id == $partner_to_post1 && $row['sent_hash'] == 1){
      if($row['submitted_hash'] != 1){
        echo '<p>Your half of the code:</p>';
        echo htmlspecialchars($row['hash_user1']);
        echo '<button type="submit" class="send-btn" name="submit_hash">Submit Your Hash</button>';
      }
      if($poster1_has_items == 0 && $row['submitted_item'] != 1){
        echo '<button type="submit" class="send-btn" name="send_item">Send your item</button>';
      }
      if($row['submitted_item'] == 1 && $row['submitted_hash'] == 1 && $row['sent_hash'] == 1){
        echo 'Your work is done! We are now waiting for others in your trade to complete their necessary tasks';
      }
    }
    elseif($current_user_id == $partner_to_post1 && $row['sent_hash'] != 1){
      echo '<p>Your partner has not yet sent you the hash for your transaction.';
    }
    elseif ($current_user_id == $partner_to_post2 && $row['sent_hash'] == 1){
      if($row['submitted_hash'] != 1){
        echo '<p>Your half of the code: ' . htmlspecialchars($row['hash_user2']) . '</p>';
        echo '<button type="submit" class="send-btn" name="submit_hash">Submit Your Hash</button>';
      }
      if($poster2_has_items == 0 && $row['submitted_item'] != 1){
        echo '<button type="submit" class="send-btn" name="send_item">Send your item</button>';
      }
      if($row['submitted_item'] == 1 && $row['submitted_hash'] == 1 && $row['sent_hash'] == 1){
        echo 'Your work is done! We are now waiting for others in your trade to complete their necessary tasks';
      }
    }
    elseif($current_user_id == $partner_to_post2 && $row['sent_hash'] != 1){
      echo '<p>Your partner has not yet sent you the hash for your transaction.';
    }
    ?>

<?php
    // Fetch the status of all users in the match
    $query_check_all_status = "
        SELECT user_id, sent_hash, submitted_item, submitted_hash
        FROM trade_confirmation
        WHERE match_id = ?
    ";

    $stmt_check_all_status = $conn->prepare($query_check_all_status);
    $stmt_check_all_status->bind_param("i", $match_id);
    $stmt_check_all_status->execute();
    $result_all_status = $stmt_check_all_status->get_result();

    // Initialize an array to store the status for each user
    $status_complete = [
        'sent_hash' => true,
        'submitted_item' => true,
        'submitted_hash' => true
    ];

    // Loop through the results and check the status for all users
    while ($row_status = $result_all_status->fetch_assoc()) {
        if ($row_status['sent_hash'] == 0) {
            $status_complete['sent_hash'] = false;
        }
        if ($row_status['submitted_item'] == 0) {
            $status_complete['submitted_item'] = false;
        }
        if ($row_status['submitted_hash'] == 0) {
            $status_complete['submitted_hash'] = false;
        }
    }

    // Check if all users have completed the required actions
    if ($status_complete['sent_hash'] && $status_complete['submitted_item'] && $status_complete['submitted_hash']) {
      echo "All users have completed the trade actions.";
  
      // Update the status of the trade in both tables to 'completed'
      $query_update_status = "
          UPDATE posts
          SET status = 'completed'
          WHERE match_id = ?
      ";
      $stmt_update_status = $conn->prepare($query_update_status);
      $stmt_update_status->bind_param("i", $match_id);
      $stmt_update_status->execute();
  
      $query_update_trade_match = "
          UPDATE trade_match
          SET status = 'completed'
          WHERE match_id = ?
      ";
      $stmt_update_trade_match = $conn->prepare($query_update_trade_match);
      $stmt_update_trade_match->bind_param("i", $match_id);
      $stmt_update_trade_match->execute();
    } else {
        echo '<p>Not all users have completed the required actions. Please check their status.</p>';
    }
    ?>
  </form>

  <br><a href="dashboard.php" class="nav-button">Back to Dashboard</a>
</div>
</body>
</html>