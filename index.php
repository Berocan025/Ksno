<?php
require_once 'config.php';

// Sayfa meta bilgileri
$pageTitle = getSiteText('hero_title', 'BonusBoss - Profesyonel Casino Yayıncısı');
$pageDescription = getSiteText('hero_description', 'Casino dünyasında güvenilir ve profesyonel hizmet anlayışıyla, sizin başarınız için çalışıyoruz.');
$pageKeywords = getSetting('site_keywords', 'casino, yayıncı, bonus, boss, profesyonel');

// Header'ı dahil et
include 'includes/header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <div class="container">
            <h1 data-aos="fade-up"><?= getSiteText('hero_title', 'BonusBoss - Profesyonel Casino Yayıncısı') ?></h1>
            <p data-aos="fade-up" data-aos-delay="200"><?= getSiteText('hero_description', 'Casino dünyasında güvenilir ve profesyonel hizmet anlayışıyla, sizin başarınız için çalışıyoruz.') ?></p>
            <div class="hero-buttons" data-aos="fade-up" data-aos-delay="400">
                <a href="services.php" class="btn btn-primary">
                    <i class="fas fa-cogs"></i>
                    <?= getSiteText('hero_cta_primary', 'Hizmetlerimizi Keşfet') ?>
                </a>
                <a href="portfolio.php" class="btn btn-outline">
                    <i class="fas fa-briefcase"></i>
                    <?= getSiteText('hero_cta_secondary', 'Portföyümü Gör') ?>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Services Section -->
<section class="section">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2><?= getSiteText('services_section_title', 'Hizmetlerimiz') ?></h2>
            <p><?= getSiteText('services_section_subtitle', 'Size sunduğumuz profesyonel hizmetler') ?></p>
        </div>
        
        <div class="row">
            <?php
            // Hizmetleri veritabanından çek
            try {
                $stmt = $pdo->prepare("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC LIMIT 6");
                $stmt->execute();
                $services = $stmt->fetchAll();
            } catch (Exception $e) {
                $services = [];
            }
            
            // Varsayılan hizmetler
            if (empty($services)) {
                $services = [
                    [
                        'title' => 'Canlı Yayın Hizmetleri',
                        'description' => 'Profesyonel ekipman ve deneyimle kaliteli canlı yayın hizmetleri sunuyoruz.',
                        'icon' => 'fas fa-video'
                    ],
                    [
                        'title' => 'Sosyal Medya Yönetimi',
                        'description' => 'Tüm sosyal medya platformlarında etkili içerik yönetimi ve strateji geliştirme.',
                        'icon' => 'fas fa-share-alt'
                    ],
                    [
                        'title' => 'Influencer Pazarlama',
                        'description' => 'Etkili influencer işbirlikleri ile markanızı güçlendirin.',
                        'icon' => 'fas fa-star'
                    ],
                    [
                        'title' => 'Meta Reklamları',
                        'description' => 'Facebook, Instagram ve diğer platformlarda hedefli reklam kampanyaları.',
                        'icon' => 'fas fa-ad'
                    ],
                    [
                        'title' => 'SMS/Mail Kampanyaları',
                        'description' => 'Etkili SMS ve e-posta pazarlama kampanyaları ile müşteri etkileşimini artırın.',
                        'icon' => 'fas fa-envelope'
                    ],
                    [
                        'title' => 'Telegram Grup Yönetimi',
                        'description' => 'Telegram kanalları ve gruplarında profesyonel yönetim hizmetleri.',
                        'icon' => 'fab fa-telegram-plane'
                    ]
                ];
            }
            
            foreach ($services as $service):
            ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up">
                <div class="card">
                    <div class="card-icon">
                        <i class="<?= $service['icon'] ?? 'fas fa-cog' ?>"></i>
                    </div>
                    <h3><?= htmlspecialchars($service['title'] ?? 'Hizmet') ?></h3>
                    <p><?= htmlspecialchars($service['description'] ?? 'Hizmet açıklaması') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="services.php" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i>
                Tüm Hizmetlerimizi Gör
            </a>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="section" style="background: var(--bg-darker);">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2><?= getSiteText('stats_section_title', 'Rakamlarla BonusBoss') ?></h2>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card text-center">
                    <div class="card-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3><span data-counter="1000">0</span>+</h3>
                    <p>Mutlu Müşteri</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card text-center">
                    <div class="card-icon">
                        <i class="fas fa-project-diagram"></i>
                    </div>
                    <h3><span data-counter="500">0</span>+</h3>
                    <p>Tamamlanan Proje</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="card text-center">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3><span data-counter="5">0</span>+</h3>
                    <p>Yıllık Deneyim</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="card text-center">
                    <div class="card-icon">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <h3><span data-counter="95">0</span>%</h3>
                    <p>Müşteri Memnuniyeti</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Portfolio Section -->
