<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Ana Konfigürasyon Dosyası
 */

// Hata raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session başlat
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Zaman dilimi
date_default_timezone_set('Europe/Istanbul');

// Veritabanı bağlantı bilgileri
define('DB_HOST', 'localhost');
define('DB_NAME', 'bonusboss');
define('DB_USER', 'root');
define('DB_PASS', ''); // Şifrenizi buraya yazın
define('DB_CHARSET', 'utf8mb4');

// Site sabitleri
define('SITE_URL', 'http://localhost/bonusboss');
define('SITE_NAME', 'BonusBoss');
define('ADMIN_EMAIL', 'admin@bonusboss.com');
define('UPLOAD_PATH', 'assets/uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('SESSION_TIMEOUT', 3600); // 1 saat

// PDO veritabanı bağlantısı
$pdo = null;
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Veritabanı bağlantı hatası durumunda site çalışmaya devam etsin
    error_log("Veritabanı bağlantı hatası: " . $e->getMessage());
    $pdo = null;
}

/**
 * Güvenlik fonksiyonları
 */

// Giriş kontrolü
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Admin kontrolü
function isAdmin() {
    return isLoggedIn() && $_SESSION['role'] === 'admin';
}

// CSRF token oluştur
function generateCSRFToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

// CSRF token doğrula
function verifyCSRFToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

// Input temizleme
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Güvenli şifre hash
function hashPassword($password) {
    return password_hash($password, PASSWORD_DEFAULT);
}

// Şifre doğrulama
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Site ayarları fonksiyonları
 */

