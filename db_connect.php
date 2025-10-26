<?php
// Database configuration
$servername = "localhost";   // or 127.0.0.1
$username = "root";          // default user in XAMPP
$password = "";              // leave blank if no password
$dbname = "capstone";   // your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// Uncomment the next line if you want to check successful connection
// echo "Connected successfully";
?>
