<?php
require_once 'includes/config.php';

$message = '';

if (isset($_GET['email'])) {
    $email = urldecode($_GET['email']);

    $sql = "UPDATE newsletter SET status = 'unsubscribed' WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);

    if ($stmt->execute()) {
        $message = "You have been successfully unsubscribed from our newsletter.";
    } else {
        $message = "An error occurred. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unsubscribe - MagicalArts</title>
    <link rel="stylesheet" href="style.css">
</head>

<body
    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; display: flex; align-items: center; justify-content: center;">
    <div style="background: white; padding: 40px; border-radius: 20px; max-width: 500px; text-align: center;">
        <i class="fas fa-envelope-open" style="font-size: 60px; color: #667eea; margin-bottom: 20px;"></i>
        <h2 style="color: #333; margin-bottom: 20px;">
            <?php echo $message; ?>
        </h2>
        <p>We're sorry to see you go. You can always resubscribe anytime.</p>
        <a href="index.php"
            style="display: inline-block; margin-top: 20px; padding: 12px 25px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; text-decoration: none; border-radius: 10px;">Back
            to Home</a>
    </div>
</body>

</html>


