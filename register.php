<?php
require_once 'includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header("Location: admin/dashboard.php");
    } else {
        header("Location: admin/viewer_page.php");
    }
    exit();
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Validation
    $errors = [];

    if (empty($username)) {
        $errors[] = "Username is required";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username must be at least 3 characters";
    } elseif (!preg_match("/^[a-zA-Z0-9_]+$/", $username)) {
        $errors[] = "Username can only contain letters, numbers, and underscores";
    }

    if (empty($email)) {
        $errors[] = "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address";
    }

    // Check if email already exists
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    if ($check_result->num_rows > 0) {
        $errors[] = "Email already registered";
    }

    // Check if username already exists
    $check_username_sql = "SELECT id FROM users WHERE username = ?";
    $check_username_stmt = $conn->prepare($check_username_sql);
    $check_username_stmt->bind_param("s", $username);
    $check_username_stmt->execute();
    $check_username_result = $check_username_stmt->get_result();
    if ($check_username_result->num_rows > 0) {
        $errors[] = "Username already taken";
    }

    // Password validation
    if (empty($password)) {
        $errors[] = "Password is required";
    } elseif (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    } elseif (!preg_match("/[a-z]/", $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    } elseif (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Password must contain at least one number";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match";
    }

    if (empty($errors)) {
        // Hash password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert new viewer user
        $sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'viewer')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            $message = "Your magical account has been created successfully! You can now login.";

            // Send welcome email
            $to = $email;
            $subject = "Welcome to MagicalArts";

            $message_body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
                    .content { background: #f9f9f9; padding: 30px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Welcome to MagicalArts!</h2>
                    </div>
                    <div class='content'>
                        <p>Hello <strong>$username</strong>,</p>
                        <p>Your account has been created successfully!</p>
                        <p>Explore our magical gallery and order your own portraits.</p>
                        <p><a href='" . BASE_URL . "login.php' style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Login to Your Account</a></p>
                        <p>Best regards,<br>The MagicalArts Team</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            $headers = "MIME-Version: 1.0\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8\r\n";
            $headers .= "From: noreply@magicalarts.com\r\n";

            @mail($to, $subject, $message_body, $headers);
            $_POST = [];
        } else {
            $error = "Registration failed. Please try again.";
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MagicalArts</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #a8c0ff 0%, #3f2b96 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 40px 20px;
            position: relative;
            margin: 0;
            overflow-x: hidden;
        }

        .register-box {
            position: relative;
            z-index: 10;
            background: #ffffff;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 550px;
            animation: slideUp 0.8s ease;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(50px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .bg-bubbles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .bg-bubbles li {
            position: absolute;
            list-style: none;
            display: block;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.15);
            bottom: -160px;
            animation: square 25s infinite;
            transition-timing-function: linear;
            border-radius: 50%;
        }

        .bg-bubbles li:nth-child(1) { left: 10%; width: 80px; height: 80px; animation-duration: 12s; }
        .bg-bubbles li:nth-child(2) { left: 20%; width: 40px; height: 40px; animation-delay: 2s; animation-duration: 15s; }
        .bg-bubbles li:nth-child(3) { left: 25%; width: 110px; height: 110px; animation-delay: 4s; }
        .bg-bubbles li:nth-child(4) { left: 40%; width: 60px; height: 60px; animation-duration: 18s; }
        .bg-bubbles li:nth-child(5) { left: 70%; width: 50px; height: 50px; }
        .bg-bubbles li:nth-child(6) { left: 80%; width: 70px; height: 70px; animation-delay: 3s; }

        @keyframes square {
            0% { transform: translateY(0); opacity: 0; }
            50% { opacity: 0.5; }
            100% { transform: translateY(-1000px) rotate(600deg); opacity: 0; }
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .header .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #a8c0ff 0%, #3f2b96 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 10px 20px rgba(63, 43, 150, 0.3);
        }

        .header .icon i {
            font-size: 40px;
            color: white;
        }

        .header h2 {
            color: #333;
            font-size: 26px;
            margin-bottom: 5px;
            font-weight: 700;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 13px;
            border-left: 4px solid #dc3545;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
            font-size: 14px;
            border-left: 4px solid #28a745;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }

        .input-group {
            position: relative;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #a0a0c0;
            font-size: 16px;
        }

        .input-group input {
            width: 100%;
            padding: 12px 15px 12px 45px;
            border: 2px solid #e8e8f0;
            border-radius: 12px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif;
            color: #333;
            background: #f8f8fc;
            transition: all 0.3s ease;
            outline: none;
        }

        .input-group input:focus {
            border-color: #3f2b96;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(63, 43, 150, 0.1);
        }

        .btn-register {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #a8c0ff 0%, #3f2b96 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(63, 43, 150, 0.4);
        }

        .form-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }

        .form-footer a {
            color: #3f2b96;
            text-decoration: none;
            font-weight: 600;
        }

        .back-home {
            position: absolute;
            top: 20px;
            left: 20px;
            color: white;
            text-decoration: none;
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 8px;
            z-index: 20;
            opacity: 0.8;
            transition: 0.3s;
        }

        .back-home:hover { opacity: 1; transform: translateX(-5px); }

        @media (max-width: 600px) {
            .form-row { grid-template-columns: 1fr; gap: 0; }
            .register-box { padding: 30px 20px; }
        }
    </style>
</head>

<body>
    <a href="index.php" class="back-home">
        <i class="fas fa-arrow-left"></i> Back to Homepage
    </a>

    <!-- Animated Background -->
    <ul class="bg-bubbles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>

    <div class="register-box">
        <form method="POST">
            <div class="header" style="padding-top: 10px;">
                <div class="icon">
                    <i class="fas fa-user-plus"></i>
                </div>
                <h2>Create Your Account</h2>
                <p>Experience the magic of custom portraits</p>
                <div style="margin-top: 15px;">
                    <a href="gallery.php" style="color: #3f2b96; text-decoration: none; font-size: 14px; font-weight: 600;">
                        <i class="fas fa-magic"></i> Explore our previous work
                    </a>
                </div>
            </div>

            <?php if ($message): ?>
                <div class="success-message">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                    <div style="margin-top: 10px;">
                        <a href="login.php" style="color: #155724; font-weight: 700;">Login Here</a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>

            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" id="username" name="username" required placeholder="User_123"
                            value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">Email Address</label>
                    <div class="input-group">
                        <i class="fas fa-envelope"></i>
                        <input type="email" id="email" name="email" required placeholder="name@example.com"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="password" name="password" required placeholder="8+ characters">
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" id="confirm_password" name="confirm_password" required placeholder="Repeat password">
                    </div>
                </div>
            </div>

            <button type="submit" class="btn-register">
                <span>Create Magical Account</span>
                <i class="fas fa-sparkles"></i>
            </button>
        </form>

        <div class="form-footer">
            Already have an account? <a href="login.php">Sign In Here</a>
        </div>
    </div>
</body>

</html>
