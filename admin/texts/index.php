<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Metin Yönetimi
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

// Filtreleme
$page_filter = isset($_GET['page']) ? clean_input($_GET['page']) : '';
$section_filter = isset($_GET['section']) ? clean_input($_GET['section']) : '';
$search = isset($_GET['search']) ? clean_input($_GET['search']) : '';

// Sayfalama
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Metinleri getir
$where_conditions = [];
$params = [];

if ($page_filter) {
    $where_conditions[] = "page_name = ?";
    $params[] = $page_filter;
}

if ($section_filter) {
    $where_conditions[] = "section = ?";
    $params[] = $section_filter;
}

if ($search) {
    $where_conditions[] = "(text_key LIKE ? OR text_value LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$where_clause = $where_conditions ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Toplam kayıt sayısı
$count_sql = "SELECT COUNT(*) as total FROM site_texts $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_records = $stmt->fetch()['total'];

$total_pages = ceil($total_records / $per_page);

// Metinleri getir
$sql = "SELECT * FROM site_texts $where_clause ORDER BY page_name, section, text_key LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$texts = $stmt->fetchAll();

// Sayfa ve bölüm listelerini getir
$pages = $pdo->query("SELECT DISTINCT page_name FROM site_texts ORDER BY page_name")->fetchAll();
$sections = $pdo->query("SELECT DISTINCT section FROM site_texts ORDER BY section")->fetchAll();

// Toplu güncelleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_update'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        show_message('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
    } else {
        $success = true;
        foreach ($_POST['texts'] as $text_id => $text_value) {
            $stmt = $pdo->prepare("UPDATE site_texts SET text_value = ?, updated_at = NOW() WHERE id = ?");
            if (!$stmt->execute([clean_input($text_value), $text_id])) {
                $success = false;
                break;
            }
        }
        
        if ($success) {
            show_message('Metinler başarıyla güncellendi.', 'success');
        } else {
            show_message('Metinler güncellenirken bir hata oluştu.', 'error');
        }
        
        redirect('index.php');
    }
}

// Yeni metin ekleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_text'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        show_message('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
    } else {
        $text_key = clean_input($_POST['text_key']);
        $text_value = clean_input($_POST['text_value']);
        $page_name = clean_input($_POST['page_name']);
        $section = clean_input($_POST['section']);
        $description = clean_input($_POST['description']);
        
        try {
            $stmt = $pdo->prepare("INSERT INTO site_texts (text_key, text_value, page_name, section, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            if ($stmt->execute([$text_key, $text_value, $page_name, $section, $description])) {
                show_message('Yeni metin başarıyla eklendi.', 'success');
            } else {
                show_message('Metin eklenirken bir hata oluştu.', 'error');
            }
        } catch (Exception $e) {
            show_message('Metin eklenirken hata oluştu: ' . $e->getMessage(), 'error');
        }
        
        redirect('index.php');
    }
}

