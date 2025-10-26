<?php
include 'db_connect.php';

// Only run backend when a POST request comes (from fetch)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        $stmt = $conn->prepare("SELECT * FROM admin WHERE Email = ? AND Password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            echo json_encode([
                "status" => "success",
                "email" => $row['Email']
            ]);
        } else {
            echo json_encode(["status" => "error", "message" => "Invalid email or password"]);
        }

        $stmt->close();
    } else {
        echo json_encode(["status" => "error", "message" => "Missing credentials"]);
    }

    $conn->close();
    exit; // stop PHP from printing HTML below
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Prestige Skin Institute</title> 
  <link rel="stylesheet" href="login.css">
  <link rel="icon" type="image/png" href="img/crown.png">
</head>
<body>
 <div class="login-page" id="loginPage">
    <div class="login-container">

      <div class="logo">
        <div class="line">
          <img src="img/crown.png" alt="Crown" class="crown">
        </div>
        <div class="title"><span>P</span>RJKESTIGE</div>
        <div class="subtitle">SKIN INSTITUTE</div>
      </div>

      <div class="login-right">
        <form class="login-form" id="loginForm">
          <div class="login-header">
            <img src="img/logo.png" alt="Prestige Logo" class="login-logo">
            <h3>WELCOME!</h3>
            <p>Login to access the admin panel.</p>
          </div>

          <div class="form-group floating-label">
            <input type="email" id="email" placeholder=" " required>
            <label for="email">Email</label>
          </div>

          <div class="form-group floating-label password-container">
            <input type="password" id="password" placeholder=" " required>
            <label for="password">Password</label>

            <!-- Password toggle with SVGs -->
            <span class="password-toggle" id="togglePassword" title="Show/Hide">
              <svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                   fill="none" stroke="currentColor" stroke-width="2"
                   stroke-linecap="round" stroke-linejoin="round" width="20" height="20">
                <path d="M2 12c2.6-5 7-8 10-8s7.4 3 10 8c-2.6 5-7 8-10 8S4.6 17 2 12z"/>
                <circle cx="12" cy="12" r="3"/>
              </svg>
              <svg id="eyeClosed" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"
                   fill="none" stroke="currentColor" stroke-width="2"
                   stroke-linecap="round" stroke-linejoin="round" width="20" height="20"
                   style="display:none;">
                <path d="M2 12c2.6-5 7-8 10-8s7.4 3 10 8c-2.6 5-7 8-10 8S4.6 17 2 12z"/>
                <circle cx="12" cy="12" r="3"/>
                <path d="M3 3l18 18"/>
              </svg>
            </span>
          </div>

          <div id="errorMessage" class="error hidden">Invalid email or password.</div>

          <button type="submit" class="login-btn">
            <span id="loginText">LOGIN</span>
            <span id="loginLoading" class="hidden">Loading…</span>
          </button>
        </form>
      </div>

    </div>
  </div>

  <script>
  document.addEventListener('DOMContentLoaded', () => {
    // Password toggle
    const toggle = document.getElementById('togglePassword');
    const input = document.getElementById('password');
    const eyeOpen = document.getElementById('eyeOpen');
    const eyeClosed = document.getElementById('eyeClosed');

    toggle.addEventListener('click', () => {
      const isHidden = input.type === 'password';
      input.type = isHidden ? 'text' : 'password';
      eyeOpen.style.display = isHidden ? 'none' : 'inline';
      eyeClosed.style.display = isHidden ? 'inline' : 'none';
    });

    // Login form
    const form = document.getElementById('loginForm');
    const errorDiv = document.getElementById('errorMessage');
    const loginText = document.getElementById('loginText');
    const loginLoading = document.getElementById('loginLoading');
    const loginBtn = document.querySelector('.login-btn');

    form.addEventListener('submit', async (e) => {
      e.preventDefault();

      const email = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;

      loginText.classList.add('hidden');
      loginLoading.classList.remove('hidden');
      loginBtn.disabled = true;

      try {
        const response = await fetch('login.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: `email=${encodeURIComponent(email)}&password=${encodeURIComponent(password)}`
        });

        const data = await response.json();

        if (data.status === 'success') {
          sessionStorage.setItem('currentUser', JSON.stringify(data));
          window.location.href = 'dashboard.html';
        } else {
          errorDiv.textContent = data.message;
          errorDiv.classList.remove('hidden');
        }
      } catch (error) {
        errorDiv.textContent = 'Server error, please try again.';
        errorDiv.classList.remove('hidden');
      }

      loginText.classList.remove('hidden');
      loginLoading.classList.add('hidden');
      loginBtn.disabled = false;
    });
  });
  </script>
</body>
</html>
