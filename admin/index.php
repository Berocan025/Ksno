<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Dashboard
 */

require_once '../includes/config.php';

// Giriş kontrolü
if (!is_logged_in()) {
    redirect('login.php');
}

// Session timeout kontrolü
if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
    session_destroy();
    redirect('login.php');
}
$_SESSION['last_activity'] = time();

// İstatistikleri getir
$stats = [];

// Toplam mesaj sayısı
$stmt = $pdo->query("SELECT COUNT(*) as total FROM contact_messages");
$stats['total_messages'] = $stmt->fetch()['total'];

// Okunmamış mesaj sayısı
$stmt = $pdo->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
$stats['unread_messages'] = $stmt->fetch()['unread'];

// Toplam portföy projesi
$stmt = $pdo->query("SELECT COUNT(*) as total FROM portfolio WHERE is_active = 1");
$stats['total_portfolio'] = $stmt->fetch()['total'];

// Toplam hizmet
$stmt = $pdo->query("SELECT COUNT(*) as total FROM services WHERE is_active = 1");
$stats['total_services'] = $stmt->fetch()['total'];

// Toplam galeri fotoğrafı
$stmt = $pdo->query("SELECT COUNT(*) as total FROM gallery_photos WHERE is_active = 1");
$stats['total_photos'] = $stmt->fetch()['total'];

// Toplam galeri videosu
$stmt = $pdo->query("SELECT COUNT(*) as total FROM gallery_videos WHERE is_active = 1");
$stats['total_videos'] = $stmt->fetch()['total'];

// Son mesajları getir
$stmt = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");
$recent_messages = $stmt->fetchAll();

// Son aktiviteleri getir (activity_logs tablosu varsa)
$recent_activities = [];
try {
    $stmt = $pdo->query("SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 10");
    $recent_activities = $stmt->fetchAll();
} catch (Exception $e) {
    // activity_logs tablosu yoksa boş bırak
}

