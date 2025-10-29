<?php
include 'db_connect.php';

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Setting up Treatment Management System</h2>";

// Drop existing tables in reverse order of dependencies
$conn->query("DROP TABLE IF EXISTS appointment_services");
$conn->query("DROP TABLE IF EXISTS appointments");
$conn->query("DROP TABLE IF EXISTS services");
$conn->query("DROP TABLE IF EXISTS staff");
$conn->query("DROP TABLE IF EXISTS rfid_users");

// Create staff table
$sql_staff = "
CREATE TABLE IF NOT EXISTS staff (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role ENUM('Aesthetician', 'Nurse') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_staff)) {
    echo "<p>✅ Staff table created successfully</p>";
} else {
    echo "<p>❌ Error creating staff table: " . $conn->error . "</p>";
}

// Create services table
$sql_services = "
CREATE TABLE IF NOT EXISTS services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql_services)) {
    echo "<p>✅ Services table created successfully</p>";
} else {
    echo "<p>❌ Error creating services table: " . $conn->error . "</p>";
}

// Create rfid_users table if it doesn't exist
$sql_rfid_users = "
CREATE TABLE IF NOT EXISTS rfid_users (
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
    echo "<p>✅ RFID Users table created successfully</p>";
} else {
    echo "<p>❌ Error creating RFID Users table: " . $conn->error . "</p>";
}

// Create appointments table
$sql_appointments = "
CREATE TABLE IF NOT EXISTS appointments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    rfid_uid VARCHAR(50) NOT NULL,
    appointment_date DATETIME NOT NULL,
    aesthetician_id INT,
    nurse_id INT,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    status ENUM('Scheduled', 'In Progress', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_rfid (rfid_uid),
    INDEX idx_aesthetician (aesthetician_id),
    INDEX idx_nurse (nurse_id),
    CONSTRAINT fk_rfid_user FOREIGN KEY (rfid_uid) REFERENCES rfid_users(rfid_uid) ON DELETE CASCADE,
    CONSTRAINT fk_aesthetician FOREIGN KEY (aesthetician_id) REFERENCES staff(id),
    CONSTRAINT fk_nurse FOREIGN KEY (nurse_id) REFERENCES staff(id)
)";

if ($conn->query($sql_appointments)) {
    echo "<p>✅ Appointments table created successfully</p>";
} else {
    echo "<p>❌ Error creating appointments table: " . $conn->error . "</p>";
}

// Create appointment services table
$sql_appointment_services = "
CREATE TABLE IF NOT EXISTS appointment_services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT NOT NULL,
    service_id INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id)
)";

if ($conn->query($sql_appointment_services)) {
    echo "<p>✅ Appointment services table created successfully</p>";
} else {
    echo "<p>❌ Error creating appointment services table: " . $conn->error . "</p>";
}

// Insert sample RFID users
$sample_rfid_users = [
    ['0015784932', 'John Doe', 'john.doe@email.com', '09171234567'],
    ['0089563214', 'Jane Wilson', 'jane.wilson@email.com', '09189876543'],
    ['0034567890', 'Robert Smith', 'robert.smith@email.com', '09157894561']
];

$stmt = $conn->prepare("INSERT INTO rfid_users (rfid_uid, name, email, phone) VALUES (?, ?, ?, ?)");
foreach ($sample_rfid_users as $user) {
    $stmt->bind_param("ssss", $user[0], $user[1], $user[2], $user[3]);
    if ($stmt->execute()) {
        echo "<p>✅ Added RFID user: {$user[1]} (ID: {$user[0]})</p>";
    } else {
        echo "<p>❌ Error adding RFID user {$user[1]}: " . $stmt->error . "</p>";
    }
}

// Insert sample staff
$sample_staff = [
    ['Jane Smith', 'Aesthetician'],
    ['Mary Johnson', 'Aesthetician'],
    ['Sarah Brown', 'Nurse'],
    ['Emily Davis', 'Nurse']
];

// First add a sample RFID user
$sample_users = [
    [
        'A32F32E2',  // This is the RFID UID we saw in debug
        'John Doe',
        'Male',
        35,
        '213 Sample St.',
        '09123456789',
        'john.doe@email.com',
        '09876543210'
    ]
];

$stmt = $conn->prepare("INSERT INTO rfid_users (rfid_uid, full_name, sex, age, address, contact_number, email, emergency_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
foreach ($sample_users as $user) {
    $stmt->bind_param("sssissss", $user[0], $user[1], $user[2], $user[3], $user[4], $user[5], $user[6], $user[7]);
    if ($stmt->execute()) {
        echo "<p>✅ Added sample user: {$user[1]}</p>";
    } else {
        echo "<p>❌ Error adding sample user {$user[1]}: " . $stmt->error . "</p>";
    }
}

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

echo "<h3>Setup Complete!</h3>";
echo "<p>You can now:</p>";
echo "<ol>";
echo "<li>Go to <a href='home.php'>home.php</a> to start using the system</li>";
echo "<li>Tap an RFID card to see client details</li>";
echo "<li>Click 'Add New Treatment' to record treatments</li>";
echo "</ol>";
?>