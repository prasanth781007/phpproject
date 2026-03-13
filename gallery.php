<?php
require_once 'includes/config.php';
// Publicly accessible gallery page

// Page basic settings
$page_title = "Our Gallery - MagicalArts";
$is_logged_in = isLoggedIn();
$username = $_SESSION['username'] ?? 'Guest';

// Fetch all available masterpieces (assuming these are from orders marked as 'completed' or 'delivered' 
// OR just all orders for now as display cases)
$sql = "SELECT * FROM orders ORDER BY order_date DESC";
$result = $conn->query($sql);
$gallery_items = [];
if ($result) {
    while($row = $result->fetch_assoc()) {
        $gallery_items[] = $row;
    }
}

include 'includes/header.php';
?>

<div class="gallery-page">
    <!-- Gallery Hero -->
    <section class="gallery-hero">
        <div class="container">
            <div class="hero-text">
                <span class="hero-badge">✨ Our Masterpieces</span>
                <h1>Explore Our <span class="text-gradient">Cherished Works</span></h1>
                <p>A collection of hand-drawn memories that we've had the honor of bringing to life. Each piece represents a unique story and a special bond.</p>
            </div>
        </div>
    </section>

    <!-- Gallery Filter & Search -->
    <div class="gallery-toolbar">
        <div class="container">
            <div class="toolbar-content">
                <div class="filter-group">
                    <button class="filter-btn active" data-filter="all">All Styles</button>
                    <button class="filter-btn" data-filter="color">Full Color</button>
                    <button class="filter-btn" data-filter="black_white">Black & White</button>
                </div>
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" id="gallerySearch" placeholder="Search by customer name...">
                </div>
            </div>
        </div>
    </div>

    <!-- Main Gallery Grid -->
    <section class="gallery-main">
        <div class="container">
            <div class="gallery-grid" id="mainGallery">
                <?php if (empty($gallery_items)): ?>
                    <div class="empty-state">
                        <i class="fas fa-palette"></i>
                        <h3>Gallery is Empty</h3>
                        <p>We're currently working on fresh masterpieces. Check back soon!</p>
                        <a href="order.php" class="btn btn-primary">Start Your Own <i class="fas fa-magic"></i></a>
                    </div>
                <?php else: ?>
                    <?php foreach ($gallery_items as $index => $item): ?>
                        <div class="gallery-card-item" data-type="<?php echo $item['color_type']; ?>" data-name="<?php echo strtolower($item['customer_name']); ?>">
                            <div class="image-box" onclick="openGalleryModal(<?php echo $index; ?>)">
                                <img src="<?php echo base_url($item['image_path']); ?>" alt="Portrait for <?php echo htmlspecialchars($item['customer_name']); ?>">
                                <div class="card-overlay">
                                    <span class="badge <?php echo $item['color_type']; ?>">
                                        <?php echo $item['color_type'] == 'color' ? 'Full Color' : 'B&W Sketch'; ?>
                                    </span>
                                    <i class="fas fa-expand-alt expand-icon"></i>
                                </div>
                            </div>
                            <div class="card-info">
                                <h3><?php echo htmlspecialchars($item['customer_name']); ?>'s Portrait</h3>
                                <div class="info-meta">
                                    <span><i class="fas fa-ruler"></i> <?php echo $item['drawing_size']; ?></span>
                                    <span><i class="fas fa-calendar-alt"></i> <?php echo date('M Y', strtotime($item['order_date'])); ?></span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>
</div>

<!-- Gallery Preview Modal -->
<div class="gallery-modal" id="galleryModal">
    <div class="modal-overlay" onclick="closeGalleryModal()"></div>
    <div class="modal-wrapper">
        <button class="modal-close" onclick="closeGalleryModal()">&times;</button>
        <div class="modal-body">
            <button class="nav-btn prev" onclick="changeImage(-1)"><i class="fas fa-chevron-left"></i></button>
            <div class="modal-image-container">
                <img id="modalImg" src="" alt="Gallery Preview">
                <div class="modal-details">
                    <h2 id="modalTitle">Customer Portrait</h2>
                    <p id="modalMeta">Size & Date</p>
                </div>
            </div>
            <button class="nav-btn next" onclick="changeImage(1)"><i class="fas fa-chevron-right"></i></button>
        </div>
    </div>
