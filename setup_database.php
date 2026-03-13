<?php
// Turn on error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'magicalarts_db');

// Create connection without database selection first
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if ($conn->query($sql) === TRUE) {
    echo "✅ Database '" . DB_NAME . "' created successfully or already exists.<br>";
} else {
    die("❌ Error creating database: " . $conn->error);
}

// Select the database
$conn->select_db(DB_NAME);

// Set charset
$conn->set_charset("utf8mb4");

echo "<h2>🔧 Setting up MagicalArts Database...</h2>";

// Drop existing tables if they exist (optional - comment out if you want to keep data)
// $conn->query("DROP TABLE IF EXISTS password_resets");
// $conn->query("DROP TABLE IF EXISTS users");
// $conn->query("DROP TABLE IF EXISTS orders");

// Create users table
$sql_users = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'viewer') DEFAULT 'viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    is_active BOOLEAN DEFAULT TRUE,
    phone VARCHAR(20) DEFAULT NULL,
    profile_pic VARCHAR(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_users)) {
    echo "✅ Table 'users' created successfully.<br>";
} else {
    echo "❌ Error creating users table: " . $conn->error . "<br>";
}

// Create orders table
$sql_orders = "CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    image_path VARCHAR(255) NOT NULL,
    delivery_date DATE NOT NULL,
    drawing_size VARCHAR(50) NOT NULL,
    color_type ENUM('color', 'black_white') NOT NULL,
    special_instructions TEXT,
    order_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'processing', 'completed', 'delivered') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'refunded') DEFAULT 'pending',
    amount DECIMAL(10,2) DEFAULT 0.00,
    notes TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_orders)) {
    echo "✅ Table 'orders' created successfully.<br>";
} else {
    echo "❌ Error creating orders table: " . $conn->error . "<br>";
}

// Create password_resets table
$sql_resets = "CREATE TABLE IF NOT EXISTS password_resets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used BOOLEAN DEFAULT FALSE,
    INDEX(email),
    INDEX(token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_resets)) {
    echo "✅ Table 'password_resets' created successfully.<br>";
} else {
    echo "❌ Error creating password_resets table: " . $conn->error . "<br>";
}

// Create sessions table (optional - for better session management)
$sql_sessions = "CREATE TABLE IF NOT EXISTS user_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    session_token VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at DATETIME,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX(session_token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_sessions)) {
    echo "✅ Table 'user_sessions' created successfully.<br>";
} else {
    echo "❌ Error creating user_sessions table: " . $conn->error . "<br>";
}

// Create activity_log table (for auditing)
$sql_logs = "CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

if ($conn->query($sql_logs)) {
    echo "✅ Table 'activity_log' created successfully.<br>";
} else {
    echo "❌ Error creating activity_log table: " . $conn->error . "<br>";
}

echo "<h3>👥 Creating default users...</h3>";

// Function to create password hash
function createPasswordHash($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Default admin users with different passwords
$default_users = [
    // Admin users
    ['admin', 'admin@magicalarts.com', 'prasanth', 'admin'],
    ['superadmin', 'superadmin@magicalarts.com', 'prasanth', 'admin'],
    ['john_admin', 'john@magicalarts.com', 'prasanth', 'admin'],
    ['sarah_admin', 'sarah@magicalarts.com', 'prasanth', 'admin'],
    ['mike_admin', 'mike@magicalarts.com', 'prasanth', 'admin'],
    
    // Viewer users
    ['viewer', 'viewer@magicalarts.com', 'prasanth', 'viewer'],
    ['alice_viewer', 'alice@example.com', 'prasanth', 'viewer'],
    ['bob_viewer', 'bob@example.com', 'prasanth', 'viewer'],
    ['charlie_viewer', 'charlie@example.com', 'prasanth', 'viewer'],
    ['diana_viewer', 'diana@example.com', 'prasanth', 'viewer']
];

$user_count = 0;
foreach ($default_users as $user) {
    $username = $user[0];
    $email = $user[1];
    $password = createPasswordHash($user[2]);
    $role = $user[3];
    
    // Check if user already exists
    $check = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $check->bind_param("ss", $email, $username);
    $check->execute();
    $check->store_result();
    
    if ($check->num_rows == 0) {
        $insert = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
        $insert->bind_param("ssss", $username, $email, $password, $role);
        
        if ($insert->execute()) {
            $user_count++;
            echo "✅ Created user: <strong>$username</strong> ($role) - Email: $email, Password: {$user[2]}<br>";
        }
    } else {
        echo "⏭️ User <strong>$username</strong> already exists, skipping.<br>";
    }
}

echo "<p>📊 Total new users created: $user_count</p>";

// Create sample orders
echo "<h3>🖼️ Creating sample orders...</h3>";

// First, ensure uploads directory exists
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
    echo "✅ Created uploads directory.<br>";
}

