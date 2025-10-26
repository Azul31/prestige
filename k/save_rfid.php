<?php
header('Content-Type: application/json');
include 'db_connect.php';

if (!isset($_GET['uid'])) {
    http_response_code(400);
    echo json_encode(['error' => 'UID required']);
    exit;
}

$uid = $_GET['uid'];

$stmt = $conn->prepare("SELECT id, full_name, status, rfid_uid FROM rfid_users WHERE rfid_uid = ?");
if ($stmt === false) {
    http_response_code(500);
    echo json_encode(['error' => $conn->error]);
    exit;
}

$stmt->bind_param("s", $uid);
$stmt->execute();
$res = $stmt->get_result();

$last_card_file = __DIR__ . DIRECTORY_SEPARATOR . "last_card.txt";

if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    $update = $conn->prepare("UPDATE rfid_users SET last_seen = NOW(), status = 'Arrived' WHERE id = ?");
    if ($update) {
        $update->bind_param("i", $row['id']);
        $update->execute();
    }

    // Save the last detected UID
    @file_put_contents($last_card_file, $uid);

    $name = $row['full_name'] ?: $row['rfid_uid'];

    echo json_encode([
        'status' => 'Arrived',
        'id' => $row['id'],
        'name' => $name
    ]);
} else {
    $defaultName = "Client " . substr($uid, -4);
    $ins = $conn->prepare("INSERT INTO rfid_users (rfid_uid, full_name, status, created_at) VALUES (?, ?, 'New', NOW())");
    if ($ins === false) {
        http_response_code(500);
        echo json_encode(['error' => $conn->error]);
        exit;
    }

    $ins->bind_param("ss", $uid, $defaultName);
    $ins->execute();

    // Save the last detected UID
    @file_put_contents($last_card_file, $uid);

    echo json_encode([
        'status' => 'New',
        'uid' => $uid,
        'name' => $defaultName,
        'insert_id' => $ins->insert_id
    ]);
}
?>
