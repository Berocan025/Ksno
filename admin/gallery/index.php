<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Galeri Yönetimi
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

// İçerik silme
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $content_id = (int)$_POST['content_id'];
    $content_type = clean_input($_POST['content_type']);
    
    if ($content_type === 'photo') {
        // Fotoğrafı sil
        $stmt = $pdo->prepare("SELECT image FROM gallery_photos WHERE id = ?");
        $stmt->execute([$content_id]);
        $photo = $stmt->fetch();
        
        if ($photo && $photo['image']) {
            $image_path = UPLOAD_PATH . $photo['image'];
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM gallery_photos WHERE id = ?");
    } else {
        // Videoyu sil
        $stmt = $pdo->prepare("SELECT thumbnail FROM gallery_videos WHERE id = ?");
        $stmt->execute([$content_id]);
        $video = $stmt->fetch();
        
        if ($video && $video['thumbnail']) {
            $thumbnail_path = UPLOAD_PATH . $video['thumbnail'];
            if (file_exists($thumbnail_path)) {
                unlink($thumbnail_path);
            }
        }
        
        $stmt = $pdo->prepare("DELETE FROM gallery_videos WHERE id = ?");
    }
    
    if ($stmt->execute([$content_id])) {
        show_message('İçerik başarıyla silindi.', 'success');
    } else {
        show_message('İçerik silinirken bir hata oluştu.', 'error');
    }
    
    redirect('index.php');
}

// Durum değiştirme
if (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    $content_id = (int)$_POST['content_id'];
    $content_type = clean_input($_POST['content_type']);
    
    if ($content_type === 'photo') {
        $stmt = $pdo->prepare("UPDATE gallery_photos SET is_active = NOT is_active WHERE id = ?");
    } else {
        $stmt = $pdo->prepare("UPDATE gallery_videos SET is_active = NOT is_active WHERE id = ?");
    }
    
    if ($stmt->execute([$content_id])) {
        show_message('İçerik durumu güncellendi.', 'success');
    } else {
        show_message('Durum güncellenirken bir hata oluştu.', 'error');
    }
    
    redirect('index.php');
}

// Filtreleme
$category_filter = isset($_GET['category']) ? clean_input($_GET['category']) : '';
$type_filter = isset($_GET['type']) ? clean_input($_GET['type']) : '';

// Sayfalama
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$per_page = 12;
$offset = ($page - 1) * $per_page;

// İçerikleri getir
$where_conditions = [];
$params = [];

if ($category_filter) {
    $where_conditions[] = "c.slug = ?";
    $params[] = $category_filter;
}

