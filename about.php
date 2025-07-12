<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Hakkımda Sayfası
 */

// Sayfa meta bilgileri
$page_title = get_site_text('about_title', 'Hakkımda - BonusBoss');
$page_description = get_site_text('about_subtitle', 'Casino dünyasında profesyonel deneyim');
$page_keywords = 'hakkımda, casino yayıncısı, deneyim, bonusboss';

// Header'ı dahil et
include 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header bg-gradient-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h1 class="page-title" data-aos="fade-up">
                    <?php echo get_site_text('about_title', 'Hakkımda'); ?>
                </h1>
                <p class="page-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo get_site_text('about_subtitle', 'Casino dünyasında profesyonel deneyim'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- About Story Section -->
<section class="section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="about-image">
                    <img src="assets/images/about-me.jpg" alt="BonusBoss" class="img-fluid rounded-3 shadow-lg">
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <div class="about-content">
                    <h2 class="section-title">
                        <?php echo get_site_text('about_story_title', 'Hikayem'); ?>
                    </h2>
                    <p class="lead">
                        <?php echo get_site_text('about_story_content', 'Casino sektöründe uzun yıllara dayanan deneyimimle, müşterilerime en kaliteli hizmeti sunmaya odaklanıyorum. Her projede başarı odaklı yaklaşımım ve güvenilir ortaklık anlayışımla sektörde öncü konumda yer alıyorum.'); ?>
                    </p>
                    <p>
                        Profesyonel ekipman ve deneyimle, casino dünyasında güvenilir bir marka haline geldim. 
                        Müşterilerimin başarısı için çalışırken, her zaman şeffaflık ve kalite prensiplerimden ödün vermiyorum.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Experience Section -->
<section class="section bg-gradient-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-title text-white" data-aos="fade-up">
                    <?php echo get_site_text('about_experience_title', 'Deneyim ve Başarılar'); ?>
                </h2>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="experience-card text-center">
                    <div class="experience-icon">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <h3 class="experience-number">5+</h3>
                    <p class="experience-label">Yıl Deneyim</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="experience-card text-center">
                    <div class="experience-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h3 class="experience-number">1000+</h3>
                    <p class="experience-label">Başarılı Proje</p>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="experience-card text-center">
                    <div class="experience-icon">
                        <i class="fas fa-smile"></i>
                    </div>
                    <h3 class="experience-number">%95</h3>
                    <p class="experience-label">Müşteri Memnuniyeti</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="experience-content text-center" data-aos="fade-up" data-aos-delay="400">
                    <p class="lead text-white">
                        <?php echo get_site_text('about_experience_content', '5+ yıl casino yayıncılığı deneyimi, 1000+ başarılı proje, %95 müşteri memnuniyeti oranı ile sektörde güvenilir bir marka haline geldim.'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Principles Section -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-title" data-aos="fade-up">
                    <?php echo get_site_text('about_principles_title', 'Çalışma Prensiplerim'); ?>
                </h2>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="principle-card text-center">
                    <div class="principle-icon">
                        <i class="fas fa-eye"></i>
                    </div>
                    <h3>Şeffaflık</h3>
                    <p>Tüm işlemlerde açık ve dürüst yaklaşım</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="principle-card text-center">
                    <div class="principle-icon">
                        <i class="fas fa-handshake"></i>
                    </div>
                    <h3>Güvenilirlik</h3>
                    <p>Verilen sözlerin tutulması ve güven inşa etme</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="principle-card text-center">
                    <div class="principle-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Kalite</h3>
                    <p>En yüksek kalitede hizmet sunma</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="principle-card text-center">
                    <div class="principle-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Sürekli Gelişim</h3>
                    <p>Kendini sürekli geliştirme ve yenilik</p>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-12">
                <div class="principles-content text-center" data-aos="fade-up" data-aos-delay="500">
                    <p class="lead">
                        <?php echo get_site_text('about_principles_content', 'Şeffaflık, güvenilirlik, kalite ve sürekli gelişim prensiplerimle her projede mükemmellik hedefliyorum.'); ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="cta-section bg-gradient-primary">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="cta-title" data-aos="fade-up">
                    Projeniz için hazırım!
                </h2>
                <p class="cta-description" data-aos="fade-up" data-aos-delay="200">
                    Casino dünyasında başarılı bir ortaklık için hemen iletişime geçin.
                </p>
                <div class="cta-buttons" data-aos="fade-up" data-aos-delay="400">
                    <a href="contact.php" class="btn btn-hero btn-hero-primary">
                        <i class="fas fa-envelope me-2"></i>
                        İletişime Geç
                    </a>
                    <a href="portfolio.php" class="btn btn-hero btn-hero-secondary">
                        <i class="fas fa-eye me-2"></i>
                        Portföyümü Gör
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