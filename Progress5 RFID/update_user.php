<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set headers for CORS and content type
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include 'db_connect.php';

// Log the received data for debugging
$input = file_get_contents('php://input');
$raw_data = $_POST ?: json_decode($input, true) ?: [];
file_put_contents('debug_log.txt', date('Y-m-d H:i:s') . " Received data:\nPOST: " . print_r($_POST, true) . "\nRAW: " . $input . "\n\n", FILE_APPEND);

// Helper to return JSON and exit
function json_err($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

// Check if we have any data
if (empty($raw_data) && empty($_POST)) {
    json_err('No data received', 400);
}

// Get data from either POST or raw input
$data = !empty($_POST) ? $_POST : $raw_data;

if (empty($data['uid'])) {
    json_err('Missing UID', 400);
}

$uid = trim($data['uid']);

// Validate required fields
if (empty($data['full_name'])) {
    json_err('Full Name is required', 400);
}

// Collect fields (normalize to empty strings if not provided)
$fields = [
    'full_name' => $data['full_name'] ?? '',
    'sex' => $data['sex'] ?? '',
    'age' => $data['age'] ? intval($data['age']) : null,
    'weight' => $data['weight'] ? floatval($data['weight']) : null,
    'height' => $data['height'] ? floatval($data['height']) : null,
    'dob' => $data['date_of_birth'] ?? null,
    'patient_id' => $data['patient_id'] ?? '',
    'allergy' => $data['allergy'] ?? '',
    'past_surgery' => $data['past_surgery'] ?? '',
    'address' => $data['address'] ?? '',
    'contact_number' => $data['contact_number'] ?? '',
    'email' => $data['email'] ? filter_var($data['email'], FILTER_VALIDATE_EMAIL) : '',
    'emergency_contact' => $data['emergency_contact'] ?? '',
    'status' => $data['status'] ?? 'New'
];

// Attempt to UPDATE first
$update_sql = "UPDATE rfid_users SET full_name=?, status=?, sex=?, age=?, weight=?, height=?, dob=?, patient_id=?, allergy=?, past_surgery=?, address=?, contact_number=?, email=?, emergency_contact=? WHERE rfid_uid=?";
$stmt = $conn->prepare($update_sql);
if ($stmt === false) {
    json_err('DB prepare failed: ' . $conn->error, 500);
}

// Bind params dynamically (mysqli requires references)
$params = array_values($fields);
$params[] = $uid; // rfid_uid at the end
$types = str_repeat('s', count($params));
$bind_names = [];
$bind_names[] = & $types;
for ($i = 0; $i < count($params); $i++) {
    $bind_names[] = & $params[$i];
}
// call bind_param with references
call_user_func_array([$stmt, 'bind_param'], $bind_names);

$ok = $stmt->execute();
if ($ok && $stmt->affected_rows > 0) {
    echo json_encode(['success' => true, 'action' => 'updated']);
    exit;
}

// If update did not affect rows, try insert (create new user)
$insert_columns = array_keys($fields);
array_unshift($insert_columns, 'rfid_uid'); // ensure rfid_uid is first
$placeholders = implode(',', array_fill(0, count($insert_columns), '?'));
$insert_sql = 'INSERT INTO rfid_users (' . implode(',', $insert_columns) . ', created_at) VALUES (' . $placeholders . ', NOW())';

$stmt2 = $conn->prepare($insert_sql);
if ($stmt2 === false) {
    // If insert can't be prepared, return DB error
    json_err('DB prepare failed (insert): ' . $conn->error, 500);
}

// Build values for insert (rfid_uid + fields)
$insert_values = [$uid];
foreach ($fields as $v) { $insert_values[] = $v; }

$types2 = str_repeat('s', count($insert_values));
$bind_insert = [];
$bind_insert[] = & $types2;
for ($i = 0; $i < count($insert_values); $i++) {
    $bind_insert[] = & $insert_values[$i];
}
call_user_func_array([$stmt2, 'bind_param'], $bind_insert);

if ($stmt2->execute()) {
    echo json_encode(['success' => true, 'action' => 'inserted', 'insert_id' => $stmt2->insert_id]);
    exit;
} else {
    json_err('DB execute failed: ' . $stmt2->error, 500);
}

?>
