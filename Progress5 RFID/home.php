<?php
include 'db_connect.php';

// Function to get client details and appointments
function getClientDetails($rfid_uid) {
    global $conn;
    
    // Get client info
    $stmt = $conn->prepare("SELECT * FROM rfid_users WHERE rfid_uid = ?");
    $stmt->bind_param("s", $rfid_uid);
    $stmt->execute();
    $client = $stmt->get_result()->fetch_assoc();
    
    if (!$client) return null;
    
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
    $stmt->bind_param("s", $rfid_uid);
    $stmt->execute();
    $appointments = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    
    return [
        'client' => $client,
        'appointments' => $appointments
    ];
}

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

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prestige Skin Institute - Client Portal</title>
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
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            margin-bottom: 40px;
        }

        .crown {
            width: 60px;
            height: 50px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
        }

        h1 {
            font-size: 36px;
            font-weight: 300;
            color: #2c2c2c;
            margin-bottom: 10px;
        }

        .client-info {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .client-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .client-name {
            font-size: 24px;
            color: #d4af37;
        }

        .button-group {
            display: flex;
            gap: 10px;
        }

        .button-group .btn {
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .client-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .detail-item {
            margin-bottom: 15px;
        }

        .detail-label {
            font-size: 12px;
            color: #666;
            margin-bottom: 5px;
        }

        .detail-value {
            font-size: 16px;
            color: #333;
        }

        .appointments {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .appointment-card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.05);
        }

        .appointment-date {
            font-size: 18px;
            color: #d4af37;
            margin-bottom: 15px;
        }

        .service-list {
            margin: 15px 0;
        }

        .service-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .total-amount {
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            font-weight: bold;
        }

        .btn {
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        #loading {
            display: none;
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #666;
        }

        @media (max-width: 768px) {
            .client-details {
                grid-template-columns: 1fr;
            }
            
            .appointments {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="crown"></div>
            <h1>Prestige Skin Institute</h1>
        </div>

        <div id="loading">
            Waiting for card...
        </div>

        <div id="clientContent" style="display: none;">
            <div class="client-info">
                <div class="client-header">
                    <h2 class="client-name" id="clientName"></h2>
                    <div class="button-group">
                        <button class="btn" id="editInfoBtn">
                            ✏️ Edit Information
                        </button>
                        <button class="btn" id="addAppointmentBtn">
                            ➕ Add New Treatment
                        </button>
                    </div>
                </div>
                <div class="client-details" id="clientDetails">
                    <!-- Client details will be populated here -->
                </div>
            </div>

            <div class="appointments" id="appointmentsList">
                <!-- Appointment cards will be populated here -->
            </div>
        </div>
    </div>

    <script>
        let lastUID = "";
        let lastCheckTime = 0;

        function formatDate(dateString) {
            const options = { 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit'
            };
            return new Date(dateString).toLocaleDateString('en-US', options);
        }

        function formatPrice(price) {
            return new Intl.NumberFormat('en-PH', {
                style: 'currency',
                currency: 'PHP'
            }).format(price);
        }

        function displayClientInfo(data) {
            if (!data.client) {
                console.error('No client data received');
                return;
            }

            const client = data.client;
            document.getElementById('clientName').textContent = client.full_name;
            
            const detailsHtml = `
                <div class="detail-item">
                    <div class="detail-label">Patient ID</div>
                    <div class="detail-value">${client.patient_id || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Sex</div>
                    <div class="detail-value">${client.sex || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Age</div>
                    <div class="detail-value">${client.age || 'N/A'}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Contact</div>
                    <div class="detail-value">${client.contact_number || 'N/A'}</div>
                </div>
            `;
            document.getElementById('clientDetails').innerHTML = detailsHtml;

            // Handle appointments display
            if (data.appointments && Array.isArray(data.appointments)) {
                const appointmentsHtml = data.appointments.map(apt => {
                    const services = apt.services ? apt.services.split(',') : [];
                    const prices = apt.prices ? apt.prices.split(',') : [];
                    
                    return `
                        <div class="appointment-card">
                            <div class="appointment-date">${formatDate(apt.appointment_date)}</div>
                            <div class="detail-item">
                                <div class="detail-label">Aesthetician</div>
                                <div class="detail-value">${apt.aesthetician_name || 'N/A'}</div>
                            </div>
                            <div class="detail-item">
                                <div class="detail-label">Nurse</div>
                                <div class="detail-value">${apt.nurse_name || 'N/A'}</div>
                            </div>
                            <div class="service-list">
                                ${services.map((service, i) => `
                                    <div class="service-item">
                                        <span>${service}</span>
                                        <span>${formatPrice(prices[i] || 0)}</span>
                                    </div>
                                `).join('')}
                            </div>
                            <div class="total-amount">
                                <span>Total</span>
                                <span>${formatPrice(apt.total_amount)}</span>
                            </div>
                            <button class="btn" onclick="location.href='add_appointment.php?id=${apt.id}'">
                                ✏️ Edit
                            </button>
                        </div>
                    `;
                }).join('');
                document.getElementById('appointmentsList').innerHTML = appointmentsHtml || '<p>No appointments found.</p>';
            } else {
                document.getElementById('appointmentsList').innerHTML = '<p>No appointments found.</p>';
            }
        }

        async function checkRFID() {
            try {
                const now = Date.now();
                if (now - lastCheckTime < 500) return; // Prevent too frequent checks
                lastCheckTime = now;

                const response = await fetch('latest_rfid.php');
                if (!response.ok) throw new Error('Server error: ' + response.status);
                
                const data = await response.json();
                console.log('RFID check response:', data);
                
                // Only hide content if we haven't detected any card yet
                if (!lastUID && (!data || data.status === 'waiting' || Object.keys(data).length === 0)) {
                    document.getElementById('loading').style.display = 'block';
                    document.getElementById('clientContent').style.display = 'none';
                    return;
                }

                // If we have a card and it's new
                if (data.rfid_uid && data.rfid_uid !== lastUID) {
                    lastUID = data.rfid_uid;
                    
                    try {
                        const clientResponse = await fetch(`get_client_details.php?uid=${data.rfid_uid}`);
                        if (!clientResponse.ok) throw new Error('Error fetching client details');
                        
                        const clientData = await clientResponse.json();
                        console.log('Client data:', clientData);
                        
                        if (clientData.error) {
                            console.error('Client data error:', clientData.error);
                            return;
                        }

                        document.getElementById('loading').style.display = 'none';
                        document.getElementById('clientContent').style.display = 'block';
                        displayClientInfo(clientData);
                    } catch (err) {
                        console.error('Error fetching client details:', err);
                    }
                }
            } catch (err) {
                console.error('Error in checkRFID:', err);
            }
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', () => {
            // Show loading initially
            document.getElementById('loading').style.display = 'block';
            document.getElementById('clientContent').style.display = 'none';

            // Start RFID checking
            checkRFID();
            setInterval(checkRFID, 500);

            // Add handler for the Add New Treatment button
            document.getElementById('addAppointmentBtn').addEventListener('click', (e) => {
                if (!lastUID) {
                    alert('Please tap an RFID card first before adding a treatment.');
                    return;
                }
                // Redirect to add_appointment.php with the RFID UID
                window.location.href = `add_appointment.php?uid=${encodeURIComponent(lastUID)}`;
            });

            // Edit handler for the Edit Information button
            document.getElementById('editInfoBtn').addEventListener('click', async (e) => {
                if (!lastUID) {
                    alert('Please tap an RFID card first before editing information.');
                    return;
                }
                
                try {
                    // Fetch latest client data first
                    const response = await fetch(`get_client_details.php?uid=${lastUID}`);
                    const data = await response.json();
                    
                    if (data && data.client) {
                        // Redirect with current data in URL
                        window.location.href = `index.php?uid=${encodeURIComponent(lastUID)}&mode=edit`;
                    } else {
                        alert('Error: Could not fetch client data');
                    }
                } catch (err) {
                    console.error('Error fetching client data:', err);
                    alert('Error: Could not fetch client data');
                }
            });
        });
    </script>
</body>
</html>