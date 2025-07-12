<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Portföy Yönetimi
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

// Proje silme
if (isset($_POST['action']) && $_POST['action'] === 'delete') {
    $project_id = (int)$_POST['project_id'];
    
    // Önce resmi sil
    $stmt = $pdo->prepare("SELECT image FROM portfolio WHERE id = ?");
    $stmt->execute([$project_id]);
    $project = $stmt->fetch();
    
    if ($project && $project['image']) {
        $image_path = UPLOAD_PATH . $project['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Projeyi sil
    $stmt = $pdo->prepare("DELETE FROM portfolio WHERE id = ?");
    if ($stmt->execute([$project_id])) {
        show_message('Proje başarıyla silindi.', 'success');
    } else {
        show_message('Proje silinirken bir hata oluştu.', 'error');
    }
    
    redirect('index.php');
}

// Durum değiştirme
if (isset($_POST['action']) && $_POST['action'] === 'toggle_status') {
    $project_id = (int)$_POST['project_id'];
    
    $stmt = $pdo->prepare("UPDATE portfolio SET is_active = NOT is_active WHERE id = ?");
    if ($stmt->execute([$project_id])) {
        show_message('Proje durumu güncellendi.', 'success');
    } else {
        show_message('Durum güncellenirken bir hata oluştu.', 'error');
    }
    
    redirect('index.php');
}

// Filtreleme
$category_filter = isset($_GET['category']) ? clean_input($_GET['category']) : '';
$status_filter = isset($_GET['status']) ? clean_input($_GET['status']) : '';

// Sayfalama
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Projeleri getir
$where_conditions = [];
$params = [];

if ($category_filter) {
    $where_conditions[] = "c.slug = ?";
    $params[] = $category_filter;
}

if ($status_filter === 'active') {
    $where_conditions[] = "p.is_active = 1";
} elseif ($status_filter === 'inactive') {
    $where_conditions[] = "p.is_active = 0";
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Toplam kayıt sayısı
$count_sql = "SELECT COUNT(*) as total FROM portfolio p LEFT JOIN categories c ON p.category_id = c.id $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_records = $stmt->fetch()['total'];

$total_pages = ceil($total_records / $per_page);

// Projeleri getir
$sql = "SELECT p.*, c.name as category_name FROM portfolio p 
        LEFT JOIN categories c ON p.category_id = c.id 
        $where_clause 
        ORDER BY p.is_featured DESC, p.created_at DESC 
        LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

// Kategorileri getir
$categories = $pdo->query("SELECT * FROM categories WHERE type = 'portfolio' ORDER BY name")->fetchAll();

// Mesaj göster
$message = get_message();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portföy Yönetimi - BonusBoss Admin</title>
    
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
        
        .project-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .project-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        
        .project-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 10px;
        }
        
        .project-info {
            flex: 1;
        }
        
        .project-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .project-category {
            color: var(--secondary-color);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .project-description {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }
        
        .project-meta {
            font-size: 0.8rem;
            color: #999;
        }
        
        .project-actions {
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
        
        .featured-badge {
            background: var(--primary-color);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: 500;
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
                        <a class="nav-link active" href="index.php">
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
                        <h1 class="h3 mb-0">Portföy Yönetimi</h1>
                        <div>
                            <a href="add.php" class="btn btn-admin me-2">
                                <i class="fas fa-plus me-2"></i>Yeni Proje Ekle
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
                                <div class="stats-label">Toplam Proje</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">
                                    <?php 
                                    $stmt = $pdo->query("SELECT COUNT(*) as active FROM portfolio WHERE is_active = 1");
                                    echo $stmt->fetch()['active'];
                                    ?>
                                </div>
                                <div class="stats-label">Aktif Proje</div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="stats-card">
                                <div class="stats-number">
                                    <?php 
                                    $stmt = $pdo->query("SELECT COUNT(*) as featured FROM portfolio WHERE is_featured = 1");
                                    echo $stmt->fetch()['featured'];
                                    ?>
                                </div>
                                <div class="stats-label">Öne Çıkan</div>
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
                        <a href="?status=" class="btn filter-btn <?php echo $status_filter === '' ? 'active' : ''; ?>">
                            <i class="fas fa-list me-2"></i>Tümü
                        </a>
                        <a href="?status=active" class="btn filter-btn <?php echo $status_filter === 'active' ? 'active' : ''; ?>">
                            <i class="fas fa-check me-2"></i>Aktif
                        </a>
                        <a href="?status=inactive" class="btn filter-btn <?php echo $status_filter === 'inactive' ? 'active' : ''; ?>">
                            <i class="fas fa-times me-2"></i>Pasif
                        </a>
                        
                        <div class="mt-2">
                            <strong>Kategori:</strong>
                            <a href="?category=&status=<?php echo $status_filter; ?>" class="btn filter-btn <?php echo $category_filter === '' ? 'active' : ''; ?>">
                                Tümü
                            </a>
                            <?php foreach ($categories as $category): ?>
                            <a href="?category=<?php echo $category['slug']; ?>&status=<?php echo $status_filter; ?>" class="btn filter-btn <?php echo $category_filter === $category['slug'] ? 'active' : ''; ?>">
                                <?php echo $category['name']; ?>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Projeler -->
                    <?php if (empty($projects)): ?>
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                            <h5>Henüz proje bulunmuyor</h5>
                            <p class="text-muted">Yeni projeler ekleyerek portföyünüzü oluşturmaya başlayın.</p>
                            <a href="add.php" class="btn btn-admin">
                                <i class="fas fa-plus me-2"></i>İlk Projeyi Ekle
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($projects as $project): ?>
                    <div class="project-card">
                        <div class="row align-items-center">
                            <div class="col-md-2">
                                <?php if ($project['image']): ?>
                                <img src="../../assets/uploads/<?php echo $project['image']; ?>" alt="<?php echo $project['title']; ?>" class="project-image">
                                <?php else: ?>
                                <div class="project-image bg-light d-flex align-items-center justify-content-center">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="project-info">
                                    <div class="project-title"><?php echo htmlspecialchars($project['title']); ?></div>
                                    <div class="project-category">
                                        <i class="fas fa-tag me-1"></i><?php echo $project['category_name'] ?: 'Kategorisiz'; ?>
                                    </div>
                                    <div class="project-description">
                                        <?php echo htmlspecialchars(substr($project['description'], 0, 100)); ?>
                                        <?php if (strlen($project['description']) > 100): ?>...<?php endif; ?>
                                    </div>
                                    <div class="project-meta">
                                        <i class="fas fa-calendar me-1"></i><?php echo format_date($project['created_at']); ?>
                                        <?php if ($project['client']): ?>
                                        <span class="ms-3"><i class="fas fa-user me-1"></i><?php echo htmlspecialchars($project['client']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="text-center">
                                    <?php if ($project['is_featured']): ?>
                                    <div class="featured-badge mb-2">
                                        <i class="fas fa-star me-1"></i>Öne Çıkan
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="status-badge <?php echo $project['is_active'] ? 'status-active' : 'status-inactive'; ?>">
                                        <?php echo $project['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-2">
                                <div class="project-actions">
                                    <a href="edit.php?id=<?php echo $project['id']; ?>" class="btn btn-primary btn-sm" title="Düzenle">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" style="display: inline;" onsubmit="return confirm('Bu projeyi silmek istediğinizden emin misiniz?');">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                        <button type="submit" class="btn btn-danger btn-sm" title="Sil">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="toggle_status">
                                        <input type="hidden" name="project_id" value="<?php echo $project['id']; ?>">
                                        <button type="submit" class="btn btn-<?php echo $project['is_active'] ? 'warning' : 'success'; ?> btn-sm" title="<?php echo $project['is_active'] ? 'Pasif Yap' : 'Aktif Yap'; ?>">
                                            <i class="fas fa-<?php echo $project['is_active'] ? 'eye-slash' : 'eye'; ?>"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Sayfalama -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Proje sayfaları">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?category=<?php echo $category_filter; ?>&status=<?php echo $status_filter; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
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