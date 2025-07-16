<?php
/**
 * Performans Test Sistemi
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 */

require_once '../../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

try {
    $start_time = microtime(true);
    $start_memory = memory_get_usage();
    
    // Test sonuçları
    $results = [
        'page_load_time' => 0,
        'db_query_time' => 0,
        'memory_usage' => 0,
        'cache_status' => 'Aktif değil',
        'recommendations' => []
    ];
    
    // 1. Database Performans Testi
    $db_start = microtime(true);
    global $pdo;
    
    // Basit sorgu testi
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM projects");
    $stmt->execute();
    $project_count = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM services");
    $stmt->execute();
    $service_count = $stmt->fetchColumn();
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors");
    $stmt->execute();
    $visitor_count = $stmt->fetchColumn();
    
    $db_end = microtime(true);
    $results['db_query_time'] = round(($db_end - $db_start) * 1000, 2);
    
    // 2. Cache Durumu Kontrolü
    $cache_enabled = getSetting('cache_enabled', '0');
    if ($cache_enabled === '1') {
        $results['cache_status'] = 'Aktif';
    } else {
        $results['recommendations'][] = 'Cache sistemini aktifleştirin';
    }
    
    // 3. Dosya Sistemi Testi
    $file_test_start = microtime(true);
    $test_file = __DIR__ . '/../../tmp/performance_test.tmp';
    
    // Test dizini oluştur
    $tmp_dir = dirname($test_file);
    if (!is_dir($tmp_dir)) {
        mkdir($tmp_dir, 0755, true);
    }
    
    // Dosya yazma testi
    $test_data = str_repeat('Performance Test Data ', 1000); // ~20KB
    file_put_contents($test_file, $test_data);
    
    // Dosya okuma testi
    $read_data = file_get_contents($test_file);
    
    // Test dosyasını sil
    if (file_exists($test_file)) {
        unlink($test_file);
    }
    
    $file_test_end = microtime(true);
    $file_io_time = round(($file_test_end - $file_test_start) * 1000, 2);
    
    // 4. Görsel Optimizasyon Kontrolü
    $image_compression = getSetting('image_compression', '0');
    if ($image_compression !== '1') {
        $results['recommendations'][] = 'Görsel sıkıştırmayı aktifleştirin';
    }
    
    // 5. CSS/JS Minifikasyon Kontrolü
    $css_minify = getSetting('css_minify', '0');
    $js_minify = getSetting('js_minify', '0');
    
    if ($css_minify !== '1') {
        $results['recommendations'][] = 'CSS minifikasyonunu aktifleştirin';
    }
    
    if ($js_minify !== '1') {
        $results['recommendations'][] = 'JS minifikasyonunu aktifleştirin';
    }
    
    // 6. Database Boyut Kontrolü
    $db_file = __DIR__ . '/../../database/portfolio.db';
    $db_size_mb = 0;
    if (file_exists($db_file)) {
        $db_size_mb = round(filesize($db_file) / 1024 / 1024, 2);
    }
    
    if ($db_size_mb > 50) {
        $results['recommendations'][] = 'Veritabanı boyutu büyük, optimizasyon yapın';
    }
    
    // 7. PHP Konfigürasyon Kontrolü
    $memory_limit = ini_get('memory_limit');
    $max_execution_time = ini_get('max_execution_time');
    $upload_max_filesize = ini_get('upload_max_filesize');
    
    // 8. Sunucu Performans Metrikleri
    $server_load = null;
    if (function_exists('sys_getloadavg')) {
        $load = sys_getloadavg();
        $server_load = round($load[0], 2);
    }
    
    // 9. Disk Alanı Kontrolü
    $free_space = disk_free_space(__DIR__);
    $total_space = disk_total_space(__DIR__);
    $disk_usage_percent = 100 - (($free_space / $total_space) * 100);
    
    if ($disk_usage_percent > 80) {
        $results['recommendations'][] = 'Disk alanı %80\'in üzerinde, temizlik yapın';
    }
    
    // 10. Genel Performans Değerlendirmesi
    if ($results['db_query_time'] > 100) {
        $results['recommendations'][] = 'Database sorgu süresi yüksek, optimize edin';
    }
    
    if ($file_io_time > 50) {
        $results['recommendations'][] = 'Dosya I/O performansı düşük';
    }
    
    // Toplam süre hesaplama
    $end_time = microtime(true);
    $end_memory = memory_get_usage();
    
    $results['page_load_time'] = round(($end_time - $start_time) * 1000, 2);
    $results['memory_usage'] = round(($end_memory - $start_memory) / 1024 / 1024, 2);
    
    // Performans skoru hesaplama
    $performance_score = 100;
    
    if ($results['db_query_time'] > 50) $performance_score -= 10;
    if ($results['page_load_time'] > 200) $performance_score -= 15;
    if ($cache_enabled !== '1') $performance_score -= 20;
    if ($image_compression !== '1') $performance_score -= 10;
    if ($css_minify !== '1') $performance_score -= 5;
    if ($js_minify !== '1') $performance_score -= 5;
    
    $performance_score = max(0, $performance_score);
    
    // Öneriler listesi
    if (empty($results['recommendations'])) {
        $results['recommendations'][] = 'Performans optimum seviyede';
    }
    
    // Detaylı rapor
    $detailed_report = [
        'database_size' => $db_size_mb . ' MB',
        'file_io_time' => $file_io_time . ' ms',
        'memory_limit' => $memory_limit,
        'max_execution_time' => $max_execution_time . ' saniye',
        'upload_max_filesize' => $upload_max_filesize,
        'server_load' => $server_load ?? 'Bilinmiyor',
        'disk_usage' => round($disk_usage_percent, 1) . '%',
        'performance_score' => $performance_score . '/100',
        'total_projects' => $project_count,
        'total_services' => $service_count,
        'total_visitors' => $visitor_count
    ];
    
    echo json_encode([
        'success' => true,
        'message' => 'Performans testi tamamlandı',
        'page_load_time' => $results['page_load_time'],
        'db_query_time' => $results['db_query_time'],
        'memory_usage' => $results['memory_usage'],
        'cache_status' => $results['cache_status'],
        'recommendations' => $results['recommendations'],
        'performance_score' => $performance_score,
        'detailed_report' => $detailed_report
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Performans testi sırasında hata: ' . $e->getMessage()
    ]);
}
?>