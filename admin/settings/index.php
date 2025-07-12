<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Site Ayarları
 */

require_once '../../config.php';

// Oturum kontrolü
if (!isLoggedIn()) {
    header('Location: ../login.php');
    exit;
}

// Ayarları kaydet
if (isset($_POST['save_settings'])) {
    try {
        foreach ($_POST['settings'] as $key => $value) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$value, $key]);
        }
        
        // Logo yükleme
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
            $upload_dir = '../../assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $filename = 'logo_' . time() . '_' . uniqid() . '.' . $file_extension;
                $filepath = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $filepath)) {
                    $logo_path = 'assets/uploads/' . $filename;
                    
                    // Eski logoyu sil
                    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_logo'");
                    $stmt->execute();
                    $old_logo = $stmt->fetchColumn();
                    
                    if ($old_logo && file_exists('../../' . $old_logo)) {
                        unlink('../../' . $old_logo);
                    }
                    
                    // Yeni logo yolunu kaydet
                    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'site_logo'");
                    $stmt->execute([$logo_path]);
                }
            }
        }
        
        // Favicon yükleme
        if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] == 0) {
            $upload_dir = '../../assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['site_favicon']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['ico', 'png', 'jpg', 'jpeg'];
            
            if (in_array($file_extension, $allowed_extensions)) {
                $filename = 'favicon_' . time() . '_' . uniqid() . '.' . $file_extension;
                $filepath = $upload_dir . $filename;
                
                if (move_uploaded_file($_FILES['site_favicon']['tmp_name'], $filepath)) {
                    $favicon_path = 'assets/uploads/' . $filename;
                    
                    // Eski favicon'u sil
                    $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = 'site_favicon'");
                    $stmt->execute();
                    $old_favicon = $stmt->fetchColumn();
                    
                    if ($old_favicon && file_exists('../../' . $old_favicon)) {
                        unlink('../../' . $old_favicon);
                    }
                    
                    // Yeni favicon yolunu kaydet
                    $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'site_favicon'");
                    $stmt->execute([$favicon_path]);
                }
            }
        }
        
        $_SESSION['success'] = 'Ayarlar başarıyla kaydedildi.';
    } catch (Exception $e) {
        $_SESSION['error'] = 'Ayarlar kaydedilirken hata oluştu: ' . $e->getMessage();
    }
    
    header('Location: index.php');
    exit;
}

// Ayarları al
try {
    $stmt = $pdo->prepare("SELECT * FROM settings ORDER BY setting_group, setting_key");
    $stmt->execute();
    $settings = $stmt->fetchAll();
    
    // Ayarları grupla
    $grouped_settings = [];
    foreach ($settings as $setting) {
        $grouped_settings[$setting['setting_group']][] = $setting;
    }
} catch (Exception $e) {
    $_SESSION['error'] = 'Ayarlar alınırken hata oluştu: ' . $e->getMessage();
    $grouped_settings = [];
}

