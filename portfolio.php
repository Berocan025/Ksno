<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Portföy Sayfası
 */

// Sayfa meta bilgileri
$page_title = get_site_text('portfolio_title', 'Portföyüm - BonusBoss');
$page_description = get_site_text('portfolio_subtitle', 'Başarıyla tamamladığımız projeler');
$page_keywords = 'portföy, projeler, casino, yayıncı, bonusboss';

// Header'ı dahil et
include 'includes/header.php';

// Kategorileri getir
$categories_query = "SELECT * FROM categories WHERE type = 'portfolio' AND is_active = 1 ORDER BY sort_order ASC";
$categories_result = $conn->query($categories_query);

// Aktif kategori filtresi
$active_category = isset($_GET['category']) ? clean_input($_GET['category']) : 'all';

// Portföy projelerini getir
$portfolio_query = "SELECT p.*, c.name as category_name, c.slug as category_slug 
                   FROM portfolio p 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   WHERE p.is_active = 1";

if ($active_category !== 'all') {
    $portfolio_query .= " AND c.slug = '" . $conn->real_escape_string($active_category) . "'";
}

$portfolio_query .= " ORDER BY p.is_featured DESC, p.created_at DESC";
$portfolio_result = $conn->query($portfolio_query);
?>

<!-- Page Header -->
<section class="page-header bg-gradient-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h1 class="page-title" data-aos="fade-up">
                    <?php echo get_site_text('portfolio_title', 'Portföyüm'); ?>
                </h1>
                <p class="page-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo get_site_text('portfolio_subtitle', 'Başarıyla tamamladığımız projeler'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Portfolio Filter -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <div class="portfolio-filter" data-aos="fade-up">
                    <button class="filter-btn <?php echo $active_category === 'all' ? 'active' : ''; ?>" data-filter="all">
                        <?php echo get_site_text('portfolio_filter_all', 'Tümü'); ?>
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

