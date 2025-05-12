<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$current_user_id = $_SESSION['user_id'];

/* Get current user's info
$stmt = $conn->prepare("SELECT full_name, partner_id FROM users WHERE user_id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$stmt->bind_result($full_name, $partner_id);
$stmt->fetch();
$stmt->close();

if ($partner_id) {
    echo "You already have a partner! <a href='dashboard.php'>Return to Dashboard</a>";
    exit();
}*/

// Get current user's name
$stmt = $conn->prepare("SELECT full_name FROM users WHERE user_id = ?");
$stmt->bind_param("i", $current_user_id);
$stmt->execute();
$stmt->bind_result($full_name);
$stmt->fetch();
$stmt->close();

// Check if user is already in a partnership
$stmt = $conn->prepare("SELECT 1 FROM partners WHERE user1_id = ? OR user2_id = ? LIMIT 1");
$stmt->bind_param("ii", $current_user_id, $current_user_id);
$stmt->execute();
$stmt->store_result();
$has_partner = $stmt->num_rows > 0;
$stmt->close();

if ($has_partner) {
    echo "You already have a partner! <a href='dashboard.php'>Return to Dashboard</a>";
    exit();
}

// If a partner is being selected
/*if (isset($_GET['pair_with'])) {
    $selected_partner_id = intval($_GET['pair_with']);

    // Set current user's partner
    $stmt1 = $conn->prepare("UPDATE users SET partner_id = ? WHERE user_id = ?");
    $stmt1->bind_param("ii", $selected_partner_id, $current_user_id);
    $stmt1->execute();

    // Set selected user's partner
    $stmt2 = $conn->prepare("UPDATE users SET partner_id = ? WHERE user_id = ?");
    $stmt2->bind_param("ii", $current_user_id, $selected_partner_id);
    $stmt2->execute();

    header("Location: dashboard.php");
    exit();
}*/
if (isset($_GET['pair_with'])) {
    $selected_partner_id = intval($_GET['pair_with']);

    // Insert new partnership into partners table
    $stmt = $conn->prepare("INSERT INTO partners (user1_id, user2_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $current_user_id, $selected_partner_id);
    $stmt->execute();
    $stmt->close();

    header("Location: dashboard.php");
    exit();
}

// Get list of users without partners
//$result = $conn->query("SELECT user_id, full_name FROM users WHERE partner_id IS NULL AND user_id != $current_user_id AND (is_admin IS NULL OR is_admin = 0) AND is_approved = 1");
$result = $conn->query("
    SELECT u.user_id, u.full_name
    FROM users u
    LEFT JOIN partners p1 ON u.user_id = p1.user1_id
    LEFT JOIN partners p2 ON u.user_id = p2.user2_id
    WHERE p1.user1_id IS NULL
    AND p2.user2_id IS NULL
    AND u.user_id != $current_user_id
    AND (u.is_admin IS NULL OR u.is_admin = 0)
    AND u.status = 'approved'

");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Find Partner</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="navbar">
        <div class="navbar-left">
          <div class="logo">BarterBuddies</div>
        </div>
        <div class="navbar-right">
          <a href="find_partner.php" class="nav-button active">Find Partner</a>
          <a href="dashboard.php" class="nav-button">User Dashboard</a>
          <a href="logout.php" class="nav-button">Log Out</a>
        </div>
    </div>

    <div class="section-header">
        <h2>Potential Partners</h2>
        <?php if ($result->num_rows > 0): ?>
            <ul>
            <?php while ($row = $result->fetch_assoc()): ?>
                <li style="font-size: 17px;">
                    <span>User ID: <?php echo htmlspecialchars($row['user_id']); ?></span>
                    <a href="?pair_with=<?php echo $row['user_id']; ?>" class="small-btn">Pair Up</a>
                </li>
            <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No users available to pair with right now. Please check back later.</p>
        <?php endif; ?>
    </div>

</body>
</html>
