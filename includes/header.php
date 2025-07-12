<?php
require_once __DIR__ . '/../config.php';

// Sayfa başlığı ve meta bilgileri
$pageTitle = $pageTitle ?? getSetting('site_title', 'BonusBoss');
$pageDescription = $pageDescription ?? getSetting('site_description', 'Kazançlı ortaklıklar için doğru adres');
$pageKeywords = $pageKeywords ?? getSetting('site_keywords', 'casino, yayıncı, bonus, boss, profesyonel');
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <meta name="description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta name="keywords" content="<?= htmlspecialchars($pageKeywords) ?>">
    
    <!-- Favicon -->
    <?php $favicon = getSetting('site_favicon'); ?>
    <?php if ($favicon): ?>
        <link rel="icon" type="image/x-icon" href="<?= UPLOAD_PATH . $favicon ?>">
    <?php endif; ?>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?= generateCanonicalURL($_SERVER['REQUEST_URI'] ?? '') ?>">
    <?php $logo = getSetting('site_logo'); ?>
    <?php if ($logo): ?>
        <meta property="og:image" content="<?= SITE_URL . '/' . UPLOAD_PATH . $logo ?>">
    <?php endif; ?>
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= htmlspecialchars($pageTitle) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($pageDescription) ?>">
    <?php if ($logo): ?>
        <meta name="twitter:image" content="<?= SITE_URL . '/' . UPLOAD_PATH . $logo ?>">
    <?php endif; ?>
    
    <!-- Canonical URL -->
    <link rel="canonical" href="<?= generateCanonicalURL($_SERVER['REQUEST_URI'] ?? '') ?>">
    
    <!-- CSS Dosyaları -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/responsive.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- AOS Animation -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Custom CSS Variables -->
    <style>
        :root {
            --primary-color: <?= getSetting('primary_color', '#FFD700') ?>;
            --secondary-color: <?= getSetting('secondary_color', '#0099FF') ?>;
            --dark-color: <?= getSetting('dark_color', '#003366') ?>;
            --text-color: #ffffff;
            --text-muted: #b0b0b0;
            --bg-dark: #0a0a0a;
            --bg-darker: #050505;
            --border-color: #333;
            --gradient-primary: linear-gradient(135deg, var(--primary-color), #FFA500);
            --gradient-secondary: linear-gradient(135deg, var(--secondary-color), #0066CC);
            --gradient-dark: linear-gradient(135deg, var(--dark-color), #001a33);
            --shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            --shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body>
    <!-- Loading Spinner -->
    <div id="loading-spinner">
        <div class="spinner">
            <div class="spinner-inner"></div>
        </div>
    </div>

    <!-- Header -->
    <header class="header">
        <nav class="navbar">
            <div class="container">
                <div class="navbar-brand">
                    <a href="index.php" class="logo">
                        <?php $logo = getSetting('site_logo'); ?>
                        <?php if ($logo): ?>
                            <img src="<?= UPLOAD_PATH . $logo ?>" alt="<?= SITE_NAME ?>" class="logo-img">
                        <?php else: ?>
                            <span class="logo-text"><?= SITE_NAME ?></span>
                        <?php endif; ?>
                    </a>
                </div>
                
                <div class="navbar-menu" id="navbar-menu">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a href="index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>">
                                <?= getSiteText('nav_home', 'Ana Sayfa') ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="about.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : '' ?>">
                                <?= getSiteText('nav_about', 'Hakkımda') ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="services.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : '' ?>">
                                <?= getSiteText('nav_services', 'Hizmetler') ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="portfolio.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'portfolio.php' ? 'active' : '' ?>">
                                <?= getSiteText('nav_portfolio', 'Portföy') ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="gallery.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : '' ?>">
                                <?= getSiteText('nav_gallery', 'Galeri') ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="contact.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : '' ?>">
                                <?= getSiteText('nav_contact', 'İletişim') ?>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="navbar-actions">
                    <a href="contact.php" class="btn btn-primary">
                        <i class="fas fa-phone"></i>
                        <?= getSiteText('btn_contact', 'İletişime Geç') ?>
                    </a>
                    
                    <button class="navbar-toggler" id="navbar-toggler">
                        <span></span>
                        <span></span>
                        <span></span>
                    </button>
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