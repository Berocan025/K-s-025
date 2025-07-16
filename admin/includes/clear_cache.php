<?php
/**
 * Clear Cache - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 */

header('Content-Type: application/json');
require_once '../../includes/functions.php';

// Admin authentication check
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit;
}

try {
    $cleared_files = 0;
    $cleared_folders = 0;
    $error_count = 0;
    
    // Cache dizinleri
    $cache_directories = [
        '../../cache/',
        '../../assets/cache/',
        '../../tmp/',
        '../../storage/cache/'
    ];
    
    foreach ($cache_directories as $dir) {
        if (is_dir($dir)) {
            $files = glob($dir . '*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    if (unlink($file)) {
                        $cleared_files++;
                    } else {
                        $error_count++;
                    }
                } elseif (is_dir($file) && basename($file) !== '.' && basename($file) !== '..') {
                    if (rmdir($file)) {
                        $cleared_folders++;
                    } else {
                        $error_count++;
                    }
                }
            }
        }
    }
    
    // Browser cache headers temizliği için
    if (function_exists('opcache_reset')) {
        opcache_reset();
    }
    
    // Session cache temizle
    if (session_status() === PHP_SESSION_ACTIVE) {
        session_regenerate_id(true);
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Cache başarıyla temizlendi!',
        'stats' => [
            'cleared_files' => $cleared_files,
            'cleared_folders' => $cleared_folders,
            'errors' => $error_count
        ]
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Cache temizlenirken hata oluştu: ' . $e->getMessage()
    ]);
}
?>