// Sample orders data
$sample_orders = [
    ['John Doe', 'john@example.com', 'uploads/sample1.jpg', date('Y-m-d', strtotime('+10 days')), '8x10', 'color', 'Please focus on the eyes', 'pending'],
    ['Jane Smith', 'jane@example.com', 'uploads/sample2.jpg', date('Y-m-d', strtotime('+15 days')), '11x14', 'black_white', 'Add magical background', 'processing'],
    ['Bob Johnson', 'bob@example.com', 'uploads/sample3.jpg', date('Y-m-d', strtotime('+7 days')), '12x16', 'color', 'Make it look like a fantasy scene', 'completed'],
    ['Alice Williams', 'alice@example.com', 'uploads/sample4.jpg', date('Y-m-d', strtotime('+20 days')), '16x20', 'color', 'Include family pet', 'pending'],
    ['Charlie Brown', 'charlie@example.com', 'uploads/sample5.jpg', date('Y-m-d', strtotime('+5 days')), '8x10', 'black_white', 'Vintage style please', 'delivered'],
    ['Diana Prince', 'diana@example.com', 'uploads/sample6.jpg', date('Y-m-d', strtotime('+12 days')), '18x24', 'color', 'Make it epic', 'processing'],
    ['Edward Norton', 'edward@example.com', 'uploads/sample7.jpg', date('Y-m-d', strtotime('+8 days')), '11x14', 'black_white', 'Dramatic lighting', 'pending'],
    ['Fiona Apple', 'fiona@example.com', 'uploads/sample8.jpg', date('Y-m-d', strtotime('+25 days')), '12x16', 'color', 'Add floral elements', 'completed']
];

$order_count = 0;
foreach ($sample_orders as $order) {
    $check_order = $conn->prepare("SELECT id FROM orders WHERE customer_email = ? AND order_date >= DATE_SUB(NOW(), INTERVAL 1 DAY)");
    $check_order->bind_param("s", $order[1]);
    $check_order->execute();
    $check_order->store_result();
    
    if ($check_order->num_rows == 0) {
        $insert = $conn->prepare("INSERT INTO orders (customer_name, customer_email, image_path, delivery_date, drawing_size, color_type, special_instructions, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->bind_param("ssssssss", $order[0], $order[1], $order[2], $order[3], $order[4], $order[5], $order[6], $order[7]);
        
        if ($insert->execute()) {
            $order_count++;
            echo "✅ Created sample order for: <strong>{$order[0]}</strong><br>";
            
            // Create placeholder image files (you'd replace these with actual images)
            $image_file = 'uploads/sample' . $order_count . '.jpg';
            if (!file_exists($image_file)) {
                // Create a simple placeholder image (you'd copy actual images here)
                file_put_contents($image_file, 'Placeholder for order image');
            }
        }
    }
}

echo "<p>📊 Total sample orders created: $order_count</p>";

// Display summary
echo "<h2>📋 Setup Complete!</h2>";
echo "<div style='background: #f0f0f0; padding: 20px; border-radius: 10px; margin-top: 20px;'>";
echo "<h3>🔑 Default Login Credentials:</h3>";
echo "<table border='1' cellpadding='8' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Username</th><th>Email</th><th>Password</th><th>Role</th><th>Login Page</th></tr>";
echo "<tr><td>admin</td><td>admin@magicalarts.com</td><td>prasanth</td><td>Admin</td><td><a href='login.php'>Login</a></td></tr>";
echo "<tr><td>superadmin</td><td>superadmin@magicalarts.com</td><td>prasanth</td><td>Admin</td><td><a href='login.php'>Login</a></td></tr>";
echo "<tr><td>viewer</td><td>viewer@magicalarts.com</td><td>prasanth</td><td>Viewer</td><td><a href='login.php'>Login</a></td></tr>";
echo "<tr><td>john_admin</td><td>john@magicalarts.com</td><td>prasanth</td><td>Admin</td><td><a href='login.php'>Login</a></td></tr>";
echo "<tr><td>sarah_admin</td><td>sarah@magicalarts.com</td><td>prasanth</td><td>Admin</td><td><a href='login.php'>Login</a></td></tr>";
echo "</table>";

echo "<h3>🔐 Admin Registration Codes:</h3>";
echo "<ul>";
echo "<li><code>ADMIN2024</code></li>";
echo "<li><code>MAGICALARTS</code></li>";
echo "<li><code>SECRETCODE</code></li>";
echo "</ul>";

echo "<h3>🌐 Important Links:</h3>";
echo "<ul>";
echo "<li><a href='index.php'>🏠 Homepage</a></li>";
echo "<li><a href='login.php'>🔑 User Login</a></li>";
echo "<li><a href='admin/login.php'>👑 Admin Login</a></li>";
echo "<li><a href='admin_register.php'>📝 Register as Admin</a></li>";
echo "<li><a href='order.php'>🖼️ Place Order</a></li>";
echo "<li><a href='viewer_page.php'>👁️ Viewer Gallery</a></li>";
echo "<li><a href='admin/dashboard.php'>📊 Admin Dashboard</a></li>";
echo "<li><a href='forgot_password.php'>❓ Forgot Password</a></li>";
echo "</ul>";

echo "<h3>📁 Directory Structure:</h3>";
echo "<pre>";
echo "magicalarts/\n";
echo "├── uploads/ (Image storage directory)\n";
echo "├── index.php\n";
echo "├── login.php\n";
echo "├── admin/login.php\n";
echo "├── admin_register.php\n";
echo "├── admin/dashboard.php\n";
echo "├── admin/orders.php\n";
echo "├── admin/users.php\n";
echo "├── admin_create_user.php\n";
echo "├── viewer_page.php\n";
echo "├── order.php\n";
echo "├── forgot_password.php\n";
echo "├── reset_password.php\n";
echo "├── logout.php\n";
echo "├── config.php\n";
echo "└── setup_database.php (this file)\n";
echo "</pre>";

echo "<p style='color: green; font-weight: bold;'>✅ Database setup completed successfully!</p>";
echo "<p><a href='index.php' style='background: #667eea; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Homepage</a></p>";

echo "</div>";

$conn->close();
?>

