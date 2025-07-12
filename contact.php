<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * İletişim Sayfası
 */

// Config dosyasını dahil et
require_once 'includes/config.php';

// Sayfa meta bilgileri
$page_title = get_site_text('contact_title', 'İletişim - BonusBoss');
$page_description = get_site_text('contact_subtitle', 'Bizimle iletişime geçin');
$page_keywords = 'iletişim, casino yayıncısı, bonusboss, mesaj';

// Header'ı dahil et
include 'includes/header.php';

// İletişim formu işleme
$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['contact_form'])) {
    // CSRF token kontrolü
    if (!verify_csrf_token($_POST['csrf_token'])) {
        $message = 'Güvenlik hatası. Lütfen tekrar deneyin.';
        $message_type = 'error';
    } else {
        // Form verilerini al
        $name = clean_input($_POST['name']);
        $email = clean_input($_POST['email']);
        $subject = clean_input($_POST['subject']);
        $message_text = clean_input($_POST['message']);
        
        // Validasyon
        $errors = [];
        
        if (empty($name)) {
            $errors[] = 'Adınız gereklidir.';
        }
        
        if (empty($email)) {
            $errors[] = 'E-posta adresiniz gereklidir.';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Geçerli bir e-posta adresi girin.';
        }
        
        if (empty($subject)) {
            $errors[] = 'Konu gereklidir.';
        }
        
        if (empty($message_text)) {
            $errors[] = 'Mesajınız gereklidir.';
        }
        
        if (empty($errors)) {
            // Mesajı veritabanına kaydet
            $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message, ip_address) VALUES (?, ?, ?, ?, ?)");
            
            if ($stmt->execute([$name, $email, $subject, $message_text, get_client_ip()])) {
                $message = get_site_text('success_message_sent', 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.');
                $message_type = 'success';
                
                // Form alanlarını temizle
                $name = $email = $subject = $message_text = '';
            } else {
                $message = get_site_text('error_message_sent', 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.');
                $message_type = 'error';
            }
        } else {
            $message = implode('<br>', $errors);
            $message_type = 'error';
        }
    }
}
?>

<!-- Page Header -->
<section class="page-header bg-gradient-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h1 class="page-title" data-aos="fade-up">
                    <?php echo get_site_text('contact_title', 'İletişim'); ?>
                </h1>
                <p class="page-subtitle" data-aos="fade-up" data-aos-delay="200">
                    <?php echo get_site_text('contact_subtitle', 'Bizimle iletişime geçin'); ?>
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section -->
<section class="section">
    <div class="container">
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-5" data-aos="fade-right">
                <div class="contact-form-card">
                    <h2 class="section-title">
                        <?php echo get_site_text('contact_form_title', 'Mesaj Gönder'); ?>
                    </h2>
                    
                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message_type === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="" class="contact-form">
                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                        <input type="hidden" name="contact_form" value="1">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">
                                    <?php echo get_site_text('contact_form_name', 'Adınız'); ?> *
                                </label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo isset($name) ? htmlspecialchars($name) : ''; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">
                                    <?php echo get_site_text('contact_form_email', 'E-posta Adresiniz'); ?> *
                                </label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">
                                <?php echo get_site_text('contact_form_subject', 'Konu'); ?> *
                            </label>
                            <input type="text" class="form-control" id="subject" name="subject" value="<?php echo isset($subject) ? htmlspecialchars($subject) : ''; ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="message" class="form-label">
                                <?php echo get_site_text('contact_form_message', 'Mesajınız'); ?> *
                            </label>
                            <textarea class="form-control" id="message" name="message" rows="5" required><?php echo isset($message_text) ? htmlspecialchars($message_text) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>
                            <?php echo get_site_text('contact_form_submit', 'Mesaj Gönder'); ?>
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-4" data-aos="fade-left">
                <div class="contact-info-card">
                    <h3 class="section-title">
                        <?php echo get_site_text('contact_info_title', 'İletişim Bilgileri'); ?>
                    </h3>
                    
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="contact-details">
                            <h4>E-posta</h4>
                            <p><?php echo get_setting('contact_email', 'info@bonusboss.com'); ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-phone"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Telefon</h4>
                            <p><?php echo get_setting('contact_phone', '+90 555 123 4567'); ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Adres</h4>
                            <p><?php echo get_setting('contact_address', 'İstanbul, Türkiye'); ?></p>
                        </div>
                    </div>
                    
                    <div class="contact-info-item">
                        <div class="contact-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="contact-details">
                            <h4>Çalışma Saatleri</h4>
                            <p><?php echo get_setting('working_hours', '7/24 Hizmet'); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Contact -->
                <div class="quick-contact-card">
                    <h3 class="section-title">
                        <?php echo get_site_text('contact_quick_title', 'Hızlı İletişim'); ?>
                    </h3>
                    
                    <div class="quick-contact-buttons">
                        <a href="https://wa.me/905551234567" class="btn btn-success btn-lg w-100 mb-3" target="_blank">
                            <i class="fab fa-whatsapp me-2"></i>
                            WhatsApp
                        </a>
                        
                        <a href="<?php echo get_setting('telegram_channel', 'https://t.me/bonusboss'); ?>" class="btn btn-info btn-lg w-100 mb-3" target="_blank">
                            <i class="fab fa-telegram-plane me-2"></i>
                            Telegram Kanalı
                        </a>
                        
                        <a href="<?php echo get_setting('telegram_group', 'https://t.me/bonusbossgroup'); ?>" class="btn btn-primary btn-lg w-100" target="_blank">
                            <i class="fab fa-telegram-plane me-2"></i>
                            Telegram Grubu
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Social Media Section -->
<section class="section bg-gradient-dark">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 text-center">
                <h2 class="section-title text-white" data-aos="fade-up">
                    Sosyal Medyada Takip Edin
                </h2>
                <p class="section-subtitle text-white" data-aos="fade-up" data-aos-delay="200">
                    Güncel içerikler ve kampanyalar için sosyal medya hesaplarımızı takip edin
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="100">
                <a href="<?php echo get_setting('social_facebook', 'https://facebook.com/bonusboss'); ?>" class="social-card facebook" target="_blank">
                    <div class="social-icon">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <h3>Facebook</h3>
                    <p>Güncel paylaşımlar</p>
                </a>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="200">
                <a href="<?php echo get_setting('social_instagram', 'https://instagram.com/bonusboss'); ?>" class="social-card instagram" target="_blank">
                    <div class="social-icon">
                        <i class="fab fa-instagram"></i>
                    </div>
                    <h3>Instagram</h3>
                    <p>Görsel içerikler</p>
                </a>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="300">
                <a href="<?php echo get_setting('social_twitter', 'https://twitter.com/bonusboss'); ?>" class="social-card twitter" target="_blank">
                    <div class="social-icon">
                        <i class="fab fa-twitter"></i>
                    </div>
                    <h3>Twitter</h3>
                    <p>Anlık güncellemeler</p>
                </a>
            </div>
            
            <div class="col-lg-3 col-md-6 mb-4" data-aos="fade-up" data-aos-delay="400">
                <a href="<?php echo get_setting('social_youtube', 'https://youtube.com/bonusboss'); ?>" class="social-card youtube" target="_blank">
                    <div class="social-icon">
                        <i class="fab fa-youtube"></i>
                    </div>
                    <h3>YouTube</h3>
                    <p>Video içerikler</p>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section class="section">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="map-card" data-aos="fade-up">
                    <h3 class="section-title text-center">Konum</h3>
                    <div class="map-container">
                        <iframe 
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3010.275583369147!2d28.978358315414!3d41.008237979299!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14caa4c1f3d30f01%3A0x7c76cf3d3b0a5f1!2zVGFrc2ltIE1leWRhbsSxLCBHw7xtw7zFn3N1eXUsIDM0NDM1IEJleW_En2x1L8Swc3RhbmJ1bA!5e0!3m2!1str!2str!4v1640995200000!5m2!1str!2str" 
                            width="100%" 
                            height="400" 
                            style="border:0;" 
                            allowfullscreen="" 
                            loading="lazy">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Footer'ı dahil et
include 'includes/footer.php';
?>