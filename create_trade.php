<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'config.php';

$user_id = $_SESSION['user_id'];

// Fetch all available items
$item_sql = "SELECT item_id, item_name FROM items ORDER BY item_name ASC";
$item_result = $conn->query($item_sql);

// Check if user has a partner
/*$user_sql = "SELECT partner_id FROM users WHERE user_id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($partner_id);
$stmt->fetch();
$stmt->close();

if (!$partner_id) {
    echo "<p>You must choose a partner before creating trades.</p>";
    echo '<a href="find_partner.php">Find a Partner</a>';
    exit();
}*/

// Check if user has a partner (check the partners table)
$partner_sql = "SELECT user1_id, user2_id FROM partners WHERE user1_id = ? OR user2_id = ?";
$stmt = $conn->prepare($partner_sql);
$stmt->bind_param("ii", $user_id, $user_id);  // Check for either user1_id or user2_id
$stmt->execute();
$stmt->store_result();

// If no partner is found (i.e., no rows returned), prompt the user to find a partner
if ($stmt->num_rows == 0) {
    echo "<p>You must choose a partner before creating trades.</p>";
    echo '<a href="find_partner.php">Find a Partner</a>';
    exit();
}

$stmt->close();


// Handle form submission
/*if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_offered = $_POST['item_offered'];
    $quantity_offered = $_POST['quantity_offered'];
    $item_desired = $_POST['item_desired'];
    $quantity_desired = $_POST['quantity_desired'];
    $who_has = $_POST['who_has']; // 'me' or 'partner'

    // Insert trade into database
    $insert_sql = "INSERT INTO trades (sender_id, receiver_id, item_offered, quantity_offered, item_desired, quantity_desired, offered_by)
                   VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("iiisiis", $user_id, $partner_id, $item_offered, $quantity_offered, $item_desired, $quantity_desired, $who_has);

    if ($stmt->execute()) {
        $new_trade_id = $stmt->insert_id;

        // Check for a matching open trade (from anyone, excluding current user's new trade)
        $match_sql = "SELECT trade_id FROM trades 
            WHERE item_offered = ? 
            AND quantity_offered = ? 
            AND item_desired = ? 
            AND quantity_desired = ? 
            AND status = 'open'
            AND trade_id != ?"; // don't match your own trade

        $match_stmt = $conn->prepare($match_sql);
        $match_stmt->bind_param("iiiii", 
            $item_desired,        // they offer what you want
            $quantity_desired,    
            $item_offered,        // they want what you're offering
            $quantity_offered,
            $new_trade_id         // avoid matching your own trade
        );
        $match_stmt->execute();
        $match_stmt->store_result();
        $match_stmt->bind_result($matching_trade_id);

        if ($match_stmt->fetch()) {
            // Generate a 16-character random hash key
            $hash_key = bin2hex(random_bytes(8)); // 16 hex digits
            $hash_a = substr($hash_key, 0, 8);
            $hash_b = substr($hash_key, 8, 8);
        
            // Update both trades to "matched" and assign half of the hash
            $update_sql = "UPDATE trades 
                           SET status = 'matched', transaction_hash = CASE 
                               WHEN trade_id = ? THEN ? 
                               WHEN trade_id = ? THEN ? 
                           END
                           WHERE trade_id IN (?, ?)";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("isisii", 
                $new_trade_id, $hash_a, 
                $matching_trade_id, $hash_b,
                $new_trade_id, $matching_trade_id
            );
            $update_stmt->execute();
            $update_stmt->close();
            */
// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $item_offered = $_POST['item_offered'];
    $quantity_offered = $_POST['quantity_offered'];
    $item_desired = $_POST['item_desired'];
    $quantity_desired = $_POST['quantity_desired'];
    $who_has = $_POST['who_has']; // 'me' or 'partner'

    // Step 1: Insert trade post into the posts table
    $post_sql = "INSERT INTO posts (status, created_at) VALUES ('open', NOW())";
    $stmt = $conn->prepare($post_sql);
    $stmt->execute();
    $post_id = $stmt->insert_id;
    $stmt->close();

    // Step 2: Insert trade member into trade_members table
    // Assuming user_id is the creator of the post
    //$poster_has_items = ($who_has == 'me') ? 1 : 0;
    $poster_has_items = isset($_POST['who_has']) && $_POST['who_has'] === 'user' ? 1 : 0;
    $member_sql = "INSERT INTO trade_members (post_id, created_by, poster_has_items) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($member_sql);
    $stmt->bind_param("iii", $post_id, $user_id, $poster_has_items);
    $stmt->execute();
    $stmt->close();

    // Step 3: Insert trade details into the trade_details table
    $details_sql = "INSERT INTO trade_details (post_id, item_offered, item_desired, quantity_offered, quantity_desired) 
                    VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($details_sql);
    $stmt->bind_param("iiiii", $post_id, $item_offered, $item_desired, $quantity_offered, $quantity_desired);
    $stmt->execute();
    $stmt->close();

    // Step 4: Check for a matching trade post
    $match_sql = "SELECT p.post_id 
        FROM posts p
        JOIN trade_details d ON p.post_id = d.post_id
        WHERE d.item_offered = ? 
        AND d.quantity_offered = ? 
        AND d.item_desired = ? 
        AND d.quantity_desired = ? 
        AND p.status = 'open' 
        AND p.post_id != ?";

    $match_stmt = $conn->prepare($match_sql);
    $match_stmt->bind_param("iiiii", 
        $item_desired,        // they offer what you want
        $quantity_desired,    
        $item_offered,        // they want what you're offering
        $quantity_offered,
        $post_id              // avoid matching your own trade
    );
    $match_stmt->execute();
    $match_stmt->store_result();
    $match_stmt->bind_result($matching_post_id);

    if ($match_stmt->fetch()) {
        // Step 5: Generate a 16-character random hash key
        $hash_key = bin2hex(random_bytes(8)); // 16 hex digits
        $hash_a = substr($hash_key, 0, 8);
        $hash_b = substr($hash_key, 8, 8);

        // Step 6: Insert trade match into the trade_match table
        $match_sql = "INSERT INTO trade_match (hash_code, hash_user1, hash_user2, status, post1_id, post2_id)
                      VALUES (?, ?, ?, 'in progress', ?, ?)";
        $match_stmt = $conn->prepare($match_sql);
        $match_stmt->bind_param("sssii", $hash_key, $hash_a, $hash_b, $post_id, $matching_post_id);
        $match_stmt->execute();
        $match_id = $match_stmt->insert_id;
        $match_stmt->close();

        // Step 7: Update both trade posts to matched status
        $update_sql = "UPDATE posts SET status = 'matched' WHERE post_id IN (?, ?)";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ii", $post_id, $matching_post_id);
        $update_stmt->execute();
        $update_stmt->close();

        // Step 8: Insert trade confirmations (if applicable) in the trade_confirmation table
// Step 0: Get post1_id and post2_id from the match_id
$get_posts_stmt = $conn->prepare("SELECT post1_id, post2_id FROM trade_match WHERE match_id = ?");
$get_posts_stmt->bind_param("i", $match_id);
$get_posts_stmt->execute();
$get_posts_stmt->bind_result($post1_id, $post2_id);
$get_posts_stmt->fetch();
$get_posts_stmt->close();

// Proceed only if both post IDs were successfully fetched
if ($post1_id && $post2_id) {

    // Helper function to get created_by user from a post
    function getUserFromPost($conn, $post_id) {
        $stmt = $conn->prepare("SELECT created_by FROM trade_members WHERE post_id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();
        return $user_id;
    }

    // Helper function to get partner of a user
    function getPartner($conn, $user_id) {
        $stmt = $conn->prepare("
            SELECT CASE 
                WHEN user1_id = ? THEN user2_id 
                ELSE user1_id 
            END AS partner_id
            FROM partners
            WHERE user1_id = ? OR user2_id = ?");
        $stmt->bind_param("iii", $user_id, $user_id, $user_id);
        $stmt->execute();
        $stmt->bind_result($partner_id);
        $stmt->fetch();
        $stmt->close();
        return $partner_id;
    }

    // Step 1: Get both users from the matched posts
    $user1_id = getUserFromPost($conn, $post1_id);
    $user2_id = getUserFromPost($conn, $post2_id);

    // Step 2: Get their partners
    $user1_partner_id = getPartner($conn, $user1_id);
    $user2_partner_id = getPartner($conn, $user2_id);

    // Step 3: Insert all 4 users into trade_confirmation, only if not null
    $insert_stmt = $conn->prepare("INSERT INTO trade_confirmation (match_id, user_id) VALUES (?, ?)");

    foreach ([$user1_id, $user1_partner_id, $user2_id, $user2_partner_id] as $uid) {
        if (!is_null($uid)) {
            $insert_stmt->bind_param("ii", $match_id, $uid);
            $insert_stmt->execute();
        }
    }

    $insert_stmt->close();

} else {
    echo "Failed to find posts for match_id = $match_id.";
}

        // Step 9: Inform the user that the trade was matched
        echo "<script>
            window.onload = function() {
                alert('Trade matched and recorded. Transaction in progress!');
                window.location.href = 'dashboard.php';
            }
        </script>";
    } else {
        // No match found
        echo "<script>
            window.onload = function() {
                alert('Trade created successfully. Waiting for a match!');
                window.location.href = 'dashboard.php';
            }
        </script>";
    }

    $match_stmt->close();
}
/*
            // Now get details for both trades to insert into transactions
            $details_sql = "SELECT trade_id, sender_id, receiver_id, item_offered, quantity_offered 
                            FROM trades WHERE trade_id IN (?, ?)";
            $details_stmt = $conn->prepare($details_sql);
            $details_stmt->bind_param("ii", $new_trade_id, $matching_trade_id);
            $details_stmt->execute();
            $details_result = $details_stmt->get_result();
        
            $trades = [];
            while ($row = $details_result->fetch_assoc()) {
                $trades[$row['trade_id']] = $row;
            }
            $details_stmt->close();
        
            // Assign based on new_trade vs match_trade for direction
            $a = $trades[$new_trade_id];
            $b = $trades[$matching_trade_id];
        
            $insert_txn_sql = "INSERT INTO transactions 
                (user_a_id, user_b_id, partner_a_id, partner_b_id, 
                 item_from_a, qty_from_a, item_from_b, qty_from_b, 
                 value_adjusted_a, value_adjusted_b, 
                 hash_key, hash_a, hash_b, status, created_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0, 0, ?, ?, ?, 'pending', NOW())";
        
            $txn_stmt = $conn->prepare($insert_txn_sql);
            $txn_stmt->bind_param("iiiiiiissss", 
                $a['sender_id'], $b['sender_id'], $a['receiver_id'], $b['receiver_id'],
                $a['item_offered'], $a['quantity_offered'],
                $b['item_offered'], $b['quantity_offered'],
                $hash_key, $hash_a, $hash_b
            );
            $txn_stmt->execute();
            $txn_id = $txn_stmt->insert_id;
            $txn_stmt->close();
        
            $update_trades_sql = "UPDATE trades SET transaction_id = ? WHERE trade_id IN (?, ?)";
            $link_stmt = $conn->prepare($update_trades_sql);
            $link_stmt->bind_param("iii", $txn_id, $new_trade_id, $matching_trade_id);
            $link_stmt->execute();
            $link_stmt->close();

            echo "<script>
                window.onload = function() {
                    alert('Trade matched and recorded. Transaction in progress!');
                    window.location.href = 'dashboard.php';
                }
            </script>";
        
        } else {
            echo "<script>
                window.onload = function() {
                    alert('Trade created successfully. Waiting for a match!');
                    window.location.href = 'dashboard.php';
                }
            </script>";

        }

        $match_stmt->close();
    }
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Trade</title>
  <link rel="stylesheet" href="styles.css">
  <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">
</head>
<body>

    <div class="navbar">
        <div class="navbar-left">
          <div class="logo">BarterBuddies</div>
        </div>
        <div class="navbar-right">
          <a href="create_trade.php" class="nav-button active">Create Trade</a>
          <a href="dashboard.php" class="nav-button">User Dashboard</a>
          <a href="logout.php" class="nav-button">Log Out</a>
        </div>
      </div>

      <div class="trade-form-container">
        <h2>Create a Trade</h2>
        <form method="POST" action="create_trade.php" class="trade-form">
            <label for="item_offered">Item to Offer:</label>
            <select name="item_offered" required>
                <?php while ($row = $item_result->fetch_assoc()) {
                    echo "<option value='" . $row['item_id'] . "'>" . htmlspecialchars($row['item_name']) . "</option>";
                } ?>
            </select><br><br>

            <label for="quantity_offered">Quantity to Offer:</label>
            <input type="number" name="quantity_offered" min="1" required><br><br>

            <label for="item_desired">Item Desired:</label>
            <select name="item_desired" required>
                <?php
                // Re-run query since first result set is consumed
                $item_result = $conn->query($item_sql);
                while ($row = $item_result->fetch_assoc()) {
                    echo "<option value='" . $row['item_id'] . "'>" . htmlspecialchars($row['item_name']) . "</option>";
                } ?>
            </select><br><br>

            <label for="quantity_desired">Quantity Desired:</label>
            <input type="number" name="quantity_desired" min="1" required><br><br>

            <label>Who has the item being offered?</label><br>
            <input type="radio" name="who_has" value="user" checked> I have the item<br>
            <input type="radio" name="who_has" value="partner"> My partner has it<br><br>

            <input type="submit" value="Submit Trade" class="submit-btn">
        </form>
      </div>

</body>
</html>
