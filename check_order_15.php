<?php
require_once 'c:/xampp/htdocs/1magical/includes/config.php';

$order_id = 15;
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if ($order) {
    echo "Order Found:\n";
    print_r($order);
    $image_path = $order['image_path'];
    echo "\nImage Path: " . $image_path . "\n";
    if (file_exists('c:/xampp/htdocs/1magical/' . $image_path)) {
        echo "File EXISTS on server.\n";
    } else {
        echo "File DOES NOT EXIST on server.\n";
    }
} else {
    echo "Order ID $order_id NOT FOUND in database.\n";
}
?>