</div>

<style>
    .gallery-page { padding-bottom: 80px; }
    
    .gallery-hero { 
        padding: 120px 0 60px;
        text-align: center;
        background: linear-gradient(to bottom, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
    }
    
    .hero-text h1 { font-size: 3.5rem; line-height: 1.2; margin: 15px 0 20px; font-weight: 800; }
    .hero-text p { font-size: 1.2rem; color: #666; max-width: 700px; margin: 0 auto; line-height: 1.6; }
    
    .gallery-toolbar { 
        position: sticky; 
        top: 80px; 
        background: rgba(255,255,255,0.8); 
        backdrop-filter: blur(15px); 
        z-index: 100;
        padding: 20px 0;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        margin-bottom: 50px;
    }
    
    .toolbar-content { display: flex; justify-content: space-between; align-items: center; gap: 20px; }
    
    .filter-group { display: flex; gap: 10px; }
    .filter-btn { 
        padding: 10px 25px; 
        border: none; 
        border-radius: 50px; 
        background: #f0f2f5; 
        color: #555; 
        font-weight: 600; 
        cursor: pointer; 
        transition: 0.3s;
    }
    .filter-btn.active, .filter-btn:hover { background: #667eea; color: white; }
    
    .search-box { position: relative; }
    .search-box i { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: #999; }
    .search-box input { 
        padding: 12px 20px 12px 45px; 
        border: 2px solid #e8eaf0; 
        border-radius: 50px; 
        width: 300px; 
        outline: none; 
        transition: 0.3s; 
    }
    .search-box input:focus { border-color: #667eea; width: 350px; }
    
    .gallery-grid { 
        display: grid; 
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); 
        gap: 30px; 
    }
    
    .gallery-card-item { 
        background: white; 
        border-radius: 20px; 
        overflow: hidden; 
        box-shadow: 0 10px 30px rgba(0,0,0,0.05); 
        transition: 0.4s;
    }
    .gallery-card-item:hover { transform: translateY(-10px); box-shadow: 0 20px 40px rgba(102, 126, 234, 0.15); }
    
    .image-box { position: relative; height: 350px; cursor: pointer; overflow: hidden; }
    .image-box img { width: 100%; height: 100%; object-fit: cover; transition: 0.6s; }
    .gallery-card-item:hover .image-box img { transform: scale(1.1); }
    
    .card-overlay { 
        position: absolute; 
        top: 0; left: 0; width: 100%; height: 100%; 
        background: linear-gradient(to top, rgba(0,0,0,0.5), transparent); 
        opacity: 0; transition: 0.3s; 
        display: flex; flex-direction: column; justify-content: space-between; padding: 20px;
    }
    .gallery-card-item:hover .card-overlay { opacity: 1; }
    
    .expand-icon { color: white; font-size: 24px; align-self: flex-end; }
    .badge { 
        padding: 5px 15px; border-radius: 50px; font-size: 12px; font-weight: 700; width: fit-content;
        text-transform: uppercase; letter-spacing: 1px;
    }
    .badge.color { background: #e3f2fd; color: #1976d2; }
    .badge.black_white { background: #f5f5f5; color: #616161; }
    
    .card-info { padding: 25px; }
    .card-info h3 { font-size: 1.2rem; margin-bottom: 10px; color: #333; }
    .info-meta { display: flex; gap: 20px; color: #888; font-size: 0.9rem; }
    .info-meta i { margin-right: 5px; color: #667eea; }
    
    /* Modal Styles */
    .gallery-modal { 
        position: fixed; 
        top: 0; left: 0; width: 100%; height: 100%; 
        z-index: 2000; 
        display: none; 
        align-items: center; 
        justify-content: center;
    }
    .gallery-modal.active { display: flex; }
    .modal-overlay { position: absolute; width: 100%; height: 100%; background: rgba(0,0,0,0.9); backdrop-filter: blur(10px); }
    .modal-wrapper { position: relative; width: 90%; max-width: 1200px; }
    .modal-close { position: absolute; top: -50px; right: 0; color: white; font-size: 40px; background: none; border: none; cursor: pointer; }
    
    .modal-body { display: flex; align-items: center; gap: 30px; }
    .modal-image-container { flex: 1; background: white; border-radius: 15px; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }
    .modal-image-container img { width: 100%; max-height: 75vh; object-fit: contain; background: #000; display: block; }
    
    .modal-details { padding: 25px; background: white; border-top: 1px solid #eee; text-align: left; }
    .modal-details h2 { color: #333; margin-bottom: 5px; }
    .modal-details p { color: #666; font-size: 1rem; }
    
    .nav-btn { background: rgba(255,255,255,0.1); color: white; border: none; width: 60px; height: 60px; border-radius: 50%; cursor: pointer; font-size: 24px; transition: 0.3s; flex-shrink: 0; }
    .nav-btn:hover { background: #667eea; }
    
    @media (max-width: 992px) {
        .toolbar-content { flex-direction: column; align-items: stretch; }
        .search-box input { width: 100%; }
        .search-box input:focus { width: 100%; }
        .hero-text h1 { font-size: 2.5rem; }
    }
</style>

<script>
    const items = <?php echo json_encode($gallery_items); ?>;
    let currentIndex = 0;
    
    const searchInput = document.getElementById('gallerySearch');
    const filterBtns = document.querySelectorAll('.filter-btn');
    const mainGallery = document.getElementById('mainGallery');
    const cards = document.querySelectorAll('.gallery-card-item');

    // Filter Logic
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            filterGallery();
        });
    });

    // Search Logic
    searchInput.addEventListener('input', filterGallery);

    function filterGallery() {
        const query = searchInput.value.toLowerCase();
        const activeFilter = document.querySelector('.filter-btn.active').dataset.filter;

        cards.forEach(card => {
            const type = card.dataset.type;
            const name = card.dataset.name;
            
            const matchesFilter = activeFilter === 'all' || type === activeFilter;
            const matchesSearch = name.includes(query);

            if (matchesFilter && matchesSearch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Modal Logic
    function openGalleryModal(index) {
        currentIndex = index;
        updateModal();
        document.getElementById('galleryModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeGalleryModal() {
        document.getElementById('galleryModal').classList.remove('active');
        document.body.style.overflow = 'auto';
    }

    function updateModal() {
        if (items.length === 0) return;
        const item = items[currentIndex];
        const modalImg = document.getElementById('modalImg');
        const modalTitle = document.getElementById('modalTitle');
        const modalMeta = document.getElementById('modalMeta');

        modalImg.src = '<?php echo base_url(""); ?>' + item.image_path;
        modalTitle.innerText = item.customer_name + "'s Portrait";
        modalMeta.innerHTML = `<i class="fas fa-ruler"></i> Size: ${item.drawing_size} | <i class="fas fa-palette"></i> Style: ${item.color_type === 'color' ? 'Full Color' : 'B&W Sketch'}`;
    }

    function changeImage(dir) {
        currentIndex += dir;
        if (currentIndex < 0) currentIndex = items.length - 1;
        if (currentIndex >= items.length) currentIndex = 0;
        updateModal();
    }

    document.addEventListener('keydown', (e) => {
        if (!document.getElementById('galleryModal').classList.contains('active')) return;
        if (e.key === 'Escape') closeGalleryModal();
        if (e.key === 'ArrowLeft') changeImage(-1);
        if (e.key === 'ArrowRight') changeImage(1);
    });
</script>

<?php include 'includes/footer.php'; ?>
