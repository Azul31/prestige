<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>RFID System Debug</h2>";

// 1. Check last_card.txt
echo "<h3>1. Checking last_card.txt:</h3>";
if (file_exists("last_card.txt")) {
    $content = file_get_contents("last_card.txt");
    echo "File exists. Content: '<strong>" . htmlspecialchars($content) . "</strong>'<br>";
    echo "File last modified: " . date("Y-m-d H:i:s", filemtime("last_card.txt")) . "<br>";
} else {
    echo "❌ last_card.txt does not exist<br>";
}

// 2. Check database connection
echo "<h3>2. Checking database connection:</h3>";
include 'db_connect.php';
if ($conn->ping()) {
    echo "✅ Database connection successful<br>";
} else {
    echo "❌ Database connection failed<br>";
}

// 3. Check rfid_users table
echo "<h3>3. Checking rfid_users table:</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM rfid_users");
$row = $result->fetch_assoc();
echo "Number of registered users: " . $row['count'] . "<br>";

// 4. Check staff table
echo "<h3>4. Checking staff table:</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM staff");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Number of staff members: " . $row['count'] . "<br>";
} else {
    echo "❌ Staff table not found. Please run setup_system.php first<br>";
}

// 5. Check services table
echo "<h3>5. Checking services table:</h3>";
$result = $conn->query("SELECT COUNT(*) as count FROM services");
if ($result) {
    $row = $result->fetch_assoc();
    echo "Number of services: " . $row['count'] . "<br>";
} else {
    echo "❌ Services table not found. Please run setup_system.php first<br>";
}

// 6. Test latest_rfid.php
echo "<h3>6. Testing latest_rfid.php:</h3>";
$response = file_get_contents("http://localhost/prestige_rfid/latest_rfid.php");
echo "Response from latest_rfid.php: '<strong>" . htmlspecialchars($response) . "</strong>'<br>";

echo "<hr>";
echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>If you see any ❌ errors above, they need to be fixed first.</li>";
echo "<li>If you haven't run setup_system.php yet, <a href='setup_system.php'>click here to run it</a></li>";
echo "<li>After fixing any errors, <a href='home.php'>try the home page again</a></li>";
echo "</ol>";
?>