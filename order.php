<?php 
require_once 'includes/config.php';
include 'includes/header.php'; 
?>

<section class="order-section">
    <div class="order-container">
        <h1>Order Your Magical Portrait</h1>
        <p class="order-subtitle">Fill in the details below and we'll create your masterpiece</p>
        
        <form action="scripts/process_order.php" method="POST" enctype="multipart/form-data" class="order-form" id="orderForm">
            <div class="form-group">
                <label for="name"><i class="fas fa-user"></i> Your Name *</label>
                <input type="text" id="name" name="name" required placeholder="Enter your full name" value="<?php echo isLoggedIn() ? htmlspecialchars($_SESSION['username']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email Address *</label>
                <input type="email" id="email" name="email" required placeholder="Enter your email" value="<?php echo isLoggedIn() ? htmlspecialchars($_SESSION['email']) : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="image"><i class="fas fa-image"></i> Upload Reference Image *</label>
                <div class="file-upload">
                    <input type="file" id="image" name="image" accept="image/*" required>
                    <div class="file-upload-label">
                        <i class="fas fa-cloud-upload-alt"></i>
                        <span>Choose an image</span>
                    </div>
                </div>
                <div id="imagePreview" class="image-preview"></div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="delivery_date"><i class="fas fa-calendar"></i> Delivery Date *</label>
                    <input type="date" id="delivery_date" name="delivery_date" required min="<?php echo date('Y-m-d', strtotime('+7 days')); ?>">
                </div>
                
                <div class="form-group">
                    <label for="size"><i class="fas fa-ruler"></i> Drawing Size *</label>
                    <select id="size" name="size" required>
                        <option value="">Select size</option>
                        <option value="8x10">8 x 10 inches</option>
                        <option value="11x14">11 x 14 inches</option>
                        <option value="12x16">12 x 16 inches</option>
                        <option value="16x20">16 x 20 inches</option>
                        <option value="18x24">18 x 24 inches</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label><i class="fas fa-palette"></i> Color Type *</label>
                <div class="radio-group">
                    <label class="radio-label">
                        <input type="radio" name="color_type" value="color" checked>
                        <span class="radio-custom"></span>
                        Full Color
                    </label>
                    <label class="radio-label">
                        <input type="radio" name="color_type" value="black_white">
                        <span class="radio-custom"></span>
                        Black & White
                    </label>
                </div>
            </div>
            
            <div class="form-group">
                <label for="special_instructions"><i class="fas fa-pencil-alt"></i> Special Instructions (Optional)</label>
                <textarea id="special_instructions" name="special_instructions" rows="4" placeholder="Any specific details you'd like us to include?"></textarea>
            </div>
            
            <div class="form-group terms">
                <label class="checkbox-label">
                    <input type="checkbox" name="terms" required>
                    <span class="checkbox-custom"></span>
                    I agree to the <a href="#">terms and conditions</a> *
                </label>
            </div>
            
            <button type="submit" class="btn-submit">Place Order <i class="fas fa-arrow-right"></i></button>
        </form>
    </div>
</section>

<?php include 'includes/footer.php'; ?>


