<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Galeri İçeriği Ekleme
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
$categories = $pdo->query("SELECT * FROM categories WHERE type = 'gallery' ORDER BY name")->fetchAll();

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
    $category_id = (int)$_POST['category_id'];
    $content_type = clean_input($_POST['content_type']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $sort_order = (int)$_POST['sort_order'];
    
    // Validasyon
    $errors = [];
    
    if (empty($title)) {
        $errors[] = 'İçerik başlığı gereklidir.';
    }
    
    if ($category_id <= 0) {
        $errors[] = 'Kategori seçimi gereklidir.';
    }
    
    if (empty($content_type)) {
        $errors[] = 'İçerik türü seçimi gereklidir.';
    }
    
    // İçerik türüne göre işlem
    if ($content_type === 'photo') {
        // Fotoğraf yükleme
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Fotoğraf yükleme gereklidir.';
        } else {
            $upload_result = upload_file($_FILES['image'], UPLOAD_PATH);
            if ($upload_result) {
                $image = $upload_result;
            } else {
                $errors[] = 'Fotoğraf yüklenirken bir hata oluştu.';
            }
        }
    } elseif ($content_type === 'video') {
        // Video URL kontrolü
        $video_url = clean_input($_POST['video_url']);
        if (empty($video_url)) {
            $errors[] = 'Video URL gereklidir.';
        } else {
            $image = '';
            // Thumbnail yükleme (opsiyonel)
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $upload_result = upload_file($_FILES['thumbnail'], UPLOAD_PATH);
                if ($upload_result) {
                    $image = $upload_result;
                }
            }
        }
    }
    
    // Hata yoksa kaydet
    if (empty($errors)) {
        if ($content_type === 'photo') {
            $stmt = $pdo->prepare("INSERT INTO gallery_photos (title, description, image, category_id, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([$title, $description, $image, $category_id, $is_active, $sort_order]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO gallery_videos (title, description, video_url, thumbnail, category_id, is_active, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $result = $stmt->execute([$title, $description, $video_url, $image, $category_id, $is_active, $sort_order]);
        }
        
        if ($result) {
            log_activity('gallery_added', "Yeni galeri içeriği eklendi: $title");
            show_message('Galeri içeriği başarıyla eklendi.', 'success');
            redirect('index.php');
        } else {
            show_message('Galeri içeriği eklenirken bir hata oluştu.', 'error');
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
    <title>Galeri İçeriği Ekle - BonusBoss Admin</title>
    
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
        
        .content-type-selector {
            display: flex;
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .type-option {
            flex: 1;
            padding: 1rem;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .type-option:hover {
            border-color: var(--primary-color);
        }
        
        .type-option.selected {
            border-color: var(--primary-color);
            background: rgba(255, 215, 0, 0.1);
        }
        
        .type-option i {
            font-size: 2rem;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
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
                        <h1 class="h3 mb-0">Yeni Galeri İçeriği Ekle</h1>
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
                            <h5 class="mb-0">İçerik Bilgileri</h5>
                        </div>
                        <div class="card-body">
                            <form method="POST" class="needs-validation" enctype="multipart/form-data" novalidate>
                                <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">
                                
                                <!-- İçerik Türü Seçimi -->
                                <div class="mb-4">
                                    <label class="form-label">İçerik Türü *</label>
                                    <div class="content-type-selector">
                                        <div class="type-option" data-type="photo" onclick="selectContentType('photo')">
                                            <i class="fas fa-image"></i>
                                            <div><strong>Fotoğraf</strong></div>
                                            <small>Resim dosyası yükle</small>
                                        </div>
                                        <div class="type-option" data-type="video" onclick="selectContentType('video')">
                                            <i class="fas fa-video"></i>
                                            <div><strong>Video</strong></div>
                                            <small>Video URL ekle</small>
                                        </div>
                                    </div>
                                    <input type="hidden" name="content_type" id="content_type" value="<?php echo isset($_POST['content_type']) ? htmlspecialchars($_POST['content_type']) : ''; ?>" required>
                                    <div class="invalid-feedback">
                                        İçerik türü seçimi gereklidir.
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="mb-3">
                                            <label for="title" class="form-label">İçerik Başlığı *</label>
                                            <input type="text" class="form-control" id="title" name="title" value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>" required>
                                            <div class="invalid-feedback">
                                                İçerik başlığı gereklidir.
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
                                    <label for="description" class="form-label">Açıklama</label>
                                    <textarea class="form-control" id="description" name="description" rows="3"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                                </div>
                                
                                <!-- Fotoğraf Yükleme Alanı -->
                                <div id="photo-section" class="content-section" style="display: none;">
                                    <div class="mb-3">
                                        <label for="image" class="form-label">Fotoğraf *</label>
                                        <input type="file" class="form-control" id="image" name="image" accept="image/*" onchange="previewImage(this)">
                                        <div class="form-text">
                                            Önerilen boyut: 800x600px, Maksimum: 5MB
                                        </div>
                                        <div id="image-preview"></div>
                                    </div>
                                </div>
                                
                                <!-- Video URL Alanı -->
                                <div id="video-section" class="content-section" style="display: none;">
                                    <div class="mb-3">
                                        <label for="video_url" class="form-label">Video URL *</label>
                                        <input type="url" class="form-control" id="video_url" name="video_url" value="<?php echo isset($_POST['video_url']) ? htmlspecialchars($_POST['video_url']) : ''; ?>" placeholder="https://www.youtube.com/watch?v=...">
                                        <div class="form-text">
                                            YouTube, Vimeo veya diğer video platformlarından URL ekleyin.
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="thumbnail" class="form-label">Video Thumbnail (Opsiyonel)</label>
                                        <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*" onchange="previewThumbnail(this)">
                                        <div class="form-text">
                                            Video için özel thumbnail ekleyebilirsiniz.
                                        </div>
                                        <div id="thumbnail-preview"></div>
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
                                                Aktif içerikler sitede görünür.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="index.php" class="btn btn-secondary">
                                        <i class="fas fa-times me-2"></i>İptal
                                    </a>
                                    <button type="submit" class="btn btn-admin">
                                        <i class="fas fa-save me-2"></i>İçeriği Kaydet
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
        // İçerik türü seçimi
        function selectContentType(type) {
            // Tüm seçenekleri temizle
            document.querySelectorAll('.type-option').forEach(option => {
                option.classList.remove('selected');
            });
            
            // Seçilen seçeneği işaretle
            document.querySelector(`[data-type="${type}"]`).classList.add('selected');
            
            // Hidden input'u güncelle
            document.getElementById('content_type').value = type;
            
            // İlgili bölümleri göster/gizle
            if (type === 'photo') {
                document.getElementById('photo-section').style.display = 'block';
                document.getElementById('video-section').style.display = 'none';
                document.getElementById('image').required = true;
                document.getElementById('video_url').required = false;
            } else if (type === 'video') {
                document.getElementById('photo-section').style.display = 'none';
                document.getElementById('video-section').style.display = 'block';
                document.getElementById('image').required = false;
                document.getElementById('video_url').required = true;
            }
        }
        
        // Sayfa yüklendiğinde mevcut seçimi göster
        document.addEventListener('DOMContentLoaded', function() {
            const currentType = document.getElementById('content_type').value;
            if (currentType) {
                selectContentType(currentType);
            }
        });
        
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
        
        // Thumbnail önizleme
        function previewThumbnail(input) {
            const preview = document.getElementById('thumbnail-preview');
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" class="image-preview" alt="Thumbnail Önizleme">`;
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