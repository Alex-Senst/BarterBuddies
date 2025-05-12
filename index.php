<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BarterBuddies Homepage</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KyZXEJ2uB2iPrJ6tQ3b4tczj+d6KN2v2OgeJ3T2UrbGDL0tLw44xQfuRR5qfbHUS" crossorigin="anonymous">

    <!-- Google Fonts for Cursive Font -->
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&display=swap" rel="stylesheet">

    <style>
        /* Large purple strip */
        html, body{
            margin: 0;
            padding: 0;
            height: 100%;
        }
        .header {
            background-color: #260656;
            height: 80px;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
        }

        /* BarterBuddies text on the left */
        .header-title {
            font-size: 36px;
            font-weight: bold;
            color: white;
        }

        /* Background image and overlay */
        .background {
            background-image: url('https://mrstaceydavis.com/wp-content/uploads/2024/11/Bartering-For-Small-Businesses.webp');
            background-size: cover;
            background-position: top center;
            height: 100vh;
            width: 100%;
            position: relative;
        }

        /* Opaque lilac overlay */
        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(148, 0, 211, 0.6);
        }

        /* Main title in center */
        .title {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            font-family: 'Dancing Script', cursive;
            font-size: 72px;
            color: white;
            text-align: center;
        }
        .navbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #260656;; /* Light purple */
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
  
        .navbar-left {
            display: flex;
            align-items: center;
        }

        .navbar-right {
            display: flex;
            align-items: center;
        }  
  
        .logo {
            font-weight: bold;
            font-size: 35px;;
            margin-right: 20px;
            color: white;
            font-family: 'Dancing Script', cursive;
        }
  
        .nav-button {
            margin-right: 15px;
            background-color: #4b0082;
            border: none;
            font-family: 'Tahoma';
            font-size: 17px;
            cursor: pointer;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            transition: background-color 0.2s;
            text-decoration: none;
        }
  
        .nav-button:hover, .nav-button.active {
            background-color: #7412ba; /* Slightly darker purple on hover */
        }
    </style>
</head>
<body>
    <div class="navbar">
        <div class="navbar-left">
          <div class="logo">BarterBuddies</div>
        </div>
        <div class="navbar-right">
          <a href="login.php" class="nav-button">Log In</a>
          <a href="signup.php" class="nav-button">Sign Up</a>
        </div>
    </div>
    <div class="background">
        <div class="overlay"></div>
        <div class="title">BarterBuddies</div>
    </div>

    <!-- Bootstrap JS (optional but useful) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" integrity="sha384-pzjw8f+ua7Kw1TIq0YYA7eGpoh9vO4bwFlHbiQAiPsoXtWn5GlV+2EdGVX5WZjXs" crossorigin="anonymous"></script>

</body>
</html>