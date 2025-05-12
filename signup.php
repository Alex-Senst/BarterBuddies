<?php
// signup.php
require 'config.php'; // Contains DB connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = $_POST['first_name'] . ' ' . $_POST['last_name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password1'], PASSWORD_DEFAULT); // From password1 field
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    // Insert user into database
    $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, phone, address) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $full_name, $email, $password, $phone, $address);
    $stmt->execute();

    echo "<script>
        alert('Registration successful! Await admin approval.');
        setTimeout(function() {
            window.location.href = 'index.php'; // Redirect to homepage
        }, 100); // Delay the redirect by 100ms to allow the alert to be acknowledged
    </script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register Page</title>
    <style>
        * {
        margin: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
        }

        body {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        background-image: url('https://github.com/devression/Login-Form/blob/main/background-image.jpeg?raw=true');
        background-size: cover;
        }

        .glass-container {
        width: 400px;
        padding: 30px 20px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
        border: 1px solid #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        position: relative;
        z-index: 1;
        }

        .glass-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        border-radius: 10px;
        backdrop-filter: blur(5px);
        -webkit-backdrop-filter: blur(5px);
        z-index: -1;
        }

        .register-box {
        text-align: center;
        }

        h1 {
        color: #fff;
        margin-bottom: 20px;
        }

        form {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        }

        input[type="text"],
        input[type="email"],
        input[type="password"],
        input[type="tel"] {
        padding: 10px;
        margin-top: 12px;
        border: none;
        border-radius: 10px;
        background: transparent;
        border: 1px solid #fff;
        color: #fff;
        font-size: 13px;
        width: 100%;
        }

        input::placeholder {
        color: #fff;
        }

        input:focus {
        outline: none;
        }

        input[type="submit"] {
            background: #fff;
            color: black;
            ursor: pointer;
            font-weight: bold;
            margin-top: 15px;
            width: 100%;
            padding: 5px;
            font-size: 14px;
            border-radius: 10px;
        }

        input[type="submit"]:hover {
            background: transparent;
            color: white;
            outline: 1px solid #fff;
        }

        .show-password {
        margin-top: 5px;
        margin-bottom: 10px;
        color: #fff;
        font-size: 12px;
        }

        label {
        color: #fff;
        font-size: 13px;
        margin-top: 10px;
        }

        p {
        font-size: 12px;
        color: #fff;
        margin-top: 15px;
        text-align: center;
        width: 100%;
        }

        .login-link {
        color: #fff;
        font-weight: bold;
        text-decoration: none;
        }

        .login-link:hover {
        text-decoration: underline;
        }
    </style>
</head>
<body>
  <div class="glass-container">
    <div class="register-box">
      <h1>Register now!</h1>
      <form method="POST" onsubmit="return checkPasswords()">
        <input type="text" name="first_name" placeholder="First Name" style="text-transform: capitalize;" required />
        <input type="text" name="last_name" placeholder="Last Name" style="text-transform: capitalize;" required />
        <input type="email" name="email" placeholder="Email" autocomplete="email" required />
        
        <input type="password" id="password1" name="password1" placeholder="Password" required />
        <label class="show-password">
          <input type="checkbox" onclick="togglePassword('password1')"> Show
        </label>
        
        <input type="password" id="password2" name="password2" placeholder="Confirm Password" required />
        <label class="show-password">
          <input type="checkbox" onclick="togglePassword('password2')"> Show
        </label>

        <input type="tel" name="phone" placeholder="Phone Number" pattern="[0-9]{10}" title="Enter 10 digit number" required />
        <input type="text" name="address" placeholder="Address" required />
        <input type="submit" value="Register" />
      </form>
      <p>Already have an account? <a href="login.php" class="login-link">Log In Here!</a></p>
    </div>
  </div>

  <script>
    function togglePassword(id) {
      const input = document.getElementById(id);
      input.type = input.type === "password" ? "text" : "password";
    }

    function checkPasswords() {
      const p1 = document.getElementById("password1").value;
      const p2 = document.getElementById("password2").value;
      if (p1 !== p2) {
        alert("Passwords do not match!");
        return false;
      }
      return true;
    }
  </script>
</body>
</html>
