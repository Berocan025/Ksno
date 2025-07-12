<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Veritabanı ve genel site ayarları
 */

// Hata raporlama (production'da kapatın)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Hata yakalama fonksiyonu
function handle_error($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    
    $error_message = "Hata [$errno] $errstr\n";
    $error_message .= "Satır $errline dosyada $errfile\n";
    
    error_log($error_message);
    
    if (ini_get('display_errors')) {
        echo "<div style='background: #f8d7da; color: #721c24; padding: 10px; margin: 10px; border: 1px solid #f5c6cb; border-radius: 5px;'>";
        echo "<strong>Hata:</strong> $errstr<br>";
        echo "<strong>Dosya:</strong> $errfile<br>";
        echo "<strong>Satır:</strong> $errline";
        echo "</div>";
    }
    
    return true;
}

set_error_handler("handle_error");

// Zaman dilimi ayarı
date_default_timezone_set('Europe/Istanbul');

// Session başlat
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Veritabanı ayarları
define('DB_HOST', 'localhost');
define('DB_NAME', 'bonusboss');
define('DB_USER', 'root'); // cPanel'de veritabanı kullanıcı adınızı girin
define('DB_PASS', ''); // cPanel'de veritabanı şifrenizi girin
define('DB_CHARSET', 'utf8mb4');

// Site ayarları
define('SITE_URL', 'http://localhost'); // Site URL'nizi girin
define('SITE_NAME', 'BonusBoss');
define('ADMIN_EMAIL', 'admin@bonusboss.com');

// Dosya yükleme ayarları
define('UPLOAD_PATH', '../assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_VIDEO_TYPES', ['mp4', 'avi', 'mov', 'wmv']);

// Güvenlik ayarları
define('CSRF_TOKEN_NAME', 'bonusboss_csrf_token');
define('SESSION_TIMEOUT', 3600); // 1 saat
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_TIMEOUT', 900); // 15 dakika

// Veritabanı bağlantısı
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    error_log("PDO Veritabanı bağlantı hatası: " . $e->getMessage());
    $pdo = null;
}

// MySQLi bağlantısı (eski kodlar için)
try {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        error_log("MySQLi Veritabanı bağlantı hatası: " . $conn->connect_error);
        $conn = null;
    } else {
        $conn->set_charset(DB_CHARSET);
    }
} catch (Exception $e) {
    error_log("MySQLi Bağlantı hatası: " . $e->getMessage());
    $conn = null;
}

// Güvenlik fonksiyonları
function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

function generate_csrf_token() {
    if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
        $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
    }
    return $_SESSION[CSRF_TOKEN_NAME];
}

function verify_csrf_token($token) {
    return isset($_SESSION[CSRF_TOKEN_NAME]) && hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
}

function is_logged_in() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function is_admin() {
    return is_logged_in() && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}

function get_client_ip() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

// Site ayarlarını cache'le
function get_site_settings() {
    global $pdo;
    static $settings = null;
    
    if ($settings === null) {
        $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings;
}

// Site ayarı getir
function get_setting($key, $default = '') {
    $settings = get_site_settings();
    return isset($settings[$key]) ? $settings[$key] : $default;
}

// Site metni getir
function get_site_text($key, $default = '') {
    global $pdo;
    static $texts = null;
    
    if ($pdo === null) {
        return $default;
    }
    
    if ($texts === null) {
        try {
            $stmt = $pdo->query("SELECT text_key, text_value FROM site_texts");
            $texts = [];
            while ($row = $stmt->fetch()) {
                $texts[$row['text_key']] = $row['text_value'];
            }
        } catch (Exception $e) {
            error_log("Site metinleri yüklenirken hata: " . $e->getMessage());
            $texts = [];
        }
    }
    
    return isset($texts[$key]) ? $texts[$key] : $default;
}

// Dosya yükleme fonksiyonu
function upload_file($file, $destination, $allowed_types = ALLOWED_IMAGE_TYPES) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }
    
    $file_info = pathinfo($file['name']);
    $extension = strtolower($file_info['extension']);
    
    if (!in_array($extension, $allowed_types)) {
        return false;
    }
    
    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }
    
    $filename = uniqid() . '.' . $extension;
    $filepath = $destination . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }
    
    return false;
}

