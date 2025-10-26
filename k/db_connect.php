<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "prestige_rfid";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Ensure proper charset
$conn->set_charset('utf8mb4');
?>
