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
      <button onclick="closePopup()">❌ Cancel</button>
    </div>
  </div>

  <script>
    let lastUID = "";

    async function checkRFID() {
      try {
        const response = await fetch("latest_rfid.php");
        const data = await response.json();

        if (data && data.rfid_uid && data.rfid_uid !== lastUID) {
          lastUID = data.rfid_uid;
          document.getElementById("status").textContent = "Card Detected!";
          document.getElementById("card-info").textContent = `UID: ${data.rfid_uid}`;
          openPopup(data);
        }
      } catch (err) {
        console.error("Error fetching latest RFID:", err);
      }
    }

    function openPopup(data) {
      document.getElementById("popup").style.display = "block";
      document.getElementById("overlay").style.display = "block";

      document.getElementById("popup-uid").value = data.rfid_uid || "";
      document.getElementById("popup-name").value = data.full_name || "";
      document.getElementById("popup-sex").value = data.sex || "";
      document.getElementById("popup-age").value = data.age || "";
      document.getElementById("popup-weight").value = data.weight || "";
      document.getElementById("popup-height").value = data.height || "";
      document.getElementById("popup-dob").value = data.date_of_birth || "";
      document.getElementById("popup-patient-id").value = data.patient_id || "";
      document.getElementById("popup-blood").value = data.blood_type || "";
      document.getElementById("popup-allergy").value = data.allergy || "";
      document.getElementById("popup-surgery").value = data.past_surgery || "";
      document.getElementById("popup-address").value = data.address || "";
      document.getElementById("popup-contact").value = data.contact_number || "";
      document.getElementById("popup-email").value = data.email || "";
      document.getElementById("popup-emergency").value = data.emergency_contact || "";
      document.getElementById("popup-status").value = data.status || "";
    }

    function closePopup() {
      document.getElementById("popup").style.display = "none";
      document.getElementById("overlay").style.display = "none";
    }

    async function saveInfo() {
      const uid = document.getElementById("popup-uid").value;
      if (!uid) {
        alert("⚠️ No RFID UID found!");
        return;
      }

      const formData = new URLSearchParams();
      formData.append("uid", uid);
      formData.append("full_name", document.getElementById("popup-name").value);
      formData.append("sex", document.getElementById("popup-sex").value);
      formData.append("age", document.getElementById("popup-age").value);
      formData.append("weight", document.getElementById("popup-weight").value);
      formData.append("height", document.getElementById("popup-height").value);
      formData.append("date_of_birth", document.getElementById("popup-dob").value);
      formData.append("patient_id", document.getElementById("popup-patient-id").value);
      formData.append("blood_type", document.getElementById("popup-blood").value);
      formData.append("allergy", document.getElementById("popup-allergy").value);
      formData.append("past_surgery", document.getElementById("popup-surgery").value);
      formData.append("address", document.getElementById("popup-address").value);
      formData.append("contact_number", document.getElementById("popup-contact").value);
      formData.append("email", document.getElementById("popup-email").value);
      formData.append("emergency_contact", document.getElementById("popup-emergency").value);
      formData.append("status", document.getElementById("popup-status").value);

      try {
        const response = await fetch("update_user.php", {
          method: "POST",
          headers: { "Content-Type": "application/x-www-form-urlencoded" },
          body: formData.toString()
        });

        if (!response.ok) {
          alert("⚠️ Server error: " + response.status);
          return;
        }

        const result = await response.json();

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
    });

    setInterval(checkRFID, 2000);
  </script>
</body>
</html>