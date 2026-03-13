<?php
require_once 'includes/config.php';
include 'includes/header.php';

$message = '';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message_text = trim($_POST['message'] ?? '');

    // Validation
    if (empty($name) || empty($email) || empty($subject) || empty($message_text)) {
        $error = "Please fill in all required fields";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        // Save to database
        $sql = "INSERT INTO contact_messages (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        if ($stmt) {
            $stmt->bind_param("sssss", $name, $email, $phone, $subject, $message_text);
            if ($stmt->execute()) {
                $message = "Thank you for contacting us! We'll get back to you within 24 hours.";
                // Clear POST data after successful submission
                $_POST = [];
            } else {
                $error = "Sorry, something went wrong saving your message. Please try again.";
            }
            $stmt->close();
        } else {
            $error = "Database error. Please try again later.";
        }
    }
}
?>
<style>
        .contact-page {
            padding-top: 80px;
        }

        /* Hero Section */
        .contact-hero {
            height: 400px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .contact-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320"><path fill="%23ffffff" fill-opacity="0.1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,154.7C960,171,1056,181,1152,170.7C1248,160,1344,128,1392,112L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path></svg>');
            background-repeat: no-repeat;
            background-position: bottom;
            background-size: cover;
            opacity: 0.3;
        }

        .contact-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
        }

        .contact-hero h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: fadeInUp 1s ease;
        }

        .contact-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.2s both;
        }

        /* Contact Info Section */
        .contact-info-section {
            padding: 100px 0 50px;
            background: white;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .info-card {
            text-align: center;
            padding: 40px 30px;
            background: #f8f9fa;
            border-radius: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }

        .info-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .info-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .info-icon i {
            font-size: 35px;
            color: white;
        }

        .info-card h3 {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 15px;
        }

        .info-card p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 5px;
        }

        .info-card .highlight {
            color: #667eea;
            font-weight: 600;
        }

        /* Contact Form Section */
        .contact-form-section {
            padding: 50px 0 100px;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .form-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .form-card {
            background: white;
            border-radius: 30px;
            padding: 50px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .form-card h2 {
            font-size: 2rem;
            color: #333;
            margin-bottom: 10px;
            text-align: center;
        }

        .form-card>p {
            color: #666;
            text-align: center;
            margin-bottom: 40px;
        }

        .message-alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .message-alert.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message-alert.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
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

        .form-group label i {
            color: #667eea;
            margin-right: 8px;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e8e8f0;
            border-radius: 12px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }

        .btn-submit {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
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

        /* Map Section */
        .map-section {
            height: 450px;
            position: relative;
        }

        .map-section iframe {
            width: 100%;
            height: 100%;
            border: none;
        }

        .map-overlay {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            display: flex;
            align-items: center;
            gap: 15px;
            pointer-events: none;
        }

        .map-overlay i {
            font-size: 30px;
            color: #667eea;
        }

        .map-overlay span {
            font-weight: 600;
            color: #333;
        }

        /* FAQ Section */
        .faq-section {
            padding: 100px 0;
            background: white;
        }

        .faq-container {
            max-width: 800px;
            margin: 50px auto 0;
            padding: 0 20px;
        }

        .faq-item {
            margin-bottom: 20px;
            border: 1px solid #e8e8f0;
            border-radius: 12px;
            overflow: hidden;
        }

        .faq-question {
            padding: 20px 25px;
            background: #f8f9fa;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s ease;
        }

        .faq-question:hover {
            background: #f0f2f8;
        }

        .faq-question h3 {
            font-size: 1.1rem;
            color: #333;
            font-weight: 600;
        }

        .faq-question i {
            color: #667eea;
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-question i {
            transform: rotate(180deg);
        }

        .faq-answer {
            padding: 0 25px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.3s ease;
            background: white;
        }

        .faq-item.active .faq-answer {
            padding: 20px 25px;
            max-height: 200px;
        }

        .faq-answer p {
            color: #666;
            line-height: 1.6;
        }

        /* Animations */
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

        /* Responsive */
        @media (max-width: 768px) {
            .contact-hero h1 {
                font-size: 2.5rem;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .form-card {
                padding: 30px 20px;
            }

            .map-overlay {
                padding: 15px 20px;
            }

            .map-overlay i {
                font-size: 24px;
            }

            .map-overlay span {
                font-size: 14px;
            }
        }
    </style>


    <div class="contact-page">
        <!-- Hero Section -->
        <section class="contact-hero">
            <div class="contact-hero-content">
                <h1>Get in Touch</h1>
                <p>We'd love to hear from you. Let's create something magical together!</p>
            </div>
        </section>

        <!-- Contact Info Section -->
        <section class="contact-info-section">
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <h3>Visit Us</h3>
                    <p>123 Art Street</p>
                    <p>Creative City, CC 12345</p>
                    <p class="highlight">Open Mon-Sat, 9AM-6PM</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-phone"></i>
                    </div>
                    <h3>Call Us</h3>
                    <p>+1 234 567 890</p>
                    <p>+1 234 567 891</p>
                    <p class="highlight">24/7 Customer Support</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3>Email Us</h3>
                    <p>info@magicalarts.com</p>
                    <p>support@magicalarts.com</p>
                    <p class="highlight">Response within 24h</p>
                </div>

                <div class="info-card">
                    <div class="info-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Working Hours</h3>
                    <p>Monday - Friday: 9AM - 6PM</p>
                    <p>Saturday: 10AM - 4PM</p>
                    <p class="highlight">Sunday: Closed</p>
                </div>
            </div>
        </section>

        <!-- Contact Form Section -->
        <section class="contact-form-section" id="contact">
            <div class="form-container">
                <div class="form-card">
                    <h2>Send Us a Message</h2>
                    <p>Have a question? We're here to help!</p>

                    <?php if ($message): ?>
                        <div class="message-alert success">
                            <i class="fas fa-check-circle"></i>
                            <?php echo $message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="message-alert error">
                            <i class="fas fa-exclamation-circle"></i>
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="name"><i class="fas fa-user"></i> Your Name *</label>
                                <input type="text" id="name" name="name" required placeholder="John Doe"
                                    value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="email"><i class="fas fa-envelope"></i> Email *</label>
                                <input type="email" id="email" name="email" required placeholder="john@example.com"
                                    value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                            </div>
                        </div>

                        <div class="form-grid">
                            <div class="form-group">
                                <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                                <input type="tel" id="phone" name="phone" placeholder="+1 234 567 890"
                                    value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>">
                            </div>

                            <div class="form-group">
                                <label for="subject"><i class="fas fa-tag"></i> Subject *</label>
                                <select id="subject" name="subject" required>
                                    <option value="">Select a subject</option>
                                    <option value="general">General Inquiry</option>
                                    <option value="order">Order Related</option>
                                    <option value="portrait">Portrait Request</option>
                                    <option value="collaboration">Collaboration</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="message"><i class="fas fa-comment"></i> Message *</label>
                            <textarea id="message" name="message" required
                                placeholder="Tell us how we can help..."><?php echo htmlspecialchars($_POST['message'] ?? ''); ?></textarea>
                        </div>

                        <button type="submit" class="btn-submit">
                            <span>Send Message</span>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Map Section -->
        <section class="map-section">
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.9663095343003!2d-73.98510768458983!3d40.75889697932745!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1620000000000!5m2!1sen!2sus"
                allowfullscreen="" loading="lazy">
            </iframe>
            <div class="map-overlay">
                <i class="fas fa-map-pin"></i>
                <span>123 Art Street, Creative City</span>
            </div>
        </section>

        <!-- FAQ Section -->
        <section class="faq-section">
            <div class="section-header">
                <h2>Frequently Asked Questions</h2>
                <p>Find quick answers to common questions</p>
            </div>

            <div class="faq-container">
                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>How long does it take to create a portrait?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Typically, portraits take 7-14 days to complete, depending on complexity and size. We'll
                            provide you with a digital preview before shipping.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>What payment methods do you accept?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We accept all major credit cards, PayPal, and bank transfers. All payments are processed
                            securely through encrypted channels.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>Do you ship internationally?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Yes! We ship worldwide. Shipping costs and delivery times vary by location. We use tracked
                            shipping for all international orders.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>Can I request revisions?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>Absolutely! We provide a digital preview before shipping and offer unlimited revisions during
                            the preview stage to ensure your satisfaction.</p>
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question" onclick="toggleFAQ(this)">
                        <h3>What size portraits do you offer?</h3>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p>We offer various sizes from 8x10 inches up to 18x24 inches. Custom sizes are also available
                            upon request.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        function toggleFAQ(element) {
            const faqItem = element.parentElement;
            faqItem.classList.toggle('active');
        }

        // Auto-select subject from URL parameter (if any)
        const urlParams = new URLSearchParams(window.location.search);
        const subject = urlParams.get('subject');
        if (subject) {
            document.getElementById('subject').value = subject;
        }
    </script>

    <?php include 'includes/footer.php'; ?>
</body>

</html>


