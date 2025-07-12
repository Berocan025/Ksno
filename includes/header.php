<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Header dosyası
 */

require_once 'config.php';

// Sayfa başlığı ve meta bilgileri
$page_title = isset($page_title) ? $page_title : '';
$page_description = isset($page_description) ? $page_description : '';
$page_keywords = isset($page_keywords) ? $page_keywords : '';
$page_image = isset($page_image) ? $page_image : '';

// Meta tag'leri oluştur
$meta_tags = generate_meta_tags($page_title, $page_description, $page_keywords, $page_image);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <?php echo $meta_tags; ?>
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/assets/images/favicon.ico">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Lightbox CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    
    <!-- Custom CSS Variables -->
    <style>
        :root {
            --primary-color: <?php echo get_setting('primary_color', '#FFD700'); ?>;
            --secondary-color: <?php echo get_setting('secondary_color', '#0099FF'); ?>;
            --dark-color: <?php echo get_setting('dark_color', '#003366'); ?>;
            --light-color: #f8f9fa;
            --text-color: #333;
            --text-muted: #6c757d;
            --border-color: #dee2e6;
            --shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --shadow-lg: 0 1rem 3rem rgba(0, 0, 0, 0.175);
            --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            --gradient-dark: linear-gradient(135deg, var(--dark-color), #001a33);
        }
    </style>
</head>
<body>
    <!-- Loading Spinner -->
    <div id="loading-spinner" class="loading-spinner">
        <div class="spinner-border text-warning" role="status">
            <span class="visually-hidden">Yükleniyor...</span>
        </div>
    </div>

    <!-- Header -->
    <header class="header fixed-top">
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="<?php echo SITE_URL; ?>">
                    <div class="logo">
                        <span class="logo-text">
                            <span class="logo-bonus">Bonus</span>
                            <span class="logo-boss">Boss</span>
                        </span>
                        <i class="fas fa-crown logo-icon"></i>
                    </div>
                </a>

                <!-- Mobile Toggle -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Navigation Menu -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active_menu('index'); ?>" href="<?php echo SITE_URL; ?>">
                                <i class="fas fa-home"></i> <?php echo get_site_text('nav_home', 'Ana Sayfa'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active_menu('about'); ?>" href="<?php echo SITE_URL; ?>/about.php">
                                <i class="fas fa-user"></i> <?php echo get_site_text('nav_about', 'Hakkımda'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active_menu('services'); ?>" href="<?php echo SITE_URL; ?>/services.php">
                                <i class="fas fa-cogs"></i> <?php echo get_site_text('nav_services', 'Hizmetler'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active_menu('portfolio'); ?>" href="<?php echo SITE_URL; ?>/portfolio.php">
                                <i class="fas fa-briefcase"></i> <?php echo get_site_text('nav_portfolio', 'Portföy'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active_menu('gallery'); ?>" href="<?php echo SITE_URL; ?>/gallery.php">
                                <i class="fas fa-images"></i> <?php echo get_site_text('nav_gallery', 'Galeri'); ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo is_active_menu('contact'); ?>" href="<?php echo SITE_URL; ?>/contact.php">
                                <i class="fas fa-envelope"></i> <?php echo get_site_text('nav_contact', 'İletişim'); ?>
                            </a>
                        </li>
                        <?php if (is_admin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="<?php echo SITE_URL; ?>/admin/">
                                <i class="fas fa-cog"></i> <?php echo get_site_text('nav_admin', 'Admin Panel'); ?>
                            </a>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="main-content">
        <?php
        // Mesaj göster
        $message = get_message();
        if ($message): ?>
        <div class="alert alert-<?php echo $message['type']; ?> alert-dismissible fade show" role="alert">
            <?php echo $message['text']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>