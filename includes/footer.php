    </main>

    <!-- Footer -->
    <footer class="footer bg-dark text-light">
        <div class="container">
            <div class="row">
                <!-- Logo ve Açıklama -->
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="footer-logo mb-3">
                        <div class="logo">
                            <span class="logo-text">
                                <span class="logo-bonus">Bonus</span>
                                <span class="logo-boss">Boss</span>
                            </span>
                            <i class="fas fa-crown logo-icon"></i>
                        </div>
                    </div>
                    <p class="text-muted">
                        <?php echo get_setting('site_description', 'Kazançlı ortaklıklar için doğru adres'); ?>
                    </p>
                    <div class="social-links">
                        <a href="<?php echo get_setting('social_facebook', '#'); ?>" class="social-link" target="_blank">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="<?php echo get_setting('social_instagram', '#'); ?>" class="social-link" target="_blank">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="<?php echo get_setting('social_twitter', '#'); ?>" class="social-link" target="_blank">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="<?php echo get_setting('social_youtube', '#'); ?>" class="social-link" target="_blank">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="<?php echo get_setting('telegram_channel', '#'); ?>" class="social-link" target="_blank">
                            <i class="fab fa-telegram-plane"></i>
                        </a>
                    </div>
                </div>

                <!-- Hızlı Linkler -->
                <div class="col-lg-2 col-md-6 mb-4">
                    <h5 class="text-warning mb-3">Hızlı Linkler</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>" class="footer-link">Ana Sayfa</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/about.php" class="footer-link">Hakkımda</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/services.php" class="footer-link">Hizmetler</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/portfolio.php" class="footer-link">Portföy</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/gallery.php" class="footer-link">Galeri</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/contact.php" class="footer-link">İletişim</a></li>
                    </ul>
                </div>

                <!-- Hizmetler -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-warning mb-3">Hizmetlerimiz</h5>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo SITE_URL; ?>/services.php#live-streaming" class="footer-link">Canlı Yayın</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/services.php#social-media" class="footer-link">Sosyal Medya</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/services.php#influencer" class="footer-link">Influencer</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/services.php#ads" class="footer-link">Meta Reklamları</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/services.php#sms" class="footer-link">SMS/Mail</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/services.php#telegram" class="footer-link">Telegram</a></li>
                    </ul>
                </div>

                <!-- İletişim Bilgileri -->
                <div class="col-lg-3 col-md-6 mb-4">
                    <h5 class="text-warning mb-3">İletişim</h5>
                    <div class="contact-info">
                        <div class="contact-item mb-2">
                            <i class="fas fa-envelope text-warning me-2"></i>
                            <a href="mailto:<?php echo get_setting('contact_email', 'info@bonusboss.com'); ?>" class="footer-link">
                                <?php echo get_setting('contact_email', 'info@bonusboss.com'); ?>
                            </a>
                        </div>
                        <div class="contact-item mb-2">
                            <i class="fas fa-phone text-warning me-2"></i>
                            <a href="tel:<?php echo get_setting('contact_phone', '+90 555 123 4567'); ?>" class="footer-link">
                                <?php echo get_setting('contact_phone', '+90 555 123 4567'); ?>
                            </a>
                        </div>
                        <div class="contact-item mb-2">
                            <i class="fas fa-map-marker-alt text-warning me-2"></i>
                            <span class="text-muted"><?php echo get_setting('contact_address', 'İstanbul, Türkiye'); ?></span>
                        </div>
                        <div class="contact-item mb-2">
                            <i class="fas fa-clock text-warning me-2"></i>
                            <span class="text-muted"><?php echo get_setting('working_hours', '7/24 Hizmet'); ?></span>
                        </div>
                    </div>
                    
                    <!-- Telegram Butonları -->
                    <div class="telegram-buttons mt-3">
                        <a href="<?php echo get_setting('telegram_channel', '#'); ?>" class="btn btn-outline-warning btn-sm me-2" target="_blank">
                            <i class="fab fa-telegram-plane"></i> Kanal
                        </a>
                        <a href="<?php echo get_setting('telegram_group', '#'); ?>" class="btn btn-outline-warning btn-sm" target="_blank">
                            <i class="fab fa-telegram-plane"></i> Grup
                        </a>
                    </div>
                </div>
            </div>

            <!-- Alt Çizgi -->
            <hr class="border-secondary my-4">

            <!-- Alt Footer -->
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 text-muted">
                        <?php echo get_setting('footer_text', '© 2024 BonusBoss. Tüm hakları saklıdır.'); ?>
                    </p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 text-muted">
                        <small>Yazılımcı: <a href="#" class="text-warning">BERAT K</a></small>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="back-to-top" title="Yukarı Çık">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- WhatsApp Float Button -->
    <a href="https://wa.me/905551234567" class="whatsapp-float" target="_blank" title="WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- AOS Animation -->
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    
    <!-- Lightbox JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>
    
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>

    <!-- Page Specific Scripts -->
    <?php if (isset($page_scripts)): ?>
        <?php foreach ($page_scripts as $script): ?>
            <script src="<?php echo $script; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Google Analytics -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=GA_MEASUREMENT_ID"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'GA_MEASUREMENT_ID');
    </script>

    <!-- Schema.org Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Organization",
        "name": "BonusBoss",
        "description": "<?php echo get_setting('site_description', 'Kazançlı ortaklıklar için doğru adres'); ?>",
        "url": "<?php echo SITE_URL; ?>",
        "logo": "<?php echo SITE_URL; ?>/assets/images/logo.png",
        "contactPoint": {
            "@type": "ContactPoint",
            "telephone": "<?php echo get_setting('contact_phone', '+90 555 123 4567'); ?>",
            "contactType": "customer service",
            "email": "<?php echo get_setting('contact_email', 'info@bonusboss.com'); ?>"
        },
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "İstanbul",
            "addressCountry": "TR"
        },
        "sameAs": [
            "<?php echo get_setting('social_facebook', '#'); ?>",
            "<?php echo get_setting('social_instagram', '#'); ?>",
            "<?php echo get_setting('social_twitter', '#'); ?>",
            "<?php echo get_setting('social_youtube', '#'); ?>"
        ]
    }
    </script>

</body>
</html>