// Mesaj göster
$message = get_message();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Metin Yönetimi - BonusBoss Admin</title>
    
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
        
        .table {
            margin-bottom: 0;
        }
        
        .table th {
            border-top: none;
            font-weight: 600;
            color: var(--dark-color);
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
        
        .filter-form {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }
        
        .text-preview {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
        
        .badge-page {
            background: var(--primary-color);
            color: var(--dark-color);
        }
        
        .badge-section {
            background: var(--secondary-color);
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
                        <a href="../logout.php" class="logout-btn">
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
                        <a class="nav-link" href="../index.php">
                            <i class="fas fa-tachometer-alt"></i>
                            Dashboard
                        </a>
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-edit"></i>
                            Metin Yönetimi
                        </a>
                        <a class="nav-link" href="../content/">
                            <i class="fas fa-file-alt"></i>
                            İçerik Yönetimi
                        </a>
                        <a class="nav-link" href="../portfolio/">
                            <i class="fas fa-briefcase"></i>
                            Portföy
                        </a>
                        <a class="nav-link" href="../gallery/">
                            <i class="fas fa-images"></i>
                            Galeri
                        </a>
                        <a class="nav-link" href="../services/">
                            <i class="fas fa-cogs"></i>
                            Hizmetler
                        </a>
                        <a class="nav-link" href="../messages/">
                            <i class="fas fa-envelope"></i>
                            Mesajlar
                        </a>
                        <a class="nav-link" href="../settings/">
                            <i class="fas fa-cog"></i>
                            Ayarlar
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <!-- Page Header -->
                    <div class="row mb-4">
                        <div class="col-12">
                            <h1 class="h3 mb-0">
                                <i class="fas fa-edit me-2 text-warning"></i>
                                Metin Yönetimi
                            </h1>
                            <p class="text-muted">Site genelindeki tüm metinleri düzenleyin ve yönetin.</p>
                        </div>
                    </div>

                    <!-- Messages -->
                    <?php if ($message): ?>
                    <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
                        <?php echo $message['text']; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <!-- Filters -->
                    <div class="filter-form">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label for="page" class="form-label">Sayfa</label>
                                    <select class="form-select" name="page" id="page">
                                        <option value="">Tümü</option>
                                        <?php foreach ($pages as $page_item): ?>
                                        <option value="<?php echo $page_item['page_name']; ?>" <?php echo $page_filter === $page_item['page_name'] ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($page_item['page_name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label for="section" class="form-label">Bölüm</label>
                                    <select class="form-select" name="section" id="section">
                                        <option value="">Tümü</option>
                                        <?php foreach ($sections as $section_item): ?>
                                        <option value="<?php echo $section_item['section']; ?>" <?php echo $section_filter === $section_item['section'] ? 'selected' : ''; ?>>
                                            <?php echo ucfirst($section_item['section']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="search" class="form-label">Arama</label>
                                    <input type="text" class="form-control" name="search" id="search" value="<?php echo htmlspecialchars($search); ?>" placeholder="Metin anahtarı, değer veya açıklama...">
                                </div>
                                <div class="col-md-2 mb-3">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-admin">
                                            <i class="fas fa-search me-2"></i>
                                            Ara
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Actions -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <button type="button" class="btn btn-admin" data-bs-toggle="modal" data-bs-target="#bulkEditModal">
                                <i class="fas fa-edit me-2"></i>
                                Toplu Düzenleme
                            </button>
                            <button type="button" class="btn btn-admin" data-bs-toggle="modal" data-bs-target="#addTextModal">
                                <i class="fas fa-plus me-2"></i>
                                Yeni Metin Ekle
                            </button>
                        </div>
                    </div>

                    <!-- Texts Table -->
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-list me-2"></i>
                            Metinler (<?php echo $total_records; ?> kayıt)
                        </div>
                        <div class="card-body">
                            <?php if (empty($texts)): ?>
                            <p class="text-muted text-center">Metin bulunamadı.</p>
                            <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Metin Anahtarı</th>
                                            <th>Değer</th>
                                            <th>Sayfa</th>
                                            <th>Bölüm</th>
                                            <th>Açıklama</th>
                                            <th>Son Güncelleme</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($texts as $text): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($text['text_key']); ?></strong>
                                            </td>
                                            <td>
                                                <div class="text-preview" title="<?php echo htmlspecialchars($text['text_value']); ?>">
                                                    <?php echo htmlspecialchars($text['text_value']); ?>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($text['page_name']): ?>
                                                <span class="badge badge-page"><?php echo ucfirst($text['page_name']); ?></span>
                                                <?php else: ?>
                                                <span class="badge bg-secondary">Genel</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($text['section']): ?>
                                                <span class="badge badge-section"><?php echo ucfirst($text['section']); ?></span>
                                                <?php else: ?>
                                                <span class="badge bg-secondary">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($text['description']); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo format_date($text['updated_at'], 'd.m.Y H:i'); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <a href="edit.php?id=<?php echo $text['id']; ?>" class="btn btn-sm btn-outline-primary" title="Düzenle">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-info" onclick="previewText('<?php echo htmlspecialchars($text['text_key']); ?>', '<?php echo htmlspecialchars($text['text_value']); ?>')" title="Önizle">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($total_pages > 1): ?>
                            <nav aria-label="Sayfalama">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $page - 1])); ?>">Önceki</a>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $i])); ?>"><?php echo $i; ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['p' => $page + 1])); ?>">Sonraki</a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div class="modal fade" id="previewModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Metin Önizleme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label"><strong>Metin Anahtarı:</strong></label>
                        <div id="preview-key" class="form-control-plaintext"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label"><strong>Metin Değeri:</strong></label>
                        <div id="preview-value" class="form-control-plaintext"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Toplu Düzenleme Modal -->
    <div class="modal fade" id="bulkEditModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Toplu Metin Düzenleme</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="bulk_update" value="1">
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Metin Anahtarı</th>
                                        <th>Mevcut Değer</th>
                                        <th>Yeni Değer</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($texts as $text): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($text['text_key']); ?></strong>
                                            <br><small class="text-muted"><?php echo htmlspecialchars($text['description']); ?></small>
                                        </td>
                                        <td>
                                            <div class="text-preview" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis;">
                                                <?php echo htmlspecialchars($text['text_value']); ?>
                                            </div>
                                        </td>
                                        <td>
                                            <textarea class="form-control form-control-sm" name="texts[<?php echo $text['id']; ?>]" rows="2"><?php echo htmlspecialchars($text['text_value']); ?></textarea>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-admin">
                            <i class="fas fa-save me-2"></i>Değişiklikleri Kaydet
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Yeni Metin Ekleme Modal -->
    <div class="modal fade" id="addTextModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Yeni Metin Ekle</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                    <input type="hidden" name="add_text" value="1">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="text_key" class="form-label">Metin Anahtarı *</label>
                            <input type="text" class="form-control" id="text_key" name="text_key" required>
                            <small class="text-muted">Örnek: hero_title, about_description</small>
                        </div>
                        
                        <div class="mb-3">
                            <label for="text_value" class="form-label">Metin Değeri *</label>
                            <textarea class="form-control" id="text_value" name="text_value" rows="3" required></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="page_name" class="form-label">Sayfa</label>
                                    <select class="form-select" id="page_name" name="page_name">
                                        <option value="">Genel</option>
                                        <option value="home">Ana Sayfa</option>
                                        <option value="about">Hakkımda</option>
                                        <option value="services">Hizmetler</option>
                                        <option value="portfolio">Portföy</option>
                                        <option value="gallery">Galeri</option>
                                        <option value="contact">İletişim</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="section" class="form-label">Bölüm</label>
                                    <select class="form-select" id="section" name="section">
                                        <option value="">Genel</option>
                                        <option value="hero">Hero</option>
                                        <option value="header">Başlık</option>
                                        <option value="content">İçerik</option>
                                        <option value="footer">Alt Bilgi</option>
                                        <option value="buttons">Butonlar</option>
                                        <option value="messages">Mesajlar</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="description" class="form-label">Açıklama</label>
                            <input type="text" class="form-control" id="description" name="description">
                            <small class="text-muted">Bu metnin ne için kullanıldığını açıklayın</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                        <button type="submit" class="btn btn-admin">
                            <i class="fas fa-plus me-2"></i>Metin Ekle
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function previewText(key, value) {
            document.getElementById('preview-key').textContent = key;
            document.getElementById('preview-value').textContent = value;
            new bootstrap.Modal(document.getElementById('previewModal')).show();
        }
        
        // Console log
        console.log('🎰 BonusBoss Admin Panel - Metin Yönetimi');
        console.log('👨‍💻 Yazılımcı: BERAT K');
    </script>
</body>
</html>