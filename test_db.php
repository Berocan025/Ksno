<?php
/**
 * Veritabanı Bağlantı Testi
 */

// Hata raporlama
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Veritabanı Bağlantı Testi</h2>";

// Veritabanı bağlantı bilgileri
$host = 'localhost';
$dbname = 'bonusboss';
$username = 'root';
$password = ''; // Şifrenizi buraya yazın

echo "<p><strong>Bağlantı Bilgileri:</strong></p>";
echo "<ul>";
echo "<li>Host: $host</li>";
echo "<li>Database: $dbname</li>";
echo "<li>Username: $username</li>";
echo "<li>Password: " . ($password ? '***' : 'Boş') . "</li>";
echo "</ul>";

// MySQL bağlantısını test et
echo "<h3>1. MySQL Bağlantı Testi</h3>";
try {
    $mysqli = new mysqli($host, $username, $password);
    
    if ($mysqli->connect_error) {
        echo "<p style='color: red;'>❌ MySQL Bağlantı Hatası: " . $mysqli->connect_error . "</p>";
    } else {
        echo "<p style='color: green;'>✅ MySQL Bağlantısı Başarılı!</p>";
        
        // Veritabanı var mı kontrol et
        $result = $mysqli->query("SHOW DATABASES LIKE '$dbname'");
        if ($result->num_rows > 0) {
            echo "<p style='color: green;'>✅ '$dbname' veritabanı mevcut!</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ '$dbname' veritabanı bulunamadı!</p>";
        }
        
        $mysqli->close();
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ MySQL Bağlantı Hatası: " . $e->getMessage() . "</p>";
}

// PDO bağlantısını test et
echo "<h3>2. PDO Bağlantı Testi</h3>";
try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    
    echo "<p style='color: green;'>✅ PDO Bağlantısı Başarılı!</p>";
    
    // Tabloları kontrol et
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<p><strong>Mevcut Tablolar:</strong></p>";
    if (empty($tables)) {
        echo "<p style='color: orange;'>⚠️ Hiç tablo bulunamadı!</p>";
    } else {
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>$table</li>";
        }
        echo "</ul>";
    }
    
    // Örnek veri çekme testi
    if (in_array('settings', $tables)) {
        echo "<h3>3. Veri Çekme Testi</h3>";
        try {
            $stmt = $pdo->query("SELECT * FROM settings LIMIT 5");
            $settings = $stmt->fetchAll();
            
            if (empty($settings)) {
                echo "<p style='color: orange;'>⚠️ Settings tablosunda veri yok!</p>";
            } else {
                echo "<p style='color: green;'>✅ Settings tablosundan " . count($settings) . " kayıt çekildi!</p>";
                echo "<ul>";
                foreach ($settings as $setting) {
                    echo "<li>{$setting['setting_key']}: {$setting['setting_value']}</li>";
                }
                echo "</ul>";
            }
        } catch (Exception $e) {
            echo "<p style='color: red;'>❌ Veri çekme hatası: " . $e->getMessage() . "</p>";
        }
    }
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ PDO Bağlantı Hatası: " . $e->getMessage() . "</p>";
    
    // Hata koduna göre öneriler
    if ($e->getCode() == 1045) {
        echo "<p><strong>Çözüm Önerileri:</strong></p>";
        echo "<ul>";
        echo "<li>MySQL kullanıcı adı ve şifresini kontrol edin</li>";
        echo "<li>MySQL servisinin çalıştığından emin olun</li>";
        echo "<li>Kullanıcının veritabanına erişim izni olduğundan emin olun</li>";
        echo "</ul>";
    } elseif ($e->getCode() == 1049) {
        echo "<p><strong>Çözüm Önerileri:</strong></p>";
        echo "<ul>";
        echo "<li>'$dbname' veritabanını oluşturun</li>";
        echo "<li>database/bonusboss.sql dosyasını çalıştırın</li>";
        echo "</ul>";
    }
}

// PHP bilgileri
echo "<h3>4. PHP Bilgileri</h3>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>PDO MySQL: " . (extension_loaded('pdo_mysql') ? '✅ Yüklü' : '❌ Yüklü Değil') . "</li>";
echo "<li>MySQL: " . (extension_loaded('mysqli') ? '✅ Yüklü' : '❌ Yüklü Değil') . "</li>";
echo "</ul>";

// Config.php test
echo "<h3>5. Config.php Test</h3>";
if (file_exists('config.php')) {
    echo "<p style='color: green;'>✅ config.php dosyası mevcut!</p>";
    
    // Config.php'yi include et ve test et
    try {
        require_once 'config.php';
        
        if (isset($pdo) && $pdo) {
            echo "<p style='color: green;'>✅ Config.php'den PDO bağlantısı başarılı!</p>";
        } else {
            echo "<p style='color: orange;'>⚠️ Config.php'den PDO bağlantısı başarısız!</p>";
        }
        
        // Fonksiyonları test et
        if (function_exists('getSetting')) {
            echo "<p style='color: green;'>✅ getSetting fonksiyonu mevcut!</p>";
        } else {
            echo "<p style='color: red;'>❌ getSetting fonksiyonu bulunamadı!</p>";
        }
        
    } catch (Exception $e) {
        echo "<p style='color: red;'>❌ Config.php hatası: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>❌ config.php dosyası bulunamadı!</p>";
}

echo "<hr>";
echo "<p><strong>Test tamamlandı!</strong></p>";
echo "<p>Eğer hatalar varsa, yukarıdaki çözüm önerilerini takip edin.</p>";
?>