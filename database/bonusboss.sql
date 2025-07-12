-- BonusBoss Casino Yayıncısı Portföy Sitesi Veritabanı
-- Yazılımcı: BERAT K

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- --------------------------------------------------------
-- Veritabanı: bonusboss
-- --------------------------------------------------------

CREATE DATABASE IF NOT EXISTS `bonusboss` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `bonusboss`;

-- --------------------------------------------------------
-- Tablo: users (Admin kullanıcıları)
-- --------------------------------------------------------

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','editor') NOT NULL DEFAULT 'editor',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan admin kullanıcısı (şifre: admin123)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('admin', 'admin@bonusboss.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BonusBoss Admin', 'admin');

-- --------------------------------------------------------
-- Tablo: settings (Site ayarları)
-- --------------------------------------------------------

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text,
  `setting_type` enum('text','textarea','image','color','number','boolean') NOT NULL DEFAULT 'text',
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan site ayarları
INSERT INTO `settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_title', 'BonusBoss - Profesyonel Casino Yayıncısı', 'text', 'Site başlığı'),
('site_description', 'Kazançlı ortaklıklar için doğru adres', 'textarea', 'Site açıklaması'),
('site_keywords', 'casino, yayıncı, bonus, boss, profesyonel', 'text', 'Site anahtar kelimeleri'),
('site_logo', '', 'image', 'Site logosu'),
('site_favicon', '', 'image', 'Site favicon'),
('primary_color', '#FFD700', 'color', 'Ana renk (Altın)'),
('secondary_color', '#0099FF', 'color', 'İkincil renk (Mavi)'),
('dark_color', '#003366', 'color', 'Koyu renk'),
('contact_email', 'info@bonusboss.com', 'text', 'İletişim e-posta'),
('contact_phone', '+90 555 123 4567', 'text', 'İletişim telefon'),
('contact_address', 'İstanbul, Türkiye', 'text', 'İletişim adresi'),
('social_facebook', 'https://facebook.com/bonusboss', 'text', 'Facebook linki'),
('social_instagram', 'https://instagram.com/bonusboss', 'text', 'Instagram linki'),
('social_twitter', 'https://twitter.com/bonusboss', 'text', 'Twitter linki'),
('social_youtube', 'https://youtube.com/bonusboss', 'text', 'YouTube linki'),
('telegram_channel', 'https://t.me/bonusboss', 'text', 'Telegram kanalı'),
('telegram_group', 'https://t.me/bonusbossgroup', 'text', 'Telegram grubu'),
('working_hours', '7/24 Hizmet', 'text', 'Çalışma saatleri'),
('footer_text', '© 2024 BonusBoss. Tüm hakları saklıdır.', 'text', 'Footer metni');

-- --------------------------------------------------------
-- Tablo: site_texts (Tüm site metinleri)
-- --------------------------------------------------------

CREATE TABLE `site_texts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `text_key` varchar(100) NOT NULL,
  `text_value` text,
  `page_name` varchar(50) DEFAULT NULL,
  `section` varchar(50) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `text_key` (`text_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Ana sayfa metinleri
INSERT INTO `site_texts` (`text_key`, `text_value`, `page_name`, `section`, `description`) VALUES
('hero_title', 'BonusBoss - Profesyonel Casino Yayıncısı', 'home', 'hero', 'Ana sayfa hero başlığı'),
('hero_subtitle', 'Kazançlı ortaklıklar için doğru adres', 'home', 'hero', 'Ana sayfa hero alt başlığı'),
('hero_description', 'Casino dünyasında güvenilir ve profesyonel hizmet anlayışıyla, sizin başarınız için çalışıyoruz.', 'home', 'hero', 'Ana sayfa hero açıklaması'),
('hero_cta_primary', 'Hizmetlerimizi Keşfet', 'home', 'hero', 'Ana CTA butonu'),
('hero_cta_secondary', 'Portföyümü Gör', 'home', 'hero', 'İkincil CTA butonu'),
('services_section_title', 'Hizmetlerimiz', 'home', 'services', 'Hizmetler bölüm başlığı'),
('services_section_subtitle', 'Size sunduğumuz profesyonel hizmetler', 'home', 'services', 'Hizmetler bölüm alt başlığı'),
('portfolio_section_title', 'Son Projelerimiz', 'home', 'portfolio', 'Portföy bölüm başlığı'),
('portfolio_section_subtitle', 'Başarıyla tamamladığımız projelerden örnekler', 'home', 'portfolio', 'Portföy bölüm alt başlığı'),
('stats_section_title', 'Rakamlarla BonusBoss', 'home', 'stats', 'İstatistikler bölüm başlığı'),
('testimonials_section_title', 'Müşteri Yorumları', 'home', 'testimonials', 'Testimonial bölüm başlığı'),
('testimonials_section_subtitle', 'Müşterilerimizin bizim hakkımızda söyledikleri', 'home', 'testimonials', 'Testimonial bölüm alt başlığı');

-- Navigasyon metinleri
INSERT INTO `site_texts` (`text_key`, `text_value`, `page_name`, `section`, `description`) VALUES
('nav_home', 'Ana Sayfa', 'navigation', 'menu', 'Ana sayfa menü linki'),
('nav_about', 'Hakkımda', 'navigation', 'menu', 'Hakkımda menü linki'),
('nav_services', 'Hizmetler', 'navigation', 'menu', 'Hizmetler menü linki'),
('nav_portfolio', 'Portföy', 'navigation', 'menu', 'Portföy menü linki'),
('nav_gallery', 'Galeri', 'navigation', 'menu', 'Galeri menü linki'),
('nav_contact', 'İletişim', 'navigation', 'menu', 'İletişim menü linki'),
('nav_admin', 'Admin Panel', 'navigation', 'menu', 'Admin panel linki');

-- Hakkımda sayfası metinleri
INSERT INTO `site_texts` (`text_key`, `text_value`, `page_name`, `section`, `description`) VALUES
('about_title', 'Hakkımda', 'about', 'header', 'Hakkımda sayfa başlığı'),
('about_subtitle', 'Casino dünyasında profesyonel deneyim', 'about', 'header', 'Hakkımda sayfa alt başlığı'),
('about_story_title', 'Hikayem', 'about', 'story', 'Kişisel hikaye başlığı'),
('about_story_content', 'Casino sektöründe uzun yıllara dayanan deneyimimle, müşterilerime en kaliteli hizmeti sunmaya odaklanıyorum. Her projede başarı odaklı yaklaşımım ve güvenilir ortaklık anlayışımla sektörde öncü konumda yer alıyorum.', 'about', 'story', 'Kişisel hikaye içeriği'),
('about_experience_title', 'Deneyim ve Başarılar', 'about', 'experience', 'Deneyim başlığı'),
('about_experience_content', '5+ yıl casino yayıncılığı deneyimi, 1000+ başarılı proje, %95 müşteri memnuniyeti oranı ile sektörde güvenilir bir marka haline geldim.', 'about', 'experience', 'Deneyim içeriği'),
('about_principles_title', 'Çalışma Prensiplerim', 'about', 'principles', 'Prensipler başlığı'),
('about_principles_content', 'Şeffaflık, güvenilirlik, kalite ve sürekli gelişim prensiplerimle her projede mükemmellik hedefliyorum.', 'about', 'principles', 'Prensipler içeriği');

-- Hizmetler sayfası metinleri
INSERT INTO `site_texts` (`text_key`, `text_value`, `page_name`, `section`, `description`) VALUES
('services_title', 'Hizmetlerim', 'services', 'header', 'Hizmetler sayfa başlığı'),
('services_subtitle', 'Size sunduğumuz kapsamlı hizmetler', 'services', 'header', 'Hizmetler sayfa alt başlığı'),
('service_live_streaming_title', 'Canlı Yayın Hizmetleri', 'services', 'live_streaming', 'Canlı yayın hizmeti başlığı'),
('service_live_streaming_desc', 'Profesyonel ekipman ve deneyimle kaliteli canlı yayın hizmetleri sunuyoruz.', 'services', 'live_streaming', 'Canlı yayın hizmeti açıklaması'),
('service_social_media_title', 'Sosyal Medya Yönetimi', 'services', 'social_media', 'Sosyal medya hizmeti başlığı'),
('service_social_media_desc', 'Tüm sosyal medya platformlarında etkili içerik yönetimi ve strateji geliştirme.', 'services', 'social_media', 'Sosyal medya hizmeti açıklaması'),
('service_influencer_title', 'Influencer Pazarlama', 'services', 'influencer', 'Influencer hizmeti başlığı'),
('service_influencer_desc', 'Etkili influencer işbirlikleri ile markanızı güçlendirin.', 'services', 'influencer', 'Influencer hizmeti açıklaması'),
('service_ads_title', 'Meta Reklamları', 'services', 'ads', 'Reklam hizmeti başlığı'),
('service_ads_desc', 'Facebook, Instagram ve diğer platformlarda hedefli reklam kampanyaları.', 'services', 'ads', 'Reklam hizmeti açıklaması'),
('service_sms_title', 'SMS/Mail Kampanyaları', 'services', 'sms', 'SMS hizmeti başlığı'),
('service_sms_desc', 'Etkili SMS ve e-posta pazarlama kampanyaları ile müşteri etkileşimini artırın.', 'services', 'sms', 'SMS hizmeti açıklaması'),
('service_telegram_title', 'Telegram Grup Yönetimi', 'services', 'telegram', 'Telegram hizmeti başlığı'),
('service_telegram_desc', 'Telegram kanalları ve gruplarında profesyonel yönetim hizmetleri.', 'services', 'telegram', 'Telegram hizmeti açıklaması');

-- Portföy sayfası metinleri
INSERT INTO `site_texts` (`text_key`, `text_value`, `page_name`, `section`, `description`) VALUES
('portfolio_title', 'Portföyüm', 'portfolio', 'header', 'Portföy sayfa başlığı'),
('portfolio_subtitle', 'Başarıyla tamamladığımız projeler', 'portfolio', 'header', 'Portföy sayfa alt başlığı'),
('portfolio_filter_all', 'Tümü', 'portfolio', 'filter', 'Tümü filtresi'),
('portfolio_filter_casino', 'Casino', 'portfolio', 'filter', 'Casino filtresi'),
('portfolio_filter_streaming', 'Yayın', 'portfolio', 'filter', 'Yayın filtresi'),
('portfolio_filter_marketing', 'Pazarlama', 'portfolio', 'filter', 'Pazarlama filtresi'),
('portfolio_view_project', 'Projeyi Görüntüle', 'portfolio', 'buttons', 'Proje görüntüleme butonu');

-- Galeri sayfası metinleri
INSERT INTO `site_texts` (`text_key`, `text_value`, `page_name`, `section`, `description`) VALUES
('gallery_title', 'Galeri', 'gallery', 'header', 'Galeri sayfa başlığı'),
('gallery_subtitle', 'Çalışmalarımızdan görsel örnekler', 'gallery', 'header', 'Galeri sayfa alt başlığı'),
('gallery_filter_all', 'Tümü', 'gallery', 'filter', 'Tümü filtresi'),
('gallery_filter_photos', 'Fotoğraflar', 'gallery', 'filter', 'Fotoğraf filtresi'),
('gallery_filter_videos', 'Videolar', 'gallery', 'filter', 'Video filtresi');

-- İletişim sayfası metinleri
INSERT INTO `site_texts` (`text_key`, `text_value`, `page_name`, `section`, `description`) VALUES
('contact_title', 'İletişim', 'contact', 'header', 'İletişim sayfa başlığı'),
('contact_subtitle', 'Bizimle iletişime geçin', 'contact', 'header', 'İletişim sayfa alt başlığı'),
('contact_form_title', 'Mesaj Gönder', 'contact', 'form', 'İletişim formu başlığı'),
('contact_form_name', 'Adınız', 'contact', 'form', 'İsim alanı'),
('contact_form_email', 'E-posta Adresiniz', 'contact', 'form', 'E-posta alanı'),
('contact_form_subject', 'Konu', 'contact', 'form', 'Konu alanı'),
('contact_form_message', 'Mesajınız', 'contact', 'form', 'Mesaj alanı'),
('contact_form_submit', 'Mesaj Gönder', 'contact', 'form', 'Gönder butonu'),
('contact_info_title', 'İletişim Bilgileri', 'contact', 'info', 'İletişim bilgileri başlığı'),
('contact_quick_title', 'Hızlı İletişim', 'contact', 'quick', 'Hızlı iletişim başlığı');

-- Form ve buton metinleri
INSERT INTO `site_texts` (`text_key`, `text_value`, `page_name`, `section`, `description`) VALUES
('btn_read_more', 'Devamını Oku', 'buttons', 'general', 'Devamını oku butonu'),
('btn_view_all', 'Tümünü Gör', 'buttons', 'general', 'Tümünü gör butonu'),
('btn_contact', 'İletişime Geç', 'buttons', 'general', 'İletişime geç butonu'),
('btn_submit', 'Gönder', 'buttons', 'forms', 'Gönder butonu'),
('btn_cancel', 'İptal', 'buttons', 'forms', 'İptal butonu'),
('btn_save', 'Kaydet', 'buttons', 'forms', 'Kaydet butonu'),
('btn_edit', 'Düzenle', 'buttons', 'forms', 'Düzenle butonu'),
('btn_delete', 'Sil', 'buttons', 'forms', 'Sil butonu'),
('btn_back', 'Geri', 'buttons', 'navigation', 'Geri butonu'),
('btn_next', 'İleri', 'buttons', 'navigation', 'İleri butonu');

-- Sistem mesajları
INSERT INTO `site_texts` (`text_key`, `text_value`, `page_name`, `section`, `description`) VALUES
('success_message_sent', 'Mesajınız başarıyla gönderildi. En kısa sürede size dönüş yapacağız.', 'messages', 'success', 'Mesaj gönderme başarı mesajı'),
('error_message_sent', 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.', 'messages', 'error', 'Mesaj gönderme hata mesajı'),
('success_form_submitted', 'Form başarıyla gönderildi.', 'messages', 'success', 'Form gönderme başarı mesajı'),
('error_form_submitted', 'Form gönderilirken bir hata oluştu.', 'messages', 'error', 'Form gönderme hata mesajı'),
('success_data_saved', 'Veriler başarıyla kaydedildi.', 'messages', 'success', 'Veri kaydetme başarı mesajı'),
('error_data_saved', 'Veriler kaydedilirken bir hata oluştu.', 'messages', 'error', 'Veri kaydetme hata mesajı'),
('error_required_fields', 'Lütfen tüm gerekli alanları doldurun.', 'messages', 'error', 'Gerekli alan hatası'),
('error_invalid_email', 'Geçersiz e-posta adresi.', 'messages', 'error', 'Geçersiz e-posta hatası'),
('error_file_upload', 'Dosya yüklenirken bir hata oluştu.', 'messages', 'error', 'Dosya yükleme hatası'),
('error_not_found', 'Aradığınız sayfa bulunamadı.', 'messages', 'error', 'Sayfa bulunamadı hatası');

-- --------------------------------------------------------
-- Tablo: services (Hizmetler)
-- --------------------------------------------------------

CREATE TABLE `services` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `icon` varchar(100) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `content` longtext,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan hizmetler
INSERT INTO `services` (`title`, `description`, `icon`, `content`, `sort_order`) VALUES
('Canlı Yayın Hizmetleri', 'Profesyonel ekipman ve deneyimle kaliteli canlı yayın hizmetleri', 'fas fa-video', 'Detaylı canlı yayın hizmetleri açıklaması...', 1),
('Sosyal Medya Yönetimi', 'Tüm sosyal medya platformlarında etkili içerik yönetimi', 'fab fa-facebook', 'Sosyal medya yönetimi detayları...', 2),
('Influencer Pazarlama', 'Etkili influencer işbirlikleri ile markanızı güçlendirin', 'fas fa-star', 'Influencer pazarlama detayları...', 3),
('Meta Reklamları', 'Facebook, Instagram ve diğer platformlarda hedefli reklam kampanyaları', 'fab fa-facebook-f', 'Meta reklamları detayları...', 4),
('SMS/Mail Kampanyaları', 'Etkili SMS ve e-posta pazarlama kampanyaları', 'fas fa-envelope', 'SMS/Mail kampanyaları detayları...', 5),
('Telegram Grup Yönetimi', 'Telegram kanalları ve gruplarında profesyonel yönetim', 'fab fa-telegram-plane', 'Telegram yönetimi detayları...', 6);

-- --------------------------------------------------------
-- Tablo: categories (Kategoriler)
-- --------------------------------------------------------

CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text,
  `type` enum('portfolio','gallery') NOT NULL DEFAULT 'portfolio',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Varsayılan kategoriler
INSERT INTO `categories` (`name`, `slug`, `description`, `type`, `sort_order`) VALUES
('Casino', 'casino', 'Casino projeleri', 'portfolio', 1),
('Canlı Yayın', 'live-streaming', 'Canlı yayın projeleri', 'portfolio', 2),
('Pazarlama', 'marketing', 'Pazarlama projeleri', 'portfolio', 3),
('Fotoğraflar', 'photos', 'Fotoğraf galerisi', 'gallery', 1),
('Videolar', 'videos', 'Video galerisi', 'gallery', 2);

-- --------------------------------------------------------
-- Tablo: portfolio (Portföy projeleri)
-- --------------------------------------------------------

CREATE TABLE `portfolio` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `content` longtext,
  `image` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `client` varchar(255) DEFAULT NULL,
  `project_date` date DEFAULT NULL,
  `project_url` varchar(255) DEFAULT NULL,
  `is_featured` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `portfolio_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tablo: gallery_photos (Galeri fotoğrafları)
-- --------------------------------------------------------

CREATE TABLE `gallery_photos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `image` varchar(255) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `gallery_photos_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tablo: gallery_videos (Galeri videoları)
-- --------------------------------------------------------

CREATE TABLE `gallery_videos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `description` text,
  `video_url` varchar(255) NOT NULL,
  `thumbnail` varchar(255) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `gallery_videos_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Tablo: contact_messages (İletişim mesajları)
-- --------------------------------------------------------

CREATE TABLE `contact_messages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `is_spam` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- İndeksler
-- --------------------------------------------------------

ALTER TABLE `users` ADD INDEX `idx_username` (`username`);
ALTER TABLE `users` ADD INDEX `idx_email` (`email`);
ALTER TABLE `settings` ADD INDEX `idx_setting_key` (`setting_key`);
ALTER TABLE `site_texts` ADD INDEX `idx_text_key` (`text_key`);
ALTER TABLE `site_texts` ADD INDEX `idx_page_name` (`page_name`);
ALTER TABLE `services` ADD INDEX `idx_is_active` (`is_active`);
ALTER TABLE `services` ADD INDEX `idx_sort_order` (`sort_order`);
ALTER TABLE `categories` ADD INDEX `idx_type` (`type`);
ALTER TABLE `categories` ADD INDEX `idx_is_active` (`is_active`);
ALTER TABLE `portfolio` ADD INDEX `idx_category_id` (`category_id`);
ALTER TABLE `portfolio` ADD INDEX `idx_is_featured` (`is_featured`);
ALTER TABLE `portfolio` ADD INDEX `idx_is_active` (`is_active`);
ALTER TABLE `gallery_photos` ADD INDEX `idx_category_id` (`category_id`);
ALTER TABLE `gallery_photos` ADD INDEX `idx_is_active` (`is_active`);
ALTER TABLE `gallery_videos` ADD INDEX `idx_category_id` (`category_id`);
ALTER TABLE `gallery_videos` ADD INDEX `idx_is_active` (`is_active`);
ALTER TABLE `contact_messages` ADD INDEX `idx_is_read` (`is_read`);
ALTER TABLE `contact_messages` ADD INDEX `idx_created_at` (`created_at`);

COMMIT;