<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Galeri Ekleme
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

$message = '';

// Kategorileri getir
$categories = [];
try {
    $stmt = $pdo->query("SELECT * FROM categories WHERE is_active = 1 ORDER BY name ASC");
    $categories = $stmt->fetchAll();
} catch (Exception $e) {
    $message = 'Kategoriler yüklenirken hata oluştu: ' . $e->getMessage();
}

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title']);
    $description = clean_input($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $media_type = clean_input($_POST['media_type']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    
    // Dosya yükleme
    $file_path = '';
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/uploads/gallery/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
        $new_filename = 'gallery_' . time() . '.' . $file_extension;
        $upload_path = $upload_dir . $new_filename;
        
        $allowed_types = ($media_type === 'video') ? ALLOWED_VIDEO_TYPES : ALLOWED_IMAGE_TYPES;
        
        if (in_array($file_extension, $allowed_types) && move_uploaded_file($_FILES['file']['tmp_name'], $upload_path)) {
            $file_path = 'assets/uploads/gallery/' . $new_filename;
        }
    }
    
    try {
        if ($media_type === 'video') {
            $stmt = $pdo->prepare("INSERT INTO gallery_videos (title, description, category_id, video_path, is_active, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$title, $description, $category_id, $file_path, $is_active]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO gallery_photos (title, description, category_id, image_path, is_active, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$title, $description, $category_id, $file_path, $is_active]);
        }
        
        log_activity('gallery_added', "Yeni galeri öğesi eklendi: $title");
        redirect('index.php');
    } catch (Exception $e) {
        $message = 'Galeri öğesi eklenirken hata oluştu: ' . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Ekle - BonusBoss Admin</title>
    
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
        
        .form-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="admin-header">
        <div class="container">
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
                    <div class="dropdown">
                        <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user me-2"></i><?php echo $_SESSION['username']; ?>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../dashboard.php"><i class="fas fa-tachometer-alt me-2"></i>Dashboard</a></li>
                            <li><a class="dropdown-item" href="../settings.php"><i class="fas fa-cog me-2"></i>Ayarlar</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="../logout.php"><i class="fas fa-sign-out-alt me-2"></i>Çıkış</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav class="col-md-3 col-lg-2 d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="../dashboard.php">
                                <i class="fas fa-tachometer-alt"></i>
                                Dashboard
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../services/index.php">
                                <i class="fas fa-cogs"></i>
                                Hizmetler
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../portfolio/index.php">
                                <i class="fas fa-briefcase"></i>
                                Portföy
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="index.php">
                                <i class="fas fa-images"></i>
                                Galeri
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../messages/index.php">
                                <i class="fas fa-envelope"></i>
                                Mesajlar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../texts/index.php">
                                <i class="fas fa-file-alt"></i>
                                Site Metinleri
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../settings.php">
                                <i class="fas fa-cog"></i>
                                Ayarlar
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Yeni Galeri Öğesi Ekle</h1>
                    <div class="btn-toolbar mb-2 mb-md-0">
                        <a href="index.php" class="btn btn-admin">
                            <i class="fas fa-arrow-left me-2"></i>Geri Dön
                        </a>
                    </div>
                </div>

                <?php if ($message): ?>
                <div class="alert alert-danger">
                    <?php echo $message; ?>
                </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="fas fa-plus me-2"></i>Galeri Bilgileri</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Başlık *</label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="description" class="form-label">Açıklama</label>
                                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="media_type" class="form-label">Medya Türü *</label>
                                                <select class="form-select" id="media_type" name="media_type" required>
                                                    <option value="">Tür Seçin</option>
                                                    <option value="image">Resim</option>
                                                    <option value="video">Video</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label for="category_id" class="form-label">Kategori</label>
                                                <select class="form-select" id="category_id" name="category_id">
                                                    <option value="">Kategori Seçin</option>
                                                    <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>"><?php echo $category['name']; ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Dosya *</label>
                                        <input type="file" class="form-control" id="file" name="file" accept="image/*,video/*" required>
                                        <small class="text-muted">
                                            <span id="fileInfo">Resim: JPG, PNG, GIF, WEBP | Video: MP4, AVI, MOV, WMV</span>
                                        </small>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                            <label class="form-check-label" for="is_active">
                                                Aktif
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info">
                                        <h6><i class="fas fa-info-circle me-2"></i>Dosya Bilgileri</h6>
                                        <ul class="mb-0">
                                            <li>Maksimum dosya boyutu: 5MB</li>
                                            <li>Resim boyutu: 1920x1080px önerilir</li>
                                            <li>Video süresi: 5 dakika önerilir</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="text-end">
                                <button type="submit" class="btn btn-admin">
                                    <i class="fas fa-save me-2"></i>Galeri Öğesini Kaydet
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Medya türü değiştiğinde dosya kabul türünü güncelle
        document.getElementById('media_type').addEventListener('change', function() {
            const fileInput = document.getElementById('file');
            const fileInfo = document.getElementById('fileInfo');
            
            if (this.value === 'image') {
                fileInput.accept = 'image/*';
                fileInfo.textContent = 'Resim: JPG, PNG, GIF, WEBP';
            } else if (this.value === 'video') {
                fileInput.accept = 'video/*';
                fileInfo.textContent = 'Video: MP4, AVI, MOV, WMV';
            } else {
                fileInput.accept = 'image/*,video/*';
                fileInfo.textContent = 'Resim: JPG, PNG, GIF, WEBP | Video: MP4, AVI, MOV, WMV';
            }
        });
    </script>
</body>
</html>