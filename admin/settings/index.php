<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Site Ayarları
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

// Ayarları kaydet
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    // CSRF token kontrolü
    if (!verify_csrf_token($_POST['csrf_token'])) {
        show_message('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
    } else {
        $settings = [
            'site_title' => clean_input($_POST['site_title']),
            'site_description' => clean_input($_POST['site_description']),
            'site_keywords' => clean_input($_POST['site_keywords']),
            'contact_email' => clean_input($_POST['contact_email']),
            'contact_phone' => clean_input($_POST['contact_phone']),
            'contact_address' => clean_input($_POST['contact_address']),
            'social_facebook' => clean_input($_POST['social_facebook']),
            'social_instagram' => clean_input($_POST['social_instagram']),
            'social_twitter' => clean_input($_POST['social_twitter']),
            'social_youtube' => clean_input($_POST['social_youtube']),
            'telegram_channel' => clean_input($_POST['telegram_channel']),
            'telegram_group' => clean_input($_POST['telegram_group']),
            'working_hours' => clean_input($_POST['working_hours']),
            'footer_text' => clean_input($_POST['footer_text'])
        ];
        
        $success = true;
        foreach ($settings as $key => $value) {
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            if (!$stmt->execute([$value, $key])) {
                $success = false;
                break;
            }
        }
        
        if ($success) {
            show_message('Ayarlar başarıyla kaydedildi.', 'success');
        } else {
            show_message('Ayarlar kaydedilirken bir hata oluştu.', 'error');
        }
        
        redirect('index.php');
    }
}

