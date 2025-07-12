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
$media_type = isset($_GET['type']) ? clean_input($_GET['type']) : 'photo';

// Kategorileri getir
try {
    $stmt = $pdo->query("SELECT * FROM categories WHERE type = 'gallery' AND is_active = 1 ORDER BY sort_order ASC");
    $categories = $stmt->fetchAll();
} catch (Exception $e) {
    $message = 'Kategoriler yüklenirken hata oluştu: ' . $e->getMessage();
}

// Form gönderildi mi?
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = clean_input($_POST['title']);
    $description = clean_input($_POST['description']);
    $category_id = (int)$_POST['category_id'];
    $sort_order = (int)$_POST['sort_order'];
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $media_type = clean_input($_POST['media_type']);
    
    try {
        if ($media_type === 'photo') {
            // Fotoğraf yükleme
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../assets/uploads/gallery/photos/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $new_filename = 'photo_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (in_array($file_extension, ALLOWED_IMAGE_TYPES) && move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $image_path = 'assets/uploads/gallery/photos/' . $new_filename;
                    
                    $stmt = $pdo->prepare("INSERT INTO gallery_photos (title, description, image_path, category_id, is_active, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())");
                    if ($stmt->execute([$title, $description, $image_path, $category_id, $is_active, $sort_order])) {
                        log_activity('gallery_photo_created', "Yeni galeri fotoğrafı eklendi: $title");
                        redirect('index.php');
                    }
                } else {
                    $message = 'Fotoğraf yüklenirken hata oluştu.';
                }
            } else {
                $message = 'Lütfen bir fotoğraf seçin.';
            }
        } else {
            // Video ekleme
            $video_path = clean_input($_POST['video_path']);
            $thumbnail = null;
            
            if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = '../../assets/uploads/gallery/thumbnails/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
                $new_filename = 'thumbnail_' . time() . '.' . $file_extension;
                $upload_path = $upload_dir . $new_filename;
                
                if (in_array($file_extension, ALLOWED_IMAGE_TYPES) && move_uploaded_file($_FILES['thumbnail']['tmp_name'], $upload_path)) {
                    $thumbnail = 'assets/uploads/gallery/thumbnails/' . $new_filename;
                }
            }
            
            $stmt = $pdo->prepare("INSERT INTO gallery_videos (title, description, video_path, thumbnail, category_id, is_active, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");
            if ($stmt->execute([$title, $description, $video_path, $thumbnail, $category_id, $is_active, $sort_order])) {
                log_activity('gallery_video_created', "Yeni galeri videosu eklendi: $title");
                redirect('index.php');
            }
        }
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
        
        .media-type-tabs {
            margin-bottom: 2rem;
        }
        
        .media-type-tabs .nav-link {
            border-radius: 10px;
            margin-right: 0.5rem;
            font-weight: 500;
        }
        
        .media-type-tabs .nav-link.active {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
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

                <!-- Medya Tipi Seçimi -->
                <div class="media-type-tabs">
                    <ul class="nav nav-tabs" id="mediaTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo $media_type === 'photo' ? 'active' : ''; ?>" id="photo-tab" data-bs-toggle="tab" data-bs-target="#photo" type="button" role="tab">
                                <i class="fas fa-image me-2"></i>Fotoğraf
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?php echo $media_type === 'video' ? 'active' : ''; ?>" id="video-tab" data-bs-toggle="tab" data-bs-target="#video" type="button" role="tab">
                                <i class="fas fa-video me-2"></i>Video
                            </button>
                        </li>
                    </ul>
                </div>

                <div class="tab-content" id="mediaTypeTabContent">
                    <!-- Fotoğraf Ekleme -->
                    <div class="tab-pane fade <?php echo $media_type === 'photo' ? 'show active' : ''; ?>" id="photo" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-image me-2"></i>Fotoğraf Ekle</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="media_type" value="photo">
                                    
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
                                            
                                            <div class="mb-3">
                                                <label for="image" class="form-label">Fotoğraf *</label>
                                                <input type="file" class="form-control" id="image" name="image" accept="image/*" required>
                                                <small class="text-muted">Önerilen boyut: 1200x800px</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="category_id" class="form-label">Kategori</label>
                                                <select class="form-select" id="category_id" name="category_id">
                                                    <option value="">Kategori Seçin</option>
                                                    <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="sort_order" class="form-label">Sıralama</label>
                                                <input type="number" class="form-control" id="sort_order" name="sort_order" value="0" min="0">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                                    <label class="form-check-label" for="is_active">
                                                        Aktif
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-admin">
                                            <i class="fas fa-save me-2"></i>Fotoğrafı Kaydet
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Video Ekleme -->
                    <div class="tab-pane fade <?php echo $media_type === 'video' ? 'show active' : ''; ?>" id="video" role="tabpanel">
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-video me-2"></i>Video Ekle</h5>
                            </div>
                            <div class="card-body">
                                <form method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="media_type" value="video">
                                    
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="mb-3">
                                                <label for="video_title" class="form-label">Başlık *</label>
                                                <input type="text" class="form-control" id="video_title" name="title" required>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="video_description" class="form-label">Açıklama</label>
                                                <textarea class="form-control" id="video_description" name="description" rows="3"></textarea>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="video_path" class="form-label">Video URL *</label>
                                                <input type="url" class="form-control" id="video_path" name="video_path" placeholder="https://www.youtube.com/watch?v=..." required>
                                                <small class="text-muted">YouTube, Vimeo veya diğer video platformlarından URL</small>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="mb-3">
                                                <label for="video_category_id" class="form-label">Kategori</label>
                                                <select class="form-select" id="video_category_id" name="category_id">
                                                    <option value="">Kategori Seçin</option>
                                                    <?php foreach ($categories as $category): ?>
                                                    <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="thumbnail" class="form-label">Önizleme Görseli</label>
                                                <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/*">
                                                <small class="text-muted">Video önizleme görseli</small>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label for="video_sort_order" class="form-label">Sıralama</label>
                                                <input type="number" class="form-control" id="video_sort_order" name="sort_order" value="0" min="0">
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="video_is_active" name="is_active" checked>
                                                    <label class="form-check-label" for="video_is_active">
                                                        Aktif
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" class="btn btn-admin">
                                            <i class="fas fa-save me-2"></i>Videoyu Kaydet
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>