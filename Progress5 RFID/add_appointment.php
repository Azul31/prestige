<?php
include 'db_connect.php';

// Function to get all staff members
function getStaff() {
    global $conn;
    $result = $conn->query("SELECT * FROM staff ORDER BY role, name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get all services
function getServices() {
    global $conn;
    $result = $conn->query("SELECT * FROM services ORDER BY name");
    return $result->fetch_all(MYSQLI_ASSOC);
}

// Function to get appointment details
function getAppointment($id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            GROUP_CONCAT(as2.service_id) as service_ids,
            GROUP_CONCAT(as2.price) as service_prices
        FROM appointments a
        LEFT JOIN appointment_services as2 ON a.id = as2.appointment_id
        WHERE a.id = ?
        GROUP BY a.id
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

$staff = getStaff();
$services = getServices();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rfid_uid = $_POST['rfid_uid'];
    $appointment_date = $_POST['appointment_date'];
    $aesthetician_id = $_POST['aesthetician_id'];
    $nurse_id = $_POST['nurse_id'];
    $service_ids = $_POST['service_ids'] ?? [];
    $service_prices = $_POST['service_prices'] ?? [];
    $total_amount = array_sum($service_prices);
    
    // Start transaction
    $conn->begin_transaction();
    
    try {
        if (isset($_POST['appointment_id'])) {
            // Update existing appointment
            $stmt = $conn->prepare("
                UPDATE appointments 
                SET appointment_date = ?, aesthetician_id = ?, nurse_id = ?, total_amount = ?
                WHERE id = ?
            ");
            $stmt->bind_param("siidi", $appointment_date, $aesthetician_id, $nurse_id, $total_amount, $_POST['appointment_id']);
            $stmt->execute();
            
            // Delete existing services
            $stmt = $conn->prepare("DELETE FROM appointment_services WHERE appointment_id = ?");
            $stmt->bind_param("i", $_POST['appointment_id']);
            $stmt->execute();
            
            $appointment_id = $_POST['appointment_id'];
        } else {
            // Create new appointment
            $stmt = $conn->prepare("
                INSERT INTO appointments (rfid_uid, appointment_date, aesthetician_id, nurse_id, total_amount)
                VALUES (?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("ssiid", $rfid_uid, $appointment_date, $aesthetician_id, $nurse_id, $total_amount);
            $stmt->execute();
            
            $appointment_id = $conn->insert_id;
        }
        
        // Add services
        $stmt = $conn->prepare("INSERT INTO appointment_services (appointment_id, service_id, price) VALUES (?, ?, ?)");
        foreach ($service_ids as $i => $service_id) {
            $stmt->bind_param("iid", $appointment_id, $service_id, $service_prices[$i]);
            $stmt->execute();
        }
        
        $conn->commit();
        header("Location: home.php");
        exit;
    } catch (Exception $e) {
        $conn->rollback();
        $error = $e->getMessage();
    }
}

// Get appointment for editing if ID is provided
$appointment = null;
if (isset($_GET['id'])) {
    $appointment = getAppointment($_GET['id']);
}

// Get RFID from last_card.txt if not editing
$rfid_uid = "";
if (!$appointment) {
    // First try to get from URL parameter (support both uid and rfid parameters)
    if (isset($_GET['uid'])) {
        $rfid_uid = $_GET['uid'];
    } elseif (isset($_GET['rfid'])) {
        $rfid_uid = $_GET['rfid'];
    }
    // Then try current_card.txt
    elseif (file_exists("current_card.txt")) {
        $rfid_uid = trim(file_get_contents("current_card.txt"));
    }
    // Finally try last_card.txt as fallback
    elseif (file_exists("last_card.txt")) {
        $rfid_uid = trim(file_get_contents("last_card.txt"));
    }
    
    // Verify RFID exists in database
    if ($rfid_uid) {
        $verify = $conn->prepare("SELECT rfid_uid FROM rfid_users WHERE rfid_uid = ?");
        $verify->bind_param("s", $rfid_uid);
        $verify->execute();
        $result = $verify->get_result();
        if ($result->num_rows === 0) {
            die("Error: RFID card not registered. Please register the card first.");
        }
    } else {
        die("Error: No RFID card detected. Please scan a card first.");
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add/Edit Treatment - Prestige Skin Institute</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #fdfbf7 0%, #f5f0e8 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        h1 {
            font-size: 28px;
            color: #2c2c2c;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
            font-size: 14px;
        }

        input[type="text"],
        input[type="datetime-local"],
        select {
            width: 100%;
            padding: 10px;
            border: 2px solid #e8e8e8;
            border-radius: 8px;
            font-size: 15px;
            transition: all 0.3s;
        }

        input:focus,
        select:focus {
            outline: none;
            border-color: #d4af37;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.1);
        }

        .services-container {
            border: 2px solid #e8e8e8;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .service-row {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 10px;
            margin-bottom: 10px;
            align-items: center;
        }

        .btn {
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 16px;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .btn-add {
            background: #28a745;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-remove {
            background: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-top: 20px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            .service-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?php echo $appointment ? 'Edit Treatment' : 'Add New Treatment'; ?></h1>
        </div>

        <?php if (isset($error)): ?>
            <div style="color: red; margin-bottom: 20px;"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST">
            <?php if ($appointment): ?>
                <input type="hidden" name="appointment_id" value="<?php echo $appointment['id']; ?>">
                <input type="hidden" name="rfid_uid" value="<?php echo $appointment['rfid_uid']; ?>">
            <?php else: ?>
                <input type="hidden" name="rfid_uid" value="<?php echo htmlspecialchars($rfid_uid); ?>">
            <?php endif; ?>

            <div class="form-group">
                <label>Appointment Date & Time</label>
                <input type="datetime-local" name="appointment_date" required 
                    value="<?php echo $appointment ? date('Y-m-d\TH:i', strtotime($appointment['appointment_date'])) : date('Y-m-d\TH:i'); ?>">
            </div>

            <div class="form-group">
                <label>Aesthetician</label>
                <select name="aesthetician_id" required>
                    <option value="">Select Aesthetician</option>
                    <?php foreach ($staff as $s): ?>
                        <?php if ($s['role'] === 'Aesthetician'): ?>
                            <option value="<?php echo $s['id']; ?>" 
                                <?php echo ($appointment && $appointment['aesthetician_id'] == $s['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['name']); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Nurse</label>
                <select name="nurse_id" required>
                    <option value="">Select Nurse</option>
                    <?php foreach ($staff as $s): ?>
                        <?php if ($s['role'] === 'Nurse'): ?>
                            <option value="<?php echo $s['id']; ?>"
                                <?php echo ($appointment && $appointment['nurse_id'] == $s['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($s['name']); ?>
                            </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Services</label>
                <div class="services-container" id="servicesContainer">
                    <!-- Service rows will be added here -->
                </div>
                <button type="button" class="btn-add" onclick="addServiceRow()">+ Add Service</button>
            </div>

            <div class="buttons">
                <button type="submit" class="btn">💾 Save Treatment</button>
                <button type="button" class="btn btn-secondary" onclick="location.href='home.php'">Cancel</button>
            </div>
        </form>
    </div>

    <script>
        const services = <?php echo json_encode($services); ?>;
        const appointmentServices = <?php echo $appointment ? json_encode([
            'ids' => explode(',', $appointment['service_ids']),
            'prices' => explode(',', $appointment['service_prices'])
        ]) : 'null'; ?>;

        function addServiceRow(serviceId = '', price = '') {
            const container = document.getElementById('servicesContainer');
            const row = document.createElement('div');
            row.className = 'service-row';
            
            row.innerHTML = `
                <select name="service_ids[]" required onchange="updatePrice(this)">
                    <option value="">Select Service</option>
                    ${services.map(s => `
                        <option value="${s.id}" data-price="${s.price}" ${serviceId == s.id ? 'selected' : ''}>
                            ${s.name}
                        </option>
                    `).join('')}
                </select>
                <input type="number" name="service_prices[]" value="${price || ''}" 
                    step="0.01" required placeholder="Price" style="width: 120px;">
                <button type="button" class="btn-remove" onclick="this.parentElement.remove()">✕</button>
            `;
            
            container.appendChild(row);
        }

        function updatePrice(select) {
            const price = select.options[select.selectedIndex].dataset.price;
            const priceInput = select.parentElement.querySelector('input[name="service_prices[]"]');
            priceInput.value = price;
        }

        // Initialize with existing services or add empty row
        if (appointmentServices) {
            appointmentServices.ids.forEach((id, index) => {
                addServiceRow(id, appointmentServices.prices[index]);
            });
        } else {
            addServiceRow();
        }
    </script>
</body>
</html>