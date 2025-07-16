<?php
/**
 * Manage Blog Categories
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 */

require_once '../../includes/functions.php';
requireLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? '';

try {
    createBlogTables(); // Tabloları kontrol et
    
    switch ($action) {
        case 'add_category':
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if (empty($name)) {
                throw new Exception('Kategori adı gerekli');
            }
            
            // Slug oluştur
            $slug = createBlogSlug($name);
            
            // Aynı isimde kategori var mı kontrol et
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_categories WHERE name = ?");
            $stmt->execute([$name]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Bu isimde bir kategori zaten mevcut');
            }
            
            // Kategori ekle
            $stmt = $pdo->prepare("INSERT INTO blog_categories (name, slug, description) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $slug, $description])) {
                echo json_encode(['success' => true, 'message' => 'Kategori başarıyla eklendi']);
            } else {
                throw new Exception('Kategori eklenirken hata oluştu');
            }
            break;
            
        case 'edit_category':
            $id = (int)($_POST['id'] ?? 0);
            $name = trim($_POST['name'] ?? '');
            $description = trim($_POST['description'] ?? '');
            
            if ($id <= 0 || empty($name)) {
                throw new Exception('Geçersiz kategori ID veya kategori adı');
            }
            
            // Slug güncelle
            $slug = createBlogSlug($name);
            
            // Aynı isimde başka kategori var mı kontrol et (kendi hariç)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_categories WHERE name = ? AND id != ?");
            $stmt->execute([$name, $id]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Bu isimde bir kategori zaten mevcut');
            }
            
            // Kategori güncelle
            $stmt = $pdo->prepare("UPDATE blog_categories SET name = ?, slug = ?, description = ? WHERE id = ?");
            if ($stmt->execute([$name, $slug, $description, $id])) {
                echo json_encode(['success' => true, 'message' => 'Kategori başarıyla güncellendi']);
            } else {
                throw new Exception('Kategori güncellenirken hata oluştu');
            }
            break;
            
        case 'delete_category':
            $id = (int)($_POST['id'] ?? 0);
            
            if ($id <= 0) {
                throw new Exception('Geçersiz kategori ID');
            }
            
            // Bu kategoride yazı var mı kontrol et
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE category_id = ?");
            $stmt->execute([$id]);
            $post_count = $stmt->fetchColumn();
            
            if ($post_count > 0) {
                throw new Exception("Bu kategori silinemez çünkü {$post_count} adet blog yazısı bu kategoride bulunuyor");
            }
            
            // Kategori sil
            $stmt = $pdo->prepare("DELETE FROM blog_categories WHERE id = ?");
            if ($stmt->execute([$id])) {
                echo json_encode(['success' => true, 'message' => 'Kategori başarıyla silindi']);
            } else {
                throw new Exception('Kategori silinirken hata oluştu');
            }
            break;
            
        default:
            throw new Exception('Geçersiz işlem');
    }
    
} catch(Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}