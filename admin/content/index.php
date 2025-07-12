<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - İçerik Yönetimi
 */

require_once '../../includes/config.php';

// Giriş kontrolü
if (!is_logged_in()) {
    redirect('../login.php');
}

// Session timeout kontrolü
if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
    session_destroy();
    redirect('../login.php');
}
$_SESSION['last_activity'] = time();

// Mesaj göster
$message = get_message();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>İçerik Yönetimi - BonusBoss Admin</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
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
        
        .content-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .content-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }
        
        .content-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .content-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .content-description {
            color: #6c757d;
            margin-bottom: 1.5rem;
        }
        
        .stats-card {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 1.5rem;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
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
                        <i class="fas fa-crown logo-icon"></i>
                        <div class="logo-text">
                            <span class="logo-bonus">Bonus</span><span class="logo-boss">Boss</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="d-flex align-items-center justify-content-end">
                        <span class="me-3">Hoş geldin, <?php echo $_SESSION['username']; ?></span>
                        <a href="../logout.php" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-sign-out-alt me-1"></i>Çıkış
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
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-tachometer-alt"></i>Dashboard
                        </a>
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-edit"></i>İçerik Yönetimi
                        </a>
                        <a class="nav-link" href="../texts/">
                            <i class="fas fa-font"></i>Metin Yönetimi
                        </a>
                        <a class="nav-link" href="../portfolio/">
                            <i class="fas fa-briefcase"></i>Portföy Yönetimi
                        </a>
                        <a class="nav-link" href="../gallery/">
                            <i class="fas fa-images"></i>Galeri Yönetimi
                        </a>
                        <a class="nav-link" href="../services/">
                            <i class="fas fa-cogs"></i>Hizmet Yönetimi
                        </a>
                        <a class="nav-link" href="../messages/">
                            <i class="fas fa-envelope"></i>Mesaj Yönetimi
                        </a>
                        <a class="nav-link" href="../settings/">
                            <i class="fas fa-cog"></i>Site Ayarları
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">İçerik Yönetimi</h1>
                        <a href="../index.php" class="btn btn-admin">
                            <i class="fas fa-arrow-left me-2"></i>Geri Dön
                        </a>
                    </div>

                    <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <!-- İstatistikler -->
                    <div class="row mb-4">
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">6</div>
                                <div class="stats-label">Aktif Hizmet</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">12</div>
                                <div class="stats-label">Portföy Projesi</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">24</div>
                                <div class="stats-label">Galeri İçeriği</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">5</div>
                                <div class="stats-label">Kategori</div>
                            </div>
                        </div>
                    </div>

                    <!-- İçerik Yönetimi Kartları -->
                    <div class="row">
                        <div class="col-lg-4 col-md-6">
                            <div class="content-card text-center">
                                <div class="content-icon">
                                    <i class="fas fa-cogs"></i>
                                </div>
                                <h3 class="content-title">Hizmet Yönetimi</h3>
                                <p class="content-description">Sunduğunuz hizmetleri düzenleyin, yeni hizmetler ekleyin ve mevcut hizmetleri güncelleyin.</p>
                                <a href="../services/" class="btn btn-admin">
                                    <i class="fas fa-edit me-2"></i>Hizmetleri Yönet
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="content-card text-center">
                                <div class="content-icon">
                                    <i class="fas fa-briefcase"></i>
                                </div>
                                <h3 class="content-title">Portföy Yönetimi</h3>
                                <p class="content-description">Portföy projelerinizi yönetin, yeni projeler ekleyin ve mevcut projeleri düzenleyin.</p>
                                <a href="../portfolio/" class="btn btn-admin">
                                    <i class="fas fa-edit me-2"></i>Portföyü Yönet
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="content-card text-center">
                                <div class="content-icon">
                                    <i class="fas fa-images"></i>
                                </div>
                                <h3 class="content-title">Galeri Yönetimi</h3>
                                <p class="content-description">Fotoğraf ve video galerinizi yönetin, yeni içerikler ekleyin ve düzenleyin.</p>
                                <a href="../gallery/" class="btn btn-admin">
                                    <i class="fas fa-edit me-2"></i>Galeriyi Yönet
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="content-card text-center">
                                <div class="content-icon">
                                    <i class="fas fa-font"></i>
                                </div>
                                <h3 class="content-title">Metin Yönetimi</h3>
                                <p class="content-description">Sitenizdeki tüm metinleri düzenleyin ve güncelleyin.</p>
                                <a href="../texts/" class="btn btn-admin">
                                    <i class="fas fa-edit me-2"></i>Metinleri Yönet
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="content-card text-center">
                                <div class="content-icon">
                                    <i class="fas fa-envelope"></i>
                                </div>
                                <h3 class="content-title">Mesaj Yönetimi</h3>
                                <p class="content-description">Gelen iletişim mesajlarını görüntüleyin ve yönetin.</p>
                                <a href="../messages/" class="btn btn-admin">
                                    <i class="fas fa-edit me-2"></i>Mesajları Yönet
                                </a>
                            </div>
                        </div>

                        <div class="col-lg-4 col-md-6">
                            <div class="content-card text-center">
                                <div class="content-icon">
                                    <i class="fas fa-cog"></i>
                                </div>
                                <h3 class="content-title">Site Ayarları</h3>
                                <p class="content-description">Site genel ayarlarını, logo, sosyal medya linklerini düzenleyin.</p>
                                <a href="../settings/" class="btn btn-admin">
                                    <i class="fas fa-edit me-2"></i>Ayarları Yönet
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Hızlı İşlemler -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Hızlı İşlemler</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <a href="../services/add.php" class="btn btn-outline-primary w-100">
                                        <i class="fas fa-plus me-2"></i>Yeni Hizmet Ekle
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="../portfolio/add.php" class="btn btn-outline-success w-100">
                                        <i class="fas fa-plus me-2"></i>Yeni Proje Ekle
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="../gallery/add.php" class="btn btn-outline-info w-100">
                                        <i class="fas fa-plus me-2"></i>Galeri İçeriği Ekle
                                    </a>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <a href="../messages/" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-envelope me-2"></i>Mesajları Görüntüle
                                    </a>
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
</body>
</html>