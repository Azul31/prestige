<?php
header('Content-Type: application/json');
include 'db_connect.php';

$last_card_file = __DIR__ . DIRECTORY_SEPARATOR . "last_card.txt";

// If the file doesn't exist or is empty, return a consistent JSON object
if (!file_exists($last_card_file)) {
    echo json_encode(['rfid_uid' => null]);
    exit;
}

$last_uid = trim(file_get_contents($last_card_file));

if (!$last_uid) {
    echo json_encode(['rfid_uid' => null]);
    exit;
}

$res = $conn->prepare("SELECT * FROM rfid_users WHERE rfid_uid = ?");
if ($res === false) {
    // DB prepare error
    echo json_encode(['rfid_uid' => $last_uid, 'error' => $conn->error]);
    // clear the last card so the front-end doesn't repeatedly fetch the same failing UID
    file_put_contents($last_card_file, "");
    exit;
}

$res->bind_param("s", $last_uid);
$res->execute();
$row = $res->get_result()->fetch_assoc();

// Reset file after showing once
file_put_contents($last_card_file, "");

if (!$row) {
    // No user found for the UID — still return the UID so front-end can open the popup
    echo json_encode([
        'rfid_uid' => $last_uid,
        'full_name' => null,
        'status' => null
    ]);
} else {
    // Map DB `dob` column to `date_of_birth` expected by front-end
    $row['date_of_birth'] = isset($row['dob']) ? $row['dob'] : null;
    // Also keep the original keys, but returning the mapped key ensures index.php receives date_of_birth
    echo json_encode($row);
}
?>