<!-- Portfolio Grid -->
<section class="section">
    <div class="container">
        <div class="row">
            <?php 
            if ($portfolio_result && $portfolio_result->num_rows > 0):
                while ($project = $portfolio_result->fetch_assoc()):
            ?>
            <div class="col-lg-4 col-md-6 mb-4 portfolio-item" data-category="<?php echo $project['category_slug']; ?>" data-aos="fade-up">
                <div class="portfolio-card">
                    <div class="portfolio-image">
                        <?php if ($project['image']): ?>
                        <img src="assets/uploads/<?php echo $project['image']; ?>" alt="<?php echo $project['title']; ?>" class="img-fluid">
                        <?php else: ?>
                        <img src="assets/images/portfolio-placeholder.jpg" alt="<?php echo $project['title']; ?>" class="img-fluid">
                        <?php endif; ?>
                        <div class="portfolio-overlay">
                            <div class="portfolio-overlay-content">
                                <h4><?php echo $project['title']; ?></h4>
                                <p><?php echo $project['category_name']; ?></p>
                                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#portfolioModal<?php echo $project['id']; ?>">
                                    <?php echo get_site_text('portfolio_view_project', 'Projeyi Görüntüle'); ?>
                                </a>
                            </div>
                        </div>
                        <?php if ($project['is_featured']): ?>
                        <div class="portfolio-badge">
                            <i class="fas fa-star"></i> Öne Çıkan
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="portfolio-content">
                        <h3 class="portfolio-title"><?php echo $project['title']; ?></h3>
                        <p class="portfolio-category"><?php echo $project['category_name']; ?></p>
                        <p class="portfolio-description"><?php echo substr($project['description'], 0, 100); ?>...</p>
                    </div>
                </div>
            </div>
            
            <!-- Portfolio Modal -->
            <div class="modal fade" id="portfolioModal<?php echo $project['id']; ?>" tabindex="-1" aria-labelledby="portfolioModalLabel<?php echo $project['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="portfolioModalLabel<?php echo $project['id']; ?>"><?php echo $project['title']; ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-6">
                                    <?php if ($project['image']): ?>
                                    <img src="assets/uploads/<?php echo $project['image']; ?>" alt="<?php echo $project['title']; ?>" class="img-fluid rounded">
                                    <?php else: ?>
                                    <img src="assets/images/portfolio-placeholder.jpg" alt="<?php echo $project['title']; ?>" class="img-fluid rounded">
                                    <?php endif; ?>
                                </div>
                                <div class="col-lg-6">
                                    <h4>Proje Detayları</h4>
                                    <p><strong>Kategori:</strong> <?php echo $project['category_name']; ?></p>
                                    <?php if ($project['client']): ?>
                                    <p><strong>Müşteri:</strong> <?php echo $project['client']; ?></p>
                                    <?php endif; ?>
                                    <?php if ($project['project_date']): ?>
                                    <p><strong>Tarih:</strong> <?php echo format_date($project['project_date']); ?></p>
                                    <?php endif; ?>
                                    <p><strong>Açıklama:</strong></p>
                                    <p><?php echo $project['description']; ?></p>
                                    <?php if ($project['content']): ?>
                                    <div class="project-content">
                                        <?php echo $project['content']; ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <?php if ($project['project_url']): ?>
                            <a href="<?php echo $project['project_url']; ?>" class="btn btn-primary" target="_blank">
                                <i class="fas fa-external-link-alt me-2"></i>Projeyi Ziyaret Et
                            </a>
                            <?php endif; ?>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <!-- Varsayılan portföy projeleri -->
            <div class="col-lg-4 col-md-6 mb-4 portfolio-item" data-category="casino" data-aos="fade-up">
                <div class="portfolio-card">
                    <div class="portfolio-image">
                        <img src="assets/images/portfolio-1.jpg" alt="Casino Projesi 1" class="img-fluid">
                        <div class="portfolio-overlay">
                            <div class="portfolio-overlay-content">
                                <h4>Casino Yayın Projesi</h4>
                                <p>Casino</p>
                                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#portfolioModal1">
                                    Projeyi Görüntüle
                                </a>
                            </div>
                        </div>
                        <div class="portfolio-badge">
                            <i class="fas fa-star"></i> Öne Çıkan
                        </div>
                    </div>
                    <div class="portfolio-content">
                        <h3 class="portfolio-title">Casino Yayın Projesi</h3>
                        <p class="portfolio-category">Casino</p>
                        <p class="portfolio-description">Büyük ölçekli casino yayın projesi, canlı yayın ve sosyal medya entegrasyonu ile...</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4 portfolio-item" data-category="live-streaming" data-aos="fade-up">
                <div class="portfolio-card">
                    <div class="portfolio-image">
                        <img src="assets/images/portfolio-2.jpg" alt="Canlı Yayın Projesi" class="img-fluid">
                        <div class="portfolio-overlay">
                            <div class="portfolio-overlay-content">
                                <h4>Canlı Yayın Projesi</h4>
                                <p>Canlı Yayın</p>
                                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#portfolioModal2">
                                    Projeyi Görüntüle
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio-content">
                        <h3 class="portfolio-title">Canlı Yayın Projesi</h3>
                        <p class="portfolio-category">Canlı Yayın</p>
                        <p class="portfolio-description">Profesyonel canlı yayın seti kurulumu ve yayın yönetimi hizmetleri...</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4 portfolio-item" data-category="marketing" data-aos="fade-up">
                <div class="portfolio-card">
                    <div class="portfolio-image">
                        <img src="assets/images/portfolio-3.jpg" alt="Pazarlama Projesi" class="img-fluid">
                        <div class="portfolio-overlay">
                            <div class="portfolio-overlay-content">
                                <h4>Pazarlama Kampanyası</h4>
                                <p>Pazarlama</p>
                                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#portfolioModal3">
                                    Projeyi Görüntüle
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio-content">
                        <h3 class="portfolio-title">Pazarlama Kampanyası</h3>
                        <p class="portfolio-category">Pazarlama</p>
                        <p class="portfolio-description">Kapsamlı dijital pazarlama kampanyası, sosyal medya ve influencer işbirlikleri...</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4 portfolio-item" data-category="casino" data-aos="fade-up">
                <div class="portfolio-card">
                    <div class="portfolio-image">
                        <img src="assets/images/portfolio-4.jpg" alt="Casino Projesi 2" class="img-fluid">
                        <div class="portfolio-overlay">
                            <div class="portfolio-overlay-content">
                                <h4>Casino Promosyon Projesi</h4>
                                <p>Casino</p>
                                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#portfolioModal4">
                                    Projeyi Görüntüle
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio-content">
                        <h3 class="portfolio-title">Casino Promosyon Projesi</h3>
                        <p class="portfolio-category">Casino</p>
                        <p class="portfolio-description">Casino promosyon kampanyası, bonus sistemi ve müşteri çekme stratejileri...</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4 portfolio-item" data-category="live-streaming" data-aos="fade-up">
                <div class="portfolio-card">
                    <div class="portfolio-image">
                        <img src="assets/images/portfolio-5.jpg" alt="Yayın Projesi 2" class="img-fluid">
                        <div class="portfolio-overlay">
                            <div class="portfolio-overlay-content">
                                <h4>Multi-Platform Yayın</h4>
                                <p>Canlı Yayın</p>
                                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#portfolioModal5">
                                    Projeyi Görüntüle
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio-content">
                        <h3 class="portfolio-title">Multi-Platform Yayın</h3>
                        <p class="portfolio-category">Canlı Yayın</p>
                        <p class="portfolio-description">Çoklu platform canlı yayın sistemi, Facebook, Instagram ve YouTube entegrasyonu...</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4 portfolio-item" data-category="marketing" data-aos="fade-up">
                <div class="portfolio-card">
                    <div class="portfolio-image">
                        <img src="assets/images/portfolio-6.jpg" alt="Pazarlama Projesi 2" class="img-fluid">
                        <div class="portfolio-overlay">
                            <div class="portfolio-overlay-content">
                                <h4>Influencer Kampanyası</h4>
                                <p>Pazarlama</p>
                                <a href="#" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#portfolioModal6">
                                    Projeyi Görüntüle
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="portfolio-content">
                        <h3 class="portfolio-title">Influencer Kampanyası</h3>
                        <p class="portfolio-category">Pazarlama</p>
                        <p class="portfolio-description">Büyük ölçekli influencer pazarlama kampanyası, 50+ influencer ile işbirliği...</p>
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
                    Projenizi Hayata Geçirelim!
                </h2>
                <p class="cta-description" data-aos="fade-up" data-aos-delay="200">
                    Başarılı projelerimizden ilham alın ve sizin projenizi de gerçekleştirelim.
                </p>
                <div class="cta-buttons" data-aos="fade-up" data-aos-delay="400">
                    <a href="contact.php" class="btn btn-hero btn-hero-primary">
                        <i class="fas fa-envelope me-2"></i>
                        İletişime Geç
                    </a>
                    <a href="services.php" class="btn btn-hero btn-hero-secondary">
                        <i class="fas fa-list me-2"></i>
                        Hizmetlerimizi Gör
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Portfolio filter functionality
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const portfolioItems = document.querySelectorAll('.portfolio-item');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.getAttribute('data-filter');
            
            // Update active button
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Filter items
            portfolioItems.forEach(item => {
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