<?php
// First, make sure we have error reporting on
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database connection
include 'db_connect.php';

echo "<h2>🔄 Resetting Database</h2>";

// First, drop the database if it exists and create it fresh
$conn->query("DROP DATABASE IF EXISTS prestige_rfid");
$conn->query("CREATE DATABASE prestige_rfid");
$conn->select_db("prestige_rfid");

echo "<p>✅ Database reset complete</p>";

echo "<h2>📝 Creating Tables</h2>";

// Drop existing tables in reverse order
$tables = [
    "appointment_services",
    "appointments",
    "services",
    "staff",
    "rfid_users"
];

foreach ($tables as $table) {
    if ($conn->query("DROP TABLE IF EXISTS $table")) {
        echo "<p>✅ Dropped table: $table</p>";
    } else {
        echo "<p>❌ Error dropping table $table: " . $conn->error . "</p>";
    }
}

// Create rfid_users table
$sql_rfid_users = "
CREATE TABLE rfid_users (
    rfid_uid VARCHAR(50) PRIMARY KEY,
    full_name VARCHAR(100) NOT NULL,
    sex VARCHAR(10),
    age INT,
    weight DECIMAL(5,2),
    height DECIMAL(5,2),
    dob DATE,
    patient_id VARCHAR(50),
    allergy TEXT,
    past_surgery TEXT,
    address TEXT,
    contact_number VARCHAR(20),
    email VARCHAR(100),
    emergency_contact VARCHAR(100),
    status VARCHAR(20) DEFAULT 'New',
    last_seen TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_rfid_users)) {
    echo "<p>✅ Created table: rfid_users</p>";
} else {
    echo "<p>❌ Error creating rfid_users table: " . $conn->error . "</p>";
}

// Create staff table
$sql_staff = "
CREATE TABLE staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role ENUM('Aesthetician', 'Nurse') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_staff)) {
    echo "<p>✅ Created table: staff</p>";
} else {
    echo "<p>❌ Error creating staff table: " . $conn->error . "</p>";
}

// Create services table
$sql_services = "
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_services)) {
    echo "<p>✅ Created table: services</p>";
} else {
    echo "<p>❌ Error creating services table: " . $conn->error . "</p>";
}

// Create appointments table
$sql_appointments = "
CREATE TABLE appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rfid_uid VARCHAR(50) NOT NULL,
    appointment_date DATETIME NOT NULL,
    aesthetician_id INT,
    nurse_id INT,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('Scheduled', 'In Progress', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rfid_uid) REFERENCES rfid_users(rfid_uid) ON DELETE CASCADE,
    FOREIGN KEY (aesthetician_id) REFERENCES staff(id),
    FOREIGN KEY (nurse_id) REFERENCES staff(id)
)";

if ($conn->query($sql_appointments)) {
    echo "<p>✅ Created table: appointments</p>";
} else {
    echo "<p>❌ Error creating appointments table: " . $conn->error . "</p>";
}

// Create appointment_services table
$sql_appointment_services = "
CREATE TABLE appointment_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    service_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id)
)";

if ($conn->query($sql_appointment_services)) {
    echo "<p>✅ Created table: appointment_services</p>";
} else {
    echo "<p>❌ Error creating appointment_services table: " . $conn->error . "</p>";
}

// Insert sample staff
$sample_staff = [
    ['Jane Smith', 'Aesthetician'],
    ['Mary Johnson', 'Aesthetician'],
    ['Sarah Brown', 'Nurse'],
    ['Emily Davis', 'Nurse']
];

$stmt = $conn->prepare("INSERT INTO staff (name, role) VALUES (?, ?)");
foreach ($sample_staff as $staff) {
    $stmt->bind_param("ss", $staff[0], $staff[1]);
    if ($stmt->execute()) {
        echo "<p>✅ Added staff member: {$staff[0]} ({$staff[1]})</p>";
    } else {
        echo "<p>❌ Error adding staff member {$staff[0]}: " . $stmt->error . "</p>";
    }
}

// Insert sample services
$sample_services = [
    ['Facial Treatment', 'Deep cleansing facial', 2500.00],
    ['Diamond Peel', 'Advanced exfoliation', 3000.00],
    ['RF Treatment', 'Radio frequency skin tightening', 4500.00],
    ['Laser Hair Removal', 'Permanent hair reduction', 5000.00]
];

$stmt = $conn->prepare("INSERT INTO services (name, description, price) VALUES (?, ?, ?)");
foreach ($sample_services as $service) {
    $stmt->bind_param("ssd", $service[0], $service[1], $service[2]);
    if ($stmt->execute()) {
        echo "<p>✅ Added service: {$service[0]} (₱{$service[2]})</p>";
    } else {
        echo "<p>❌ Error adding service {$service[0]}: " . $stmt->error . "</p>";
    }
}

echo "<h3>Database Setup Complete!</h3>";
echo "<p>You can now:</p>";
echo "<ol>";
echo "<li>Go to <a href='index.php'>index.php</a> to register RFID cards</li>";
echo "<li>Go to <a href='home.php'>home.php</a> to manage appointments</li>";
echo "</ol>";
?>