if ($type_filter === 'photos') {
    $where_conditions[] = "1=1"; // Sadece fotoğraflar
} elseif ($type_filter === 'videos') {
    $where_conditions[] = "1=1"; // Sadece videolar
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Fotoğrafları getir
$photos_sql = "SELECT p.*, c.name as category_name FROM gallery_photos p 
               LEFT JOIN categories c ON p.category_id = c.id 
               $where_clause 
               ORDER BY p.sort_order ASC, p.created_at DESC 
               LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($photos_sql);
$stmt->execute($params);
$photos = $stmt->fetchAll();

// Videoları getir
$videos_sql = "SELECT v.*, c.name as category_name FROM gallery_videos v 
               LEFT JOIN categories c ON v.category_id = c.id 
               $where_clause 
               ORDER BY v.sort_order ASC, v.created_at DESC 
               LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($videos_sql);
$stmt->execute($params);
$videos = $stmt->fetchAll();

// Toplam kayıt sayısı
$total_photos = $pdo->query("SELECT COUNT(*) as total FROM gallery_photos")->fetch()['total'];
$total_videos = $pdo->query("SELECT COUNT(*) as total FROM gallery_videos")->fetch()['total'];
$total_records = $total_photos + $total_videos;

$total_pages = ceil($total_records / $per_page);

// Kategorileri getir
$categories = $pdo->query("SELECT * FROM categories WHERE type = 'gallery' ORDER BY name")->fetchAll();

// Mesaj göster
$message = get_message();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Yönetimi - BonusBoss Admin</title>
    
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
        
        .gallery-item {
            background: white;
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .gallery-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        
        .gallery-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 1rem;
        }
        
        .gallery-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .gallery-category {
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .gallery-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .gallery-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .status-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .status-active {
            background: #d4edda;
            color: #155724;
        }
        
        .status-inactive {
            background: #f8d7da;
            color: #721c24;
        }
        
        .type-badge {
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .type-photo {
            background: var(--primary-color);
            color: white;
        }
        
        .type-video {
            background: var(--secondary-color);
            color: white;
        }
        
        .filter-buttons {
            margin-bottom: 2rem;
        }
        
        .filter-btn {
            background: white;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            margin-right: 0.5rem;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary-color);
            color: white;
        }
        
        .stats-card {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
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
                        <a class="nav-link" href="../content/">
                            <i class="fas fa-edit"></i>İçerik Yönetimi
                        </a>
                        <a class="nav-link" href="../texts/">
                            <i class="fas fa-font"></i>Metin Yönetimi
                        </a>
                        <a class="nav-link" href="../portfolio/">
                            <i class="fas fa-briefcase"></i>Portföy Yönetimi
                        </a>
                        <a class="nav-link active" href="index.php">
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
                        <h1 class="h3 mb-0">Galeri Yönetimi</h1>
                        <div>
                            <a href="add.php" class="btn btn-admin me-2">
                                <i class="fas fa-plus me-2"></i>Yeni İçerik Ekle
                            </a>
                            <a href="../index.php" class="btn btn-admin">
                                <i class="fas fa-arrow-left me-2"></i>Geri Dön
                            </a>
                        </div>
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
                                <div class="stats-number"><?php echo $total_records; ?></div>
                                <div class="stats-label">Toplam İçerik</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $total_photos; ?></div>
                                <div class="stats-label">Fotoğraf</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $total_videos; ?></div>
                                <div class="stats-label">Video</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo count($categories); ?></div>
                                <div class="stats-label">Kategori</div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtreler -->
                    <div class="filter-buttons">
                        <a href="?type=" class="btn filter-btn <?php echo $type_filter === '' ? 'active' : ''; ?>">
                            <i class="fas fa-list me-2"></i>Tümü
                        </a>
                        <a href="?type=photos" class="btn filter-btn <?php echo $type_filter === 'photos' ? 'active' : ''; ?>">
                            <i class="fas fa-image me-2"></i>Fotoğraflar
                        </a>
                        <a href="?type=videos" class="btn filter-btn <?php echo $type_filter === 'videos' ? 'active' : ''; ?>">
                            <i class="fas fa-video me-2"></i>Videolar
                        </a>
                        
                        <div class="mt-2">
                            <strong>Kategori:</strong>
                            <a href="?type=<?php echo $type_filter; ?>&category=" class="btn filter-btn <?php echo $category_filter === '' ? 'active' : ''; ?>">
                                Tümü
                            </a>
                            <?php foreach ($categories as $category): ?>
                            <a href="?type=<?php echo $type_filter; ?>&category=<?php echo $category['slug']; ?>" class="btn filter-btn <?php echo $category_filter === $category['slug'] ? 'active' : ''; ?>">
                                <?php echo $category['name']; ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Galeri İçerikleri -->
                    <?php if (empty($photos) && empty($videos)): ?>
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-images fa-3x text-muted mb-3"></i>
                            <h5>Henüz galeri içeriği bulunmuyor</h5>
                            <p class="text-muted">Yeni fotoğraf ve videolar ekleyerek galerinizi oluşturmaya başlayın.</p>
                            <a href="add.php" class="btn btn-admin">
                                <i class="fas fa-plus me-2"></i>İlk İçeriği Ekle
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="row">
                        <!-- Fotoğraflar -->
                        <?php foreach ($photos as $photo): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="gallery-item">
                                <img src="../../assets/uploads/<?php echo $photo['image']; ?>" alt="<?php echo $photo['title']; ?>" class="gallery-image">
                                
                                <div class="gallery-title"><?php echo htmlspecialchars($photo['title']); ?></div>
                                <div class="gallery-category">
                                    <i class="fas fa-tag me-1"></i><?php echo $photo['category_name'] ?: 'Kategorisiz'; ?>
                                </div>
                                
                                <?php if ($photo['description']): ?>
                                <div class="gallery-description">
                                    <?php echo htmlspecialchars(substr($photo['description'], 0, 50)); ?>
                                    <?php if (strlen($photo['description']) > 50): ?>...<?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="type-badge type-photo">
                                        <i class="fas fa-image me-1"></i>Fotoğraf
                                    </span>
                                    <span class="status-badge <?php echo $photo['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $photo['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                    </span>
                                </div>
                                
                                <div class="gallery-actions">
                                    <a href="edit.php?type=photo&id=<?php echo $photo['id']; ?>" class="btn btn-primary btn-sm" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Bu fotoğrafı silmek istediğinizden emin misiniz?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="content_id" value="<?php echo $photo['id']; ?>">
                                        <input type="hidden" name="content_type" value="photo">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="content_id" value="<?php echo $photo['id']; ?>">
                                        <input type="hidden" name="content_type" value="photo">
                                        <button type="submit" class="btn btn-<?php echo $photo['is_active'] ? 'warning' : 'success'; ?> btn-sm" title="<?php echo $photo['is_active'] ? 'Pasif Yap' : 'Aktif Yap'; ?>">
                                            <i class="fas fa-<?php echo $photo['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                        
                        <!-- Videolar -->
                        <?php foreach ($videos as $video): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6 mb-4">
                            <div class="gallery-item">
                                <?php if ($video['thumbnail']): ?>
                                <img src="../../assets/uploads/<?php echo $video['thumbnail']; ?>" alt="<?php echo $video['title']; ?>" class="gallery-image">
                                <?php else: ?>
                                <div class="gallery-image bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-video fa-3x text-muted"></i>
                                </div>
                                <?php endif; ?>
                                
                                <div class="gallery-title"><?php echo htmlspecialchars($video['title']); ?></div>
                                <div class="gallery-category">
                                    <i class="fas fa-tag me-1"></i><?php echo $video['category_name'] ?: 'Kategorisiz'; ?>
                                </div>
                                
                                <?php if ($video['description']): ?>
                                <div class="gallery-description">
                                    <?php echo htmlspecialchars(substr($video['description'], 0, 50)); ?>
                                    <?php if (strlen($video['description']) > 50): ?>...<?php endif; ?>
                                </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="type-badge type-video">
                                        <i class="fas fa-video me-1"></i>Video
                                    </span>
                                    <span class="status-badge <?php echo $video['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $video['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                    </span>
                                </div>
                                
                                <div class="gallery-actions">
                                    <a href="edit.php?type=video&id=<?php echo $video['id']; ?>" class="btn btn-primary btn-sm" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Bu videoyu silmek istediğinizden emin misiniz?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="content_id" value="<?php echo $video['id']; ?>">
                                        <input type="hidden" name="content_type" value="video">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="content_id" value="<?php echo $video['id']; ?>">
                                        <input type="hidden" name="content_type" value="video">
                                        <button type="submit" class="btn btn-<?php echo $video['is_active'] ? 'warning' : 'success'; ?> btn-sm" title="<?php echo $video['is_active'] ? 'Pasif Yap' : 'Aktif Yap'; ?>">
                                            <i class="fas fa-<?php echo $video['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Sayfalama -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Galeri sayfaları">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?type=<?php echo $type_filter; ?>&category=<?php echo $category_filter; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>