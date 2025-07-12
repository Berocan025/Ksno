<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Ana Sayfa
 */

// Sayfa meta bilgileri
$page_title = get_site_text('hero_title', 'BonusBoss - Profesyonel Casino Yayıncısı');
$page_description = get_site_text('hero_description', 'Casino dünyasında güvenilir ve profesyonel hizmet anlayışıyla, sizin başarınız için çalışıyoruz.');
$page_keywords = 'casino, yayıncı, bonus, boss, profesyonel, canlı yayın, sosyal medya';

// Header'ı dahil et
include 'includes/header.php';

// Hizmetleri getir
$services_query = "SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6";
$services_result = $conn->query($services_query);

// Son portföy projelerini getir
$portfolio_query = "SELECT p.*, c.name as category_name FROM portfolio p 
                   LEFT JOIN categories c ON p.category_id = c.id 
                   WHERE p.is_active = 1 
                   ORDER BY p.is_featured DESC, p.created_at DESC 
                   LIMIT 6";
$portfolio_result = $conn->query($portfolio_query);
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-12">
                <div class="hero-content" data-aos="fade-up">
                    <h1 class="hero-title typing-animation" data-text="<?php echo get_site_text('hero_title', 'BonusBoss - Profesyonel Casino Yayıncısı'); ?>">
                        <?php echo get_site_text('hero_title', 'BonusBoss - Profesyonel Casino Yayıncısı'); ?>
                    </h1>
                    <h2 class="hero-subtitle" data-aos="fade-up" data-aos-delay="200">
                        <?php echo get_site_text('hero_subtitle', 'Kazançlı ortaklıklar için doğru adres'); ?>
                    </h2>
                    <p class="hero-description" data-aos="fade-up" data-aos-delay="400">
                        <?php echo get_site_text('hero_description', 'Casino dünyasında güvenilir ve profesyonel hizmet anlayışıyla, sizin başarınız için çalışıyoruz.'); ?>
                    </p>
                    <div class="hero-buttons" data-aos="fade-up" data-aos-delay="600">
                        <a href="services.php" class="btn btn-hero btn-hero-primary">
                            <i class="fas fa-rocket me-2"></i>
                            <?php echo get_site_text('hero_cta_primary', 'Hizmetlerimizi Keşfet'); ?>
                        </a>
                        <a href="portfolio.php" class="btn btn-hero btn-hero-secondary">
                            <i class="fas fa-eye me-2"></i>
                            <?php echo get_site_text('hero_cta_secondary', 'Portföyümü Gör'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section bg-gradient-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-title" data-aos="fade-up">
                    <?php echo get_site_text('services_section_title', 'Hizmetlerimiz'); ?>
                </h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo get_site_text('services_section_subtitle', 'Size sunduğumuz profesyonel hizmetler'); ?>
                </p>
            </div>
        </div>
        
        <div class="row">
            <?php 
            $service_count = 0;
            if ($services_result && $services_result->num_rows > 0):
                while ($service = $services_result->fetch_assoc()):
                    $service_count++;
            ?>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $service_count * 100; ?>">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="<?php echo $service['icon']; ?>"></i>
                    </div>
                    <h3 class="service-title"><?php echo $service['title']; ?></h3>
                    <p class="service-description"><?php echo $service['description']; ?></p>
                    <a href="services.php#service-<?php echo $service['id']; ?>" class="btn btn-outline-warning btn-sm">
                        <?php echo get_site_text('btn_read_more', 'Devamını Oku'); ?>
                    </a>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <!-- Varsayılan hizmetler -->
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3 class="service-title">Canlı Yayın Hizmetleri</h3>
                    <p class="service-description">Profesyonel ekipman ve deneyimle kaliteli canlı yayın hizmetleri sunuyoruz.</p>
                    <a href="services.php" class="btn btn-outline-warning btn-sm">
                        <?php echo get_site_text('btn_read_more', 'Devamını Oku'); ?>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <h3 class="service-title">Sosyal Medya Yönetimi</h3>
                    <p class="service-description">Tüm sosyal medya platformlarında etkili içerik yönetimi ve strateji geliştirme.</p>
                    <a href="services.php" class="btn btn-outline-warning btn-sm">
                        <?php echo get_site_text('btn_read_more', 'Devamını Oku'); ?>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="service-title">Influencer Pazarlama</h3>
                    <p class="service-description">Etkili influencer işbirlikleri ile markanızı güçlendirin.</p>
                    <a href="services.php" class="btn btn-outline-warning btn-sm">
                        <?php echo get_site_text('btn_read_more', 'Devamını Oku'); ?>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <h3 class="service-title">Meta Reklamları</h3>
                    <p class="service-description">Facebook, Instagram ve diğer platformlarda hedefli reklam kampanyaları.</p>
                    <a href="services.php" class="btn btn-outline-warning btn-sm">
                        <?php echo get_site_text('btn_read_more', 'Devamını Oku'); ?>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="service-title">SMS/Mail Kampanyaları</h3>
                    <p class="service-description">Etkili SMS ve e-posta pazarlama kampanyaları ile müşteri etkileşimini artırın.</p>
                    <a href="services.php" class="btn btn-outline-warning btn-sm">
                        <?php echo get_site_text('btn_read_more', 'Devamını Oku'); ?>
                    </a>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                <div class="service-card">
                    <div class="service-icon">
                        <i class="fab fa-telegram-plane"></i>
                    </div>
                    <h3 class="service-title">Telegram Grup Yönetimi</h3>
                    <p class="service-description">Telegram kanalları ve gruplarında profesyonel yönetim hizmetleri.</p>
                    <a href="services.php" class="btn btn-outline-warning btn-sm">
                        <?php echo get_site_text('btn_read_more', 'Devamını Oku'); ?>
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="row">
            <div class="col-lg-12 text-center" data-aos="fade-up" data-aos-delay="700">
                <a href="services.php" class="btn btn-hero btn-hero-primary">
                    <i class="fas fa-list me-2"></i>
                    <?php echo get_site_text('btn_view_all', 'Tüm Hizmetlerimizi Gör'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="stats-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-title text-white" data-aos="fade-up">
                    <?php echo get_site_text('stats_section_title', 'Rakamlarla BonusBoss'); ?>
                </h2>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-item">
                    <span class="stat-number" data-target="500">0</span>
                    <div class="stat-label">Başarılı Proje</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-item">
                    <span class="stat-number" data-target="50">0</span>
                    <div class="stat-label">Mutlu Müşteri</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-item">
                    <span class="stat-number" data-target="5">0</span>
                    <div class="stat-label">Yıllık Deneyim</div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-item">
                    <span class="stat-number" data-target="24">0</span>
                    <div class="stat-label">Saat Hizmet</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Portfolio Section -->
<section class="section bg-gradient-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-title" data-aos="fade-up">
                    <?php echo get_site_text('portfolio_section_title', 'Son Projelerimiz'); ?>
                </h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo get_site_text('portfolio_section_subtitle', 'Başarıyla tamamladığımız projelerden örnekler'); ?>
                </p>
            </div>
        </div>
        
        <div class="row">
            <?php 
            $portfolio_count = 0;
            if ($portfolio_result && $portfolio_result->num_rows > 0):
                while ($portfolio = $portfolio_result->fetch_assoc()):
                    $portfolio_count++;
            ?>
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="<?php echo $portfolio_count * 100; ?>">
                <div class="portfolio-item" data-category="<?php echo $portfolio['category_name']; ?>">
                    <img src="<?php echo SITE_URL; ?>/assets/uploads/<?php echo $portfolio['image']; ?>" 
                         alt="<?php echo $portfolio['title']; ?>" 
                         class="portfolio-image">
                    <div class="portfolio-overlay">
                        <div class="portfolio-content">
                            <h3 class="portfolio-title"><?php echo $portfolio['title']; ?></h3>
                            <p class="portfolio-category"><?php echo $portfolio['category_name']; ?></p>
                            <a href="portfolio-detail.php?id=<?php echo $portfolio['id']; ?>" class="btn btn-outline-light btn-sm">
                                <?php echo get_site_text('portfolio_view_project', 'Projeyi Görüntüle'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <!-- Varsayılan portföy öğeleri -->
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="portfolio-item" data-category="casino">
                    <img src="<?php echo SITE_URL; ?>/assets/images/portfolio-1.jpg" alt="Casino Projesi" class="portfolio-image">
                    <div class="portfolio-overlay">
                        <div class="portfolio-content">
                            <h3 class="portfolio-title">Casino Yayın Projesi</h3>
                            <p class="portfolio-category">Casino</p>
                            <a href="portfolio.php" class="btn btn-outline-light btn-sm">
                                <?php echo get_site_text('portfolio_view_project', 'Projeyi Görüntüle'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="portfolio-item" data-category="live-streaming">
                    <img src="<?php echo SITE_URL; ?>/assets/images/portfolio-2.jpg" alt="Canlı Yayın Projesi" class="portfolio-image">
                    <div class="portfolio-overlay">
                        <div class="portfolio-content">
                            <h3 class="portfolio-title">Canlı Yayın Projesi</h3>
                            <p class="portfolio-category">Canlı Yayın</p>
                            <a href="portfolio.php" class="btn btn-outline-light btn-sm">
                                <?php echo get_site_text('portfolio_view_project', 'Projeyi Görüntüle'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="portfolio-item" data-category="marketing">
                    <img src="<?php echo SITE_URL; ?>/assets/images/portfolio-3.jpg" alt="Pazarlama Projesi" class="portfolio-image">
                    <div class="portfolio-overlay">
                        <div class="portfolio-content">
                            <h3 class="portfolio-title">Pazarlama Projesi</h3>
                            <p class="portfolio-category">Pazarlama</p>
                            <a href="portfolio.php" class="btn btn-outline-light btn-sm">
                                <?php echo get_site_text('portfolio_view_project', 'Projeyi Görüntüle'); ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="row">
            <div class="col-lg-12 text-center" data-aos="fade-up" data-aos-delay="400">
                <a href="portfolio.php" class="btn btn-hero btn-hero-primary">
                    <i class="fas fa-briefcase me-2"></i>
                    <?php echo get_site_text('btn_view_all', 'Tüm Projelerimizi Gör'); ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section bg-gradient-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-title" data-aos="fade-up">
                    <?php echo get_site_text('testimonials_section_title', 'Müşteri Yorumları'); ?>
                </h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo get_site_text('testimonials_section_subtitle', 'Müşterilerimizin bizim hakkımızda söyledikleri'); ?>
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "BonusBoss ile çalışmak gerçekten harika bir deneyimdi. Profesyonel yaklaşımları ve kaliteli hizmetleri sayesinde hedeflerimize ulaştık."
                    </div>
                    <div class="testimonial-author">Ahmet Yılmaz</div>
                    <div class="testimonial-position">Casino Sahibi</div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Sosyal medya yönetimi konusunda çok başarılılar. Takipçi sayımız kısa sürede arttı ve etkileşim oranlarımız yükseldi."
                    </div>
                    <div class="testimonial-author">Fatma Demir</div>
                    <div class="testimonial-position">Pazarlama Müdürü</div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="testimonial-card">
                    <div class="testimonial-text">
                        "Canlı yayın hizmetleri konusunda gerçekten uzmanlar. Ekipmanları kaliteli ve teknik destekleri mükemmel."
                    </div>
                    <div class="testimonial-author">Mehmet Kaya</div>
                    <div class="testimonial-position">İçerik Üreticisi</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section bg-gradient-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-title text-white" data-aos="fade-up">
                    Projenizi Hayata Geçirmeye Hazır mısınız?
                </h2>
                <p class="section-subtitle text-white" data-aos="fade-up" data-aos-delay="200">
                    Hemen iletişime geçin ve başarılı bir ortaklık için ilk adımı atın.
                </p>
                <div class="hero-buttons" data-aos="fade-up" data-aos-delay="400">
                    <a href="contact.php" class="btn btn-hero btn-hero-secondary">
                        <i class="fas fa-envelope me-2"></i>
                        <?php echo get_site_text('btn_contact', 'İletişime Geç'); ?>
                    </a>
                    <a href="tel:<?php echo get_setting('contact_phone', '+90 555 123 4567'); ?>" class="btn btn-hero btn-hero-primary">
                        <i class="fas fa-phone me-2"></i>
                        Hemen Ara
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Footer'ı dahil et
include 'includes/footer.php';
?>