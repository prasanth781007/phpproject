<?php
require_once 'includes/config.php';
include 'includes/header.php';

$order = null;
$error = "";

if (isset($_POST['track'])) {
    $search = trim($_POST['search_term']);

    // Search by ID or Email
    $sql = "SELECT * FROM orders WHERE id = ? OR customer_email = ? ORDER BY order_date DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $order = $result->fetch_assoc();
    } else {
        $error = "No order found with that ID or Email. Please double check your details.";
    }
}
?>

<div class="tracking-page-wrapper">
    <section class="tracking-hero">
        <div class="hero-content">
            <h1>Track Your Masterpiece</h1>
            <p>Stay updated on the progress of your magical portrait</p>
        </div>
    </section>

    <section class="tracking-section">
        <div class="tracking-container">
            <div class="tracking-card">
                <div class="card-header">
                    <i class="fas fa-map-marker-alt"></i>
                    <h3>Order Status</h3>
                </div>

                <form method="POST" class="tracking-form">
                    <div class="tracking-input-group">
                        <div class="input-wrapper">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search_term" placeholder="Order ID or Email Address" required
                                value="<?php echo isset($_POST['search_term']) ? htmlspecialchars($_POST['search_term']) : ''; ?>">
                        </div>
                        <button type="submit" name="track" class="btn-track">Track Now</button>
                    </div>
                </form>

                <?php if ($error): ?>
                    <div class="tracking-error animate-fade-in">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo $error; ?>
                    </div>
                <?php endif; ?>

                <?php if ($order): ?>
                    <div class="order-result animate-slide-up">
                        <div class="order-header">
                            <div class="order-num">
                                <span>Order Number</span>
                                <h4>#
                                    <?php echo $order['id']; ?>
                                </h4>
                            </div>
                            <div class="order-date">
                                <span>Order Date</span>
                                <h4>
                                    <?php echo date('M d, Y', strtotime($order['order_date'])); ?>
                                </h4>
                            </div>
                        </div>

                        <div class="status-timeline">
                            <?php
                            $statuses = ['pending', 'processing', 'completed', 'delivered'];
                            $current_status = strtolower($order['status']);
                            $current_index = array_search($current_status, $statuses);
                            if ($current_index === false)
                                $current_index = 0; // Default to pending if unknown
                            ?>

                            <?php foreach ($statuses as $index => $status): ?>
                                <div class="status-step <?php echo $index <= $current_index ? 'active' : ''; ?>">
                                    <div class="step-line"></div>
                                    <div class="step-icon">
                                        <?php if ($status == 'pending')
                                            echo '<i class="fas fa-clock"></i>'; ?>
                                        <?php if ($status == 'processing')
                                            echo '<i class="fas fa-palette"></i>'; ?>
                                        <?php if ($status == 'completed')
                                            echo '<i class="fas fa-check-circle"></i>'; ?>
                                        <?php if ($status == 'delivered')
                                            echo '<i class="fas fa-box-open"></i>'; ?>
                                    </div>
                                    <div class="step-label">
                                        <?php echo ucfirst($status); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <div class="order-info-grid">
                            <div class="info-card">
                                <label><i class="fas fa-user"></i> Customer</label>
                                <p>
                                    <?php echo htmlspecialchars($order['customer_name']); ?>
                                </p>
                            </div>
                            <div class="info-card">
                                <label><i class="fas fa-calendar-alt"></i> Target Delivery</label>
                                <p>
                                    <?php echo date('M d, Y', strtotime($order['delivery_date'])); ?>
                                </p>
                            </div>
                            <div class="info-card">
                                <label><i class="fas fa-ruler-combined"></i> Dimensions</label>
                                <p>
                                    <?php echo $order['drawing_size']; ?> inches
                                </p>
                            </div>
                            <div class="info-card">
                                <label><i class="fas fa-paint-brush"></i> Style</label>
                                <p>
                                    <?php echo ucfirst(str_replace('_', ' ', $order['color_type'])); ?>
                                </p>
                            </div>
                        </div>

                        <?php if ($order['image_path'] && file_exists($order['image_path'])): ?>
                            <div class="preview-section">
                                <label>Reference Image</label>
                                <div class="preview-img">
                                    <img src="<?php echo htmlspecialchars($order['image_path']); ?>" alt="Order Image">
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>

            <div class="tracking-help">
                <p>Having trouble? <a href="contact.php">Contact our support team</a> for assistance.</p>
            </div>
        </div>
    </section>
</div>