// Logo yükleme
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['upload_logo'])) {
    if (!verify_csrf_token($_POST['csrf_token'])) {
        show_message('Güvenlik hatası. Lütfen tekrar deneyin.', 'error');
    } else {
        if (isset($_FILES['logo']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../../assets/uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            $new_filename = 'logo_' . time() . '.' . $file_extension;
            $upload_path = $upload_dir . $new_filename;
            
            if (in_array($file_extension, ALLOWED_IMAGE_TYPES) && move_uploaded_file($_FILES['logo']['tmp_name'], $upload_path)) {
                $logo_path = 'assets/uploads/' . $new_filename;
                $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = 'site_logo'");
                if ($stmt->execute([$logo_path])) {
                    show_message('Logo başarıyla yüklendi.', 'success');
                } else {
                    show_message('Logo kaydedilirken bir hata oluştu.', 'error');
                }
            } else {
                show_message('Logo yüklenirken bir hata oluştu.', 'error');
            }
        } else {
            show_message('Lütfen bir logo dosyası seçin.', 'error');
        }
        
        redirect('index.php');
    }
}

// Mevcut ayarları getir
$current_settings = get_site_settings();

// Mesaj göster
$message = get_message();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Ayarları - BonusBoss Admin</title>
    
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
        
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e9ecef;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
        }
        
        .logo-preview {
            max-width: 200px;
            max-height: 100px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .color-picker {
            width: 50px;
            height: 50px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
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
                        <a class="nav-link" href="../gallery/">
                            <i class="fas fa-images"></i>Galeri Yönetimi
                        </a>
                        <a class="nav-link" href="../services/">
                            <i class="fas fa-cogs"></i>Hizmet Yönetimi
                        </a>
                        <a class="nav-link" href="../messages/">
                            <i class="fas fa-envelope"></i>Mesaj Yönetimi
                        </a>
                        <a class="nav-link active" href="index.php">
                            <i class="fas fa-cog"></i>Site Ayarları
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10">
                <div class="main-content">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h1 class="h3 mb-0">Site Ayarları</h1>
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

                    <!-- Genel Ayarlar -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Genel Ayarlar</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="save_settings" value="1">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="site_title" class="form-label">Site Başlığı</label>
                                        <input type="text" class="form-control" id="site_title" name="site_title" value="<?php echo htmlspecialchars($current_settings['site_title'] ?? ''); ?>" required>
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_email" class="form-label">İletişim E-posta</label>
                                        <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($current_settings['contact_email'] ?? ''); ?>" required>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="contact_phone" class="form-label">İletişim Telefon</label>
                                        <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($current_settings['contact_phone'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="working_hours" class="form-label">Çalışma Saatleri</label>
                                        <input type="text" class="form-control" id="working_hours" name="working_hours" value="<?php echo htmlspecialchars($current_settings['working_hours'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="contact_address" class="form-label">İletişim Adresi</label>
                                    <textarea class="form-control" id="contact_address" name="contact_address" rows="2"><?php echo htmlspecialchars($current_settings['contact_address'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="site_description" class="form-label">Site Açıklaması</label>
                                    <textarea class="form-control" id="site_description" name="site_description" rows="3"><?php echo htmlspecialchars($current_settings['site_description'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="site_keywords" class="form-label">Anahtar Kelimeler</label>
                                    <input type="text" class="form-control" id="site_keywords" name="site_keywords" value="<?php echo htmlspecialchars($current_settings['site_keywords'] ?? ''); ?>">
                                    <small class="form-text text-muted">Virgülle ayırarak yazın</small>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="footer_text" class="form-label">Footer Metni</label>
                                    <input type="text" class="form-control" id="footer_text" name="footer_text" value="<?php echo htmlspecialchars($current_settings['footer_text'] ?? ''); ?>">
                                </div>
                                
                                <button type="submit" class="btn btn-admin">
                                    <i class="fas fa-save me-2"></i>Ayarları Kaydet
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Logo Yükleme -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-image me-2"></i>Logo Yönetimi</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <form method="POST" action="" enctype="multipart/form-data">
                                        <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                        <input type="hidden" name="upload_logo" value="1">
                                        
                                        <div class="mb-3">
                                            <label for="logo" class="form-label">Logo Dosyası</label>
                                            <input type="file" class="form-control" id="logo" name="logo" accept="image/*" required>
                                            <small class="form-text text-muted">PNG, JPG, GIF formatları desteklenir. Maksimum 2MB.</small>
                                        </div>
                                        
                                        <button type="submit" class="btn btn-admin">
                                            <i class="fas fa-upload me-2"></i>Logo Yükle
                                        </button>
                                    </form>
                                </div>
                                
                                <div class="col-md-6">
                                    <h6>Mevcut Logo:</h6>
                                    <?php if (!empty($current_settings['site_logo'])): ?>
                                    <img src="../../assets/uploads/<?php echo $current_settings['site_logo']; ?>" alt="Site Logo" class="logo-preview">
                                    <?php else: ?>
                                    <p class="text-muted">Henüz logo yüklenmemiş</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sosyal Medya Ayarları -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fab fa-facebook me-2"></i>Sosyal Medya Ayarları</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="">
                                <input type="hidden" name="csrf_token" value="<?php echo generate_csrf_token(); ?>">
                                <input type="hidden" name="save_settings" value="1">
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="social_facebook" class="form-label">Facebook URL</label>
                                        <input type="url" class="form-control" id="social_facebook" name="social_facebook" value="<?php echo htmlspecialchars($current_settings['social_facebook'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="social_instagram" class="form-label">Instagram URL</label>
                                        <input type="url" class="form-control" id="social_instagram" name="social_instagram" value="<?php echo htmlspecialchars($current_settings['social_instagram'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="social_twitter" class="form-label">Twitter URL</label>
                                        <input type="url" class="form-control" id="social_twitter" name="social_twitter" value="<?php echo htmlspecialchars($current_settings['social_twitter'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="social_youtube" class="form-label">YouTube URL</label>
                                        <input type="url" class="form-control" id="social_youtube" name="social_youtube" value="<?php echo htmlspecialchars($current_settings['social_youtube'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="telegram_channel" class="form-label">Telegram Kanalı</label>
                                        <input type="url" class="form-control" id="telegram_channel" name="telegram_channel" value="<?php echo htmlspecialchars($current_settings['telegram_channel'] ?? ''); ?>">
                                    </div>
                                    
                                    <div class="col-md-6 mb-3">
                                        <label for="telegram_group" class="form-label">Telegram Grubu</label>
                                        <input type="url" class="form-control" id="telegram_group" name="telegram_group" value="<?php echo htmlspecialchars($current_settings['telegram_group'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <button type="submit" class="btn btn-admin">
                                    <i class="fas fa-save me-2"></i>Sosyal Medya Ayarlarını Kaydet
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Sistem Bilgileri -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Sistem Bilgileri</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <p><strong>PHP Sürümü:</strong> <?php echo PHP_VERSION; ?></p>
                                    <p><strong>Sunucu Yazılımı:</strong> <?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Bilinmiyor'; ?></p>
                                    <p><strong>Maksimum Dosya Boyutu:</strong> <?php echo ini_get('upload_max_filesize'); ?></p>
                                </div>
                                <div class="col-md-6">
                                    <p><strong>Çalışma Süresi Limiti:</strong> <?php echo ini_get('max_execution_time'); ?> saniye</p>
                                    <p><strong>Bellek Limiti:</strong> <?php echo ini_get('memory_limit'); ?></p>
                                    <p><strong>Zaman Dilimi:</strong> <?php echo date_default_timezone_get(); ?></p>
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