<section class="section">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2><?= getSiteText('portfolio_section_title', 'Son Projelerimiz') ?></h2>
            <p><?= getSiteText('portfolio_section_subtitle', 'Başarıyla tamamladığımız projelerden örnekler') ?></p>
        </div>
        
        <div class="portfolio-grid">
            <?php
            // Portföy projelerini veritabanından çek
            try {
                $stmt = $pdo->prepare("SELECT * FROM portfolio WHERE is_active = 1 ORDER BY created_at DESC LIMIT 6");
                $stmt->execute();
                $portfolioItems = $stmt->fetchAll();
            } catch (Exception $e) {
                $portfolioItems = [];
            }
            
            // Varsayılan portföy öğeleri
            if (empty($portfolioItems)) {
                $portfolioItems = [
                    [
                        'title' => 'Casino Yayın Projesi',
                        'description' => 'Profesyonel casino canlı yayın hizmeti',
                        'category' => 'casino',
                        'image_path' => 'assets/images/portfolio1.jpg'
                    ],
                    [
                        'title' => 'Sosyal Medya Kampanyası',
                        'description' => 'Etkili sosyal medya pazarlama stratejisi',
                        'category' => 'marketing',
                        'image_path' => 'assets/images/portfolio2.jpg'
                    ],
                    [
                        'title' => 'Influencer İşbirliği',
                        'description' => 'Başarılı influencer pazarlama projesi',
                        'category' => 'influencer',
                        'image_path' => 'assets/images/portfolio3.jpg'
                    ]
                ];
            }
            
            foreach ($portfolioItems as $item):
            ?>
            <div class="portfolio-item" data-aos="fade-up" data-category="<?= htmlspecialchars($item['category'] ?? 'general') ?>">
                <img src="<?= htmlspecialchars($item['image_path'] ?? 'assets/images/placeholder.jpg') ?>" alt="<?= htmlspecialchars($item['title']) ?>" class="portfolio-image">
                <div class="portfolio-content">
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= htmlspecialchars($item['description']) ?></p>
                    <span class="portfolio-category"><?= htmlspecialchars(ucfirst($item['category'] ?? 'Genel')) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        
        <div class="text-center mt-4" data-aos="fade-up">
            <a href="portfolio.php" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i>
                Tüm Projelerimizi Gör
            </a>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="section" style="background: var(--bg-darker);">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2><?= getSiteText('testimonials_section_title', 'Müşteri Yorumları') ?></h2>
            <p><?= getSiteText('testimonials_section_subtitle', 'Müşterilerimizin bizim hakkımızda söyledikleri') ?></p>
        </div>
        
        <div class="testimonial-slider" data-aos="fade-up">
            <div class="testimonial-item">
                <div class="card text-center">
                    <div class="card-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p>"BonusBoss ile çalışmak gerçekten harika bir deneyimdi. Profesyonel yaklaşımları ve kaliteli hizmetleri sayesinde hedeflerimize ulaştık."</p>
                    <h4>Ahmet Yılmaz</h4>
                    <span>Casino Sahibi</span>
                </div>
            </div>
            <div class="testimonial-item">
                <div class="card text-center">
                    <div class="card-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p>"Sosyal medya yönetimi konusunda çok başarılılar. Takipçi sayımız ve etkileşim oranlarımız önemli ölçüde arttı."</p>
                    <h4>Fatma Demir</h4>
                    <span>Pazarlama Müdürü</span>
                </div>
            </div>
            <div class="testimonial-item">
                <div class="card text-center">
                    <div class="card-icon">
                        <i class="fas fa-quote-left"></i>
                    </div>
                    <p>"Influencer işbirliklerimizde mükemmel sonuçlar aldık. BonusBoss ekibi her zaman güvenilir ve profesyonel."</p>
                    <h4>Mehmet Kaya</h4>
                    <span>İş Geliştirme Uzmanı</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Contact CTA Section -->
<section class="section">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h2>Projenizi Hayata Geçirmeye Hazır mısınız?</h2>
            <p class="mb-4">Bizimle iletişime geçin ve başarılı bir ortaklık için ilk adımı atın.</p>
            <a href="contact.php" class="btn btn-primary">
                <i class="fas fa-phone"></i>
                Hemen İletişime Geçin
            </a>
        </div>
    </div>
</section>

<?php
// Footer'ı dahil et
include 'includes/footer.php';
?>