<style>
    /* Tracking System Styling */
    .tracking-page-wrapper {
        background: #f0f4f8;
        min-height: 100vh;
    }

    .tracking-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 80px 20px 120px;
        text-align: center;
        color: white;
    }

    .hero-content h1 {
        font-size: 42px;
        font-weight: 700;
        margin-bottom: 15px;
        letter-spacing: -1px;
    }

    .hero-content p {
        font-size: 18px;
        opacity: 0.9;
    }

    .tracking-section {
        margin-top: -60px;
        padding: 0 20px 80px;
    }

    .tracking-container {
        max-width: 900px;
        margin: 0 auto;
    }

    .tracking-card {
        background: white;
        padding: 40px;
        border-radius: 24px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.08);
    }

    .card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 30px;
        color: #4a5568;
    }

    .card-header i {
        font-size: 24px;
        color: #667eea;
    }

    .card-header h3 {
        font-size: 22px;
        font-weight: 600;
    }

    .tracking-form {
        margin-bottom: 40px;
    }

    .tracking-input-group {
        display: flex;
        gap: 15px;
        background: #f7fafc;
        padding: 10px;
        border-radius: 16px;
        border: 2px solid #edf2f7;
    }

    .input-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        padding-left: 15px;
        gap: 12px;
        color: #a0aec0;
    }

    .input-wrapper input {
        width: 100%;
        padding: 12px 0;
        background: transparent;
        border: none;
        outline: none;
        font-size: 16px;
        color: #2d3748;
    }

    .btn-track {
        background: #667eea;
        color: white;
        border: none;
        padding: 12px 30px;
        border-radius: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-track:hover {
        background: #5a67d8;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .tracking-error {
        background: #fff5f5;
        color: #c53030;
        padding: 16px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 30px;
        border-left: 4px solid #fc8181;
    }

    /* Order Result */
    .order-header {
        display: flex;
        justify-content: space-between;
        padding-bottom: 30px;
        border-bottom: 2px dashed #edf2f7;
        margin-bottom: 40px;
    }

    .order-num span,
    .order-date span {
        font-size: 13px;
        color: #718096;
        text-transform: uppercase;
        letter-spacing: 1px;
    }

    .order-num h4,
    .order-date h4 {
        font-size: 20px;
        font-weight: 700;
        color: #2d3748;
        margin-top: 5px;
    }

    /* Timeline */
    .status-timeline {
        display: flex;
        justify-content: space-between;
        margin-bottom: 60px;
        position: relative;
        padding: 0 40px;
    }

    .status-step {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        flex: 1;
    }

    .step-icon {
        width: 54px;
        height: 54px;
        background: #edf2f7;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #a0aec0;
        font-size: 20px;
        z-index: 2;
        transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
        border: 4px solid white;
    }

    .step-line {
        position: absolute;
        height: 4px;
        background: #edf2f7;
        top: 25px;
        left: -50%;
        right: 50%;
        z-index: 1;
    }

    .status-step:first-child .step-line {
        display: none;
    }

    .status-step.active .step-icon {
        background: #667eea;
        color: white;
        box-shadow: 0 0 0 8px rgba(102, 126, 234, 0.1);
    }

    .status-step.active .step-line {
        background: #667eea;
    }

    .step-label {
        margin-top: 15px;
        font-size: 14px;
        font-weight: 600;
        color: #718096;
    }

    .status-step.active .step-label {
        color: #2d3748;
    }

    /* Info Grid */
    .order-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
        margin-bottom: 40px;
    }

    .info-card {
        background: #f7fafc;
        padding: 20px;
        border-radius: 16px;
        transition: transform 0.3s ease;
    }

    .info-card:hover {
        transform: translateY(-5px);
    }

    .info-card label {
        font-size: 12px;
        color: #667eea;
        font-weight: 600;
        text-transform: uppercase;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 8px;
    }

    .info-card p {
        font-size: 17px;
        font-weight: 700;
        color: #2d3748;
    }

    .preview-section label {
        display: block;
        font-weight: 600;
        color: #4a5568;
        margin-bottom: 15px;
    }

    .preview-img {
        border-radius: 16px;
        overflow: hidden;
        max-width: 300px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }

    .preview-img img {
        width: 100%;
        display: block;
    }

    .tracking-help {
        text-align: center;
        margin-top: 30px;
        color: #718096;
    }

    .tracking-help a {
        color: #667eea;
        font-weight: 600;
        text-decoration: none;
    }

    /* Animations */
    .animate-fade-in {
        animation: fadeIn 0.5s ease forwards;
    }

    .animate-slide-up {
        animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 768px) {
        .tracking-input-group {
            flex-direction: column;
        }

        .order-header {
            flex-direction: column;
            gap: 15px;
        }

        .order-info-grid {
            grid-template-columns: 1fr;
        }

        .status-timeline {
            padding: 0;
        }

        .step-icon {
            width: 45px;
            height: 45px;
            font-size: 16px;
        }

        .step-label {
            font-size: 11px;
        }
    }
</style>

<?php include 'includes/footer.php'; ?>


