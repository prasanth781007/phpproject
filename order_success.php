<?php
require_once 'includes/config.php';
include 'includes/header.php';

// Get order ID from URL
$order_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    header("Location: index.php");
    exit();
}
?>
<style>
        .success-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .success-box {
            max-width: 600px;
            width: 100%;
            background: white;
            padding: 3rem;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            text-align: center;
            animation: fadeInUp 0.8s ease;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            animation: scaleIn 0.5s ease;
        }

        .success-icon i {
            font-size: 40px;
            color: white;
        }

        .success-box h1 {
            color: #28a745;
            margin-bottom: 1rem;
            font-size: 2.5rem;
        }

        .order-details {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 2rem;
            margin: 2rem 0;
            text-align: left;
        }

        .order-details h3 {
            color: #333;
            margin-bottom: 1.5rem;
            text-align: center;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 0.8rem 0;
            border-bottom: 1px solid #dee2e6;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-label {
            font-weight: 600;
            color: #666;
        }

        .detail-value {
            color: #333;
        }

        .order-image-preview {
            margin: 1rem 0;
            text-align: center;
        }

        .order-image-preview img {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .action-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn-primary,
        .btn-secondary {
            padding: 1rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-secondary {
            background: transparent;
            color: #667eea;
            border: 2px solid #667eea;
        }

        .btn-primary:hover,
        .btn-secondary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleIn {
            from {
                transform: scale(0);
            }

            to {
                transform: scale(1);
            }
        }
    </style>

    <div class="success-container">
        <div class="success-box">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>

            <h1>Order Placed Successfully!</h1>
            <p>Thank you for choosing MagicalArts. We'll start working on your portrait soon.</p>

            <div class="order-details">
                <h3>Order Details</h3>
                <div class="order-image-preview">
                    <img src="<?php echo $order['image_path']; ?>" alt="Order Image">
                </div>
                <div class="detail-item">
                    <span class="detail-label">Order ID:</span>
                    <span class="detail-value">#<?php echo str_pad($order['id'], 6, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Name:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['customer_name']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email:</span>
                    <span class="detail-value"><?php echo htmlspecialchars($order['customer_email']); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Size:</span>
                    <span class="detail-value"><?php echo $order['drawing_size']; ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Color Type:</span>
                    <span
                        class="detail-value"><?php echo ucfirst(str_replace('_', ' ', $order['color_type'])); ?></span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Delivery Date:</span>
                    <span class="detail-value"><?php echo date('F j, Y', strtotime($order['delivery_date'])); ?></span>
                </div>
            </div>

            <p>We'll send you updates about your order via email.</p>

            <div class="action-buttons">
                <a href="index.php" class="btn-primary">Back to Home</a>
                <a href="track_order.php" class="btn-primary" style="background: #28a745;">Track Order</a>
                <a href="order.php" class="btn-secondary">Place Another Order</a>
            </div>
        </div>
    </div>
<?php include 'includes/footer.php'; ?>



