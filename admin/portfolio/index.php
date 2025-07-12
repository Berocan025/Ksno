<?php
require_once '../../config.php';

// Oturum kontrolü
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Portfolio silme işlemi
if (isset($_POST['delete_portfolio'])) {
    $portfolio_id = (int)$_POST['portfolio_id'];
    
    try {
        // Önce resim dosyasını al
        $stmt = $pdo->prepare("SELECT image_path FROM portfolio WHERE id = ?");
        $stmt->execute([$portfolio_id]);
        $portfolio = $stmt->fetch();
        
        if ($portfolio && $portfolio['image_path'] && file_exists('../../' . $portfolio['image_path'])) {
            unlink('../../' . $portfolio['image_path']);
        }
        
        // Veritabanından sil
        $stmt = $pdo->prepare("DELETE FROM portfolio WHERE id = ?");
        $stmt->execute([$portfolio_id]);
        
        // Aktivite logu
        logActivity('portfolio', $portfolio_id, 'delete', null, null);
        
        $_SESSION['success'] = 'Portfolio başarıyla silindi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Portfolio silinirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Portfolio durum değiştirme
if (isset($_POST['toggle_status'])) {
    $portfolio_id = (int)$_POST['portfolio_id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE portfolio SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$portfolio_id]);
        
        $_SESSION['success'] = 'Portfolio durumu güncellendi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Durum güncellenirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Portfolio ekleme işlemi
if (isset($_POST['add_portfolio'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $client_name = trim($_POST['client_name']);
    $project_date = $_POST['project_date'];
    $category_id = (int)$_POST['category_id'];
    $sort_order = (int)$_POST['sort_order'];
    
    // Resim yükleme
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../../assets/uploads/portfolio/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $filename = 'portfolio_' . time() . '_' . uniqid() . '.' . $file_extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                $image_path = 'assets/uploads/portfolio/' . $filename;
            }
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO portfolio (title, description, image_path, client_name, project_date, category_id, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $image_path, $client_name, $project_date, $category_id, $sort_order]);
        
        $portfolio_id = $pdo->lastInsertId();
        logActivity('portfolio', $portfolio_id, 'create', null, json_encode($_POST));
        
        $_SESSION['success'] = 'Portfolio başarıyla eklendi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Portfolio eklenirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Portfolio güncelleme işlemi
if (isset($_POST['update_portfolio'])) {
    $portfolio_id = (int)$_POST['portfolio_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $client_name = trim($_POST['client_name']);
    $project_date = $_POST['project_date'];
    $category_id = (int)$_POST['category_id'];
    $sort_order = (int)$_POST['sort_order'];
    
    // Resim yükleme
    $image_path = $_POST['current_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../../assets/uploads/portfolio/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $filename = 'portfolio_' . time() . '_' . uniqid() . '.' . $file_extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                // Eski resmi sil
                if ($image_path && file_exists('../../' . $image_path)) {
                    unlink('../../' . $image_path);
                }
                $image_path = 'assets/uploads/portfolio/' . $filename;
            }
        }
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE portfolio SET title = ?, description = ?, image_path = ?, client_name = ?, project_date = ?, category_id = ?, sort_order = ? WHERE id = ?");
        $stmt->execute([$title, $description, $image_path, $client_name, $project_date, $category_id, $sort_order, $portfolio_id]);
        
        logActivity('portfolio', $portfolio_id, 'update', null, json_encode($_POST));
        
        $_SESSION['success'] = 'Portfolio başarıyla güncellendi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Portfolio güncellenirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Portfolio listesini al
try {
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name 
        FROM portfolio p 
        LEFT JOIN categories c ON p.category_id = c.id 
        ORDER BY p.sort_order ASC, p.created_at DESC
    ");
    $stmt->execute();
    $portfolios = $stmt->fetchAll();
} catch (Exception $e) {
    $_SESSION['error'] = 'Portfolio listesi alınırken hata oluştu: ' . $e->getMessage();
    $portfolios = [];
}

// Kategorileri al
try {
    $stmt = $pdo->prepare("SELECT * FROM categories WHERE is_active = 1 ORDER BY name");
    $stmt->execute();
    $categories = $stmt->fetchAll();
} catch (Exception $e) {
    $categories = [];
}

$page_title = 'Portfolio Yönetimi';
include '../header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Portfolio Yönetimi</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPortfolioModal">
            <i class="fas fa-plus"></i> Yeni Portfolio Ekle
        </button>
    </div>

    <?php include '../alerts.php'; ?>

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Resim</th>
                            <th>Başlık</th>
                            <th>Müşteri</th>
                            <th>Kategori</th>
                            <th>Tarih</th>
                            <th>Sıra</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($portfolios as $portfolio): ?>
                        <tr>
                            <td><?= $portfolio['id'] ?></td>
                            <td>
                                <?php if ($portfolio['image_path']): ?>
                                    <img src="../../<?= htmlspecialchars($portfolio['image_path']) ?>" 
                                         alt="<?= htmlspecialchars($portfolio['title']) ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; border-radius: 5px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($portfolio['title']) ?></td>
                            <td><?= htmlspecialchars($portfolio['client_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($portfolio['category_name'] ?? '') ?></td>
                            <td><?= $portfolio['project_date'] ? date('d.m.Y', strtotime($portfolio['project_date'])) : '' ?></td>
                            <td><?= $portfolio['sort_order'] ?></td>
                            <td>
                                <span class="badge <?= $portfolio['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $portfolio['is_active'] ? 'Aktif' : 'Pasif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editPortfolio(<?= htmlspecialchars(json_encode($portfolio)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Bu portfolio öğesini silmek istediğinizden emin misiniz?')">
                                        <input type="hidden" name="portfolio_id" value="<?= $portfolio['id'] ?>">
                                        <button type="submit" name="delete_portfolio" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="portfolio_id" value="<?= $portfolio['id'] ?>">
                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-toggle-<?= $portfolio['is_active'] ? 'on' : 'off' ?>"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Portfolio Ekleme Modal -->
<div class="modal fade" id="addPortfolioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Portfolio Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="title" class="form-label">Başlık *</label>
                                <input type="text" class="form-control" id="title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="client_name" class="form-label">Müşteri Adı</label>
                                <input type="text" class="form-control" id="client_name" name="client_name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select class="form-control" id="category_id" name="category_id">
                                    <option value="">Kategori Seçin</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="project_date" class="form-label">Proje Tarihi</label>
                                <input type="date" class="form-control" id="project_date" name="project_date">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="image" class="form-label">Resim</label>
                                <input type="file" class="form-control" id="image" name="image" accept="image/*">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Sıralama</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="add_portfolio" class="btn btn-primary">Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Portfolio Düzenleme Modal -->
<div class="modal fade" id="editPortfolioModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Portfolio Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_portfolio_id" name="portfolio_id">
                <input type="hidden" id="edit_current_image" name="current_image">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_title" class="form-label">Başlık *</label>
                                <input type="text" class="form-control" id="edit_title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_client_name" class="form-label">Müşteri Adı</label>
                                <input type="text" class="form-control" id="edit_client_name" name="client_name">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_category_id" class="form-label">Kategori</label>
                                <select class="form-control" id="edit_category_id" name="category_id">
                                    <option value="">Kategori Seçin</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?= $category['id'] ?>"><?= htmlspecialchars($category['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_project_date" class="form-label">Proje Tarihi</label>
                                <input type="date" class="form-control" id="edit_project_date" name="project_date">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_image" class="form-label">Resim</label>
                                <input type="file" class="form-control" id="edit_image" name="image" accept="image/*">
                                <div id="current_image_preview" class="mt-2"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_sort_order" class="form-label">Sıralama</label>
                                <input type="number" class="form-control" id="edit_sort_order" name="sort_order">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="update_portfolio" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editPortfolio(portfolio) {
    document.getElementById('edit_portfolio_id').value = portfolio.id;
    document.getElementById('edit_title').value = portfolio.title;
    document.getElementById('edit_description').value = portfolio.description || '';
    document.getElementById('edit_client_name').value = portfolio.client_name || '';
    document.getElementById('edit_category_id').value = portfolio.category_id || '';
    document.getElementById('edit_project_date').value = portfolio.project_date || '';
    document.getElementById('edit_sort_order').value = portfolio.sort_order || 0;
    document.getElementById('edit_current_image').value = portfolio.image_path || '';
    
    // Mevcut resim önizlemesi
    const preview = document.getElementById('current_image_preview');
    if (portfolio.image_path) {
        preview.innerHTML = `<img src="../../${portfolio.image_path}" style="max-width: 100px; max-height: 100px; border-radius: 5px;">`;
    } else {
        preview.innerHTML = '';
    }
    
    new bootstrap.Modal(document.getElementById('editPortfolioModal')).show();
}
</script>

<?php include '../footer.php'; ?>