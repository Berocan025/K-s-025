<?php
/**
 * Export Content - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 */

require_once '../../includes/functions.php';

// Admin authentication check
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Unauthorized access';
    exit;
}

try {
    // Get all data
    $export_data = [];
    
    // Site settings
    $stmt = $pdo->prepare("SELECT * FROM site_settings");
    $stmt->execute();
    $export_data['site_settings'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Blog posts
    $stmt = $pdo->prepare("SELECT * FROM blog_posts ORDER BY created_at DESC");
    $stmt->execute();
    $export_data['blog_posts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Projects
    $stmt = $pdo->prepare("SELECT * FROM projects ORDER BY created_at DESC");
    $stmt->execute();
    $export_data['projects'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Products
    $stmt = $pdo->prepare("SELECT * FROM products ORDER BY created_at DESC");
    $stmt->execute();
    $export_data['products'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Services
    $stmt = $pdo->prepare("SELECT * FROM services ORDER BY sort_order ASC");
    $stmt->execute();
    $export_data['services'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Add metadata
    $export_data['export_info'] = [
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0',
        'site_url' => $_SERVER['HTTP_HOST'],
        'exported_by' => $_SESSION['admin_username']
    ];
    
    // Create filename
    $filename = 'portfolio_export_' . date('Y-m-d_H-i-s') . '.json';
    
    // Set headers for download
    header('Content-Type: application/json');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
    
    // Output JSON
    echo json_encode($export_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'error' => true,
        'message' => 'Export failed: ' . $e->getMessage()
    ]);
}
?>