<?php
require_once 'config.php';

// Sayfa meta bilgileri
$pageTitle = getSiteText('about_title', 'Hakkımda') . ' - ' . SITE_NAME;
$pageDescription = getSiteText('about_story_content', 'Casino sektöründe uzun yıllara dayanan deneyimimle, müşterilerime en kaliteli hizmeti sunmaya odaklanıyorum.');
$pageKeywords = 'hakkımda, deneyim, casino yayıncısı, bonusboss, ' . getSetting('site_keywords', 'casino, yayıncı, bonus, boss, profesyonel');

// Header'ı dahil et
include 'includes/header.php';
?>

<!-- Page Header -->
<section class="hero" style="padding: 100px 0 60px;">
    <div class="hero-content">
        <div class="container">
            <h1 data-aos="fade-up"><?= getSiteText('about_title', 'Hakkımda') ?></h1>
            <p data-aos="fade-up" data-aos-delay="200"><?= getSiteText('about_subtitle', 'Casino dünyasında profesyonel deneyim') ?></p>
        </div>
    </div>
</section>

<!-- About Content -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8" data-aos="fade-right">
                <div class="card">
                    <h2><?= getSiteText('about_story_title', 'Hikayem') ?></h2>
                    <p><?= getSiteText('about_story_content', 'Casino sektöründe uzun yıllara dayanan deneyimimle, müşterilerime en kaliteli hizmeti sunmaya odaklanıyorum. Her projede başarı odaklı yaklaşımım ve güvenilir ortaklık anlayışımla sektörde öncü konumda yer alıyorum.') ?></p>
                </div>
                
                <div class="card mt-4">
                    <h2><?= getSiteText('about_experience_title', 'Deneyim ve Başarılar') ?></h2>
                    <p><?= getSiteText('about_experience_content', '5+ yıl casino yayıncılığı deneyimi, 1000+ başarılı proje, %95 müşteri memnuniyeti oranı ile sektörde güvenilir bir marka haline geldim.') ?></p>
                    
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <div class="experience-item">
                                <i class="fas fa-calendar-alt"></i>
                                <h4>5+ Yıl</h4>
                                <p>Deneyim</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="experience-item">
                                <i class="fas fa-project-diagram"></i>
                                <h4>1000+</h4>
                                <p>Proje</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <h2><?= getSiteText('about_principles_title', 'Çalışma Prensiplerim') ?></h2>
                    <p><?= getSiteText('about_principles_content', 'Şeffaflık, güvenilirlik, kalite ve sürekli gelişim prensiplerimle her projede mükemmellik hedefliyorum.') ?></p>
                    
                    <div class="principles-grid">
                        <div class="principle-item">
                            <i class="fas fa-eye"></i>
                            <h4>Şeffaflık</h4>
                            <p>Her adımda açık ve dürüst iletişim</p>
                        </div>
                        <div class="principle-item">
                            <i class="fas fa-shield-alt"></i>
                            <h4>Güvenilirlik</h4>
                            <p>Söz verdiğimiz her şeyi zamanında teslim</p>
                        </div>
                        <div class="principle-item">
                            <i class="fas fa-star"></i>
                            <h4>Kalite</h4>
                            <p>En yüksek standartlarda hizmet</p>
                        </div>
                        <div class="principle-item">
                            <i class="fas fa-chart-line"></i>
                            <h4>Gelişim</h4>
                            <p>Sürekli öğrenme ve yenilik</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4" data-aos="fade-left">
                <div class="card">
                    <h3>Hızlı İletişim</h3>
                    <div class="contact-info">
                        <?php $phone = getSetting('contact_phone'); ?>
                        <?php if ($phone): ?>
                            <div class="contact-item">
                                <i class="fas fa-phone"></i>
                                <div>
                                    <h5>Telefon</h5>
                                    <p><?= $phone ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php $email = getSetting('contact_email'); ?>
                        <?php if ($email): ?>
                            <div class="contact-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <h5>E-posta</h5>
                                    <p><?= $email ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php $address = getSetting('contact_address'); ?>
                        <?php if ($address): ?>
                            <div class="contact-item">
                                <i class="fas fa-map-marker-alt"></i>
                                <div>
                                    <h5>Adres</h5>
                                    <p><?= $address ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php $hours = getSetting('working_hours'); ?>
                        <?php if ($hours): ?>
                            <div class="contact-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <h5>Çalışma Saatleri</h5>
                                    <p><?= $hours ?></p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="contact.php" class="btn btn-primary">
                            <i class="fas fa-envelope"></i>
                            Mesaj Gönder
                        </a>
                    </div>
                </div>
                
                <div class="card mt-4">
                    <h3>Sosyal Medya</h3>
                    <div class="social-links">
                        <?php $facebook = getSetting('social_facebook'); ?>
                        <?php if ($facebook): ?>
                            <a href="<?= $facebook ?>" target="_blank" class="social-link">
                                <i class="fab fa-facebook-f"></i>
                                <span>Facebook</span>
                            </a>
                        <?php endif; ?>
                        
                        <?php $instagram = getSetting('social_instagram'); ?>
                        <?php if ($instagram): ?>
                            <a href="<?= $instagram ?>" target="_blank" class="social-link">
                                <i class="fab fa-instagram"></i>
                                <span>Instagram</span>
                            </a>
                        <?php endif; ?>
                        
                        <?php $twitter = getSetting('social_twitter'); ?>
                        <?php if ($twitter): ?>
                            <a href="<?= $twitter ?>" target="_blank" class="social-link">
                                <i class="fab fa-twitter"></i>
                                <span>Twitter</span>
                            </a>
                        <?php endif; ?>
                        
                        <?php $youtube = getSetting('social_youtube'); ?>
                        <?php if ($youtube): ?>
                            <a href="<?= $youtube ?>" target="_blank" class="social-link">
                                <i class="fab fa-youtube"></i>
                                <span>YouTube</span>
                            </a>
                        <?php endif; ?>
                        
                        <?php $telegram = getSetting('telegram_channel'); ?>
                        <?php if ($telegram): ?>
                            <a href="<?= $telegram ?>" target="_blank" class="social-link">
                                <i class="fab fa-telegram-plane"></i>
                                <span>Telegram</span>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Skills Section -->
