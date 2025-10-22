<?php
require_once '../../config.php';

// Oturum kontrolü
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Metin güncelleme işlemi
if (isset($_POST['update_text'])) {
    $text_id = (int)$_POST['text_id'];
    $text_value = trim($_POST['text_value']);
    
    try {
        $stmt = $pdo->prepare("UPDATE site_texts SET text_value = ? WHERE id = ?");
        $stmt->execute([$text_value, $text_id]);
        
        logActivity('site_texts', $text_id, 'update', null, json_encode(['text_value' => $text_value]));
        
        $_SESSION['success'] = 'Metin başarıyla güncellendi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Metin güncellenirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Metin ekleme işlemi
if (isset($_POST['add_text'])) {
    $text_key = trim($_POST['text_key']);
    $text_value = trim($_POST['text_value']);
    $text_type = $_POST['text_type'];
    $page_section = trim($_POST['page_section']);
    
    try {
        $stmt = $pdo->prepare("INSERT INTO site_texts (text_key, text_value, text_type, page_section) VALUES (?, ?, ?, ?)");
        $stmt->execute([$text_key, $text_value, $text_type, $page_section]);
        
        $text_id = $pdo->lastInsertId();
        logActivity('site_texts', $text_id, 'create', null, json_encode($_POST));
        
        $_SESSION['success'] = 'Metin başarıyla eklendi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Metin eklenirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Metin silme işlemi
if (isset($_POST['delete_text'])) {
    $text_id = (int)$_POST['text_id'];
    
    try {
        $stmt = $pdo->prepare("DELETE FROM site_texts WHERE id = ?");
        $stmt->execute([$text_id]);
        
        logActivity('site_texts', $text_id, 'delete', null, null);
        
        $_SESSION['success'] = 'Metin başarıyla silindi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Metin silinirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Metin durum değiştirme
if (isset($_POST['toggle_status'])) {
    $text_id = (int)$_POST['text_id'];
    
    try {
        $stmt = $pdo->prepare("UPDATE site_texts SET is_active = NOT is_active WHERE id = ?");
        $stmt->execute([$text_id]);
        
        $_SESSION['success'] = 'Metin durumu güncellendi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Durum güncellenirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Metin listesini al
try {
    $stmt = $pdo->prepare("SELECT * FROM site_texts ORDER BY page_section, text_key");
    $stmt->execute();
    $texts = $stmt->fetchAll();
    
    // Metinleri grupla
    $grouped_texts = [];
    foreach ($texts as $text) {
        $grouped_texts[$text['page_section']][] = $text;
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Metin listesi alınırken hata oluştu: ' . $e->getMessage();
    $grouped_texts = [];
}

$page_title = 'Metin Yönetimi';
include '../header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Metin Yönetimi</h1>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTextModal">
            <i class="fas fa-plus"></i> Yeni Metin Ekle
        </button>
    </div>

    <?php include '../alerts.php'; ?>

    <?php foreach ($grouped_texts as $section => $section_texts): ?>
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0"><?= ucfirst(str_replace('_', ' ', $section)) ?> Metinleri</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Anahtar</th>
                            <th>Değer</th>
                            <th>Tip</th>
                            <th>Durum</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($section_texts as $text): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($text['text_key']) ?></strong>
                                <br><small class="text-muted"><?= htmlspecialchars($text['text_key']) ?></small>
                            </td>
                            <td>
                                <?php if ($text['text_type'] == 'html'): ?>
                                    <div style="max-height: 100px; overflow: hidden;">
                                        <?= htmlspecialchars(substr($text['text_value'], 0, 100)) ?>
                                        <?= strlen($text['text_value']) > 100 ? '...' : '' ?>
                                    </div>
                                <?php else: ?>
                                    <div style="max-height: 100px; overflow: hidden;">
                                        <?= htmlspecialchars(substr($text['text_value'], 0, 100)) ?>
                                        <?= strlen($text['text_value']) > 100 ? '...' : '' ?>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info"><?= strtoupper($text['text_type']) ?></span>
                            </td>
                            <td>
                                <span class="badge <?= $text['is_active'] ? 'bg-success' : 'bg-danger' ?>">
                                    <?= $text['is_active'] ? 'Aktif' : 'Pasif' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button class="btn btn-sm btn-outline-primary" 
                                            onclick="editText(<?= htmlspecialchars(json_encode($text)) ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <form method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Bu metni silmek istediğinizden emin misiniz?')">
                                        <input type="hidden" name="text_id" value="<?= $text['id'] ?>">
                                        <button type="submit" name="delete_text" class="btn btn-sm btn-outline-danger">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="text_id" value="<?= $text['id'] ?>">
                                        <button type="submit" name="toggle_status" class="btn btn-sm btn-outline-warning">
                                            <i class="fas fa-toggle-<?= $text['is_active'] ? 'on' : 'off' ?>"></i>
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
    <?php endforeach; ?>

    <?php if (empty($grouped_texts)): ?>
    <div class="card">
        <div class="card-body text-center">
            <i class="fas fa-font fa-3x text-muted mb-3"></i>
            <h5>Henüz metin bulunmuyor</h5>
            <p class="text-muted">Yeni metinler ekleyerek başlayın.</p>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Metin Ekleme Modal -->
<div class="modal fade" id="addTextModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Yeni Metin Ekle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="text_key" class="form-label">Metin Anahtarı *</label>
                                <input type="text" class="form-control" id="text_key" name="text_key" required>
                                <small class="form-text text-muted">Örn: hero_title, about_description</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="page_section" class="form-label">Sayfa Bölümü</label>
                                <select class="form-control" id="page_section" name="page_section">
                                    <option value="home">Ana Sayfa</option>
                                    <option value="about">Hakkında</option>
                                    <option value="services">Hizmetler</option>
                                    <option value="portfolio">Portföy</option>
                                    <option value="gallery">Galeri</option>
                                    <option value="contact">İletişim</option>
                                    <option value="footer">Footer</option>
                                    <option value="general">Genel</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="text_type" class="form-label">Metin Tipi</label>
                        <select class="form-control" id="text_type" name="text_type">
                            <option value="text">Düz Metin</option>
                            <option value="textarea">Uzun Metin</option>
                            <option value="html">HTML</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="text_value" class="form-label">Metin Değeri *</label>
                        <textarea class="form-control" id="text_value" name="text_value" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="add_text" class="btn btn-primary">Ekle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Metin Düzenleme Modal -->
<div class="modal fade" id="editTextModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Metin Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" id="edit_text_id" name="text_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_text_key" class="form-label">Metin Anahtarı</label>
                                <input type="text" class="form-control" id="edit_text_key" readonly>
                                <small class="form-text text-muted">Anahtar değiştirilemez</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_page_section" class="form-label">Sayfa Bölümü</label>
                                <input type="text" class="form-control" id="edit_page_section" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_text_type" class="form-label">Metin Tipi</label>
                        <input type="text" class="form-control" id="edit_text_type" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="edit_text_value" class="form-label">Metin Değeri *</label>
                        <textarea class="form-control" id="edit_text_value" name="text_value" rows="6" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="update_text" class="btn btn-primary">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editText(text) {
    document.getElementById('edit_text_id').value = text.id;
    document.getElementById('edit_text_key').value = text.text_key;
    document.getElementById('edit_page_section').value = text.page_section;
    document.getElementById('edit_text_type').value = text.text_type;
    document.getElementById('edit_text_value').value = text.text_value;
    
    new bootstrap.Modal(document.getElementById('editTextModal')).show();
}

// Metin tipine göre textarea yüksekliğini ayarla
document.getElementById('text_type').addEventListener('change', function() {
    const textValue = document.getElementById('text_value');
    if (this.value === 'html') {
        textValue.rows = 8;
    } else if (this.value === 'textarea') {
        textValue.rows = 6;
    } else {
        textValue.rows = 4;
    }
});
</script>

<?php include '../footer.php'; ?>