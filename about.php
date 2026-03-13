<?php
require_once 'includes/config.php';
include 'includes/header.php';
?>
<style>
        /* About Page Styles */
        .about-page {
            padding-top: 80px;
        }

        /* Hero Section */
        .about-hero {
            position: relative;
            height: 500px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            overflow: hidden;
        }

        .about-hero::before {
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

        .about-hero-content {
            position: relative;
            z-index: 2;
            max-width: 800px;
            padding: 0 20px;
        }

        .about-hero h1 {
            font-size: 4rem;
            margin-bottom: 20px;
            animation: fadeInUp 1s ease;
        }

        .about-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.2s both;
        }

        /* Story Section */
        .story-section {
            padding: 100px 0;
            background: white;
        }

        .story-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .story-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .story-content h2 {
            font-size: 2.5rem;
            margin-bottom: 30px;
            color: #333;
            position: relative;
        }

        .story-content h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 80px;
            height: 4px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 2px;
        }

        .story-content p {
            color: #666;
            line-height: 1.8;
            margin-bottom: 20px;
            font-size: 1.1rem;
        }

        .story-image {
            position: relative;
        }

        .story-image img {
            width: 100%;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .story-image::before {
            content: '';
            position: absolute;
            top: -20px;
            left: -20px;
            width: 100%;
            height: 100%;
            border: 4px solid #667eea;
            border-radius: 20px;
            z-index: -1;
        }

        /* Team Section */
        .team-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .section-header {
            text-align: center;
            margin-bottom: 60px;
        }

        .section-header h2 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 15px;
        }

        .section-header p {
            color: #666;
            font-size: 1.1rem;
            max-width: 600px;
            margin: 0 auto;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .team-card {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .team-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        }

        .team-image {
            height: 350px;
            overflow: hidden;
        }

        .team-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .team-card:hover .team-image img {
            transform: scale(1.1);
        }

        .team-info {
            padding: 25px;
            text-align: center;
        }

        .team-info h3 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 5px;
        }

        .team-info .position {
            color: #667eea;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .team-info p {
            color: #666;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .team-social {
            display: flex;
            justify-content: center;
            gap: 15px;
        }

        .team-social a {
            width: 40px;
            height: 40px;
            background: #f0f0f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #667eea;
            transition: all 0.3s ease;
        }

        .team-social a:hover {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-3px);
        }

        /* Values Section */
        .values-section {
            padding: 100px 0;
            background: white;
        }

        .values-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        .value-card {
            text-align: center;
            padding: 40px 30px;
            background: #f8f9fa;
            border-radius: 20px;
            transition: all 0.3s ease;
        }

        .value-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }

        .value-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 25px;
        }

        .value-icon i {
            font-size: 35px;
            color: white;
        }

        .value-card h3 {
            font-size: 1.3rem;
            color: #333;
            margin-bottom: 15px;
        }

        .value-card p {
            color: #666;
            line-height: 1.6;
        }

        /* Timeline Section */
        .timeline-section {
            padding: 100px 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .timeline {
            max-width: 800px;
            margin: 60px auto 0;
            position: relative;
            padding: 0 20px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 50%;
            transform: translateX(-50%);
            width: 2px;
            height: 100%;
            background: rgba(255, 255, 255, 0.3);
        }

        .timeline-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 50px;
            position: relative;
        }

        .timeline-item:nth-child(even) {
            flex-direction: row-reverse;
        }

        .timeline-year {
            width: 120px;
            height: 120px;
            background: white;
            color: #667eea;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            font-weight: 700;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        .timeline-content {
            width: calc(50% - 80px);
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
        }

        .timeline-content h3 {
            font-size: 1.3rem;
            margin-bottom: 10px;
        }

        .timeline-content p {
            opacity: 0.9;
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
            .about-hero h1 {
                font-size: 2.5rem;
            }

            .story-grid {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .timeline::before {
                left: 30px;
            }

            .timeline-item,
            .timeline-item:nth-child(even) {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .timeline-year {
                width: 80px;
                height: 80px;
                font-size: 1.2rem;
            }

            .timeline-content {
                width: 100%;
            }
        }
    </style>


    <div class="about-page">
        <!-- Hero Section -->
        <section class="about-hero">
            <div class="about-hero-content">
                <h1>Our Story</h1>
                <p>Bringing memories to life through the magic of art since 2020</p>
            </div>
        </section>

        <!-- Story Section -->
        <section class="story-section">
            <div class="story-container">
                <div class="story-grid">
                    <div class="story-content">
                        <h2>The MagicalArts Journey</h2>
                        <p>Founded in 2020, MagicalArts began as a dream to transform precious moments into timeless
                            pieces of art. What started as a small studio with just two passionate artists has grown
                            into a beloved destination for art lovers worldwide.</p>
                        <p>We believe that every photograph holds a story, and our mission is to tell that story through
                            the stroke of a pencil or the blend of colors. Our artists pour their heart and soul into
                            every piece, ensuring that each portrait captures not just a likeness, but the very essence
                            of the subject.</p>
                        <p>Today, we've created over 500 portraits for happy families, couples, and individuals across
                            the globe. But our philosophy remains the same: art should touch hearts and preserve
                            memories in the most beautiful way possible.</p>
                    </div>
                    <div class="story-image">
                        <img src="assets/images/artist.jpg" alt="Our Studio">
                    </div>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section class="team-section">
            <div class="section-header">
                <h2>Meet The Artists</h2>
                <p>The talented hands behind every masterpiece</p>
            </div>

            <div class="team-grid">
                <div class="team-card">
                    <div class="team-image">
                        <img src="assets/images/artist.jpg" alt="K. Prasanth">
                    </div>
                    <div class="team-info">
                        <h3>lingesh</h3>
                        <div class="position">Lead Artist & co-Founder</div>
                        <p>With over 15 years of experience in fine arts, Prasanth specializes in hyper-realistic pencil
                            portraits. His attention to detail and ability to capture emotions is unmatched.</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>

                <div class="team-card">
                    <div class="team-image">
                        <img src="assets/images/co-founderimage.jpg" alt="Co-Founder">
                    </div>
                    <div class="team-info">
                        <h3>K. Prasanth</h3>
                        <div class="position">Founder & Creative Director</div>
                        <p>A visionary artist with a passion for color art. Kumar brings vibrancy and life to every
                            portrait, making each piece uniquely magical.</p>
                        <div class="team-social">
                            <a href="#"><i class="fab fa-facebook"></i></a>
                            <a href="#"><i class="fab fa-instagram"></i></a>
                            <a href="#"><i class="fab fa-linkedin"></i></a>
                        </div>
                    </div>
                </div>


            </div>
        </section>

        <!-- Values Section -->
        <section class="values-section">
            <div class="section-header">
                <h2>Our Core Values</h2>
                <p>The principles that guide our art and service</p>
            </div>

            <div class="values-grid">
                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-heart"></i>
                    </div>
                    <h3>Passion</h3>
                    <p>We don't just draw; we pour our hearts into every artwork, ensuring each piece is a labor of
                        love.</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Excellence</h3>
                    <p>We strive for perfection in every stroke, never compromising on quality or detail.</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>Timeliness</h3>
                    <p>We respect your time and always deliver portraits by the promised date, without compromising
                        quality.</p>
                </div>

                <div class="value-card">
                    <div class="value-icon">
                        <i class="fas fa-smile"></i>
                    </div>
                    <h3>Satisfaction</h3>
                    <p>Your happiness is our success. We work until you're 100% satisfied with your portrait.</p>
                </div>
            </div>
        </section>

        <!-- Timeline Section -->
        <section class="timeline-section">
            <div class="section-header">
                <h2 style="color: white;">Our Journey</h2>
                <p style="color: rgba(255,255,255,0.9);">Milestones that shaped MagicalArts</p>
            </div>

            <div class="timeline">
                <div class="timeline-item">
                    <div class="timeline-year">2020</div>
                    <div class="timeline-content">
                        <h3>The Beginning</h3>
                        <p>MagicalArts was founded in a small studio with just two artists and a dream.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2021</div>
                    <div class="timeline-content">
                        <h3>First 100 Portraits</h3>
                        <p>Celebrated creating 100 portraits for happy customers across the country.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2022</div>
                    <div class="timeline-content">
                        <h3>Expansion</h3>
                        <p>Grew our team to 5 artists and moved to a larger studio space.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2023</div>
                    <div class="timeline-content">
                        <h3>International Reach</h3>
                        <p>Started shipping portraits globally, reaching customers in 10+ countries.</p>
                    </div>
                </div>

                <div class="timeline-item">
                    <div class="timeline-year">2024</div>
                    <div class="timeline-content">
                        <h3>500+ Milestone</h3>
                        <p>Created over 500 portraits with a 4.9/5 customer rating.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <?php include 'includes/footer.php'; ?>
</body>

</html>


