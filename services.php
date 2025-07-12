<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Hizmetler Sayfası
 */

// Config dosyasını dahil et
require_once 'includes/config.php';

// Sayfa meta bilgileri
$page_title = get_site_text('services_title', 'Hizmetlerim - BonusBoss');
$page_description = get_site_text('services_subtitle', 'Size sunduğumuz kapsamlı hizmetler');
$page_keywords = 'hizmetler, casino yayıncısı, canlı yayın, sosyal medya, pazarlama, bonusboss';

// Header'ı dahil et
include 'includes/header.php';

// Hizmetleri getir
$services_query = "SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC";
$services_result = $conn->query($services_query);
?>

<!-- Page Header -->
<section class="page-header bg-gradient-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h1 class="page-title" data-aos="fade-up">
                    <?php echo get_site_text('services_title', 'Hizmetlerim'); ?>
                </h1>
                <p class="page-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo get_site_text('services_subtitle', 'Size sunduğumuz kapsamlı hizmetler'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Services Overview -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-title" data-aos="fade-up">
                    Profesyonel Hizmetlerimiz
                </h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">
                    Casino dünyasında başarılı olmanız için ihtiyacınız olan tüm hizmetleri sunuyoruz
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
                <div class="service-card" id="service-<?php echo $service['id']; ?>">
                    <div class="service-icon">
                        <i class="<?php echo $service['icon']; ?>"></i>
                    </div>
                    <h3 class="service-title"><?php echo $service['title']; ?></h3>
                    <p class="service-description"><?php echo $service['description']; ?></p>
                    <div class="service-content">
                        <?php echo $service['content']; ?>
                    </div>
                </div>
            </div>
            <?php 
                endwhile;
            else:
            ?>
            <!-- Varsayılan hizmetler -->
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card" id="service-1">
                    <div class="service-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3 class="service-title">Canlı Yayın Hizmetleri</h3>
                    <p class="service-description">Profesyonel ekipman ve deneyimle kaliteli canlı yayın hizmetleri sunuyoruz.</p>
                    <div class="service-content">
                        <ul>
                            <li>HD kalitede canlı yayın</li>
                            <li>Profesyonel ses ekipmanı</li>
                            <li>Çoklu platform desteği</li>
                            <li>7/24 teknik destek</li>
                            <li>Özel yayın odaları</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card" id="service-2">
                    <div class="service-icon">
                        <i class="fab fa-facebook"></i>
                    </div>
                    <h3 class="service-title">Sosyal Medya Yönetimi</h3>
                    <p class="service-description">Tüm sosyal medya platformlarında etkili içerik yönetimi ve strateji geliştirme.</p>
                    <div class="service-content">
                        <ul>
                            <li>Facebook, Instagram, Twitter yönetimi</li>
                            <li>İçerik planlama ve oluşturma</li>
                            <li>Topluluk yönetimi</li>
                            <li>Analitik raporlama</li>
                            <li>Etkileşim artırma stratejileri</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="service-card" id="service-3">
                    <div class="service-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3 class="service-title">Influencer Pazarlama</h3>
                    <p class="service-description">Etkili influencer işbirlikleri ile markanızı güçlendirin.</p>
                    <div class="service-content">
                        <ul>
                            <li>Influencer seçimi ve analizi</li>
                            <li>Kampanya planlama</li>
                            <li>İçerik koordinasyonu</li>
                            <li>Performans takibi</li>
                            <li>ROI analizi</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="service-card" id="service-4">
                    <div class="service-icon">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <h3 class="service-title">Meta Reklamları</h3>
                    <p class="service-description">Facebook, Instagram ve diğer platformlarda hedefli reklam kampanyaları.</p>
                    <div class="service-content">
                        <ul>
                            <li>Hedef kitle analizi</li>
                            <li>Reklam tasarımı</li>
                            <li>Kampanya optimizasyonu</li>
                            <li>A/B testleri</li>
                            <li>Performans raporlama</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="500">
                <div class="service-card" id="service-5">
                    <div class="service-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="service-title">SMS/Mail Kampanyaları</h3>
                    <p class="service-description">Etkili SMS ve e-posta pazarlama kampanyaları ile müşteri etkileşimini artırın.</p>
                    <div class="service-content">
                        <ul>
                            <li>E-posta listesi yönetimi</li>
                            <li>Kişiselleştirilmiş mesajlar</li>
                            <li>Otomatik kampanyalar</li>
                            <li>Açılma oranı optimizasyonu</li>
                            <li>Dönüşüm takibi</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="600">
                <div class="service-card" id="service-6">
                    <div class="service-icon">
                        <i class="fab fa-telegram-plane"></i>
                    </div>
                    <h3 class="service-title">Telegram Grup Yönetimi</h3>
                    <p class="service-description">Telegram kanalları ve gruplarında profesyonel yönetim hizmetleri.</p>
                    <div class="service-content">
                        <ul>
                            <li>Grup ve kanal yönetimi</li>
                            <li>İçerik moderasyonu</li>
                            <li>Üye yönetimi</li>
                            <li>Otomatik bot entegrasyonu</li>
                            <li>Büyüme stratejileri</li>
                        </ul>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Why Choose Us -->
