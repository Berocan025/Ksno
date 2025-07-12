<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Galeri Sayfası
 */

// Config dosyasını dahil et
require_once 'includes/config.php';

// Sayfa meta bilgileri
$page_title = get_site_text('gallery_title', 'Galeri - BonusBoss');
$page_description = get_site_text('gallery_subtitle', 'Çalışmalarımızdan görsel örnekler');
$page_keywords = 'galeri, fotoğraf, video, casino, yayıncı, bonusboss';

// Header'ı dahil et
include 'includes/header.php';

// Kategorileri getir
$categories_query = "SELECT * FROM categories WHERE type = 'gallery' AND is_active = 1 ORDER BY sort_order ASC";
$categories_result = $conn->query($categories_query);

// Aktif kategori filtresi
$active_category = isset($_GET['category']) ? clean_input($_GET['category']) : 'all';

// Fotoğrafları getir
$photos_query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                FROM gallery_photos p 
                LEFT JOIN categories c ON p.category_id = c.id 
                WHERE p.is_active = 1";

if ($active_category !== 'all') {
    $photos_query .= " AND c.slug = '" . $conn->real_escape_string($active_category) . "'";
}

$photos_query .= " ORDER BY p.sort_order ASC, p.created_at DESC";
$photos_result = $conn->query($photos_query);

// Videoları getir
$videos_query = "SELECT v.*, c.name as category_name, c.slug as category_slug 
                FROM gallery_videos v 
                LEFT JOIN categories c ON v.category_id = c.id 
                WHERE v.is_active = 1";

if ($active_category !== 'all') {
    $videos_query .= " AND c.slug = '" . $conn->real_escape_string($active_category) . "'";
}

$videos_query .= " ORDER BY v.sort_order ASC, v.created_at DESC";
$videos_result = $conn->query($videos_query);
?>

