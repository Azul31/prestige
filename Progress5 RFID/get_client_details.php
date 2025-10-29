<?php
include 'db_connect.php';

header('Content-Type: application/json');

if (!isset($_GET['uid'])) {
    http_response_code(400);
    echo json_encode(['error' => 'UID required']);
    exit;
}

$uid = $_GET['uid'];

// Get client info
$stmt = $conn->prepare("SELECT * FROM rfid_users WHERE rfid_uid = ?");
$stmt->bind_param("s", $uid);
$stmt->execute();
$client = $stmt->get_result()->fetch_assoc();

if (!$client) {
    echo json_encode(['error' => 'Client not found']);
    exit;
}

// Get appointments with staff and services
$stmt = $conn->prepare("
    SELECT 
        a.*,
        aes.name as aesthetician_name,
        n.name as nurse_name,
        GROUP_CONCAT(DISTINCT s.name) as services,
        GROUP_CONCAT(DISTINCT as2.price) as prices
    FROM appointments a
    LEFT JOIN staff aes ON a.aesthetician_id = aes.id
    LEFT JOIN staff n ON a.nurse_id = n.id
    LEFT JOIN appointment_services as2 ON a.id = as2.appointment_id
    LEFT JOIN services s ON as2.service_id = s.id
    WHERE a.rfid_uid = ?
    GROUP BY a.id
    ORDER BY a.appointment_date DESC
");
$stmt->bind_param("s", $uid);
$stmt->execute();
$appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

echo json_encode([
    'client' => $client,
    'appointments' => $appointments
]);
?>