// Site ayarı al
function getSetting($key, $default = '') {
    global $pdo;
    if (!$pdo) return $default;
    
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

// Site ayarı güncelle
function updateSetting($key, $value) {
    global $pdo;
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        return $stmt->execute([$value, $key]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Site metinleri fonksiyonları
 */

// Site metni al
function getSiteText($key, $default = '') {
    global $pdo;
    if (!$pdo) return $default;
    
    try {
        $stmt = $pdo->prepare("SELECT text_value FROM site_texts WHERE text_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['text_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

// Site metni güncelle
function updateSiteText($key, $value) {
    global $pdo;
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("UPDATE site_texts SET text_value = ? WHERE text_key = ?");
        return $stmt->execute([$value, $key]);
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Dosya yükleme fonksiyonları
 */

// Güvenli dosya yükleme
function uploadFile($file, $directory, $allowedTypes = ALLOWED_IMAGE_TYPES) {
    if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return false;
    }

    $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($fileExtension, $allowedTypes)) {
        return false;
    }

    $uploadDir = $directory;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $filename = uniqid() . '_' . time() . '.' . $fileExtension;
    $filepath = $uploadDir . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return $filename;
    }

    return false;
}

// Dosya silme
function deleteFile($filename, $directory) {
    $filepath = $directory . $filename;
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

/**
 * Aktivite log fonksiyonları
 */

// Aktivite logu
function logActivity($table, $recordId, $action, $oldValues = null, $newValues = null) {
    global $pdo;
    if (!$pdo) return false;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO activity_logs (user_id, action, table_name, record_id, old_values, new_values, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'] ?? null,
            $action,
            $table,
            $recordId,
            $oldValues ? json_encode($oldValues) : null,
            $newValues ? json_encode($newValues) : null,
            $_SERVER['REMOTE_ADDR'] ?? null,
            $_SERVER['HTTP_USER_AGENT'] ?? null
        ]);
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Yardımcı fonksiyonlar
 */

// Tarih formatla
function formatDate($date, $format = 'd.m.Y H:i') {
    return date($format, strtotime($date));
}

// Para formatla
function formatMoney($amount, $currency = '₺') {
    return $currency . number_format($amount, 2, ',', '.');
}

// Slug oluştur
function createSlug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

// Rastgele string oluştur
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $string = '';
    for ($i = 0; $i < $length; $i++) {
        $string .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $string;
}

// E-posta doğrulama
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// URL doğrulama
function isValidURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Mesaj fonksiyonları
 */

// Başarı mesajı
function setSuccessMessage($message) {
    $_SESSION['success'] = $message;
}

// Hata mesajı
function setErrorMessage($message) {
    $_SESSION['error'] = $message;
}

// Uyarı mesajı
function setWarningMessage($message) {
    $_SESSION['warning'] = $message;
}

// Bilgi mesajı
function setInfoMessage($message) {
    $_SESSION['info'] = $message;
}

// Mesaj al ve sil
function getMessage($type = 'success') {
    $message = $_SESSION[$type] ?? null;
    if ($message) {
        unset($_SESSION[$type]);
    }
    return $message;
}

/**
 * Sayfalama fonksiyonları
 */

// Sayfalama oluştur
function createPagination($totalRecords, $perPage, $currentPage, $baseUrl) {
    $totalPages = ceil($totalRecords / $perPage);
    $pagination = [];
    
    if ($totalPages > 1) {
        $pagination['current_page'] = $currentPage;
        $pagination['total_pages'] = $totalPages;
        $pagination['total_records'] = $totalRecords;
        $pagination['per_page'] = $perPage;
        $pagination['offset'] = ($currentPage - 1) * $perPage;
        
        // Sayfa linkleri
        $pagination['pages'] = [];
        for ($i = 1; $i <= $totalPages; $i++) {
            $pagination['pages'][] = [
                'number' => $i,
                'url' => $baseUrl . '?page=' . $i,
                'active' => $i == $currentPage
            ];
        }
        
        // Önceki/sonraki linkleri
        if ($currentPage > 1) {
            $pagination['prev_url'] = $baseUrl . '?page=' . ($currentPage - 1);
        }
        if ($currentPage < $totalPages) {
            $pagination['next_url'] = $baseUrl . '?page=' . ($currentPage + 1);
        }
    }
    
    return $pagination;
}

/**
 * SEO fonksiyonları
 */

// Meta title oluştur
function generateMetaTitle($title) {
    $siteTitle = getSetting('site_title', SITE_NAME);
    return $title . ' - ' . $siteTitle;
}

// Meta description oluştur
function generateMetaDescription($description) {
    return substr(strip_tags($description), 0, 160);
}

// Canonical URL oluştur
function generateCanonicalURL($path = '') {
    return SITE_URL . '/' . ltrim($path, '/');
}

/**
 * Güvenlik kontrolleri
 */

// Session timeout kontrolü
function checkSessionTimeout() {
    if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
        session_destroy();
        header('Location: login.php');
        exit;
    }
    $_SESSION['last_activity'] = time();
}

// IP adresi al
function getClientIP() {
    $ipKeys = ['HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'HTTP_CLIENT_IP', 'REMOTE_ADDR'];
    foreach ($ipKeys as $key) {
        if (array_key_exists($key, $_SERVER) === true) {
            foreach (explode(',', $_SERVER[$key]) as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                    return $ip;
                }
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

// Rate limiting kontrolü
function checkRateLimit($action, $limit = 10, $period = 3600) {
    global $pdo;
    if (!$pdo) return true;
    
    $ip = getClientIP();
    
    try {
        // Eski kayıtları temizle
        $stmt = $pdo->prepare("DELETE FROM rate_limits WHERE created_at < DATE_SUB(NOW(), INTERVAL ? SECOND)");
        $stmt->execute([$period]);
        
        // Mevcut istekleri say
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM rate_limits WHERE ip_address = ? AND action = ? AND created_at > DATE_SUB(NOW(), INTERVAL ? SECOND)");
        $stmt->execute([$ip, $action, $period]);
        $result = $stmt->fetch();
        
        if ($result['count'] >= $limit) {
            return false;
        }
        
        // Yeni istek kaydet
        $stmt = $pdo->prepare("INSERT INTO rate_limits (ip_address, action) VALUES (?, ?)");
        $stmt->execute([$ip, $action]);
        
        return true;
    } catch (Exception $e) {
        return true; // Hata durumunda izin ver
    }
}

// Güvenlik kontrollerini çalıştır
if (isLoggedIn()) {
    checkSessionTimeout();
}
?>