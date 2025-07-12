<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Hizmet Ekleme
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

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF token kontrolü
    if (!verify_csrf_token($_POST['csrf_token'])) {
        show_message('Güvenlik hatası!', 'error');
        redirect('add.php');
    }
    
    // Form verilerini al
    $title = clean_input($_POST['title']);
    $description = clean_input($_POST['description']);
    $icon = clean_input($_POST['icon']);
    $content = clean_input($_POST['content']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];
    
    // Validasyon
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Hizmet başlığı gereklidir.';
    }
    
    if (empty($description)) {
        $errors[] = 'Hizmet açıklaması gereklidir.';
    }
    
    if (empty($icon)) {
        $errors[] = 'İkon seçimi gereklidir.';
    }
    
    // Hata yoksa kaydet
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO services (title, description, icon, content, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$title, $description, $icon, $content, $is_active, $sort_order])) {
            log_activity('service_added', "Yeni hizmet eklendi: $title");
            show_message('Hizmet başarıyla eklendi.', 'success');
            redirect('index.php');
        } else {
            show_message('Hizmet eklenirken bir hata oluştu.', 'error');
        }
    } else {
        show_message(implode('<br>', $errors), 'error');
    }
}

// CSRF token oluştur
$csrf_token = generate_csrf_token();

// Mesaj göster
$message = get_message();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hizmet Ekle - BonusBoss Admin</title>
    
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
        
        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(255, 215, 0, 0.25);
        }
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .icon-preview {
            font-size: 2rem;
            color: var(--primary-color);
            margin-top: 0.5rem;
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
                        <a class="nav-link active" href="index.php">
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
                        <h1 class="h3 mb-0">Yeni Hizmet Ekle</h1>
                        <a href="index.php" class="btn btn-admin">
                            <i class="fas fa-arrow-left me-2"></i>Geri Dön
                        </a>
                    </div>

                    <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?php echo $message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php endif; ?>

                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Hizmet Bilgileri</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Hizmet Başlığı *</label>
                                            <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                                            <div class="invalid-feedback">
                                                Hizmet başlığı gereklidir.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="icon" class="form-label">İkon *</label>
                                            <select class="form-control" id="icon" name="icon" required>
                                                <option value="">İkon Seçin</option>
                                                <option value="fas fa-video" <?php echo (isset($_POST['icon']) && $_POST['icon'] === 'fas fa-video') ? 'selected' : ''; ?>>Video (fas fa-video)</option>
                                                <option value="fab fa-facebook" <?php echo (isset($_POST['icon']) && $_POST['icon'] === 'fab fa-facebook') ? 'selected' : ''; ?>>Facebook (fab fa-facebook)</option>
                                                <option value="fas fa-star" <?php echo (isset($_POST['icon']) && $_POST['icon'] === 'fas fa-star') ? 'selected' : ''; ?>>Star (fas fa-star)</option>
                                                <option value="fab fa-facebook-f" <?php echo (isset($_POST['icon']) && $_POST['icon'] === 'fab fa-facebook-f') ? 'selected' : ''; ?>>Facebook F (fab fa-facebook-f)</option>
                                                <option value="fas fa-envelope" <?php echo (isset($_POST['icon']) && $_POST['icon'] === 'fas fa-envelope') ? 'selected' : ''; ?>>Envelope (fas fa-envelope)</option>
                                                <option value="fab fa-telegram-plane" <?php echo (isset($_POST['icon']) && $_POST['icon'] === 'fab fa-telegram-plane') ? 'selected' : ''; ?>>Telegram (fab fa-telegram-plane)</option>
                                                <option value="fas fa-bullhorn" <?php echo (isset($_POST['icon']) && $_POST['icon'] === 'fas fa-bullhorn') ? 'selected' : ''; ?>>Bullhorn (fas fa-bullhorn)</option>
                                                <option value="fas fa-chart-line" <?php echo (isset($_POST['icon']) && $_POST['icon'] === 'fas fa-chart-line') ? 'selected' : ''; ?>>Chart Line (fas fa-chart-line)</option>
                                                <option value="fas fa-users" <?php echo (isset($_POST['icon']) && $_POST['icon'] === 'fas fa-users') ? 'selected' : ''; ?>>Users (fas fa-users)</option>
                                                <option value="fas fa-cog" <?php echo (isset($_POST['icon']) && $_POST['icon'] === 'fas fa-cog') ? 'selected' : ''; ?>>Cog (fas fa-cog)</option>
                                            </select>
                                            <div class="invalid-feedback">
                                                İkon seçimi gereklidir.
                                            </div>
                                            <div id="icon-preview" class="icon-preview"></div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Kısa Açıklama *</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                                    <div class="invalid-feedback">
                                        Hizmet açıklaması gereklidir.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="content" class="form-label">Detaylı İçerik</label>
                                    <textarea class="form-control" id="content" name="content" rows="8"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                                    <div class="form-text">
                                        Hizmet hakkında detaylı bilgi verebilirsiniz.
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">Sıralama</label>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?php echo isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0; ?>" min="0">
                                            <div class="form-text">
                                                Düşük sayılar önce gösterilir.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo (isset($_POST['is_active']) && $_POST['is_active']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_active">
                                                    Aktif
                                                </label>
                                            </div>
                                            <div class="form-text">
                                                Aktif hizmetler sitede görünür.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>İptal
                                    </a>
                                    <button type="submit" class="btn btn-admin">
                                        <i class="fas fa-save me-2"></i>Hizmeti Kaydet
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // İkon önizleme
        document.getElementById('icon').addEventListener('change', function() {
            const preview = document.getElementById('icon-preview');
            const selectedIcon = this.value;
            
            if (selectedIcon) {
                preview.innerHTML = `<i class="${selectedIcon}"></i>`;
            } else {
                preview.innerHTML = '';
            }
        });
        
        // Form validasyonu
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>