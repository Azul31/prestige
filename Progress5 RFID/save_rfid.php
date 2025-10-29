<?php
include 'db_connect.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Log function for debugging
function log_debug($message) {
    file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " - $message\n", FILE_APPEND);
}

try {
    if (!isset($_GET['uid'])) {
        throw new Exception('UID required');
    }

    $uid = $_GET['uid'];
    log_debug("Card detected: $uid");

    // Only update last_card.txt if it's different
    $current_card = file_exists("last_card.txt") ? trim(file_get_contents("last_card.txt")) : "";
    if ($current_card !== $uid) {
        file_put_contents("last_card.txt", $uid);
        log_debug("Updated last_card.txt with: $uid");
    }

    // Check if user exists
    $stmt = $conn->prepare("SELECT * FROM rfid_users WHERE rfid_uid = ?");
    if (!$stmt) {
        throw new Exception('Database error: ' . $conn->error);
    }

    $stmt->bind_param("s", $uid);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        // Update existing user
        $update = $conn->prepare("UPDATE rfid_users SET last_seen = NOW(), status = 'Arrived' WHERE rfid_uid = ?");
        if (!$update) {
            throw new Exception('Database error: ' . $conn->error);
        }

        $update->bind_param("s", $uid);
        $update->execute();

        echo json_encode([
            'success' => true,
            'status' => 'Arrived',
            'user' => $user
        ]);
    } else {
        // Create new user with default name
        $defaultName = "Client " . substr($uid, -4);
        
        $insert = $conn->prepare("INSERT INTO rfid_users (rfid_uid, full_name, status) VALUES (?, ?, 'New')");
        if (!$insert) {
            throw new Exception('Database error: ' . $conn->error);
        }

        $insert->bind_param("ss", $uid, $defaultName);
        if (!$insert->execute()) {
            throw new Exception('Failed to create user: ' . $insert->error);
        }

        echo json_encode([
            'success' => true,
            'status' => 'New',
            'user' => [
                'rfid_uid' => $uid,
                'full_name' => $defaultName,
                'status' => 'New'
            ]
        ]);
    }
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
