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

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                // Update last login
                $update = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
                $update->bind_param("i", $user['id']);
                $update->execute();

                if ($user['role'] === 'admin') {
                    header("Location: admin/dashboard.php");
                } else {
                    header("Location: admin/viewer_page.php");
                }
                exit();
            } else {
                $error = "Invalid email or password";
            }
        } else {
            $error = "Invalid email or password";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MagicalArts</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
            position: relative;
            margin: 0;
            overflow-x: hidden;
        }

        .login-box {
            position: relative;
            z-index: 10;
            background: #ffffff;
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 450px;
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
            padding-top: 10px;
        }

        .header .icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
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

        .form-group {
            margin-bottom: 20px;
        }

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
            border-color: #667eea;
            background: #fff;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .btn-login {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .form-footer {
            text-align: center;
            margin-top: 25px;
            font-size: 14px;
            color: #666;
        }

        .form-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .social-login {
            margin-top: 30px;
            text-align: center;
        }

        .social-login p {
            font-size: 12px;
            color: #aaa;
            margin-bottom: 15px;
            position: relative;
        }

        .social-login p::before, .social-login p::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 30%;
            height: 1px;
            background: #eee;
        }

        .social-login p::before { left: 0; }
        .social-login p::after { right: 0; }

        .social-icons {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .social-icons a {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            border: 1px solid #eee;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #555;
            transition: 0.3s;
            text-decoration: none;
        }

        .social-icons a:hover {
            background: #f8f8fc;
            border-color: #667eea;
            color: #667eea;
            transform: scale(1.1);
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

        .back-home:hover {
            opacity: 1;
            transform: translateX(-5px);
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

    <div class="login-box">
        <form method="POST">
            <div class="header">
                <div class="icon">
                    <i class="fas fa-magic"></i>
                </div>
                <h2>Welcome Back</h2>
                <p>Login to access your magical gallery</p>
            </div>

            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="email">Email Address</label>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" id="email" name="email" required placeholder="Enter your email"
                        value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" id="password" name="password" required placeholder="Enter your password">
                </div>
            </div>

            <div style="text-align: right; margin-bottom: 20px;">
                <a href="forgot_password.php" style="font-size: 13px; color: #667eea; text-decoration: none;">Forgot Password?</a>
            </div>

            <button type="submit" class="btn-login">
                <span>Sign In</span>
                <i class="fas fa-sign-in-alt"></i>
            </button>
        </form>

        <div class="social-login">
            <p>OR CONTINUE WITH</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-google"></i></a>
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-apple"></i></a>
            </div>
        </div>

        <div class="form-footer">
            Don't have an account? <a href="register.php">Create Account</a>
        </div>
    </div>
</body>

</html>
