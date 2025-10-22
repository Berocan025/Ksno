-- BonusBoss Casino Yayıncısı Portföy Sitesi Veritabanı
-- Yazılımcı: BERAT K

-- Veritabanını oluştur
CREATE DATABASE IF NOT EXISTS bonusboss_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE bonusboss_db;

-- Kullanıcılar tablosu
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role ENUM('admin', 'editor') DEFAULT 'editor',
    is_active BOOLEAN DEFAULT TRUE,
    last_login DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Site ayarları tablosu
CREATE TABLE settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    setting_type ENUM('text', 'textarea', 'image', 'number', 'boolean') DEFAULT 'text',
    setting_group VARCHAR(50) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Site metinleri tablosu
CREATE TABLE site_texts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    text_key VARCHAR(100) UNIQUE NOT NULL,
    text_value TEXT,
    text_type ENUM('text', 'textarea', 'html') DEFAULT 'text',
    page_section VARCHAR(50) DEFAULT 'general',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Hizmetler tablosu
CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    icon VARCHAR(100),
    image_path VARCHAR(255),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Kategoriler tablosu
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Portföy tablosu
CREATE TABLE portfolio (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200) NOT NULL,
    description TEXT,
    image_path VARCHAR(255),
    client_name VARCHAR(200),
    project_date DATE,
    category_id INT,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
);

-- Galeri fotoğrafları tablosu
CREATE TABLE gallery_photos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    description TEXT,
    image_path VARCHAR(255) NOT NULL,
    alt_text VARCHAR(200),
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Galeri videoları tablosu
CREATE TABLE gallery_videos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    description TEXT,
    video_url VARCHAR(500) NOT NULL,
    thumbnail_path VARCHAR(255),
    video_type ENUM('youtube', 'vimeo', 'direct') DEFAULT 'youtube',
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- İletişim mesajları tablosu
CREATE TABLE contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    ip_address VARCHAR(45),
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Aktivite logları tablosu
CREATE TABLE activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(100) NOT NULL,
    table_name VARCHAR(50),
    record_id INT,
    old_values TEXT,
    new_values TEXT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Varsayılan admin kullanıcısı oluştur (şifre: admin123)
