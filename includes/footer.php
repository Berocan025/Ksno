    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="footer-top">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-md-6">
                        <div class="footer-widget">
                            <div class="footer-logo">
                                <?php $logo = getSetting('site_logo'); ?>
                                <?php if ($logo): ?>
                                    <img src="<?= UPLOAD_PATH . $logo ?>" alt="<?= SITE_NAME ?>" class="logo-img">
                                <?php else: ?>
                                    <span class="logo-text"><?= SITE_NAME ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="footer-description">
                                <?= getSetting('site_description', 'Kazançlı ortaklıklar için doğru adres') ?>
                            </p>
                            <div class="social-links">
                                <?php $facebook = getSetting('social_facebook'); ?>
                                <?php if ($facebook): ?>
                                    <a href="<?= $facebook ?>" target="_blank" class="social-link">
                                        <i class="fab fa-facebook-f"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php $instagram = getSetting('social_instagram'); ?>
                                <?php if ($instagram): ?>
                                    <a href="<?= $instagram ?>" target="_blank" class="social-link">
                                        <i class="fab fa-instagram"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php $twitter = getSetting('social_twitter'); ?>
                                <?php if ($twitter): ?>
                                    <a href="<?= $twitter ?>" target="_blank" class="social-link">
                                        <i class="fab fa-twitter"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php $youtube = getSetting('social_youtube'); ?>
                                <?php if ($youtube): ?>
                                    <a href="<?= $youtube ?>" target="_blank" class="social-link">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php $telegram = getSetting('telegram_channel'); ?>
                                <?php if ($telegram): ?>
                                    <a href="<?= $telegram ?>" target="_blank" class="social-link">
                                        <i class="fab fa-telegram-plane"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-2 col-md-6">
                        <div class="footer-widget">
                            <h4 class="footer-title">Hızlı Linkler</h4>
                            <ul class="footer-links">
                                <li><a href="index.php">Ana Sayfa</a></li>
                                <li><a href="about.php">Hakkımda</a></li>
                                <li><a href="services.php">Hizmetler</a></li>
                                <li><a href="portfolio.php">Portföy</a></li>
                                <li><a href="gallery.php">Galeri</a></li>
                                <li><a href="contact.php">İletişim</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget">
                            <h4 class="footer-title">Hizmetlerimiz</h4>
                            <ul class="footer-links">
                                <li><a href="services.php#live-streaming">Canlı Yayın</a></li>
                                <li><a href="services.php#social-media">Sosyal Medya</a></li>
                                <li><a href="services.php#influencer">Influencer</a></li>
                                <li><a href="services.php#ads">Meta Reklamları</a></li>
                                <li><a href="services.php#sms">SMS/Mail</a></li>
                                <li><a href="services.php#telegram">Telegram</a></li>
                            </ul>
                        </div>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <div class="footer-widget">
                            <h4 class="footer-title">İletişim Bilgileri</h4>
                            <div class="contact-info">
                                <?php $phone = getSetting('contact_phone'); ?>
                                <?php if ($phone): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-phone"></i>
                                        <span><?= $phone ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php $email = getSetting('contact_email'); ?>
                                <?php if ($email): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-envelope"></i>
                                        <span><?= $email ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php $address = getSetting('contact_address'); ?>
                                <?php if ($address): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span><?= $address ?></span>
                                    </div>
                                <?php endif; ?>
                                
                                <?php $hours = getSetting('working_hours'); ?>
                                <?php if ($hours): ?>
                                    <div class="contact-item">
                                        <i class="fas fa-clock"></i>
                                        <span><?= $hours ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    <div class="copyright">
                        <p><?= getSetting('footer_text', '© 2024 BonusBoss. Tüm hakları saklıdır.') ?></p>
                    </div>
                    <div class="footer-bottom-links">
                        <a href="privacy.php">Gizlilik Politikası</a>
                        <a href="terms.php">Kullanım Şartları</a>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="back-to-top">
        <i class="fas fa-chevron-up"></i>
    </button>

    <!-- JavaScript Dosyaları -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Custom Scripts -->
    <script>
        // AOS Animation başlat
        AOS.init({
            duration: 1000,
            easing: 'ease-in-out',
            once: true
        });
        
        // Loading spinner gizle
        window.addEventListener('load', function() {
            const spinner = document.getElementById('loading-spinner');
            if (spinner) {
                spinner.style.opacity = '0';
                setTimeout(() => {
                    spinner.style.display = 'none';
                }, 300);
            }
        });
        
        // Back to top button
        const backToTop = document.getElementById('back-to-top');
        if (backToTop) {
            window.addEventListener('scroll', function() {
                if (window.pageYOffset > 300) {
                    backToTop.classList.add('show');
                } else {
                    backToTop.classList.remove('show');
                }
            });
            
            backToTop.addEventListener('click', function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });
        }
        
        // Mobile menu toggle
        const navbarToggler = document.getElementById('navbar-toggler');
        const navbarMenu = document.getElementById('navbar-menu');
        
        if (navbarToggler && navbarMenu) {
            navbarToggler.addEventListener('click', function() {
                navbarMenu.classList.toggle('active');
                navbarToggler.classList.toggle('active');
            });
        }
        
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>