<section class="section" style="background: var(--bg-darker);">
    <div class="container">
        <div class="section-title" data-aos="fade-up">
            <h2>Uzmanlık Alanlarım</h2>
            <p>Casino sektöründe sunduğumuz profesyonel hizmetler</p>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card text-center">
                    <div class="card-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h3>Canlı Yayın</h3>
                    <p>Profesyonel ekipman ve teknik bilgi ile kaliteli yayın hizmetleri</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="200">
                <div class="card text-center">
                    <div class="card-icon">
                        <i class="fas fa-share-alt"></i>
                    </div>
                    <h3>Sosyal Medya</h3>
                    <p>Etkili içerik yönetimi ve strateji geliştirme</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="300">
                <div class="card text-center">
                    <div class="card-icon">
                        <i class="fas fa-star"></i>
                    </div>
                    <h3>Influencer</h3>
                    <p>Başarılı influencer işbirlikleri ve pazarlama</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="400">
                <div class="card text-center">
                    <div class="card-icon">
                        <i class="fas fa-ad"></i>
                    </div>
                    <h3>Reklam</h3>
                    <p>Hedefli reklam kampanyaları ve optimizasyon</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="section">
    <div class="container">
        <div class="text-center" data-aos="fade-up">
            <h2>Projenizi Hayata Geçirmeye Hazır mısınız?</h2>
            <p class="mb-4">Deneyimli ekibimizle birlikte başarılı bir ortaklık kuralım.</p>
            <a href="contact.php" class="btn btn-primary">
                <i class="fas fa-phone"></i>
                Hemen İletişime Geçin
            </a>
        </div>
    </div>
</section>

<style>
.experience-item {
    text-align: center;
    padding: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    margin-bottom: 20px;
}

.experience-item i {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 10px;
}

.experience-item h4 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 5px;
    color: var(--text-color);
}

.experience-item p {
    color: var(--text-muted);
    margin: 0;
}

.principles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-top: 20px;
}

.principle-item {
    text-align: center;
    padding: 20px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    border: 1px solid var(--border-color);
}

.principle-item i {
    font-size: 2rem;
    color: var(--primary-color);
    margin-bottom: 15px;
}

.principle-item h4 {
    font-size: 1.2rem;
    font-weight: 600;
    margin-bottom: 10px;
    color: var(--text-color);
}

.principle-item p {
    color: var(--text-muted);
    font-size: 0.9rem;
    margin: 0;
}

.contact-info {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.contact-item {
    display: flex;
    align-items: flex-start;
    gap: 15px;
    padding: 15px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 10px;
    border: 1px solid var(--border-color);
}

.contact-item i {
    color: var(--primary-color);
    font-size: 1.2rem;
    margin-top: 2px;
}

.contact-item h5 {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 5px;
    color: var(--text-color);
}

.contact-item p {
    color: var(--text-muted);
    margin: 0;
    font-size: 0.9rem;
}

.social-links {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.social-links .social-link {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 15px;
    background: rgba(255, 255, 255, 0.05);
    border-radius: 8px;
    text-decoration: none;
    color: var(--text-color);
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
}

.social-links .social-link:hover {
    background: var(--gradient-primary);
    color: var(--bg-dark);
    transform: translateX(5px);
}

.social-links .social-link i {
    font-size: 1.2rem;
    width: 20px;
}

@media (max-width: 768px) {
    .principles-grid {
        grid-template-columns: 1fr;
    }
    
    .contact-item {
        flex-direction: column;
        text-align: center;
    }
    
    .social-links .social-link {
        justify-content: center;
    }
}
</style>

<?php
// Footer'ı dahil et
include 'includes/footer.php';
?>