<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];
$action = $_POST['action'] ?? '';
$trade_id = intval($_POST['trade_id'] ?? 0);
$transaction_id = intval($_POST['transaction_id'] ?? 0);
$hash_code = trim($_POST['hash_code'] ?? '');

// Fetch transaction record
$txnQuery = "SELECT * FROM transactions WHERE transaction_id = ?";
$txnStmt = $conn->prepare($txnQuery);
$txnStmt->bind_param("i", $transaction_id);
$txnStmt->execute();
$txnResult = $txnStmt->get_result();

if ($txnResult->num_rows == 0) {
    echo "Invalid transaction.";
    exit();
}

$txn = $txnResult->fetch_assoc();

// Shortcuts
$isUserA = ($userId == $txn['user_a_id']);
$isUserB = ($userId == $txn['user_b_id']);
$isPartnerA = ($userId == $txn['partner_a_id']);
$isPartnerB = ($userId == $txn['partner_b_id']);

$updateQuery = "";
$complete = false;

// Handle item sending
if ($action === 'send_item_a' && $isPartnerA && !$txn['item_from_a_sent']) {
    $updateQuery = "UPDATE transactions SET item_from_a_sent = 1 WHERE transaction_id = ?";
} elseif ($action === 'send_item_b' && $isUserB && !$txn['item_from_b_sent']) {
    $updateQuery = "UPDATE transactions SET item_from_b_sent = 1 WHERE transaction_id = ?";
}

// Handle hash sending
if ($action === 'send_hash_a' && $isUserA && !$txn['hash_a_sent']) {
    $updateQuery = "UPDATE transactions SET hash_a_sent = 1 WHERE transaction_id = ?";
} elseif ($action === 'send_hash_b' && $isUserB && !$txn['hash_b_sent']) {
    $updateQuery = "UPDATE transactions SET hash_b_sent = 1 WHERE transaction_id = ?";
}

// Handle hash submission
elseif ($action === 'submit_code_a' || $action === 'submit_code_b') {
    if ($action === 'submit_code_a' && $isPartnerA && !$txn['hash_a_confirmed']) {
        if ($hash_code === $txn['hash_a']) {
            $updateQuery = "UPDATE transactions SET hash_a_confirmed = 1 WHERE transaction_id = ?";
        } else {
            echo "❌ Invalid code from User A.";
            exit();
        }
    } elseif ($action === 'submit_code_b' && $isPartnerB && !$txn['hash_b_confirmed']) {
        if ($hash_code === $txn['hash_b']) {
            $updateQuery = "UPDATE transactions SET hash_b_confirmed = 1 WHERE transaction_id = ?";
        } else {
            echo "❌ Invalid code from User B.";
            exit();
        }
    } else {
        echo "❌ Unauthorized or already verified.";
        exit();
    }
}


if (!empty($updateQuery)) {
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $transaction_id);
    $stmt->execute();
}

// Recheck transaction to see if it's ready to finalize
$checkStmt = $conn->prepare("SELECT * FROM transactions WHERE transaction_id = ?");
$checkStmt->bind_param("i", $transaction_id);
$checkStmt->execute();
$finalTxn = $checkStmt->get_result()->fetch_assoc();

if (
    $finalTxn['item_from_a_sent'] &&
    $finalTxn['item_from_b_sent'] &&
    $finalTxn['hash_a_confirmed'] &&
    $finalTxn['hash_b_confirmed']
) {
    // Update trade to complete
    $txn_hash = $finalTxn['hash_a'] . '-' . $finalTxn['hash_b'];

    $updateBoth = $conn->prepare("
        UPDATE trades 
        SET status = 'completed', transaction_hash = ?
        WHERE transaction_id = ?
    ");
    $updateBoth->bind_param("si", $txn_hash, $transaction_id);
    $updateBoth->execute();

    // Insert into completed_trades table
    $summary = "Trade between User A (ID: {$txn['user_a_id']}) and User B (ID: {$txn['user_b_id']}) completed.";
    $total_cost = calculateTotalCost($txn); // Calculate total cost with transaction fee
    $completed_at = date("Y-m-d H:i:s"); // Timestamp for when the trade is completed

    $insertCompletedTrade = $conn->prepare("INSERT INTO completed_trades (transaction_id, summary, total_cost, completed_at) VALUES (?, ?, ?, ?)");
    $insertCompletedTrade->bind_param("isss", $transaction_id, $summary, $total_cost, $completed_at);
    $insertCompletedTrade->execute();

    echo "✅ Trade completed successfully!";
} else {
    echo "✅ Action processed. Waiting for the rest of the steps.";
}


echo "<br><a href='in_progress.php?trade_id=$trade_id'>Return to Trade</a>";
function calculateTotalCost($txn) {
    // Define the transaction fee percentage (5%)
    $transaction_fee_percentage = 0.05;

    // Get the original costs of items A and B
    $itemACost = $txn['item_a_cost'] ?? 0;
    $itemBCost = $txn['item_b_cost'] ?? 0;

    // Calculate the transaction fee for each item
    $itemAFee = $itemACost * $transaction_fee_percentage;
    $itemBFee = $itemBCost * $transaction_fee_percentage;

    // Subtract the fee from each item's cost
    $itemACostAfterFee = $itemACost - $itemAFee;
    $itemBCostAfterFee = $itemBCost - $itemBFee;

    // The total cost is the sum of both items after subtracting their transaction fees
    return $itemACostAfterFee + $itemBCostAfterFee;
}

?>