$page_title = 'Site Ayarları';
include '../header.php';
?>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">Site Ayarları</h1>
    </div>

    <?php include '../alerts.php'; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="row">
            <div class="col-md-8">
                <!-- Genel Ayarlar -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Genel Ayarlar</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($grouped_settings['general'])): ?>
                            <?php foreach ($grouped_settings['general'] as $setting): ?>
                                <div class="mb-3">
                                    <label for="<?= $setting['setting_key'] ?>" class="form-label">
                                        <?= ucfirst(str_replace('_', ' ', $setting['setting_key'])) ?>
                                    </label>
                                    
                                    <?php if ($setting['setting_type'] == 'textarea'): ?>
                                        <textarea class="form-control" id="<?= $setting['setting_key'] ?>" 
                                                  name="settings[<?= $setting['setting_key'] ?>]" rows="3"><?= htmlspecialchars($setting['setting_value']) ?></textarea>
                                    <?php elseif ($setting['setting_type'] == 'boolean'): ?>
                                        <select class="form-control" id="<?= $setting['setting_key'] ?>" 
                                                name="settings[<?= $setting['setting_key'] ?>]">
                                            <option value="1" <?= $setting['setting_value'] == '1' ? 'selected' : '' ?>>Aktif</option>
                                            <option value="0" <?= $setting['setting_value'] == '0' ? 'selected' : '' ?>>Pasif</option>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" class="form-control" id="<?= $setting['setting_key'] ?>" 
                                               name="settings[<?= $setting['setting_key'] ?>]" 
                                               value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- İletişim Ayarları -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">İletişim Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($grouped_settings['contact'])): ?>
                            <?php foreach ($grouped_settings['contact'] as $setting): ?>
                                <div class="mb-3">
                                    <label for="<?= $setting['setting_key'] ?>" class="form-label">
                                        <?= ucfirst(str_replace('_', ' ', $setting['setting_key'])) ?>
                                    </label>
                                    <input type="text" class="form-control" id="<?= $setting['setting_key'] ?>" 
                                           name="settings[<?= $setting['setting_key'] ?>]" 
                                           value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sosyal Medya Ayarları -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Sosyal Medya Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($grouped_settings['social'])): ?>
                            <?php foreach ($grouped_settings['social'] as $setting): ?>
                                <div class="mb-3">
                                    <label for="<?= $setting['setting_key'] ?>" class="form-label">
                                        <?= ucfirst(str_replace('social_', '', str_replace('_', ' ', $setting['setting_key']))) ?>
                                    </label>
                                    <input type="url" class="form-control" id="<?= $setting['setting_key'] ?>" 
                                           name="settings[<?= $setting['setting_key'] ?>]" 
                                           value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- SEO Ayarları -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">SEO Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($grouped_settings['seo'])): ?>
                            <?php foreach ($grouped_settings['seo'] as $setting): ?>
                                <div class="mb-3">
                                    <label for="<?= $setting['setting_key'] ?>" class="form-label">
                                        <?= ucfirst(str_replace('_', ' ', $setting['setting_key'])) ?>
                                    </label>
                                    
                                    <?php if ($setting['setting_type'] == 'textarea'): ?>
                                        <textarea class="form-control" id="<?= $setting['setting_key'] ?>" 
                                                  name="settings[<?= $setting['setting_key'] ?>]" rows="4"><?= htmlspecialchars($setting['setting_value']) ?></textarea>
                                    <?php else: ?>
                                        <input type="text" class="form-control" id="<?= $setting['setting_key'] ?>" 
                                               name="settings[<?= $setting['setting_key'] ?>]" 
                                               value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <!-- Logo ve Favicon -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Logo ve Favicon</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="site_logo" class="form-label">Site Logo</label>
                            <input type="file" class="form-control" id="site_logo" name="site_logo" accept="image/*">
                            <?php 
                            $logo_setting = array_filter($settings, function($s) { return $s['setting_key'] == 'site_logo'; });
                            $logo_setting = reset($logo_setting);
                            if ($logo_setting && $logo_setting['setting_value']): ?>
                                <div class="mt-2">
                                    <img src="../../<?= htmlspecialchars($logo_setting['setting_value']) ?>" 
                                         alt="Mevcut Logo" style="max-width: 100px; max-height: 100px;">
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="mb-3">
                            <label for="site_favicon" class="form-label">Site Favicon</label>
                            <input type="file" class="form-control" id="site_favicon" name="site_favicon" accept="image/*,.ico">
                            <?php 
                            $favicon_setting = array_filter($settings, function($s) { return $s['setting_key'] == 'site_favicon'; });
                            $favicon_setting = reset($favicon_setting);
                            if ($favicon_setting && $favicon_setting['setting_value']): ?>
                                <div class="mt-2">
                                    <img src="../../<?= htmlspecialchars($favicon_setting['setting_value']) ?>" 
                                         alt="Mevcut Favicon" style="max-width: 32px; max-height: 32px;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Sistem Ayarları -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Sistem Ayarları</h5>
                    </div>
                    <div class="card-body">
                        <?php if (isset($grouped_settings['system'])): ?>
                            <?php foreach ($grouped_settings['system'] as $setting): ?>
                                <div class="mb-3">
                                    <label for="<?= $setting['setting_key'] ?>" class="form-label">
                                        <?= ucfirst(str_replace('_', ' ', $setting['setting_key'])) ?>
                                    </label>
                                    
                                    <?php if ($setting['setting_type'] == 'boolean'): ?>
                                        <select class="form-control" id="<?= $setting['setting_key'] ?>" 
                                                name="settings[<?= $setting['setting_key'] ?>]">
                                            <option value="1" <?= $setting['setting_value'] == '1' ? 'selected' : '' ?>>Aktif</option>
                                            <option value="0" <?= $setting['setting_value'] == '0' ? 'selected' : '' ?>>Pasif</option>
                                        </select>
                                    <?php else: ?>
                                        <input type="text" class="form-control" id="<?= $setting['setting_key'] ?>" 
                                               name="settings[<?= $setting['setting_key'] ?>]" 
                                               value="<?= htmlspecialchars($setting['setting_value']) ?>">
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <button type="submit" name="save_settings" class="btn btn-primary">
                            <i class="fas fa-save"></i> Ayarları Kaydet
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include '../footer.php'; ?>