<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');
include 'db_connect.php';

// Helper to return JSON and exit
function json_err($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $msg]);
    exit;
}

if (empty($_POST['uid'])) {
    json_err('Missing UID', 400);
}

$uid = trim($_POST['uid']);

// Collect fields (normalize to empty strings if not provided)
$fields = [
    'full_name' => $_POST['full_name'] ?? '',
    'status' => $_POST['status'] ?? '',
    'sex' => $_POST['sex'] ?? '',
    'age' => $_POST['age'] ?? '',
    'weight' => $_POST['weight'] ?? '',
    'height' => $_POST['height'] ?? '',
    // DB column is `dob` (date of birth)
    'dob' => $_POST['date_of_birth'] ?? '',
    'patient_id' => $_POST['patient_id'] ?? '',
    // `blood_type` column does not exist in DB schema; removed to match table
    'allergy' => $_POST['allergy'] ?? '',
    'past_surgery' => $_POST['past_surgery'] ?? '',
    'address' => $_POST['address'] ?? '',
    'contact_number' => $_POST['contact_number'] ?? '',
    'email' => $_POST['email'] ?? '',
    'emergency_contact' => $_POST['emergency_contact'] ?? ''
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