<section class="section bg-gradient-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-title text-white" data-aos="fade-up">
                    Neden BonusBoss?
                </h2>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3>7/24 Hizmet</h3>
                    <p>Her zaman yanınızdayız</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h3>Güvenilir</h3>
                    <p>%100 güvenilir hizmet</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3>Sonuç Odaklı</h3>
                    <p>Başarı garantisi</p>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <div class="feature-card text-center">
                    <div class="feature-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>Deneyimli Ekip</h3>
                    <p>Uzman kadro</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pricing Section -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-title" data-aos="fade-up">
                    Hizmet Paketlerimiz
                </h2>
                <p class="section-subtitle" data-aos="fade-up" data-aos-delay="200">
                    İhtiyaçlarınıza uygun esnek paketler
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="100">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>Başlangıç</h3>
                        <div class="price">₺1,000<span>/ay</span></div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li><i class="fas fa-check"></i> Canlı yayın desteği</li>
                            <li><i class="fas fa-check"></i> Sosyal medya yönetimi</li>
                            <li><i class="fas fa-check"></i> Temel analitik</li>
                            <li><i class="fas fa-check"></i> E-posta desteği</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <a href="contact.php" class="btn btn-primary">Başla</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="200">
                <div class="pricing-card featured">
                    <div class="pricing-header">
                        <h3>Profesyonel</h3>
                        <div class="price">₺2,500<span>/ay</span></div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li><i class="fas fa-check"></i> Tüm başlangıç özellikleri</li>
                            <li><i class="fas fa-check"></i> Influencer pazarlama</li>
                            <li><i class="fas fa-check"></i> Meta reklamları</li>
                            <li><i class="fas fa-check"></i> Telegram yönetimi</li>
                            <li><i class="fas fa-check"></i> SMS/Mail kampanyaları</li>
                            <li><i class="fas fa-check"></i> Öncelikli destek</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <a href="contact.php" class="btn btn-primary">Başla</a>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="pricing-card">
                    <div class="pricing-header">
                        <h3>Premium</h3>
                        <div class="price">₺5,000<span>/ay</span></div>
                    </div>
                    <div class="pricing-features">
                        <ul>
                            <li><i class="fas fa-check"></i> Tüm profesyonel özellikleri</li>
                            <li><i class="fas fa-check"></i> Özel strateji danışmanlığı</li>
                            <li><i class="fas fa-check"></i> 7/24 telefon desteği</li>
                            <li><i class="fas fa-check"></i> Özel raporlama</li>
                            <li><i class="fas fa-check"></i> Öncelikli hizmet</li>
                        </ul>
                    </div>
                    <div class="pricing-footer">
                        <a href="contact.php" class="btn btn-primary">Başla</a>
                    </div>
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
                    Hemen Başlayın!
                </h2>
                <p class="cta-description" data-aos="fade-up" data-aos-delay="200">
                    Casino dünyasında başarılı olmak için profesyonel hizmetlerimizden yararlanın.
                </p>
                <div class="cta-buttons" data-aos="fade-up" data-aos-delay="400">
                    <a href="contact.php" class="btn btn-hero btn-hero-primary">
                        <i class="fas fa-envelope me-2"></i>
                        İletişime Geç
                    </a>
                    <a href="portfolio.php" class="btn btn-hero btn-hero-secondary">
                        <i class="fas fa-eye me-2"></i>
                        Referanslarımızı Gör
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