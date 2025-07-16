<?php
/**
 * Veritabanı Optimizasyon Sistemi
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 */

require_once '../../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

try {
    global $pdo;
    
    $optimized_tables = 0;
    $total_space_saved = 0;
    $operations = [];
    
    // SQLite için optimizasyon işlemleri
    $operations[] = "VACUUM işlemi başlatılıyor...";
    
    // VACUUM komutu - SQLite veritabanını defragmente eder
    $stmt = $pdo->prepare("VACUUM");
    $stmt->execute();
    $optimized_tables++;
    $operations[] = "✅ VACUUM işlemi tamamlandı";
    
    // ANALYZE komutu - İstatistikleri günceller
    $stmt = $pdo->prepare("ANALYZE");
    $stmt->execute();
    $operations[] = "✅ ANALYZE işlemi tamamlandı";
    
    // Eski ziyaretçi kayıtlarını temizle (30 günden eski)
    $stmt = $pdo->prepare("DELETE FROM visitors WHERE visit_date < DATE('now', '-30 days')");
    $deleted_visitors = $stmt->execute();
    $deleted_count = $stmt->rowCount();
    if ($deleted_count > 0) {
        $operations[] = "✅ {$deleted_count} eski ziyaretçi kaydı silindi";
    }
    
    // Eski login attemptları temizle (7 günden eski)
    $stmt = $pdo->prepare("DELETE FROM login_attempts WHERE attempt_time < DATE('now', '-7 days')");
    $deleted_attempts = $stmt->execute();
    $deleted_attempts_count = $stmt->rowCount();
    if ($deleted_attempts_count > 0) {
        $operations[] = "✅ {$deleted_attempts_count} eski login denemesi silindi";
    }
    
    // Eski page_views kayıtlarını temizle (60 günden eski)
    $stmt = $pdo->prepare("DELETE FROM page_views WHERE view_time < DATE('now', '-60 days')");
    $deleted_views = $stmt->execute();
    $deleted_views_count = $stmt->rowCount();
    if ($deleted_views_count > 0) {
        $operations[] = "✅ {$deleted_views_count} eski sayfa görüntüleme kaydı silindi";
    }
    
    // Veritabanı boyutunu hesapla
    $db_file = __DIR__ . '/../../database/portfolio.db';
    $db_size = 0;
    if (file_exists($db_file)) {
        $db_size = round(filesize($db_file) / 1024 / 1024, 2); // MB
    }
    
    $operations[] = "📊 Mevcut veritabanı boyutu: {$db_size} MB";
    
    // Cache klasörünü temizle
    $cache_dir = __DIR__ . '/../../cache/';
    $deleted_cache_files = 0;
    if (is_dir($cache_dir)) {
        $files = glob($cache_dir . '*');
        foreach ($files as $file) {
            if (is_file($file) && (time() - filemtime($file)) > 3600) { // 1 saatten eski
                unlink($file);
                $deleted_cache_files++;
            }
        }
        if ($deleted_cache_files > 0) {
            $operations[] = "✅ {$deleted_cache_files} eski cache dosyası silindi";
        }
    }
    
    // Optimizasyon başarılı
    echo json_encode([
        'success' => true,
        'message' => 'Veritabanı optimizasyonu tamamlandı',
        'details' => [
            'optimized_tables' => $optimized_tables,
            'database_size' => $db_size . ' MB',
            'deleted_visitors' => $deleted_count ?? 0,
            'deleted_attempts' => $deleted_attempts_count ?? 0,
            'deleted_views' => $deleted_views_count ?? 0,
            'deleted_cache_files' => $deleted_cache_files,
            'operations' => $operations
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Optimizasyon sırasında hata: ' . $e->getMessage()
    ]);
}
?>