<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Portföy Projesi Ekleme
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

// Kategorileri getir
$categories = $pdo->query("SELECT * FROM categories WHERE type = 'portfolio' ORDER BY name")->fetchAll();

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
    $content = clean_input($_POST['content']);
    $category_id = (int)$_POST['category_id'];
    $client = clean_input($_POST['client']);
    $project_date = clean_input($_POST['project_date']);
    $project_url = clean_input($_POST['project_url']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];
    
    // Validasyon
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'Proje başlığı gereklidir.';
    }
    
    if (empty($description)) {
        $errors[] = 'Proje açıklaması gereklidir.';
    }
    
    if ($category_id <= 0) {
        $errors[] = 'Kategori seçimi gereklidir.';
    }
    
    // Resim yükleme
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $upload_result = upload_file($_FILES['image'], UPLOAD_PATH);
        if ($upload_result) {
            $image = $upload_result;
        } else {
            $errors[] = 'Resim yüklenirken bir hata oluştu.';
        }
    }
    
    // Hata yoksa kaydet
    if (empty($errors)) {
        $stmt = $pdo->prepare("INSERT INTO portfolio (title, description, content, image, category_id, client, project_date, project_url, is_featured, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if ($stmt->execute([$title, $description, $content, $image, $category_id, $client, $project_date, $project_url, $is_featured, $is_active, $sort_order])) {
            log_activity('portfolio_added', "Yeni portföy projesi eklendi: $title");
            show_message('Portföy projesi başarıyla eklendi.', 'success');
            redirect('index.php');
        } else {
            show_message('Portföy projesi eklenirken bir hata oluştu.', 'error');
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
    <title>Portföy Projesi Ekle - BonusBoss Admin</title>
    
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
        
        .image-preview {
            max-width: 200px;
            max-height: 200px;
            border-radius: 10px;
            margin-top: 1rem;
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
                        <h1 class="h3 mb-0">Yeni Portföy Projesi Ekle</h1>
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
                            <h5 class="mb-0">Proje Bilgileri</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">Proje Başlığı *</label>
                                            <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                                            <div class="invalid-feedback">
                                                Proje başlığı gereklidir.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="category_id" class="form-label">Kategori *</label>
                                            <select class="form-control" id="category_id" name="category_id" required>
                                                <option value="">Kategori Seçin</option>
                                                <?php foreach ($categories as $category): ?>
                                                <option value="<?php echo $category['id']; ?>" <?php echo (isset($_POST['category_id']) && $_POST['category_id'] == $category['id']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($category['name']); ?>
                                                </option>
                                                <?php endforeach; ?>
                                            </select>
                                            <div class="invalid-feedback">
                                                Kategori seçimi gereklidir.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="description" class="form-label">Kısa Açıklama *</label>
                                    <textarea class="form-control" id="description" name="description" rows="3" required><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                                    <div class="invalid-feedback">
                                        Proje açıklaması gereklidir.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="content" class="form-label">Detaylı İçerik</label>
                                    <textarea class="form-control" id="content" name="content" rows="8"><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                                    <div class="form-text">
                                        Proje hakkında detaylı bilgi verebilirsiniz.
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="client" class="form-label">Müşteri</label>
                                            <input type="text" class="form-control" id="client" name="client" value="<?php echo isset($_POST['client']) ? htmlspecialchars($_POST['client']) : ''; ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="project_date" class="form-label">Proje Tarihi</label>
                                            <input type="date" class="form-control" id="project_date" name="project_date" value="<?php echo isset($_POST['project_date']) ? htmlspecialchars($_POST['project_date']) : ''; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="project_url" class="form-label">Proje URL</label>
                                    <input type="url" class="form-control" id="project_url" name="project_url" value="<?php echo isset($_POST['project_url']) ? htmlspecialchars($_POST['project_url']) : ''; ?>" placeholder="https://example.com">
                                    <div class="form-text">
                                        Projenin canlı linkini ekleyebilirsiniz.
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="image" class="form-label">Proje Görseli</label>
                                    <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                    <div class="form-text">
                                        Önerilen boyut: 800x600px, Maksimum: 5MB
                                    </div>
                                    <div id="image-preview"></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <label for="sort_order" class="form-label">Sıralama</label>
                                            <input type="number" class="form-control" id="sort_order" name="sort_order" value="<?php echo isset($_POST['sort_order']) ? (int)$_POST['sort_order'] : 0; ?>" min="0">
                                            <div class="form-text">
                                                Düşük sayılar önce gösterilir.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" <?php echo (isset($_POST['is_featured']) && $_POST['is_featured']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_featured">
                                                    Öne Çıkan Proje
                                                </label>
                                            </div>
                                            <div class="form-text">
                                                Öne çıkan projeler ana sayfada gösterilir.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo (isset($_POST['is_active']) && $_POST['is_active']) ? 'checked' : ''; ?>>
                                                <label class="form-check-label" for="is_active">
                                                    Aktif
                                                </label>
                                            </div>
                                            <div class="form-text">
                                                Aktif projeler sitede görünür.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>İptal
                                    </a>
                                    <button type="submit" class="btn btn-admin">
                                        <i class="fas fa-save me-2"></i>Projeyi Kaydet
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
        // Resim önizleme
        function previewImage(input) {
            const preview = document.getElementById('image-preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="image-preview" alt="Önizleme">`;
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.innerHTML = '';
            }
        }
        
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