<?php
// login.php
require 'config.php'; // Contains DB connection
session_start();

// Clear any previous message
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user from database
    //$stmt = $conn->prepare("SELECT user_id, password, is_approved, is_admin, suspended FROM users WHERE email = ?");
    $stmt = $conn->prepare("SELECT user_id, password, is_admin, status FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    //$stmt->bind_result($user_id, $hashed_password, $is_approved, $is_admin, $suspended);
    $stmt->bind_result($user_id, $hashed_password, $is_admin, $status);
    $stmt->fetch();

    if ($status === 'suspended') {
      $message = "Your account has been suspended. Contact support.";
    } elseif ($status === 'pending' && password_verify($password, $hashed_password)) {
        $message = "Your account is pending approval.";
    } elseif ($status === 'approved' && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $user_id;

        if ($is_admin) {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit();
    } else {
        $message = "Invalid credentials.";
    }

    // Store the message in session
    $_SESSION['message'] = $message;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
      width: 300px;
      height: 350px;
      position: relative;
      background: rgba(255, 255, 255, 0.1);
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      border-radius: 10px;
      border: 1px solid #fff;
      z-index: 1;
    }

    .glass-container::before {
      content: '';
      position: absolute;
      width: 100%;
      height: 100%;
      border-radius: 10px;
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      z-index: -1;
    }

    .login-box {
      max-width: 250px;
      margin: 0 auto;
      text-align: center;
    }

    h2 {
      color: #fff;
      margin-top: 30px;
      margin-bottom: -20px;
    }

    form {
      display: flex;
      flex-direction: column;
      margin-top: 40px;
    }

    input {
      padding: 10px;
      margin-top: 20px;
      border: none;
      border-radius: 10px;
      background: transparent;
      border: 1px solid #fff;
      color: #fff;
      font-size: 13px;
    }

    input::placeholder {
      color: #fff;
    }

    input:focus {
      outline: none;
    }

    button {
      background: #fff;
      color: black;
      padding: 10px;
      border: none;
      border-radius: 10px;
      cursor: pointer;
      margin-top: 25px;
      font-weight: bold;
    }

    button:hover {
      background: transparent;
      color: white;
      outline: 1px solid #fff;
    }

    p {
      font-size: 12px;
      color: #fff;
      margin-top: 15px;
    }

    #register {
      text-decoration: none;
      color: #fff;
      font-weight: bold;
    }
    
    .error-message {
      color: red;
    }
    </style>
</head>
<body>
    <div class="glass-container">
        <div class="login-box">
            <h2>Log In</h2>

            <!-- Display error message if set -->
            <?php if (isset($_SESSION['message']) && !empty($_SESSION['message'])): ?>
                <script>
                    alert('<?php echo $_SESSION['message']; ?>');
                    // Remove the message after displaying the alert
                    <?php unset($_SESSION['message']); ?>
                </script>
            <?php endif; ?>

            
            <form method="POST">
                <input type="email" name="email" placeholder="Email" required />
                <input type="password" name="password" placeholder="Password" required />
                <button type="submit">Login</button>
            </form>
            <p style="margin-top: 20px; font-size: 16px;">
                Not signed up? <a href="signup.php" style="color: white;">Click here</a>.
            </p>
        </div>
    </div>

    <script>
        // Display JavaScript alert box for non-credential errors on first attempt
        <?php if ($message && strpos($message, "Invalid credentials") === false): ?>
            alert('<?php echo $message; ?>');
        <?php endif; ?>
    </script>
</body>
</html>
