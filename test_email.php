<?php
require_once 'includes/config.php';
require_once 'send_email.php';

// Test email sending
$testEmail = 'your-email@gmail.com'; // Change to your email
$result = sendNewsletterWelcome($testEmail, 'Test User');

if ($result) {
    echo "Test email sent successfully!";
} else {
    echo "Failed to send test email. Check your mail configuration.";
}
?>
