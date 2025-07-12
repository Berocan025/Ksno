<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Mesaj Yönetimi
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

// Mesaj işlemleri
if (isset($_POST['action'])) {
    $action = clean_input($_POST['action']);
    $message_id = (int)$_POST['message_id'];
    
    if ($action === 'mark_read') {
        $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        if ($stmt->execute([$message_id])) {
            show_message('Mesaj okundu olarak işaretlendi.', 'success');
        }
    } elseif ($action === 'delete') {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        if ($stmt->execute([$message_id])) {
            show_message('Mesaj başarıyla silindi.', 'success');
        }
    }
    
    redirect('index.php');
}

// Filtreleme
$filter = isset($_GET['filter']) ? clean_input($_GET['filter']) : 'all';

// Sayfalama
$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
$per_page = 20;
$offset = ($page - 1) * $per_page;

// Mesajları getir
$where_clause = '';
$params = [];

if ($filter === 'unread') {
    $where_clause = 'WHERE is_read = 0';
} elseif ($filter === 'read') {
    $where_clause = 'WHERE is_read = 1';
}

// Toplam kayıt sayısı
$count_sql = "SELECT COUNT(*) as total FROM contact_messages $where_clause";
$stmt = $pdo->prepare($count_sql);
$stmt->execute($params);
$total_records = $stmt->fetch()['total'];

$total_pages = ceil($total_records / $per_page);

// Mesajları getir
$sql = "SELECT * FROM contact_messages $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$messages = $stmt->fetchAll();

