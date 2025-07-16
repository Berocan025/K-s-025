<?php
/**
 * Get Blog Categories
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 */

require_once '../../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

try {
    createBlogTables(); // Tabloları kontrol et
    
    $stmt = $pdo->prepare("SELECT * FROM blog_categories ORDER BY name ASC");
    $stmt->execute();
    $categories = $stmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'categories' => $categories
    ]);
    
} catch(PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Kategoriler yüklenirken hata oluştu: ' . $e->getMessage()
    ]);
}