<!-- Page Header -->
<section class="page-header bg-gradient-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h1 class="page-title" data-aos="fade-up">
                    <?php echo get_site_text('gallery_title', 'Galeri'); ?>
                </h1>
                <p class="page-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo get_site_text('gallery_subtitle', 'Çalışmalarımızdan görsel örnekler'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Filter -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="gallery-filter" data-aos="fade-up">
                    <button class="filter-btn <?php echo $active_category === 'all' ? 'active' : ''; ?>" data-filter="all">
                        <?php echo get_site_text('gallery_filter_all', 'Tümü'); ?>
                    </button>
                    <button class="filter-btn <?php echo $active_category === 'photos' ? 'active' : ''; ?>" data-filter="photos">
                        <?php echo get_site_text('gallery_filter_photos', 'Fotoğraflar'); ?>
                    </button>
                    <button class="filter-btn <?php echo $active_category === 'videos' ? 'active' : ''; ?>" data-filter="videos">
                        <?php echo get_site_text('gallery_filter_videos', 'Videolar'); ?>
                    </button>
                    <?php 
                    if ($categories_result && $categories_result->num_rows > 0):
                        while ($category = $categories_result->fetch_assoc()):
                    ?>
                    <button class="filter-btn <?php echo $active_category === $category['slug'] ? 'active' : ''; ?>" data-filter="<?php echo $category['slug']; ?>">
                        <?php echo $category['name']; ?>
                    </button>
                    <?php 
                        endwhile;
                    endif;
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Gallery Grid -->
<section class="section">
    <div class="container">
        <div class="row">
            <?php 
            // Fotoğrafları göster
            if ($photos_result && $photos_result->num_rows > 0):
                while ($photo = $photos_result->fetch_assoc()):
            ?>
            <div class="col-lg-4 col-md-6 mb-4 gallery-item photo-item" data-category="<?php echo $photo['category_slug']; ?>" data-aos="fade-up">
                <div class="gallery-card">
                    <div class="gallery-image">
                        <img src="assets/uploads/<?php echo $photo['image']; ?>" alt="<?php echo $photo['title']; ?>" class="img-fluid">
                        <div class="gallery-overlay">
                            <div class="gallery-overlay-content">
                                <h4><?php echo $photo['title']; ?></h4>
                                <p><?php echo $photo['category_name']; ?></p>
                                <a href="assets/uploads/<?php echo $photo['image']; ?>" class="btn btn-primary btn-sm" data-fancybox="gallery" data-caption="<?php echo $photo['title']; ?>">
                                    <i class="fas fa-search"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-content">
                        <h3 class="gallery-title"><?php echo $photo['title']; ?></h3>
                        <p class="gallery-category"><?php echo $photo['category_name']; ?></p>
                        <?php if ($photo['description']): ?>
                        <p class="gallery-description"><?php echo $photo['description']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            endif;
            
            // Videoları göster
            if ($videos_result && $videos_result->num_rows > 0):
                while ($video = $videos_result->fetch_assoc()):
            ?>
            <div class="col-lg-4 col-md-6 mb-4 gallery-item video-item" data-category="<?php echo $video['category_slug']; ?>" data-aos="fade-up">
                <div class="gallery-card">
                    <div class="gallery-image">
                        <?php if ($video['thumbnail']): ?>
                        <img src="assets/uploads/<?php echo $video['thumbnail']; ?>" alt="<?php echo $video['title']; ?>" class="img-fluid">
                        <?php else: ?>
                        <img src="assets/images/video-placeholder.jpg" alt="<?php echo $video['title']; ?>" class="img-fluid">
                        <?php endif; ?>
                        <div class="gallery-overlay">
                            <div class="gallery-overlay-content">
                                <h4><?php echo $video['title']; ?></h4>
                                <p><?php echo $video['category_name']; ?></p>
                                <a href="<?php echo $video['video_url']; ?>" class="btn btn-primary btn-sm" data-fancybox="gallery" data-caption="<?php echo $video['title']; ?>">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                        </div>
                        <div class="video-play-icon">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    <div class="gallery-content">
                        <h3 class="gallery-title"><?php echo $video['title']; ?></h3>
                        <p class="gallery-category"><?php echo $video['category_name']; ?></p>
                        <?php if ($video['description']): ?>
                        <p class="gallery-description"><?php echo $video['description']; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            endif;
            
            // Eğer hiç içerik yoksa varsayılan içerik göster
            if ((!$photos_result || $photos_result->num_rows == 0) && (!$videos_result || $videos_result->num_rows == 0)):
            ?>
            <!-- Varsayılan galeri içeriği -->
            <div class="col-lg-4 col-md-6 mb-4 gallery-item photo-item" data-category="photos" data-aos="fade-up">
                <div class="gallery-card">
                    <div class="gallery-image">
                        <img src="assets/images/gallery-1.jpg" alt="Casino Yayın" class="img-fluid">
                        <div class="gallery-overlay">
                            <div class="gallery-overlay-content">
                                <h4>Casino Yayın</h4>
                                <p>Fotoğraflar</p>
                                <a href="assets/images/gallery-1.jpg" class="btn btn-primary btn-sm" data-fancybox="gallery" data-caption="Casino Yayın">
                                    <i class="fas fa-search"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-content">
                        <h3 class="gallery-title">Casino Yayın</h3>
                        <p class="gallery-category">Fotoğraflar</p>
                        <p class="gallery-description">Profesyonel casino yayın seti</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4 gallery-item photo-item" data-category="photos" data-aos="fade-up">
                <div class="gallery-card">
                    <div class="gallery-image">
                        <img src="assets/images/gallery-2.jpg" alt="Sosyal Medya" class="img-fluid">
                        <div class="gallery-overlay">
                            <div class="gallery-overlay-content">
                                <h4>Sosyal Medya</h4>
                                <p>Fotoğraflar</p>
                                <a href="assets/images/gallery-2.jpg" class="btn btn-primary btn-sm" data-fancybox="gallery" data-caption="Sosyal Medya">
                                    <i class="fas fa-search"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-content">
                        <h3 class="gallery-title">Sosyal Medya</h3>
                        <p class="gallery-category">Fotoğraflar</p>
                        <p class="gallery-description">Sosyal medya kampanyaları</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4 gallery-item photo-item" data-category="photos" data-aos="fade-up">
                <div class="gallery-card">
                    <div class="gallery-image">
                        <img src="assets/images/gallery-3.jpg" alt="Pazarlama" class="img-fluid">
                        <div class="gallery-overlay">
                            <div class="gallery-overlay-content">
                                <h4>Pazarlama</h4>
                                <p>Fotoğraflar</p>
                                <a href="assets/images/gallery-3.jpg" class="btn btn-primary btn-sm" data-fancybox="gallery" data-caption="Pazarlama">
                                    <i class="fas fa-search"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="gallery-content">
                        <h3 class="gallery-title">Pazarlama</h3>
                        <p class="gallery-category">Fotoğraflar</p>
                        <p class="gallery-description">Pazarlama kampanyaları</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4 gallery-item video-item" data-category="videos" data-aos="fade-up">
                <div class="gallery-card">
                    <div class="gallery-image">
                        <img src="assets/images/video-1.jpg" alt="Canlı Yayın" class="img-fluid">
                        <div class="gallery-overlay">
                            <div class="gallery-overlay-content">
                                <h4>Canlı Yayın</h4>
                                <p>Videolar</p>
                                <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="btn btn-primary btn-sm" data-fancybox="gallery" data-caption="Canlı Yayın">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                        </div>
                        <div class="video-play-icon">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    <div class="gallery-content">
                        <h3 class="gallery-title">Canlı Yayın</h3>
                        <p class="gallery-category">Videolar</p>
                        <p class="gallery-description">Profesyonel canlı yayın örneği</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4 gallery-item video-item" data-category="videos" data-aos="fade-up">
                <div class="gallery-card">
                    <div class="gallery-image">
                        <img src="assets/images/video-2.jpg" alt="Promosyon" class="img-fluid">
                        <div class="gallery-overlay">
                            <div class="gallery-overlay-content">
                                <h4>Promosyon</h4>
                                <p>Videolar</p>
                                <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="btn btn-primary btn-sm" data-fancybox="gallery" data-caption="Promosyon">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                        </div>
                        <div class="video-play-icon">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    <div class="gallery-content">
                        <h3 class="gallery-title">Promosyon</h3>
                        <p class="gallery-category">Videolar</p>
                        <p class="gallery-description">Casino promosyon videosu</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4 gallery-item video-item" data-category="videos" data-aos="fade-up">
                <div class="gallery-card">
                    <div class="gallery-image">
                        <img src="assets/images/video-3.jpg" alt="Eğitim" class="img-fluid">
                        <div class="gallery-overlay">
                            <div class="gallery-overlay-content">
                                <h4>Eğitim</h4>
                                <p>Videolar</p>
                                <a href="https://www.youtube.com/watch?v=dQw4w9WgXcQ" class="btn btn-primary btn-sm" data-fancybox="gallery" data-caption="Eğitim">
                                    <i class="fas fa-play"></i>
                                </a>
                            </div>
                        </div>
                        <div class="video-play-icon">
                            <i class="fas fa-play"></i>
                        </div>
                    </div>
                    <div class="gallery-content">
                        <h3 class="gallery-title">Eğitim</h3>
                        <p class="gallery-category">Videolar</p>
                        <p class="gallery-description">Eğitim ve tanıtım videosu</p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section bg-gradient-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="cta-title" data-aos="fade-up">
                    Daha Fazlasını Görün!
                </h2>
                <p class="cta-description" data-aos="fade-up" data-aos-delay="200">
                    Çalışmalarımızdan daha fazla örnek görmek için portföyümüzü inceleyin.
                </p>
                <div class="cta-buttons" data-aos="fade-up" data-aos-delay="400">
                    <a href="portfolio.php" class="btn btn-hero btn-hero-primary">
                        <i class="fas fa-eye me-2"></i>
                        Portföyümü Gör
                    </a>
                    <a href="contact.php" class="btn btn-hero btn-hero-secondary">
                        <i class="fas fa-envelope me-2"></i>
                        İletişime Geç
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Gallery filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const galleryItems = document.querySelectorAll('.gallery-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter items
            galleryItems.forEach(item => {
                if (filter === 'all' || item.getAttribute('data-category') === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
});
</script>

<?php
// Footer'ı dahil et
include 'includes/footer.php';
?>