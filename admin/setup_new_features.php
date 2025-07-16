<?php
/**
 * Yeni Özellikler Kurulum Sistemi
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * 
 * Bu dosya şu yeni özellikleri ekler:
 * 1. Admin kullanıcı yönetimi ve yetki sistemi
 * 2. İçerik bölümlerinin sıralama sistemi  
 * 3. Kapsamlı site metin yönetimi
 */

require_once '../config/database.php';
require_once '../includes/functions.php';

session_start();

// Admin kontrolü
if (!isLoggedIn()) {
    die("Bu sayfaya erişim için admin girişi gerekli!");
}

$success_messages = [];
$error_messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['install_features'])) {
    
    try {
        // 1. Admin Yetki Sistemi Tabloları - Developer: BERAT K
        echo "<h3>🔐 Admin Yetki Sistemi Kuruluyor...</h3>";
        
        // Admin roller tablosu
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_roles (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            role_name VARCHAR(100) NOT NULL UNIQUE,
            role_display_name VARCHAR(150) NOT NULL,
            description TEXT,
            permissions TEXT,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Mevcut admin_users tablosuna role_id ekle
        $pdo->exec("ALTER TABLE admin_users ADD COLUMN role_id INTEGER DEFAULT 1");
        $pdo->exec("ALTER TABLE admin_users ADD COLUMN is_super_admin INTEGER DEFAULT 0");
        $pdo->exec("ALTER TABLE admin_users ADD COLUMN last_login DATETIME");
        $pdo->exec("ALTER TABLE admin_users ADD COLUMN status INTEGER DEFAULT 1");
        
        echo "<p>✅ Admin yetki tabloları oluşturuldu</p>";
        
        // 2. İçerik Sıralama Sistemi Tabloları - Developer: BERAT K  
        echo "<h3>🔄 İçerik Sıralama Sistemi Kuruluyor...</h3>";
        
        // İçerik bölümleri tablosu
        $pdo->exec("CREATE TABLE IF NOT EXISTS content_sections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            section_key VARCHAR(100) NOT NULL UNIQUE,
            section_name VARCHAR(200) NOT NULL,
            section_title VARCHAR(250),
            section_description TEXT,
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            is_visible INTEGER DEFAULT 1,
            section_settings TEXT,
            template_file VARCHAR(200),
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        echo "<p>✅ İçerik sıralama tabloları oluşturuldu</p>";
        
        // 3. Site Metin Yönetimi Tabloları - Developer: BERAT K
        echo "<h3>📝 Site Metin Yönetimi Kuruluyor...</h3>";
        
        // Metin kategorileri tablosu
        $pdo->exec("CREATE TABLE IF NOT EXISTS text_categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_key VARCHAR(100) NOT NULL UNIQUE,
            category_name VARCHAR(200) NOT NULL,
            category_description TEXT,
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Site metinleri tablosu
        $pdo->exec("CREATE TABLE IF NOT EXISTS site_texts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER,
            text_key VARCHAR(200) NOT NULL UNIQUE,
            text_label VARCHAR(250) NOT NULL,
            text_value TEXT,
            default_value TEXT,
            text_type VARCHAR(50) DEFAULT 'text',
            page_location VARCHAR(200),
            admin_notes TEXT,
            is_required INTEGER DEFAULT 0,
            is_html INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            max_length INTEGER,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES text_categories(id)
        )");
        
        echo "<p>✅ Site metin yönetimi tabloları oluşturuldu</p>";
        
        // 4. Varsayılan Verileri Ekle - Developer: BERAT K
        echo "<h3>🎯 Varsayılan Veriler Ekleniyor...</h3>";
        
        // Varsayılan admin rolleri
        $default_roles = [
            ['super_admin', 'Süper Admin', 'Tüm yetkiler', 'all'],
            ['admin', 'Admin', 'Genel admin yetkileri', 'content_manage,text_manage,user_view'],
            ['editor', 'Editör', 'İçerik düzenleme yetkileri', 'content_manage,text_manage'],
            ['moderator', 'Moderatör', 'Sınırlı düzenleme yetkileri', 'text_manage']
        ];
        
        foreach ($default_roles as $role) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO admin_roles (role_name, role_display_name, description, permissions) VALUES (?, ?, ?, ?)");
            $stmt->execute($role);
        }
        
        // Mevcut admin kullanıcıyı süper admin yap
        $pdo->exec("UPDATE admin_users SET role_id = 1, is_super_admin = 1 WHERE id = 1");
        
        echo "<p>✅ Varsayılan admin rolleri oluşturuldu</p>";
        
        // Varsayılan içerik bölümleri - Developer: BERAT K
        $default_sections = [
            ['platform_services', 'Platform Hizmetleri', 'Platform Hizmetlerim', 'Platform hizmetleri bölümü', 1],
            ['platforms', 'Platformlar', 'Platformlarım', 'Mevcut platformlar bölümü', 2],
            ['premium_products', 'Premium Ürünler', 'Premium Ürünlerim', 'Premium ürünler bölümü', 3],
            ['testimonials', 'Müşteri Yorumları', 'Müşteri Deneyimleri', 'Müşteri yorumları bölümü', 4],
            ['contact_info', 'İletişim', 'Bize Ulaşın', 'İletişim bilgileri bölümü', 5]
        ];
        
        foreach ($default_sections as $section) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO content_sections (section_key, section_name, section_title, section_description, sort_order) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute($section);
        }
        
        echo "<p>✅ Varsayılan içerik bölümleri oluşturuldu</p>";
        
        // Varsayılan metin kategorileri - Developer: BERAT K
        $default_text_categories = [
            ['homepage', 'Ana Sayfa', 'Ana sayfa metinleri', 1],
            ['about', 'Hakkımda', 'Hakkımda sayfası metinleri', 2],
            ['services', 'Hizmetler', 'Hizmetler sayfası metinleri', 3],
            ['portfolio', 'Portföy', 'Portföy sayfası metinleri', 4],
            ['contact', 'İletişim', 'İletişim sayfası metinleri', 5],
            ['blog', 'Blog', 'Blog sayfası metinleri', 6],
            ['general', 'Genel', 'Genel site metinleri', 7],
            ['footer', 'Footer', 'Alt bilgi metinleri', 8],
            ['navigation', 'Navigasyon', 'Menü ve navigasyon metinleri', 9]
        ];
        
        foreach ($default_text_categories as $category) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO text_categories (category_key, category_name, category_description, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->execute($category);
        }
        
        echo "<p>✅ Varsayılan metin kategorileri oluşturuldu</p>";
        
        // Varsayılan site metinleri - Developer: BERAT K
        $default_texts = [
            [1, 'hero_title', 'Ana Başlık', 'BERAT K', 'text', 'Ana Sayfa Hero'],
            [1, 'hero_subtitle', 'Alt Başlık', 'R10', 'text', 'Ana Sayfa Hero'],
            [1, 'hero_description', 'Hero Açıklama', 'Profesyonel yazılım geliştirici olarak modern web uygulamaları, mobil çözümler ve yaratıcı dijital deneyimler tasarlıyorum.', 'textarea', 'Ana Sayfa Hero'],
            [1, 'about_section_title', 'Hakkımda Bölüm Başlığı', 'Hakkımda', 'text', 'Ana Sayfa'],
            [1, 'services_section_title', 'Hizmetler Bölüm Başlığı', 'Hizmetlerim', 'text', 'Ana Sayfa'],
            [1, 'portfolio_section_title', 'Portföy Bölüm Başlığı', 'Projelerim', 'text', 'Ana Sayfa'],
            [1, 'contact_section_title', 'İletişim Bölüm Başlığı', 'İletişime Geçin', 'text', 'Ana Sayfa'],
            [8, 'footer_copyright', 'Copyright Metni', '© 2024 BERAT K - R10. Tüm hakları saklıdır. | Developer: BERAT K', 'text', 'Footer'],
            [8, 'footer_description', 'Footer Açıklama', 'Profesyonel yazılım geliştirme hizmetleri ve yaratıcı çözümler.', 'textarea', 'Footer'],
            [9, 'nav_home', 'Ana Sayfa Menü', 'Ana Sayfa', 'text', 'Navigasyon'],
            [9, 'nav_about', 'Hakkımda Menü', 'Hakkımda', 'text', 'Navigasyon'],
            [9, 'nav_services', 'Hizmetler Menü', 'Hizmetler', 'text', 'Navigasyon'],
            [9, 'nav_portfolio', 'Portföy Menü', 'Portföy', 'text', 'Navigasyon'],
            [9, 'nav_blog', 'Blog Menü', 'Blog', 'text', 'Navigasyon'],
            [9, 'nav_contact', 'İletişim Menü', 'İletişim', 'text', 'Navigasyon']
        ];
        
        foreach ($default_texts as $text) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO site_texts (category_id, text_key, text_label, text_value, text_type, page_location) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute($text);
        }
        
        echo "<p>✅ Varsayılan site metinleri oluşturuldu</p>";
        
        echo "<div style='background: #28a745; color: white; padding: 20px; border-radius: 10px; margin: 20px 0;'>";
        echo "<h2>🎉 Kurulum Tamamlandı!</h2>";
        echo "<p><strong>Developer: BERAT K</strong> tarafından geliştirilen yeni özellikler başarıyla kuruldu:</p>";
        echo "<ul>";
        echo "<li>✅ Admin kullanıcı yönetimi ve yetki sistemi</li>";
        echo "<li>✅ İçerik bölümlerinin sıralama sistemi</li>";
        echo "<li>✅ Kapsamlı site metin yönetimi</li>";
        echo "</ul>";
        echo "<p>Artık admin panelinizdeki yeni menülerden bu özellikleri kullanabilirsiniz!</p>";
        echo "</div>";
        
        $success_messages[] = "Tüm yeni özellikler başarıyla kuruldu!";
        
    } catch(PDOException $e) {
        $error_messages[] = "Veritabanı hatası: " . $e->getMessage();
        echo "<div style='background: #dc3545; color: white; padding: 20px; border-radius: 10px;'>";
        echo "<h3>❌ Hata Oluştu</h3>";
        echo "<p>" . $e->getMessage() . "</p>";
        echo "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 Yeni Özellikler Kurulumu - BERAT K</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .install-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            padding: 40px;
            margin: 50px 0;
        }
        .btn-install {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 50px;
            padding: 15px 40px;
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="install-container">
            <div class="text-center mb-4">
                <h1><i class="fas fa-rocket"></i> Yeni Özellikler Kurulumu</h1>
                <p class="lead">Developer: BERAT K tarafından geliştirilen yeni özellikler</p>
            </div>
            
            <?php if (empty($success_messages) && empty($error_messages)): ?>
            <form method="POST">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card bg-transparent border-light h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-users fa-3x mb-3 text-info"></i>
                                <h5>Admin Yönetimi</h5>
                                <p>Yeni admin kullanıcılar ekleyin ve yetkilerini belirleyin</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-transparent border-light h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-sort fa-3x mb-3 text-warning"></i>
                                <h5>İçerik Sıralama</h5>
                                <p>Anasayfa bölümlerinin sırasını düzenleyin</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card bg-transparent border-light h-100">
                            <div class="card-body text-center">
                                <i class="fas fa-edit fa-3x mb-3 text-success"></i>
                                <h5>Metin Yönetimi</h5>
                                <p>Tüm site metinlerini admin panelinden düzenleyin</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <button type="submit" name="install_features" class="btn btn-install btn-lg">
                        <i class="fas fa-download me-2"></i>Yeni Özellikleri Kur
                    </button>
                </div>
            </form>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="dashboard.php" class="btn btn-outline-light">
                    <i class="fas fa-arrow-left me-2"></i>Dashboard'a Dön
                </a>
            </div>
        </div>
    </div>
</body>
</html>