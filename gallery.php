<?php
require_once 'config.php';

// Galeri fotoğraflarını al
try {
    $stmt = $pdo->prepare("SELECT * FROM gallery_photos WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC");
    $stmt->execute();
    $photos = $stmt->fetchAll();
} catch (Exception $e) {
    $photos = [];
}

// Galeri videolarını al
try {
    $stmt = $pdo->prepare("SELECT * FROM gallery_videos WHERE is_active = 1 ORDER BY sort_order ASC, created_at DESC");
    $stmt->execute();
    $videos = $stmt->fetchAll();
} catch (Exception $e) {
    $videos = [];
}

$page_title = 'Galeri';
include 'header.php';
?>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-content">
        <h1>Galeri</h1>
        <h2>Çalışmalarımızdan Örnekler</h2>
        <p>Casino yayıncılığında uzmanlaşmış ekibimizin en iyi anlarından seçmeler</p>
    </div>
</section>

<!-- Gallery Section -->
<section class="section gallery">
    <div class="container">
        <div class="section-title">
            <h2>Fotoğraf Galerisi</h2>
            <p>Canlı yayınlarımızdan en güzel kareler</p>
        </div>

        <?php if (!empty($photos)): ?>
        <div class="gallery-grid">
            <?php foreach ($photos as $photo): ?>
            <div class="gallery-item" onclick="openLightbox('<?= htmlspecialchars($photo['image_path']) ?>', '<?= htmlspecialchars($photo['title'] ?? '') ?>')">
                <img src="<?= htmlspecialchars($photo['image_path']) ?>" 
                     alt="<?= htmlspecialchars($photo['alt_text'] ?? $photo['title'] ?? 'Galeri Fotoğrafı') ?>" 
                     class="gallery-image">
                <div class="gallery-overlay">
                    <i class="fas fa-search-plus"></i>
                </div>
                <?php if ($photo['title']): ?>
                <div class="gallery-caption">
                    <h4><?= htmlspecialchars($photo['title']) ?></h4>
                    <?php if ($photo['description']): ?>
                    <p><?= htmlspecialchars($photo['description']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center">
            <i class="fas fa-images fa-3x text-muted mb-3"></i>
            <h5>Henüz fotoğraf bulunmuyor</h5>
            <p class="text-muted">Yakında güzel fotoğraflarımızı görebileceksiniz.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Video Gallery Section -->
<section class="section gallery" style="background: var(--bg-darker);">
    <div class="container">
        <div class="section-title">
            <h2>Video Galerisi</h2>
            <p>En iyi casino yayın anlarımızdan videolar</p>
        </div>

        <?php if (!empty($videos)): ?>
        <div class="gallery-grid">
            <?php foreach ($videos as $video): ?>
            <div class="gallery-item" onclick="openVideoModal('<?= htmlspecialchars($video['video_url']) ?>', '<?= htmlspecialchars($video['title'] ?? '') ?>')">
                <?php if ($video['thumbnail_path']): ?>
                <img src="<?= htmlspecialchars($video['thumbnail_path']) ?>" 
                     alt="<?= htmlspecialchars($video['title'] ?? 'Video') ?>" 
                     class="gallery-image">
                <?php else: ?>
                <div class="gallery-image bg-dark d-flex align-items-center justify-content-center">
                    <i class="fas fa-play fa-3x text-primary"></i>
                </div>
                <?php endif; ?>
                <div class="gallery-overlay">
                    <i class="fas fa-play"></i>
                </div>
                <?php if ($video['title']): ?>
                <div class="gallery-caption">
                    <h4><?= htmlspecialchars($video['title']) ?></h4>
                    <?php if ($video['description']): ?>
                    <p><?= htmlspecialchars($video['description']) ?></p>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php else: ?>
        <div class="text-center">
            <i class="fas fa-video fa-3x text-muted mb-3"></i>
            <h5>Henüz video bulunmuyor</h5>
            <p class="text-muted">Yakında harika videolarımızı görebileceksiniz.</p>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Lightbox Modal -->
<div class="modal fade" id="lightboxModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lightboxTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="lightboxImage" src="" alt="" style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</div>

<!-- Video Modal -->
<div class="modal fade" id="videoModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="videoTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="ratio ratio-16x9">
                    <iframe id="videoIframe" src="" frameborder="0" allowfullscreen></iframe>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function openLightbox(imageSrc, title) {
    document.getElementById('lightboxImage').src = imageSrc;
    document.getElementById('lightboxTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('lightboxModal')).show();
}

function openVideoModal(videoUrl, title) {
    // YouTube URL'sini embed URL'sine çevir
    let embedUrl = videoUrl;
    if (videoUrl.includes('youtube.com/watch?v=')) {
        const videoId = videoUrl.split('v=')[1];
        embedUrl = `https://www.youtube.com/embed/${videoId}`;
    } else if (videoUrl.includes('youtu.be/')) {
        const videoId = videoUrl.split('youtu.be/')[1];
        embedUrl = `https://www.youtube.com/embed/${videoId}`;
    }
    
    document.getElementById('videoIframe').src = embedUrl;
    document.getElementById('videoTitle').textContent = title;
    new bootstrap.Modal(document.getElementById('videoModal')).show();
}

// Modal kapandığında video'yu durdur
document.getElementById('videoModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('videoIframe').src = '';
});
</script>

<?php include 'footer.php'; ?>