INSERT INTO users (username, email, password, full_name, role) VALUES 
('admin', 'admin@bonusboss.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'BonusBoss Admin', 'admin');

-- Varsayılan site ayarları
INSERT INTO settings (setting_key, setting_value, setting_type, setting_group) VALUES
('site_title', 'BonusBoss - Casino Yayıncısı', 'text', 'general'),
('site_description', 'Profesyonel casino yayıncısı BonusBoss resmi web sitesi', 'textarea', 'general'),
('site_keywords', 'casino, yayıncı, bonus, oyun, canlı yayın', 'text', 'general'),
('site_logo', '', 'image', 'general'),
('site_favicon', '', 'image', 'general'),
('contact_email', 'info@bonusboss.com', 'text', 'contact'),
('contact_phone', '+90 555 123 4567', 'text', 'contact'),
('contact_address', 'İstanbul, Türkiye', 'text', 'contact'),
('social_facebook', 'https://facebook.com/bonusboss', 'text', 'social'),
('social_twitter', 'https://twitter.com/bonusboss', 'text', 'social'),
('social_instagram', 'https://instagram.com/bonusboss', 'text', 'social'),
('social_youtube', 'https://youtube.com/bonusboss', 'text', 'social'),
('analytics_code', '', 'textarea', 'seo'),
('google_verification', '', 'text', 'seo'),
('maintenance_mode', '0', 'boolean', 'system');

-- Varsayılan site metinleri
INSERT INTO site_texts (text_key, text_value, text_type, page_section) VALUES
('hero_title', 'BonusBoss Casino Yayıncısı', 'text', 'home'),
('hero_subtitle', 'Profesyonel Casino Yayıncısı', 'text', 'home'),
('hero_description', 'Casino dünyasının en iyi yayıncısı olarak sizlere unutulmaz deneyimler yaşatıyoruz.', 'textarea', 'home'),
('about_title', 'Hakkımızda', 'text', 'about'),
('about_subtitle', 'Casino Yayıncılığında Uzman', 'text', 'about'),
('about_description', 'BonusBoss olarak casino yayıncılığında uzmanlaşmış bir ekibiz. Yılların deneyimi ile sizlere en kaliteli içerikleri sunuyoruz.', 'textarea', 'about'),
('services_title', 'Hizmetlerimiz', 'text', 'services'),
('services_subtitle', 'Sunduğumuz Hizmetler', 'text', 'services'),
('portfolio_title', 'Portföyümüz', 'text', 'portfolio'),
('portfolio_subtitle', 'Başarılı Projelerimiz', 'text', 'portfolio'),
('gallery_title', 'Galeri', 'text', 'gallery'),
('gallery_subtitle', 'Çalışmalarımızdan Örnekler', 'text', 'gallery'),
('contact_title', 'İletişim', 'text', 'contact'),
('contact_subtitle', 'Bizimle İletişime Geçin', 'text', 'contact'),
('footer_copyright', '© 2024 BonusBoss. Tüm hakları saklıdır.', 'text', 'footer');

-- Varsayılan kategoriler
INSERT INTO categories (name, slug, description) VALUES
('Casino Yayınları', 'casino-yayinlari', 'Canlı casino yayınları'),
('Slot Oyunları', 'slot-oyunlari', 'Slot oyunu yayınları'),
('Poker Yayınları', 'poker-yayinlari', 'Poker oyunu yayınları'),
('Bonus Avı', 'bonus-avi', 'Bonus avı yayınları'),
('Turnuvalar', 'turnuvalar', 'Turnuva yayınları');

-- Varsayılan hizmetler
INSERT INTO services (title, description, icon, sort_order) VALUES
('Canlı Casino Yayınları', 'Profesyonel ekipmanlarla yüksek kaliteli casino yayınları yapıyoruz.', 'fas fa-video', 1),
('Slot Oyunu Yayınları', 'En popüler slot oyunlarını canlı olarak yayınlıyoruz.', 'fas fa-dice', 2),
('Poker Yayınları', 'Uzman poker oyuncularımızla heyecan dolu poker yayınları.', 'fas fa-spades', 3),
('Bonus Avı', 'Casino bonuslarını avlayarak kazanç elde etme yayınları.', 'fas fa-search-dollar', 4),
('Turnuva Yayınları', 'Büyük casino turnuvalarını canlı olarak yayınlıyoruz.', 'fas fa-trophy', 5);

-- Varsayılan portföy öğeleri
INSERT INTO portfolio (title, description, client_name, project_date, category_id, sort_order) VALUES
('Casino Royale Yayını', 'Büyük casino turnuvasında canlı yayın deneyimi.', 'Casino Royale', '2024-01-15', 1, 1),
('Mega Slot Avı', 'Mega jackpot slot oyununda büyük kazanç avı.', 'Mega Slots', '2024-02-20', 2, 2),
('Poker Masters', 'Profesyonel poker turnuvasında heyecan dolu anlar.', 'Poker Masters', '2024-03-10', 3, 3);

-- İndeksler
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_settings_key ON settings(setting_key);
CREATE INDEX idx_site_texts_key ON site_texts(text_key);
CREATE INDEX idx_services_active ON services(is_active);
CREATE INDEX idx_portfolio_active ON portfolio(is_active);
CREATE INDEX idx_portfolio_category ON portfolio(category_id);
CREATE INDEX idx_gallery_photos_active ON gallery_photos(is_active);
CREATE INDEX idx_gallery_videos_active ON gallery_videos(is_active);
CREATE INDEX idx_contact_messages_read ON contact_messages(is_read);
CREATE INDEX idx_activity_logs_user ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_created ON activity_logs(created_at);