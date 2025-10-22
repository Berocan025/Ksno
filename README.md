# 🎰 BonusBoss - Profesyonel Casino Yayıncısı Portföy Sitesi

**Yazılımcı: BERAT K**

## 📋 Proje Hakkında

BonusBoss, profesyonel casino yayıncıları için tasarlanmış modern ve kapsamlı bir portföy sitesidir. Lüks casino atmosferi, responsive tasarım ve tam fonksiyonlu admin paneli ile birlikte gelir.

## ✨ Özellikler

### 🎨 Tasarım Özellikleri
- **Lüks Casino Teması**: Altın (#FFD700), mavi (#0099FF), koyu mavi (#003366) renk paleti
- **Responsive Tasarım**: Mobil, tablet ve desktop uyumlu
- **Modern Animasyonlar**: AOS, CSS3 animasyonlar, hover efektleri
- **Gradient Arka Planlar**: Profesyonel görünüm için gradient efektler
- **Parlak Efektler**: Glow animasyonları ve ışık efektleri

### 🛠️ Teknik Özellikler
- **Backend**: PHP 7.4+ (cPanel uyumlu)
- **Veritabanı**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5
- **Güvenlik**: SQL injection koruması, XSS koruması, CSRF token
- **SEO Optimize**: Meta tags, sitemap, robots.txt, structured data
- **Performans**: Optimize edilmiş kodlar, image lazy loading

### 📱 Sayfa Yapısı
- **Ana Sayfa**: Hero section, hizmetler, portföy, istatistikler, testimonial
- **Hakkımda**: Kişisel hikaye, deneyimler, prensipler
- **Hizmetler**: Canlı yayın, sosyal medya, influencer, reklamlar, SMS/Mail, Telegram
- **Portföy**: Kategorilere göre filtrelenebilir projeler
- **Galeri**: Fotoğraf ve video galerisi
- **İletişim**: İletişim formu, sosyal medya linkleri

### 🔧 Admin Panel Özellikleri
- **Dashboard**: Site istatistikleri, son mesajlar, hızlı işlemler
- **Metin Yönetimi**: Tüm site metinlerini düzenleme
- **İçerik Yönetimi**: Sayfa içeriklerini düzenleme
- **Portföy Yönetimi**: Proje ekleme/düzenleme/silme
- **Galeri Yönetimi**: Fotoğraf ve video yönetimi
- **Hizmet Yönetimi**: Hizmet ekleme/düzenleme
- **Mesaj Yönetimi**: İletişim mesajlarını görüntüleme
- **Site Ayarları**: Genel ayarlar, logo, sosyal medya

## 🚀 Kurulum

### Gereksinimler
- PHP 7.4 veya üzeri
- MySQL 5.7 veya üzeri
- cPanel hosting (önerilen)

### Kurulum Adımları

1. **Dosyaları Yükleme**
   ```bash
   # Tüm dosyaları cPanel File Manager'a yükleyin
   # veya FTP ile sunucuya yükleyin
   ```

2. **Veritabanı Oluşturma**
   ```sql
   # cPanel phpMyAdmin'de yeni veritabanı oluşturun
   # database/bonusboss.sql dosyasını import edin
   ```

3. **Veritabanı Ayarları**
   ```php
   # includes/config.php dosyasını düzenleyin
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'bonusboss');
   define('DB_USER', 'veritabani_kullanici_adi');
   define('DB_PASS', 'veritabani_sifresi');
   define('SITE_URL', 'https://siteniz.com');
   ```

4. **Dosya İzinleri**
   ```bash
   # uploads klasörüne yazma izni verin
   chmod 755 assets/uploads/
   ```

5. **Admin Girişi**
   - URL: `https://siteniz.com/admin/`
   - Kullanıcı adı: `admin`
   - Şifre: `admin123`

## 📁 Dosya Yapısı

```
/
├── index.php                 # Ana sayfa
├── about.php                 # Hakkımda sayfası
├── services.php              # Hizmetler sayfası
├── portfolio.php             # Portföy sayfası
├── gallery.php               # Galeri sayfası
├── contact.php               # İletişim sayfası
├── admin/                    # Admin paneli
│   ├── index.php             # Dashboard
│   ├── login.php             # Giriş sayfası
│   ├── logout.php            # Çıkış
│   ├── texts/                # Metin yönetimi
│   ├── content/              # İçerik yönetimi
│   ├── portfolio/            # Portföy yönetimi
│   ├── gallery/              # Galeri yönetimi
│   ├── services/             # Hizmet yönetimi
│   ├── messages/             # Mesaj yönetimi
│   └── settings/             # Site ayarları
├── assets/                   # Statik dosyalar
│   ├── css/                  # CSS dosyaları
│   ├── js/                   # JavaScript dosyaları
│   ├── images/               # Resimler
│   └── uploads/              # Yüklenen dosyalar
├── includes/                 # PHP include dosyaları
│   ├── config.php            # Veritabanı ve ayarlar
│   ├── header.php            # Header
│   └── footer.php            # Footer
└── database/                 # Veritabanı dosyaları
    └── bonusboss.sql         # Veritabanı yapısı
```

## 🎯 Dinamik Metin Sistemi

Site genelindeki tüm metinler veritabanından çekilir ve admin panelinden düzenlenebilir:

```php
// Metin çağırma
echo get_site_text('hero_title', 'Varsayılan Başlık');

// Admin panelinde düzenleme
// admin/texts/ klasöründen tüm metinler yönetilebilir
```

## 🔒 Güvenlik Özellikleri

- **SQL Injection Koruması**: Prepared statements kullanımı
- **XSS Koruması**: Input sanitization
- **CSRF Token**: Form güvenliği
- **Session Yönetimi**: Güvenli oturum kontrolü
- **File Upload Güvenliği**: Dosya türü ve boyut kontrolü
- **Rate Limiting**: API koruması

## 📊 Veritabanı Tabloları

- `users` - Admin kullanıcıları
- `settings` - Site ayarları
- `site_texts` - Tüm site metinleri
- `services` - Hizmetler
- `portfolio` - Portföy projeleri
- `gallery_photos` - Galeri fotoğrafları
- `gallery_videos` - Galeri videoları
- `contact_messages` - İletişim mesajları
- `categories` - Kategoriler

## 🎨 Özelleştirme

### Renk Değiştirme
```css
:root {
    --primary-color: #FFD700;    /* Altın */
    --secondary-color: #0099FF;  /* Mavi */
    --dark-color: #003366;       /* Koyu mavi */
}
```

### Logo Değiştirme
- `assets/images/` klasörüne logo dosyalarını yükleyin
- Admin panelinden logo ayarlarını güncelleyin

### Metin Değiştirme
- Admin paneli > Metin Yönetimi bölümünden tüm metinleri düzenleyin

## 📱 Responsive Tasarım

Site tüm cihazlarda mükemmel görünür:
- **Desktop**: 1200px+
- **Tablet**: 768px - 1199px
- **Mobile**: 320px - 767px

## 🚀 Performans Optimizasyonu

- **Image Optimization**: Otomatik resim sıkıştırma
- **CSS/JS Minification**: Dosya boyutu optimizasyonu
- **Lazy Loading**: Resim ve video lazy loading
- **Caching**: Akıllı cache sistemi
- **CDN Ready**: CDN kullanımına hazır

## 🔧 Bakım ve Güncelleme

### Düzenli Bakım
- Veritabanı yedekleme
- Log dosyalarını temizleme
- Güvenlik güncellemeleri
- Performans optimizasyonu

### Güncelleme
- Yeni özellikler ekleme
- Güvenlik yamaları
- Tasarım iyileştirmeleri

## 📞 Destek

**Yazılımcı: BERAT K**

- **E-posta**: [E-posta adresi]
- **Telegram**: [Telegram linki]
- **Website**: [Website linki]

## 📄 Lisans

Bu proje özel olarak BonusBoss için geliştirilmiştir. Tüm hakları saklıdır.

---

**🎰 BonusBoss - Profesyonel Casino Yayıncısı Portföy Sitesi**
**👨‍💻 Yazılımcı: BERAT K**
**🚀 cPanel Hosting Uyumlu**
