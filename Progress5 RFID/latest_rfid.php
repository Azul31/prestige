<?php
include 'db_connect.php';
header('Content-Type: application/json');

$last_card_file = "last_card.txt";
$current_card_file = "current_card.txt"; // New file to store current active card

try {
    // If no card was tapped yet, check for current card
    if (!file_exists($last_card_file)) {
        // Check if we have a current card
        if (file_exists($current_card_file)) {
            $current_uid = trim(file_get_contents($current_card_file));
            if (!empty($current_uid)) {
                echo json_encode(['status' => 'active', 'rfid_uid' => $current_uid]);
                exit;
            }
        }
        echo json_encode(['status' => 'waiting']);
        exit;
    }

    $last_uid = trim(file_get_contents($last_card_file));

    if (empty($last_uid)) {
        // Check if we have a current card
        if (file_exists($current_card_file)) {
            $current_uid = trim(file_get_contents($current_card_file));
            if (!empty($current_uid)) {
                echo json_encode(['status' => 'active', 'rfid_uid' => $current_uid]);
                exit;
            }
        }
        echo json_encode(['status' => 'waiting']);
        exit;
    }

    // Try to get user info
    $stmt = $conn->prepare("SELECT * FROM rfid_users WHERE rfid_uid = ?");
    if (!$stmt) {
        throw new Exception("Database error: " . $conn->error);
    }

    $stmt->bind_param("s", $last_uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Update last_seen
        $update = $conn->prepare("UPDATE rfid_users SET last_seen = NOW() WHERE rfid_uid = ?");
        if ($update) {
            $update->bind_param("s", $last_uid);
            $update->execute();
        }

        // Store as current card and clear last card file
        file_put_contents($current_card_file, $last_uid);
        file_put_contents($last_card_file, "");
        
        echo json_encode([
            'status' => 'found',
            'rfid_uid' => $last_uid,
            'user' => $user
        ]);
    } else {
        // New card detected
        echo json_encode([
            'status' => 'new',
            'rfid_uid' => $last_uid
        ]);
    }

} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