// SEO dostu URL oluştur
function create_slug($string) {
    $string = strtolower($string);
    $string = str_replace(['ç', 'ğ', 'ı', 'ö', 'ş', 'ü'], ['c', 'g', 'i', 'o', 's', 'u'], $string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    $string = trim($string, '-');
    return $string;
}

// Tarih formatla
function format_date($date, $format = 'd.m.Y') {
    return date($format, strtotime($date));
}

// Para formatla
function format_money($amount, $currency = '₺') {
    return $currency . number_format($amount, 2, ',', '.');
}

// Mesaj göster
function show_message($message, $type = 'info') {
    $_SESSION['message'] = [
        'text' => $message,
        'type' => $type
    ];
}

function get_message() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
        return $message;
    }
    return null;
}

// Sayfalama fonksiyonu
function paginate($total_records, $records_per_page, $current_page, $url_pattern) {
    $total_pages = ceil($total_records / $records_per_page);
    
    if ($total_pages <= 1) {
        return '';
    }
    
    $html = '<nav aria-label="Sayfalama"><ul class="pagination justify-content-center">';
    
    // Önceki sayfa
    if ($current_page > 1) {
        $html .= '<li class="page-item"><a class="page-link" href="' . sprintf($url_pattern, $current_page - 1) . '">Önceki</a></li>';
    }
    
    // Sayfa numaraları
    $start = max(1, $current_page - 2);
    $end = min($total_pages, $current_page + 2);
    
    for ($i = $start; $i <= $end; $i++) {
        $active = ($i == $current_page) ? ' active' : '';
        $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . sprintf($url_pattern, $i) . '">' . $i . '</a></li>';
    }
    
    // Sonraki sayfa
    if ($current_page < $total_pages) {
        $html .= '<li class="page-item"><a class="page-link" href="' . sprintf($url_pattern, $current_page + 1) . '">Sonraki</a></li>';
    }
    
    $html .= '</ul></nav>';
    
    return $html;
}

// Log fonksiyonu
function log_activity($action, $details = '') {
    global $pdo;
    
    if ($pdo === null) {
        return;
    }
    
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
    $ip = get_client_ip();
    
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$user_id, $action, $details, $ip, $_SERVER['HTTP_USER_AGENT'] ?? '']);
    } catch (Exception $e) {
        error_log("Activity log hatası: " . $e->getMessage());
    }
}

// Rate limiting
function check_rate_limit($action, $limit = 10, $timeframe = 3600) {
    global $pdo;
    
    $ip = get_client_ip();
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM rate_limits WHERE ip_address = ? AND action = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)");
    $stmt->execute([$ip, $action, $timeframe]);
    $count = $stmt->fetchColumn();
    
    if ($count >= $limit) {
        return false;
    }
    
    $stmt = $pdo->prepare("INSERT INTO rate_limits (ip_address, action) VALUES (?, ?)");
    $stmt->execute([$ip, $action]);
    
    return true;
}

// Cache fonksiyonları
function cache_set($key, $data, $ttl = 3600) {
    $cache_file = sys_get_temp_dir() . '/bonusboss_' . md5($key) . '.cache';
    $cache_data = [
        'data' => $data,
        'expires' => time() + $ttl
    ];
    return file_put_contents($cache_file, serialize($cache_data));
}

function cache_get($key) {
    $cache_file = sys_get_temp_dir() . '/bonusboss_' . md5($key) . '.cache';
    
    if (!file_exists($cache_file)) {
        return false;
    }
    
    $cache_data = unserialize(file_get_contents($cache_file));
    
    if ($cache_data['expires'] < time()) {
        unlink($cache_file);
        return false;
    }
    
    return $cache_data['data'];
}

function cache_delete($key) {
    $cache_file = sys_get_temp_dir() . '/bonusboss_' . md5($key) . '.cache';
    if (file_exists($cache_file)) {
        return unlink($cache_file);
    }
    return false;
}