// Sistem bilgileri
$system_info = [
    'php_version' => PHP_VERSION,
    'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown',
    'database_size' => 'N/A', // Veritabanı boyutu hesaplanabilir
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'max_execution_time' => ini_get('max_execution_time') . 's',
    'memory_limit' => ini_get('memory_limit')
];
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BonusBoss Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            --primary-color: #FFD700;
            --secondary-color: #0099FF;
            --dark-color: #003366;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: #f8f9fa;
        }
        
        .admin-header {
            background: linear-gradient(135deg, var(--dark-color) 0%, var(--secondary-color) 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .logo-text {
            font-weight: 800;
            font-size: 1.5rem;
            letter-spacing: 1px;
        }
        
        .logo-bonus {
            color: var(--primary-color);
        }
        
        .logo-boss {
            color: var(--secondary-color);
        }
        
        .logo-icon {
            color: var(--primary-color);
            font-size: 1.2rem;
        }
        
        .sidebar {
            background: white;
            min-height: calc(100vh - 80px);
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar .nav-link {
            color: #333;
            padding: 0.75rem 1rem;
            border-radius: 0;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
        }
        
        .sidebar .nav-link i {
            width: 20px;
            margin-right: 0.5rem;
        }
        
        .main-content {
            padding: 2rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .stat-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-label {
            color: #6c757d;
            font-weight: 500;
        }
        
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .card-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px 15px 0 0 !important;
            border: none;
            font-weight: 600;
        }
        
        .btn-admin {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-admin:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255, 215, 0, 0.3);
            color: white;
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--dark-color);
        }
        
        .badge-unread {
            background: #dc3545;
            color: white;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }
        
        .user-avatar {
            width: 40px;
            height: 40px;
            background: var(--primary-color);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
        }
        
        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 25px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <!-- Admin Header -->
    <header class="admin-header">
        <div class="container-fluid">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="logo">
                        <span class="logo-text">
                            <span class="logo-bonus">Bonus</span>
                            <span class="logo-boss">Boss</span>
                        </span>
                        <i class="fas fa-crown logo-icon"></i>
                        <span class="ms-3">Admin Panel</span>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="user-info">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?>
                        </div>
                        <div>
                            <div class="fw-bold"><?php echo $_SESSION['full_name']; ?></div>
                            <small><?php echo ucfirst($_SESSION['role']); ?></small>
                        </div>
                        <a href="logout.php" class="logout-btn">
                            <i class="fas fa-sign-out-alt me-2"></i>
                            Çıkış
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2">
                <div class="sidebar">
                    <nav class="nav flex-column">
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                        <a class="nav-link" href="texts/">
                            <i class="fas fa-edit"></i>
                            Metin Yönetimi
                        </a>
                        <a class="nav-link" href="content/">
                            <i class="fas fa-file-alt"></i>
                            İçerik Yönetimi
                        </a>
                        <a class="nav-link" href="portfolio/">
                            <i class="fas fa-briefcase"></i>
                            Portföy
                        </a>
                        <a class="nav-link" href="gallery/">
                            <i class="fas fa-images"></i>
                            Galeri
                        </a>
                        <a class="nav-link" href="services/">
                            <i class="fas fa-cogs"></i>
                            Hizmetler
                        </a>
                        <a class="nav-link" href="messages/">
                            <i class="fas fa-envelope"></i>
                            Mesajlar
                            <?php if ($stats['unread_messages'] > 0): ?>
                            <span class="badge badge-unread ms-2"><?php echo $stats['unread_messages']; ?></span>
                            <?php endif; ?>
                        </a>
                        <a class="nav-link" href="settings/">
                            <i class="fas fa-cog"></i>
                            Ayarlar
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <!-- Welcome Message -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h1 class="h3 mb-0">
                                <i class="fas fa-home me-2 text-warning"></i>
                                Hoş Geldiniz, <?php echo $_SESSION['full_name']; ?>!
                            </h1>
                            <p class="text-muted">BonusBoss Admin Panel - Site yönetimi için tüm araçlar burada.</p>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="row mb-4">
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <div class="stat-number"><?php echo $stats['total_messages']; ?></div>
                                <div class="stat-label">Toplam Mesaj</div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <div class="stat-number"><?php echo $stats['total_portfolio']; ?></div>
                                <div class="stat-label">Portföy Projesi</div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <div class="stat-number"><?php echo $stats['total_services']; ?></div>
                                <div class="stat-label">Aktif Hizmet</div>
                            </div>
                        </div>
                        
                        <div class="col-lg-3 col-md-6 mb-3">
                            <div class="stat-card">
                                <div class="stat-icon">
                                    <i class="fas fa-images"></i>
                                </div>
                                <div class="stat-number"><?php echo $stats['total_photos'] + $stats['total_videos']; ?></div>
                                <div class="stat-label">Galeri Öğesi</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <!-- Recent Messages -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-envelope me-2"></i>
                                    Son Mesajlar
                                </div>
                                <div class="card-body">
                                    <?php if (empty($recent_messages)): ?>
                                    <p class="text-muted text-center">Henüz mesaj yok.</p>
                                    <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Gönderen</th>
                                                    <th>Konu</th>
                                                    <th>Tarih</th>
                                                    <th>Durum</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_messages as $message): ?>
                                                <tr>
                                                    <td><?php echo $message['name']; ?></td>
                                                    <td><?php echo substr($message['subject'], 0, 30) . '...'; ?></td>
                                                    <td><?php echo format_date($message['created_at']); ?></td>
                                                    <td>
                                                        <?php if ($message['is_read']): ?>
                                                        <span class="badge bg-success">Okundu</span>
                                                        <?php else: ?>
                                                        <span class="badge bg-warning">Yeni</span>
                                                        <?php endif; ?>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="messages/" class="btn btn-admin">
                                            <i class="fas fa-eye me-2"></i>
                                            Tüm Mesajları Gör
                                        </a>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="col-lg-6">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-bolt me-2"></i>
                                    Hızlı İşlemler
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6 mb-3">
                                            <a href="texts/" class="btn btn-admin w-100">
                                                <i class="fas fa-edit me-2"></i>
                                                Metin Düzenle
                                            </a>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <a href="portfolio/add.php" class="btn btn-admin w-100">
                                                <i class="fas fa-plus me-2"></i>
                                                Proje Ekle
                                            </a>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <a href="gallery/add.php" class="btn btn-admin w-100">
                                                <i class="fas fa-upload me-2"></i>
                                                Galeri Ekle
                                            </a>
                                        </div>
                                        <div class="col-6 mb-3">
                                            <a href="settings/" class="btn btn-admin w-100">
                                                <i class="fas fa-cog me-2"></i>
                                                Site Ayarları
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- System Info -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Sistem Bilgileri
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-3 mb-3">
                                            <strong>PHP Sürümü:</strong><br>
                                            <span class="text-muted"><?php echo $system_info['php_version']; ?></span>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <strong>Sunucu:</strong><br>
                                            <span class="text-muted"><?php echo $system_info['server_software']; ?></span>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <strong>Maksimum Dosya Boyutu:</strong><br>
                                            <span class="text-muted"><?php echo $system_info['upload_max_filesize']; ?></span>
                                        </div>
                                        <div class="col-md-3 mb-3">
                                            <strong>Bellek Limiti:</strong><br>
                                            <span class="text-muted"><?php echo $system_info['memory_limit']; ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Console log
        console.log('🎰 BonusBoss Admin Panel - Dashboard');
        console.log('👨‍💻 Yazılımcı: BERAT K');
        console.log('🚀 Admin paneli başarıyla yüklendi...');
        
        // Auto refresh stats every 30 seconds
        setInterval(function() {
            location.reload();
        }, 30000);
    </script>
</body>
</html>