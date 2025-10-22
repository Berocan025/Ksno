<?php
/**
 * BonusBoss Casino Yayıncısı Portföy Sitesi
 * Yazılımcı: BERAT K
 * 
 * Admin Panel - Logout
 */

require_once '../includes/config.php';

// Aktivite logunu kaydet
if (is_logged_in()) {
    log_activity('logout', 'Admin panelinden çıkış yapıldı');
}

// Session'ı temizle
session_destroy();

// Login sayfasına yönlendir
redirect('login.php');
?>