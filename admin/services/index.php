<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Hizmet Yönetimi
 */

require_once '../../config.php';

// Oturum kontrolü
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Hizmet silme işlemi
if (isset($_POST['delete_service'])) {
    $service_id = (int)$_POST['service_id'];
    
    try {
        // Önce resim dosyasını al
        $stmt = $pdo->prepare("SELECT image_path FROM services WHERE id = ?");
        $stmt->execute([$service_id]);
        $service = $stmt->fetch();
        
        if ($service && $service['image_path'] && file_exists('../../' . $service['image_path'])) {
            unlink('../../' . $service['image_path']);
        }
        
        // Veritabanından sil
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $stmt->execute([$service_id]);
        
        // Aktivite logu
        logActivity('services', $service_id, 'delete', null, null);
        
        $_SESSION['success'] = 'Hizmet başarıyla silindi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Hizmet silinirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Hizmet durum değiştirme
if (isset($_POST['toggle_status'])) {
    $service_id = (int)$_POST['service_id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE services SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$service_id]);
        
        $_SESSION['success'] = 'Hizmet durumu güncellendi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Durum güncellenirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Hizmet ekleme işlemi
if (isset($_POST['add_service'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);
    $sort_order = (int)$_POST['sort_order'];
    
    // Resim yükleme
    $image_path = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../../assets/uploads/services/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $filename = 'service_' . time() . '_' . uniqid() . '.' . $file_extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                $image_path = 'assets/uploads/services/' . $filename;
            }
        }
    }
    
    try {
        $stmt = $pdo->prepare("INSERT INTO services (title, description, icon, image_path, sort_order) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $icon, $image_path, $sort_order]);
        
        $service_id = $pdo->lastInsertId();
        logActivity('services', $service_id, 'create', null, json_encode($_POST));
        
        $_SESSION['success'] = 'Hizmet başarıyla eklendi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Hizmet eklenirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Hizmet güncelleme işlemi
if (isset($_POST['update_service'])) {
    $service_id = (int)$_POST['service_id'];
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $icon = trim($_POST['icon']);
    $sort_order = (int)$_POST['sort_order'];
    
    // Resim yükleme
    $image_path = $_POST['current_image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $upload_dir = '../../assets/uploads/services/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $filename = 'service_' . time() . '_' . uniqid() . '.' . $file_extension;
            $filepath = $upload_dir . $filename;
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $filepath)) {
                // Eski resmi sil
                if ($image_path && file_exists('../../' . $image_path)) {
                    unlink('../../' . $image_path);
                }
                $image_path = 'assets/uploads/services/' . $filename;
            }
        }
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, icon = ?, image_path = ?, sort_order = ? WHERE id = ?");
        $stmt->execute([$title, $description, $icon, $image_path, $sort_order, $service_id]);
        
        logActivity('services', $service_id, 'update', null, json_encode($_POST));
        
        $_SESSION['success'] = 'Hizmet başarıyla güncellendi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Hizmet güncellenirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Hizmet listesini al
try {
    $stmt = $pdo->prepare("SELECT * FROM services ORDER BY sort_order ASC, created_at DESC");
    $stmt->execute();
    $services = $stmt->fetchAll();
} catch (Exception $e) {
    $_SESSION['error'] = 'Hizmet listesi alınırken hata oluştu: ' . $e->getMessage();
    $services = [];
}

$page_title = 'Hizmet Yönetimi';
include '../header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Hizmet Yönetimi</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addServiceModal">
            <i class="fas fa-plus"></i> Yeni Hizmet Ekle
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
                            <th>İkon</th>
                            <th>Sıra</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($services as $service): ?>
                        <tr>
                            <td><?= $service['id'] ?></td>
                            <td>
                                <?php if ($service['image_path']): ?>
                                    <img src="../../<?= htmlspecialchars($service['image_path']) ?>" 
                                         alt="<?= htmlspecialchars($service['title']) ?>" 
                                         style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <div class="bg-secondary text-white d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px; border-radius: 5px;">
                                        <i class="fas fa-image"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($service['title']) ?></td>
                            <td>
                                <i class="<?= htmlspecialchars($service['icon']) ?>"></i>
                                <small class="text-muted"><?= htmlspecialchars($service['icon']) ?></small>
                            </td>
                            <td><?= $service['sort_order'] ?></td>
                            <td>
                                <span class="badge <?= $service['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $service['is_active'] ? 'Aktif' : 'Pasif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editService(<?= htmlspecialchars(json_encode($service)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Bu hizmeti silmek istediğinizden emin misiniz?')">
                                        <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                        <button type="submit" name="delete_service" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="service_id" value="<?= $service['id'] ?>">
                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-toggle-<?= $service['is_active'] ? 'on' : 'off' ?>"></i>
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

<!-- Hizmet Ekleme Modal -->
<div class="modal fade" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Hizmet Ekle</h5>
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
                                <label for="icon" class="form-label">İkon (Font Awesome)</label>
                                <input type="text" class="form-control" id="icon" name="icon" placeholder="fas fa-cog">
                                <small class="form-text text-muted">Font Awesome ikon sınıfı (örn: fas fa-cog)</small>
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
                    <button type="submit" name="add_service" class="btn btn-primary">Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Hizmet Düzenleme Modal -->
<div class="modal fade" id="editServiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Hizmet Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_service_id" name="service_id">
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
                                <label for="edit_icon" class="form-label">İkon (Font Awesome)</label>
                                <input type="text" class="form-control" id="edit_icon" name="icon">
                                <small class="form-text text-muted">Font Awesome ikon sınıfı (örn: fas fa-cog)</small>
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
                    <button type="submit" name="update_service" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editService(service) {
    document.getElementById('edit_service_id').value = service.id;
    document.getElementById('edit_title').value = service.title;
    document.getElementById('edit_description').value = service.description || '';
    document.getElementById('edit_icon').value = service.icon || '';
    document.getElementById('edit_sort_order').value = service.sort_order || 0;
    document.getElementById('edit_current_image').value = service.image_path || '';
    
    // Mevcut resim önizlemesi
    const preview = document.getElementById('current_image_preview');
    if (service.image_path) {
        preview.innerHTML = `<img src="../../${service.image_path}" style="max-width: 100px; max-height: 100px; border-radius: 5px;">`;
    } else {
        preview.innerHTML = '';
    }
    
    new bootstrap.Modal(document.getElementById('editServiceModal')).show();
}
</script>

<?php include '../footer.php'; ?>