// Mesaj göster
$message = get_message();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mesaj Yönetimi - BonusBoss Admin</title>
    
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
        
        .message-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }
        
        .message-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        }
        
        .message-card.unread {
            border-left: 4px solid var(--primary-color);
            background: #f8f9fa;
        }
        
        .message-header {
            display: flex;
            justify-content: between;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .message-info {
            flex: 1;
        }
        
        .message-actions {
            display: flex;
            gap: 0.5rem;
        }
        
        .message-subject {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 0.5rem;
        }
        
        .message-meta {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .message-content {
            color: #333;
            line-height: 1.6;
        }
        
        .filter-buttons {
            margin-bottom: 2rem;
        }
        
        .filter-btn {
            background: white;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 25px;
            margin-right: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .filter-btn:hover,
        .filter-btn.active {
            background: var(--primary-color);
            color: white;
        }
        
        .stats-card {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 15px;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            font-size: 1rem;
            opacity: 0.9;
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
                        <a class="nav-link active" href="index.php">
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
                        <h1 class="h3 mb-0">Mesaj Yönetimi</h1>
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

                    <!-- İstatistikler -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-number"><?php echo $total_records; ?></div>
                                <div class="stats-label">Toplam Mesaj</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-number">
                                    <?php 
                                    $stmt = $pdo->query("SELECT COUNT(*) as unread FROM contact_messages WHERE is_read = 0");
                                    echo $stmt->fetch()['unread'];
                                    ?>
                                </div>
                                <div class="stats-label">Okunmamış Mesaj</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card">
                                <div class="stats-number">
                                    <?php 
                                    $stmt = $pdo->query("SELECT COUNT(*) as read FROM contact_messages WHERE is_read = 1");
                                    echo $stmt->fetch()['read'];
                                    ?>
                                </div>
                                <div class="stats-label">Okunmuş Mesaj</div>
                            </div>
                        </div>
                    </div>

                    <!-- Filtreler -->
                    <div class="filter-buttons">
                        <a href="?filter=all" class="btn filter-btn <?php echo $filter === 'all' ? 'active' : ''; ?>">
                            <i class="fas fa-list me-2"></i>Tümü
                        </a>
                        <a href="?filter=unread" class="btn filter-btn <?php echo $filter === 'unread' ? 'active' : ''; ?>">
                            <i class="fas fa-envelope me-2"></i>Okunmamış
                        </a>
                        <a href="?filter=read" class="btn filter-btn <?php echo $filter === 'read' ? 'active' : ''; ?>">
                            <i class="fas fa-envelope-open me-2"></i>Okunmuş
                        </a>
                    </div>

                    <!-- Mesajlar -->
                    <?php if (empty($messages)): ?>
                    <div class="card">
                        <div class="card-body text-center">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5>Henüz mesaj bulunmuyor</h5>
                            <p class="text-muted">İletişim formundan gelen mesajlar burada görünecek.</p>
                        </div>
                    </div>
                    <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                    <div class="message-card <?php echo $msg['is_read'] ? '' : 'unread'; ?>">
                        <div class="message-header">
                            <div class="message-info">
                                <div class="message-subject"><?php echo htmlspecialchars($msg['subject']); ?></div>
                                <div class="message-meta">
                                    <strong><?php echo htmlspecialchars($msg['name']); ?></strong> 
                                    (<?php echo htmlspecialchars($msg['email']); ?>) - 
                                    <?php echo format_date($msg['created_at'], 'd.m.Y H:i'); ?>
                                    <?php if ($msg['ip_address']): ?>
                                    - IP: <?php echo $msg['ip_address']; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="message-actions">
                                <?php if (!$msg['is_read']): ?>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="action" value="mark_read">
                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                    <button type="submit" class="btn btn-success btn-sm" title="Okundu olarak işaretle">
                                        <i class="fas fa-check"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                                <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#messageModal<?php echo $msg['id']; ?>" title="Mesajı Görüntüle">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <form method="POST" style="display: inline;" onsubmit="return confirm('Bu mesajı silmek istediğinizden emin misiniz?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm" title="Mesajı Sil">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                        <div class="message-content">
                            <?php echo nl2br(htmlspecialchars(substr($msg['message'], 0, 200))); ?>
                            <?php if (strlen($msg['message']) > 200): ?>
                            <span class="text-muted">...</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Mesaj Detay Modal -->
                    <div class="modal fade" id="messageModal<?php echo $msg['id']; ?>" tabindex="-1" aria-labelledby="messageModalLabel<?php echo $msg['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="messageModalLabel<?php echo $msg['id']; ?>"><?php echo htmlspecialchars($msg['subject']); ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Gönderen:</strong> <?php echo htmlspecialchars($msg['name']); ?></p>
                                            <p><strong>E-posta:</strong> <?php echo htmlspecialchars($msg['email']); ?></p>
                                            <p><strong>Tarih:</strong> <?php echo format_date($msg['created_at'], 'd.m.Y H:i'); ?></p>
                                            <?php if ($msg['ip_address']): ?>
                                            <p><strong>IP Adresi:</strong> <?php echo $msg['ip_address']; ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Konu:</strong> <?php echo htmlspecialchars($msg['subject']); ?></p>
                                            <p><strong>Durum:</strong> 
                                                <?php if ($msg['is_read']): ?>
                                                <span class="badge bg-success">Okundu</span>
                                                <?php else: ?>
                                                <span class="badge bg-warning">Okunmadı</span>
                                                <?php endif; ?>
                                            </p>
                                        </div>
                                    </div>
                                    <hr>
                                    <h6>Mesaj:</h6>
                                    <div class="message-full-content">
                                        <?php echo nl2br(htmlspecialchars($msg['message'])); ?>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <?php if (!$msg['is_read']): ?>
                                    <form method="POST" style="display: inline;">
                                        <input type="hidden" name="action" value="mark_read">
                                        <input type="hidden" name="message_id" value="<?php echo $msg['id']; ?>">
                                        <button type="submit" class="btn btn-success">
                                            <i class="fas fa-check me-2"></i>Okundu Olarak İşaretle
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kapat</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                    <?php endif; ?>

                    <!-- Sayfalama -->
                    <?php if ($total_pages > 1): ?>
                    <nav aria-label="Mesaj sayfaları">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                            <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                <a class="page-link" href="?filter=<?php echo $filter; ?>&p=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>