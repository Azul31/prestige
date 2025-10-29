<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prestige Skin Institute</title>
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
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      position: relative;
      overflow-x: hidden;
    }

    body::before {
      content: '';
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-image: 
        radial-gradient(circle at 20% 30%, rgba(218, 165, 32, 0.08) 0%, transparent 50%),
        radial-gradient(circle at 80% 70%, rgba(184, 134, 11, 0.06) 0%, transparent 50%);
      pointer-events: none;
      z-index: 0;
    }

    .container {
      position: relative;
      z-index: 1;
      text-align: center;
      max-width: 600px;
      width: 100%;
    }

    .logo-section {
      margin-bottom: 50px;
    }

    .crown {
      width: 60px;
      height: 50px;
      margin: 0 auto 20px;
      background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
      clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
      filter: drop-shadow(0 4px 8px rgba(212, 175, 55, 0.3));
    }

    h1 {
      font-size: 48px;
      font-weight: 300;
      letter-spacing: 8px;
      color: #2c2c2c;
      margin-bottom: 10px;
      text-transform: uppercase;
    }

    .subtitle {
      font-size: 16px;
      letter-spacing: 4px;
      color: #8b7355;
      font-weight: 400;
      text-transform: uppercase;
    }

    .card-panel {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      border-radius: 24px;
      padding: 60px 40px;
      box-shadow: 0 20px 60px rgba(0, 0, 0, 0.1);
      border: 1px solid rgba(212, 175, 55, 0.2);
    }

    #status {
      font-size: 28px;
      color: #d4af37;
      font-weight: 600;
      margin-bottom: 20px;
      text-transform: uppercase;
      letter-spacing: 2px;
    }

    #card-info {
      font-size: 18px;
      color: #666;
      padding: 15px;
      background: rgba(212, 175, 55, 0.05);
      border-radius: 12px;
      border: 1px solid rgba(212, 175, 55, 0.15);
      min-height: 50px;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    /* Popup Overlay */
    #overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(8px);
      z-index: 9998;
    }

    /* Popup */
    #popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 40px;
      border-radius: 24px;
      box-shadow: 0 30px 80px rgba(0, 0, 0, 0.3);
      width: 90%;
      max-width: 600px;
      max-height: 90vh;
      overflow-y: auto;
      text-align: left;
      z-index: 9999;
      border: 2px solid rgba(212, 175, 55, 0.3);
    }

    #popup::-webkit-scrollbar {
      width: 8px;
    }

    #popup::-webkit-scrollbar-track {
      background: #f1f1f1;
      border-radius: 10px;
    }

    #popup::-webkit-scrollbar-thumb {
      background: linear-gradient(135deg, #d4af37, #f4d03f);
      border-radius: 10px;
    }

    #popup h3 {
      text-align: center;
      color: #2c2c2c;
      margin-bottom: 30px;
      font-size: 28px;
      font-weight: 300;
      letter-spacing: 3px;
      text-transform: uppercase;
      position: relative;
      padding-bottom: 15px;
    }

    #popup h3::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 50%;
      transform: translateX(-50%);
      width: 60px;
      height: 3px;
      background: linear-gradient(90deg, #d4af37, #f4d03f);
      border-radius: 2px;
    }

    fieldset {
      border: 2px solid rgba(212, 175, 55, 0.2);
      border-radius: 16px;
      margin-bottom: 24px;
      padding: 20px;
      background: rgba(250, 248, 245, 0.5);
    }

    legend {
      padding: 0 12px;
      color: #d4af37;
      font-weight: 600;
      font-size: 16px;
      letter-spacing: 1px;
      text-transform: uppercase;
    }

    label {
      display: block;
      margin-top: 16px;
      font-size: 14px;
      color: #555;
      font-weight: 500;
      margin-bottom: 6px;
    }

    #popup input,
    #popup textarea {
      width: 100%;
      padding: 12px 16px;
      margin-top: 4px;
      border-radius: 10px;
      border: 2px solid #e8e8e8;
      box-sizing: border-box;
      font-family: inherit;
      font-size: 15px;
      transition: all 0.3s ease;
      background: white;
    }

    #popup input:focus,
    #popup textarea:focus {
      outline: none;
      border-color: #d4af37;
      box-shadow: 0 0 0 4px rgba(212, 175, 55, 0.1);
    }

    #popup input[readonly] {
      background: #f5f5f5;
      cursor: not-allowed;
    }

    #popup textarea {
      resize: vertical;
      min-height: 80px;
    }

    .button-group {
      text-align: center;
      margin-top: 30px;
      display: flex;
      gap: 12px;
      justify-content: center;
    }

    #popup button {
      background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
      color: white;
      border: none;
      padding: 14px 32px;
      border-radius: 12px;
      cursor: pointer;
      font-size: 16px;
      font-weight: 600;
      letter-spacing: 1px;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(212, 175, 55, 0.3);
      text-transform: uppercase;
    }

    #popup button:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(212, 175, 55, 0.4);
    }

    #popup button:active {
      transform: translateY(0);
    }

    #popup button:last-child {
      background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
      box-shadow: 0 4px 15px rgba(108, 117, 125, 0.3);
    }

    #popup button:last-child:hover {
      box-shadow: 0 6px 20px rgba(108, 117, 125, 0.4);
    }

    @media (max-width: 768px) {
      h1 {
        font-size: 36px;
        letter-spacing: 4px;
      }

      .subtitle {
        font-size: 14px;
        letter-spacing: 2px;
      }

      .card-panel {
        padding: 40px 30px;
      }

      #popup {
        padding: 30px 20px;
      }

      .button-group {
        flex-direction: column;
      }

      #popup button {
        width: 100%;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="logo-section">
      <div class="crown"></div>
      <h1>Prestige</h1>
      <div class="subtitle">Skin Institute</div>
    </div>

    <div class="card-panel">
      <div id="status">Tap the Card</div>
      <div id="card-info"></div>
    </div>
  </div>

  <div id="overlay"></div>

  <div id="popup">
    <h3>Edit Card Information</h3>

    <fieldset>
      <legend>RFID Info</legend>
      <label>UID:</label>
      <input type="text" id="popup-uid" readonly>
      <label>Status:</label>
      <input type="text" id="popup-status" placeholder="Arrived / Checked out">
    </fieldset>

    <fieldset>
      <legend>Personal Details</legend>
      <label>Full Name:</label>
      <input type="text" id="popup-name" placeholder="Enter full name">
      <label>Sex:</label>
      <input type="text" id="popup-sex" placeholder="Male / Female">
      <label>Age:</label>
      <input type="number" id="popup-age" placeholder="Enter age">
      <label>Date of Birth:</label>
      <input type="date" id="popup-dob">
      <label>Height (cm):</label>
      <input type="text" id="popup-height" placeholder="Enter height">
      <label>Weight (kg):</label>
      <input type="text" id="popup-weight" placeholder="Enter weight">
    </fieldset>

    <fieldset>
      <legend>Medical Information</legend>
      <label>Patient ID:</label>
      <input type="text" id="popup-patient-id" placeholder="Enter patient ID">
      <label>Blood Type:</label>
      <input type="text" id="popup-blood" placeholder="e.g. O+, A-, B+">
      <label>Allergies:</label>
      <textarea id="popup-allergy" placeholder="List any allergies"></textarea>
      <label>Past Surgery:</label>
      <textarea id="popup-surgery" placeholder="Describe past surgeries"></textarea>
    </fieldset>

    <fieldset>
      <legend>Contact Details</legend>
      <label>Address:</label>
      <textarea id="popup-address" placeholder="Enter address"></textarea>
      <label>Contact Number:</label>
      <input type="text" id="popup-contact" placeholder="Enter contact number">
      <label>Email:</label>
      <input type="email" id="popup-email" placeholder="Enter email">
      <label>Emergency Contact:</label>
      <input type="text" id="popup-emergency" placeholder="Enter emergency contact">
    </fieldset>

    <div class="button-group">
      <button id="saveBtn">💾 Save</button>
      <button onclick="goToAppointments()">📅 Manage Appointments</button>
      <button onclick="closePopup()">❌ Cancel</button>
    </div>
  </div>

  <script>
    function goToAppointments() {
      const uid = document.getElementById("popup-uid").value;
      if (uid) {
        window.location.href = 'home.php';
      } else {
        alert('Please scan an RFID card first!');
      }
    }

    let lastUID = "";

    async function checkRFID() {
      // Skip the check if popup is open to prevent overwriting user input
      if (isPopupOpen) {
        return;
      }

      try {
        const response = await fetch("latest_rfid.php");
        if (!response.ok) {
          console.error("Server error:", response.status);
          return;
        }

        const data = await response.json();
        console.log("RFID check response:", data); // Debug log

        // Reset status if no card data
        if (!data || (!data.rfid_uid && Object.keys(data).length === 0)) {
          document.getElementById("status").textContent = "Tap the Card";
          document.getElementById("card-info").textContent = "";
          return;
        }

        // If we have a card and it's new
        // Check for edit mode in URL
        const urlParams = new URLSearchParams(window.location.search);
        const editMode = urlParams.get('mode') === 'edit';
        const editUid = urlParams.get('uid');

        if (editMode && editUid) {
            // In edit mode, use the UID from URL
            lastUID = editUid;
            document.getElementById("status").textContent = "Edit Mode";
            document.getElementById("card-info").textContent = `UID: ${editUid}`;
            // Fetch user data and open popup
            fetch(`get_client_details.php?uid=${editUid}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.client) {
                        // Store the fetched data for later reference
                        console.log("Fetched client data:", data.client);
                        // Set lastUID to maintain context
                        lastUID = editUid;
                        // Prepare data with proper field mapping
                        const userData = {
                            rfid_uid: editUid,
                            ...data.client,
                            date_of_birth: data.client.dob // Map dob to date_of_birth
                        };
                        console.log("Prepared user data:", userData);
                        openPopup(userData);
                    }
                })
                .catch(err => console.error('Error fetching user data:', err));
        }
        else if (data.rfid_uid && data.rfid_uid !== lastUID) {
            lastUID = data.rfid_uid;
            document.getElementById("status").textContent = "Card Detected!";
            document.getElementById("card-info").textContent = `UID: ${data.rfid_uid}`;
            console.log("Opening popup for UID:", data.rfid_uid); // Debug log
            openPopup(data);
        }
      } catch (err) {
        console.error("Error fetching latest RFID:", err);
      }
    }

    let isPopupOpen = false; // Add this flag at the top of your script

    function openPopup(data) {
      console.log("Opening popup with data:", data);
      document.getElementById("popup").style.display = "block";
      document.getElementById("overlay").style.display = "block";
      isPopupOpen = true; // Set flag when popup is opened

      // Helper function to set input value safely
      const setInputValue = (id, value) => {
        const element = document.getElementById(id);
        if (element) {
          element.value = value || '';
          // Log the value being set
          console.log(`Setting ${id} to:`, value);
        }
      };

      setInputValue("popup-uid", data.rfid_uid);
      setInputValue("popup-name", data.full_name);
      setInputValue("popup-sex", data.sex);
      setInputValue("popup-age", data.age);
      setInputValue("popup-weight", data.weight);
      setInputValue("popup-height", data.height);
      setInputValue("popup-dob", data.date_of_birth);
      setInputValue("popup-patient-id", data.patient_id);
      setInputValue("popup-blood", data.blood_type);
      setInputValue("popup-allergy", data.allergy);
      setInputValue("popup-surgery", data.past_surgery);
      setInputValue("popup-address", data.address);
      setInputValue("popup-contact", data.contact_number);
      setInputValue("popup-email", data.email);
      setInputValue("popup-emergency", data.emergency_contact);
      setInputValue("popup-status", data.status);
    }

    function closePopup() {
      document.getElementById("popup").style.display = "none";
      document.getElementById("overlay").style.display = "none";
      isPopupOpen = false; // Reset flag when popup is closed
    }

    async function saveInfo() {
      const uid = document.getElementById("popup-uid").value;
      if (!uid) {
        alert("⚠️ No RFID UID found!");
        return;
      }

      // Validate required fields
      const fullName = document.getElementById("popup-name").value.trim();
      if (!fullName) {
        alert("⚠️ Full Name is required!");
        return;
      }

      // Create FormData object
      const formData = new FormData();
      
      // Helper function to safely get input value
      const getInputValue = (id) => {
        const element = document.getElementById(id);
        return element ? element.value : '';
      };

      // Append all form fields
      formData.append("uid", uid);
      formData.append("full_name", fullName);
      formData.append("sex", getInputValue("popup-sex"));
      formData.append("age", getInputValue("popup-age"));
      formData.append("weight", getInputValue("popup-weight"));
      formData.append("height", getInputValue("popup-height"));
      formData.append("date_of_birth", getInputValue("popup-dob"));
      formData.append("patient_id", getInputValue("popup-patient-id"));
      formData.append("allergy", getInputValue("popup-allergy"));
      formData.append("past_surgery", getInputValue("popup-surgery"));
      formData.append("address", getInputValue("popup-address"));
      formData.append("contact_number", getInputValue("popup-contact"));
      formData.append("email", getInputValue("popup-email"));
      formData.append("emergency_contact", getInputValue("popup-emergency"));
      formData.append("status", getInputValue("popup-status") || 'New');

      // Log the data being sent
      console.log("Sending form data:", Object.fromEntries(formData));

      try {
        // Convert FormData to URLSearchParams for proper form submission
        const params = new URLSearchParams();
        for (let pair of formData.entries()) {
            params.append(pair[0], pair[1]);
        }
        
        console.log('Sending data:', Object.fromEntries(params));
        
        const response = await fetch("update_user.php", {
          method: "POST",
          headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
          },
          body: params.toString()
        });

        const result = await response.json();
        console.log('Server response:', result);

        if (!response.ok) {
          throw new Error(result.error || `Server error: ${response.status}`);
        }

        if (result.success) {
          alert("✅ Card information updated successfully!");
          closePopup();
        } else {
          alert("❌ Error updating card info: " + (result.error || "Unknown error"));
        }
      } catch (err) {
        alert("⚠️ Connection error: " + err);
      }
    }

    document.addEventListener("DOMContentLoaded", () => {
      document.getElementById("saveBtn").addEventListener("click", saveInfo);
      // Check immediately when page loads
      checkRFID();
      // Then check every 500ms (much faster than before)
      setInterval(checkRFID, 500);
    });
  </script>
</body>
</html>