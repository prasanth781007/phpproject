<?php
require_once 'includes/config.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'viewer_page.php'));
    exit();
}

$message = '';
$error = '';
$show_form = false;
$email = '';

// Check if token is provided
if (isset($_GET['token'])) {
    $token = $_GET['token'];
    
    // Verify token
    $sql = "SELECT email, expires_at FROM password_resets WHERE token = ? AND expires_at > NOW()";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $email = $row['email'];
        $show_form = true;
    } else {
        $error = "Invalid or expired reset link. Please request a new one.";
    }
}

// Handle password reset submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $token = $_POST['token'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validate password
    if (strlen($password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif (!preg_match("/[A-Z]/", $password)) {
        $error = "Password must contain at least one uppercase letter";
    } elseif (!preg_match("/[a-z]/", $password)) {
        $error = "Password must contain at least one lowercase letter";
    } elseif (!preg_match("/[0-9]/", $password)) {
        $error = "Password must contain at least one number";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match";
    } else {
        // Verify token again
        $sql = "SELECT email FROM password_resets WHERE token = ? AND expires_at > NOW()";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $email = $row['email'];
            
            // Hash new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Update user's password
            $update_sql = "UPDATE users SET password = ? WHERE email = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("ss", $hashed_password, $email);
            
            if ($update_stmt->execute()) {
                // Delete used token
                $delete_sql = "DELETE FROM password_resets WHERE email = ?";
                $delete_stmt = $conn->prepare($delete_sql);
                $delete_stmt->bind_param("s", $email);
                $delete_stmt->execute();
                
                $message = "Password has been reset successfully! You can now login with your new password.";
                $show_form = false;
            } else {
                $error = "An error occurred. Please try again.";
            }
        } else {
            $error = "Invalid or expired reset link. Please request a new one.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - MagicalArts</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            overflow-y: auto;
            padding: 60px 0;
        }

        /* Animated Background */
        .bg-bubbles {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
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
            padding: 20px;
        }

        .reset-box {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 30px;
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.3);
            padding: 55px 45px;
            animation: slideUp 0.8s ease;
            border: 1px solid rgba(255, 255, 255, 0.2);
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
            margin-bottom: 45px;
        }

        .header .icon {
            width: 100px;
            height: 100px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .header .icon i {
            font-size: 50px;
            color: white;
        }

        .header h2 {
            color: #333;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .header p {
            color: #666;
            font-size: 14px;
        }

        .message {
            background: #d4edda;
            color: #155724;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 14px;
            border-left: 4px solid #28a745;
        }

        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            text-align: center;
            font-size: 14px;
            border-left: 4px solid #dc3545;
            animation: shake 0.5s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
            20%, 40%, 60%, 80% { transform: translateX(5px); }
        }

        .password-requirements {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            font-size: 13px;
        }

        .password-requirements p {
            color: #666;
            margin-bottom: 8px;
            font-weight: 600;
        }

        .password-requirements ul {
            list-style: none;
        }

        .password-requirements li {
            color: #999;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .password-requirements li i {
            font-size: 12px;
        }

        .password-requirements li.valid {
            color: #28a745;
        }

        .password-requirements li.invalid {
            color: #dc3545;
        }

        .form-group {
            margin-bottom: 35px;
        }

        .form-group label {
            display: block;
            margin-bottom: 14px;
            color: #1a1a2e;
            font-weight: 700;
            font-size: 15px;
        }

        .input-group {
            position: relative;
            display: flex;
            align-items: center;
        }

        .input-group i {
            position: absolute;
            left: 20px;
            color: #a0a0c0;
            font-size: 18px;
            z-index: 2;
            transition: color 0.3s;
        }

        .input-group input {
            width: 100%;
            padding: 18px 55px 18px 55px;
            border: 2px solid #e8e8f0;
            border-radius: 14px;
            font-size: 16px;
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
            right: 15px;
            cursor: pointer;
            color: #999;
            transition: color 0.3s ease;
        }

        .toggle-password:hover {
            color: #667eea;
        }

        .btn-reset {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 15px;
            font-size: 17px;
            font-weight: 700;
            font-family: 'Poppins', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin: 25px 0;
        }

        .btn-reset:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px rgba(102, 126, 234, 0.4);
        }

        .btn-reset i {
            transition: transform 0.3s ease;
        }

        .btn-reset:hover i {
            transform: translateX(5px);
        }

        .back-to-login {
            text-align: center;
        }

        .back-to-login a {
            color: #667eea;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
            transition: color 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .back-to-login a:hover {
            color: #764ba2;
        }

        .back-link {
            text-align: center;
            margin-top: 20px;
        }

        .back-link a {
            color: rgba(255,255,255,0.8);
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .back-link a:hover {
            color: white;
        }

        .back-link a i {
            margin-right: 5px;
        }

        @media (max-width: 480px) {
            .reset-box {
                padding: 30px 20px;
            }

            .header .icon {
                width: 80px;
                height: 80px;
            }

            .header .icon i {
                font-size: 40px;
            }

            .header h2 {
                font-size: 24px;
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
    </ul>

    <div class="container">
        <div class="reset-box">
            <div class="header">
                <div class="icon">
                    <i class="fas fa-lock-open"></i>
                </div>
                <h2>Reset Password</h2>
                <p>Create a new password for your account</p>
            </div>
            
            <?php if ($message): ?>
                <div class="message">
                    <i class="fas fa-check-circle"></i> <?php echo $message; ?>
                    <div style="margin-top: 15px;">
                        <a href="admin/login.php" class="btn-reset" style="display: inline-block; width: auto; padding: 10px 25px;">
                            <i class="fas fa-sign-in-alt"></i> Go to Login
                        </a>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if ($show_form): ?>
                <div class="password-requirements">
                    <p>Password Requirements:</p>
                    <ul id="requirements">
                        <li id="length"><i class="fas fa-circle"></i> At least 8 characters</li>
                        <li id="uppercase"><i class="fas fa-circle"></i> At least one uppercase letter</li>
                        <li id="lowercase"><i class="fas fa-circle"></i> At least one lowercase letter</li>
                        <li id="number"><i class="fas fa-circle"></i> At least one number</li>
                        <li id="match"><i class="fas fa-circle"></i> Passwords match</li>
                    </ul>
                </div>
                
                <form method="POST" action="" id="resetForm">
                    <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                    <input type="hidden" name="reset_password" value="1">
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> New Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" required 
                                   placeholder="Enter new password">
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('password')"></i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password"><i class="fas fa-lock"></i> Confirm Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="confirm_password" name="confirm_password" required 
                                   placeholder="Confirm new password">
                            <i class="fas fa-eye toggle-password" onclick="togglePassword('confirm_password')"></i>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-reset" id="resetBtn">
                        <span>Reset Password</span>
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    
                    <div class="back-to-login">
                        <a href="admin/login.php">
                            <i class="fas fa-arrow-left"></i> Back to Login
                        </a>
                    </div>
                </form>
            <?php elseif (!$message): ?>
                <div class="back-to-login">
                    <a href="forgot_password.php">
                        <i class="fas fa-redo"></i> Request New Reset Link
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="back-link">
            <a href="index.php"><i class="fas fa-home"></i> Back to Homepage</a>
        </div>
    </div>

    <script>
        function togglePassword(fieldId) {
            const password = document.getElementById(fieldId);
            const toggleIcon = password.nextElementSibling;
            
            if (password.type === 'password') {
                password.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        }

        // Real-time password validation
        <?php if ($show_form): ?>
        const password = document.getElementById('password');
        const confirm = document.getElementById('confirm_password');
        
        function validatePassword() {
            const val = password.value;
            
            // Check length
            const lengthReq = document.getElementById('length');
            if (val.length >= 8) {
                lengthReq.className = 'valid';
                lengthReq.innerHTML = '<i class="fas fa-check-circle"></i> At least 8 characters';
            } else {
                lengthReq.className = 'invalid';
                lengthReq.innerHTML = '<i class="fas fa-times-circle"></i> At least 8 characters';
            }
            
            // Check uppercase
            const upperReq = document.getElementById('uppercase');
            if (/[A-Z]/.test(val)) {
                upperReq.className = 'valid';
                upperReq.innerHTML = '<i class="fas fa-check-circle"></i> At least one uppercase letter';
            } else {
                upperReq.className = 'invalid';
                upperReq.innerHTML = '<i class="fas fa-times-circle"></i> At least one uppercase letter';
            }
            
            // Check lowercase
            const lowerReq = document.getElementById('lowercase');
            if (/[a-z]/.test(val)) {
                lowerReq.className = 'valid';
                lowerReq.innerHTML = '<i class="fas fa-check-circle"></i> At least one lowercase letter';
            } else {
                lowerReq.className = 'invalid';
                lowerReq.innerHTML = '<i class="fas fa-times-circle"></i> At least one lowercase letter';
            }
            
            // Check number
            const numReq = document.getElementById('number');
            if (/[0-9]/.test(val)) {
                numReq.className = 'valid';
                numReq.innerHTML = '<i class="fas fa-check-circle"></i> At least one number';
            } else {
                numReq.className = 'invalid';
                numReq.innerHTML = '<i class="fas fa-times-circle"></i> At least one number';
            }
            
            // Check match
            const matchReq = document.getElementById('match');
            if (val && confirm.value && val === confirm.value) {
                matchReq.className = 'valid';
                matchReq.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match';
            } else {
                matchReq.className = 'invalid';
                matchReq.innerHTML = '<i class="fas fa-times-circle"></i> Passwords match';
            }
        }
        
        password.addEventListener('input', validatePassword);
        confirm.addEventListener('input', validatePassword);
        
        // Form submission validation
        document.getElementById('resetForm').addEventListener('submit', function(e) {
            const val = password.value;
            const errors = [];
            
            if (val.length < 8) errors.push('Password must be at least 8 characters');
            if (!/[A-Z]/.test(val)) errors.push('Password must contain an uppercase letter');
            if (!/[a-z]/.test(val)) errors.push('Password must contain a lowercase letter');
            if (!/[0-9]/.test(val)) errors.push('Password must contain a number');
            if (val !== confirm.value) errors.push('Passwords do not match');
            
            if (errors.length > 0) {
                e.preventDefault();
                alert('Please fix the following:\n- ' + errors.join('\n- '));
                return;
            }
            
            // Show loading state
            document.getElementById('resetBtn').innerHTML = '<span>Resetting...</span> <i class="fas fa-spinner fa-spin"></i>';
        });
        <?php endif; ?>
    </script>
</body>
</html>


