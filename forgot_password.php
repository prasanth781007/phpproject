<?php
require_once 'includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'viewer_page.php'));
    exit();
}

$error = '';
$step = 'verify'; // verify or reset
$verified_email = '';

// Step 1: Verify email + phone
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['verify_identity'])) {
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if (empty($email) || empty($phone)) {
        $error = "Please enter both email and phone number";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        // Check if email + phone match
        $sql = "SELECT id, email FROM users WHERE email = ? AND phone = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $email, $phone);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $step = 'reset';
            $verified_email = $email;
        } else {
            $error = "Email and phone number do not match any account";
        }
    }
}

// Step 2: Set new password
$success = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $email = $_POST['verified_email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($password)) {
        $error = "Please enter a new password";
        $step = 'reset';
        $verified_email = $email;
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters";
        $step = 'reset';
        $verified_email = $email;
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match";
        $step = 'reset';
        $verified_email = $email;
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update->bind_param("ss", $hashed, $email);

        if ($update->execute() && $update->affected_rows > 0) {
            $success = true;
        } else {
            $error = "Failed to update password. Please try again.";
            $step = 'reset';
            $verified_email = $email;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - MagicalArts</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow-x: hidden;
            padding: 20px;
        }

        /* Animated Background */
        .bg-bubbles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
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

        .bg-bubbles li:nth-child(1) {
            left: 10%;
            width: 80px;
            height: 80px;
            animation-delay: 0s;
            animation-duration: 12s;
        }

        .bg-bubbles li:nth-child(2) {
            left: 20%;
            width: 40px;
            height: 40px;
            animation-delay: 2s;
            animation-duration: 15s;
        }

        .bg-bubbles li:nth-child(3) {
            left: 25%;
            width: 110px;
            height: 110px;
            animation-delay: 4s;
        }

        .bg-bubbles li:nth-child(4) {
            left: 40%;
            width: 60px;
            height: 60px;
            animation-delay: 0s;
            animation-duration: 18s;
        }

        .bg-bubbles li:nth-child(5) {
            left: 70%;
            width: 50px;
            height: 50px;
            animation-delay: 0s;
        }

        .bg-bubbles li:nth-child(6) {
            left: 80%;
            width: 70px;
            height: 70px;
            animation-delay: 3s;
        }

        .bg-bubbles li:nth-child(7) {
            left: 55%;
            width: 90px;
            height: 90px;
            animation-delay: 7s;
            animation-duration: 20s;
        }

        .bg-bubbles li:nth-child(8) {
            left: 90%;
            width: 45px;
            height: 45px;
            animation-delay: 5s;
            animation-duration: 16s;
        }

        @keyframes square {
            0% {
                transform: translateY(0);
                opacity: 0;
            }

            50% {
                opacity: 0.5;
            }

            100% {
                transform: translateY(-1000px) rotate(600deg);
                opacity: 0;
            }
        }

        .container {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 500px;
            margin: 0 auto;
        }

        .forgot-box {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            padding: 45px 40px;
            animation: slideUp 0.8s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
            width: 100%;
            position: relative;
            z-index: 100;
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

        .header {
            text-align: center;
            margin-bottom: 35px;
        }

        .header .icon {
            width: 90px;
            height: 90px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .header .icon i {
            font-size: 45px;
            color: white;
        }

        .header h2 {
            color: #333;
            font-size: 26px;
            margin-bottom: 8px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 25px;
        }

        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e0e0e0;
            transition: all 0.3s ease;
        }

        .step-dot.active {
            background: #667eea;
            transform: scale(1.3);
        }

        .step-dot.done {
            background: #28a745;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 14px;
            border-left: 4px solid #dc3545;
            animation: shake 0.5s ease;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 15px;
            border-left: 4px solid #28a745;
        }

        .success-message i {
            font-size: 50px;
            display: block;
            margin-bottom: 15px;
            color: #28a745;
        }

        @keyframes shake {

            0%,
            100% {
                transform: translateX(0);
            }

            10%,
            30%,
            50%,
            70%,
            90% {
                transform: translateX(-5px);
            }

            20%,
            40%,
            60%,
            80% {
                transform: translateX(5px);
            }
        }

        .form-group {
            margin-bottom: 25px;
        }

        .form-group label {
            display: block;
            margin-bottom: 10px;
            color: #1a1a2e;
            font-weight: 600;
            font-size: 14px;
            letter-spacing: 0.3px;
        }

        .form-group label i {
            color: #667eea;
            margin-right: 8px;
            width: 18px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group i.input-icon {
            position: absolute;
            left: 18px;
            color: #a0a0c0;
            font-size: 18px;
            z-index: 2;
            transition: color 0.3s;
        }

        .input-group input {
            width: 100%;
            padding: 16px 20px 16px 50px;
            border: 2px solid #e8e8f0;
            border-radius: 14px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            color: #333;
            background: #f8f8fc;
            transition: all 0.3s ease;
        }

        .input-group input::placeholder {
            color: #b0b0c0;
        }

        .input-group input:focus {
            outline: none;
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.12);
        }

        .toggle-password {
            position: absolute;
            right: 18px;
            cursor: pointer;
            color: #999;
            font-size: 18px;
            z-index: 2;
            transition: color 0.3s;
        }

        .toggle-password:hover {
            color: #667eea;
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 14px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 20px 0 15px;
        }

        .btn-submit:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .btn-submit i {
            transition: transform 0.3s ease;
        }

        .btn-submit:hover i {
            transform: translateX(5px);
        }

        .form-links {
            text-align: center;
            margin: 15px 0;
        }

        .form-links a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .form-links a:hover {
            color: #764ba2;
        }

        .info-text {
            background: #e8f4fd;
            border-left: 4px solid #2196F3;
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            font-size: 13px;
            color: #0c5460;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .info-text i {
            color: #2196F3;
            font-size: 20px;
        }

        .back-links {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #f0f0f0;
            flex-wrap: wrap;
        }

        .back-link {
            text-align: center;
        }

        .back-link a {
            color: #666;
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .back-link a:hover {
            color: #667eea;
        }

        .back-link a i {
            font-size: 14px;
        }

        .footer-links {
            text-align: center;
            margin-top: 20px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            font-size: 13px;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .footer-links a:hover {
            color: white;
        }

        .divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, #e0e0e0, transparent);
            margin: 25px 0 20px;
        }

        @media (max-width: 480px) {
            .forgot-box {
                padding: 30px 20px;
            }

            .header .icon {
                width: 75px;
                height: 75px;
            }

            .header .icon i {
                font-size: 35px;
            }

            .header h2 {
                font-size: 22px;
            }

            .back-links {
                flex-direction: column;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <!-- Animated Background -->
    <ul class="bg-bubbles">
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
        <li></li>
    </ul>

    <div class="container">
        <?php if ($success): ?>
            <!-- Password Reset Success -->
            <div class="forgot-box">
                <div class="header">
                    <div class="icon" style="background: linear-gradient(135deg, #28a745, #20c997);">
                        <i class="fas fa-check"></i>
                    </div>
                    <h2>Password Updated!</h2>
                    <p>Your password has been changed successfully</p>
                </div>

                <div class="success-message">
                    <i class="fas fa-check-circle"></i>
                    <strong>Success!</strong><br>
                    You can now login with your new password.
                </div>

                <a href="admin/login.php" class="btn-submit" style="text-decoration: none;">
                    <i class="fas fa-sign-in-alt"></i> Go to Login
                </a>

                <div class="footer-links">
                    <a href="index.php"><i class="fas fa-home"></i> Back to Homepage</a>
                </div>
            </div>

        <?php elseif ($step === 'reset'): ?>
            <!-- Step 2: Create New Password -->

            <div class="step-indicator">
                <div class="step-dot done"></div>
                <div class="step-dot active"></div>
            </div>

            <div class="divider"></div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <input type="hidden" name="reset_password" value="1">
                <input type="hidden" name="verified_email" value="<?php echo htmlspecialchars($verified_email); ?>">

                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> New Password</label>
                    <div class="input-group">
                        <i class="fas fa-lock input-icon"></i>
                        <input type="password" id="password" name="password" required
                            placeholder="Enter new password (min 6 characters)" minlength="6">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('password')"></i>
                    </div>
                </div>

                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-shield-alt"></i> Confirm Password</label>
                    <div class="input-group">
                        <i class="fas fa-shield-alt input-icon"></i>
                        <input type="password" id="confirm_password" name="confirm_password" required
                            placeholder="Re-enter your new password" minlength="6">
                        <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
                    </div>
                </div>

                <div class="info-text">
                    <i class="fas fa-info-circle"></i>
                    <span>Password must be at least 6 characters long. Both fields must match.</span>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Update Password
                </button>

                <div class="form-links">
                    <a href="forgot_password.php"><i class="fas fa-arrow-left"></i> Start Over</a>
                </div>
            </form>

            <div class="footer-links">
                <a href="index.php"><i class="fas fa-home"></i> Back to Homepage</a>
            </div>
        </div>

    <?php else: ?>
        <!-- Step 1: Verify Email + Phone -->
        <div class="forgot-box">
            <div class="header">
                <div class="icon">
                    <i class="fas fa-key"></i>
                </div>
                <h2>Forgot Password?</h2>
                <p>Verify your identity to reset your password</p>
            </div>

            <div class="step-indicator">
                <div class="step-dot active"></div>
                <div class="step-dot"></div>
            </div>

            <div class="divider"></div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="info-text">
                <i class="fas fa-info-circle"></i>
                <span>Enter your registered email and phone number to verify your identity.</span>
            </div>

            <form method="POST" action="">
                <input type="hidden" name="verify_identity" value="1">

                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                    <div class="input-group">
                        <i class="fas fa-envelope input-icon"></i>
                        <input type="email" id="email" name="email" required placeholder="Enter your email"
                            value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <div class="input-group">
                        <i class="fas fa-phone input-icon"></i>
                        <input type="tel" id="phone" name="phone" required placeholder="Enter your phone number"
                            value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                    </div>
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fas fa-shield-alt"></i> Verify & Continue
                </button>

                <div class="form-links">
                    <a href="admin/login.php"><i class="fas fa-arrow-left"></i> Back to Login</a>
                </div>
            </form>

            <div class="back-links">
                <div class="back-link">
                    <a href="index.php"><i class="fas fa-home"></i> Back to Homepage</a>
                </div>
                <div class="back-link">
                    <a href="admin/login.php"><i class="fas fa-user"></i> User Login</a>
                </div>
                <div class="back-link">
                    <a href="admin/login.php"><i class="fas fa-user-shield"></i> Admin Login</a>
                </div>
            </div>
        </div>
    <?php endif; ?>
    </div>

    <script>
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = field.nextElementSibling;

            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }
    </script>
</body>

</html>