// Debug fonksiyonu
function debug($data) {
    echo '<pre>';
    print_r($data);
    echo '</pre>';
}

// Mail gönderme fonksiyonu
function send_mail($to, $subject, $message, $from = null) {
    if ($from === null) {
        $from = ADMIN_EMAIL;
    }
    
    $headers = "From: " . $from . "\r\n";
    $headers .= "Reply-To: " . $from . "\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
    
    return mail($to, $subject, $message, $headers);
}

// Güvenli şifre hash'leme
function hash_password($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

function verify_password($password, $hash) {
    return password_verify($password, $hash);
}

// XSS koruması
function xss_clean($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// SQL injection koruması
function sql_escape($data) {
    global $conn;
    return $conn->real_escape_string($data);
}

// Dosya boyutu formatla
function format_file_size($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, 2) . ' ' . $units[$pow];
}

// Rastgele string oluştur
function generate_random_string($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $string;
}

// Dosya uzantısı kontrol
function get_file_extension($filename) {
    return strtolower(pathinfo($filename, PATHINFO_EXTENSION));
}

// Resim boyutlandırma
function resize_image($source_path, $destination_path, $width, $height, $quality = 80) {
    $image_info = getimagesize($source_path);
    $image_type = $image_info[2];
    
    switch ($image_type) {
        case IMAGETYPE_JPEG:
            $source = imagecreatefromjpeg($source_path);
            break;
        case IMAGETYPE_PNG:
            $source = imagecreatefrompng($source_path);
            break;
        case IMAGETYPE_GIF:
            $source = imagecreatefromgif($source_path);
            break;
        default:
            return false;
    }
    
    $source_width = imagesx($source);
    $source_height = imagesy($source);
    
    $destination = imagecreatetruecolor($width, $height);
    
    imagecopyresampled($destination, $source, 0, 0, 0, 0, $width, $height, $source_width, $source_height);
    
    switch ($image_type) {
        case IMAGETYPE_JPEG:
            imagejpeg($destination, $destination_path, $quality);
            break;
        case IMAGETYPE_PNG:
            imagepng($destination, $destination_path, round($quality / 10));
            break;
        case IMAGETYPE_GIF:
            imagegif($destination, $destination_path);
            break;
    }
    
    imagedestroy($source);
    imagedestroy($destination);
    
    return true;
}

// Aktif menü kontrolü
function is_active_menu($page) {
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    return ($current_page === $page) ? 'active' : '';
}

// Meta tag oluştur
function generate_meta_tags($title = '', $description = '', $keywords = '', $image = '') {
    $site_title = get_setting('site_title');
    $site_description = get_setting('site_description');
    $site_keywords = get_setting('site_keywords');
    
    $title = $title ? $title . ' - ' . $site_title : $site_title;
    $description = $description ?: $site_description;
    $keywords = $keywords ?: $site_keywords;
    
    $meta_tags = [
        '<meta charset="UTF-8">',
        '<meta name="viewport" content="width=device-width, initial-scale=1.0">',
        '<title>' . htmlspecialchars($title) . '</title>',
        '<meta name="description" content="' . htmlspecialchars($description) . '">',
        '<meta name="keywords" content="' . htmlspecialchars($keywords) . '">',
        '<meta name="author" content="BonusBoss">',
        '<meta name="robots" content="index, follow">',
        '<link rel="canonical" href="' . SITE_URL . $_SERVER['REQUEST_URI'] . '">'
    ];
    
    // Open Graph tags
    if ($image) {
        $meta_tags[] = '<meta property="og:image" content="' . htmlspecialchars($image) . '">';
    }
    $meta_tags[] = '<meta property="og:title" content="' . htmlspecialchars($title) . '">';
    $meta_tags[] = '<meta property="og:description" content="' . htmlspecialchars($description) . '">';
    $meta_tags[] = '<meta property="og:url" content="' . SITE_URL . $_SERVER['REQUEST_URI'] . '">';
    $meta_tags[] = '<meta property="og:type" content="website">';
    $meta_tags[] = '<meta property="og:site_name" content="' . htmlspecialchars($site_title) . '">';
    
    return implode("\n    ", $meta_tags);
}
?>