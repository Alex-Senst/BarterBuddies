<?php
session_start();
require_once 'config.php'; // your DB connection file

if (!isset($_SESSION['user_id']) || !isset($_GET['match_id'])) {
    echo "Unauthorized access.";
    exit;
}

$current_user = $_SESSION['user_id'];
$match_id = intval($_GET['match_id']);

// 1. Get the posts involved in the match
$sql = "
    SELECT tm.match_id, tm.post1_id, tm.post2_id, tm.status AS match_status,
           p1.created_at AS post1_time, p2.created_at AS post2_time,
           tm1.created_by AS post1_creator, tm2.created_by AS post2_creator
    FROM trade_match tm
    JOIN trade_members tm1 ON tm.post1_id = tm1.post_id
    JOIN trade_members tm2 ON tm.post2_id = tm2.post_id
    JOIN posts p1 ON tm.post1_id = p1.post_id
    JOIN posts p2 ON tm.post2_id = p2.post_id
    WHERE tm.match_id = ?
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $match_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows === 0) {
    echo "Match not found.";
    exit;
}
$data = $result->fetch_assoc();

// Determine if user is in this match (either directly or as a partner)
$post_ids = [$data['post1_id'], $data['post2_id']];
$creators = [$data['post1_creator'], $data['post2_creator']];
$user_post_index = -1;

for ($i = 0; $i < 2; $i++) {
    // Check if current user or their partner created the post
    $partner_query = "
        SELECT * FROM partners
        WHERE (user1_id = ? AND user2_id = ?) OR (user2_id = ? AND user1_id = ?)
    ";
    $check_user = $creators[$i];

    $stmt = $conn->prepare($partner_query);
    $stmt->bind_param("iiii", $current_user, $check_user, $current_user, $check_user);
    $stmt->execute();
    $partner_result = $stmt->get_result();

    if ($current_user == $check_user || $partner_result->num_rows > 0) {
        $user_post_index = $i;
        break;
    }
}

if ($user_post_index === -1) {
    echo "You are not a participant in this match.";
    exit;
}

$user_post_id = $post_ids[$user_post_index];
$other_post_id = $post_ids[1 - $user_post_index];
$user_creator = $creators[$user_post_index];
$other_creator = $creators[1 - $user_post_index];

// 2. Get trade details for the user’s post
$details_sql = "
    SELECT td.*, 
           io.item_name AS item_offered_name, 
           id.item_name AS item_desired_name
    FROM trade_details td
    JOIN items io ON td.item_offered = io.item_id
    JOIN items id ON td.item_desired = id.item_id
    WHERE td.post_id = ?
";

$stmt = $conn->prepare($details_sql);
$stmt->bind_param("i", $user_post_id);
$stmt->execute();
$details_result = $stmt->get_result();

if (!$details_result || $details_result->num_rows === 0) {
    echo "Trade details not found.";
    exit;
}

$trade = $details_result->fetch_assoc();

// 3. Get names of sender (the creator of this post) and receiver (the other post’s creator)
$name_sql = "SELECT user_id, full_name FROM users WHERE user_id IN (?, ?)";
$stmt = $conn->prepare($name_sql);
$stmt->bind_param("ii", $user_creator, $other_creator);
$stmt->execute();
$name_result = $stmt->get_result();

$names = [];
while ($row = $name_result->fetch_assoc()) {
    if ($row['user_id'] == $user_creator) {
        $names['sender'] = $row['full_name'];
    } else {
        $names['receiver'] = $row['full_name'];
    }
}

// 4. Calculate 5% fee on quantity desired
$quantity_received = round($trade['quantity_desired'] * 0.95, 2);

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
    <h2>Trade Match #<?php echo htmlspecialchars($match_id); ?> | Post #<?php echo htmlspecialchars($user_post_id); ?></h2>
        <ul>
            <li style="font-size: 17px;">
            <p><strong>Item Offered:</strong> <?php echo htmlspecialchars($trade['item_offered_name']); ?></p>
            <p><strong>Quantity Offered:</strong> <?php echo htmlspecialchars($trade['quantity_offered']); ?></p>
            <p><strong>Item Received:</strong> <?php echo htmlspecialchars($trade['item_desired_name']); ?></p>
            <p><strong>Quantity Received (after 5% fee):</strong> <?php echo htmlspecialchars($quantity_received); ?></p>
            <p><strong>Sender:</strong> <?php echo htmlspecialchars($names['sender']); ?></p>
            <p><strong>Receiver:</strong> <?php echo htmlspecialchars($names['receiver']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($data['match_status']); ?></p>
            <p><strong>Completed At:</strong> <?php echo htmlspecialchars($trade['completed_at']); ?></p>
            </li>
        </ul>
    </div>

</body>
</html>

