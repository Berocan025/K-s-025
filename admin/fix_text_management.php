<?php
/**
 * Metin Yönetimi Düzeltme
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * 
 * Bu sayfa mevcut settings verilerini site_texts tablosuna aktarır
 */

require_once '../includes/functions.php';
requireLogin();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fix_texts'])) {
    try {
        // Önce mevcut settings tablosundaki verileri al
        $existing_settings = $pdo->query("SELECT * FROM settings")->fetchAll();
        
        if (empty($existing_settings)) {
            $error_message = 'Settings tablosunda veri bulunamadı!';
        } else {
            // Mevcut site_texts verilerini temizle
            $pdo->exec("DELETE FROM site_texts");
            
            // Her setting için site_texts'e ekle - Developer: BERAT K
            foreach ($existing_settings as $setting) {
                $setting_key = $setting['setting_key'];
                $setting_value = $setting['setting_value'];
                
                // Kategori belirle
                $category_id = 1; // Default: homepage
                $page_location = 'Ana Sayfa';
                $text_type = 'text';
                
                if (strpos($setting_key, 'hero_') === 0) {
                    $category_id = 1; // homepage
                    $page_location = 'Ana Sayfa Hero';
                    if ($setting_key == 'hero_description') {
                        $text_type = 'textarea';
                    }
                } elseif (strpos($setting_key, 'about_') === 0) {
                    $category_id = 2; // about
                    $page_location = 'Hakkımda Sayfası';
                } elseif (strpos($setting_key, 'contact_') === 0) {
                    $category_id = 5; // contact
                    $page_location = 'İletişim Sayfası';
                    if (strpos($setting_key, 'email') !== false) {
                        $text_type = 'email';
                    }
                } elseif (strpos($setting_key, 'footer_') === 0) {
                    $category_id = 8; // footer
                    $page_location = 'Footer';
                } elseif (strpos($setting_key, 'site_') === 0) {
                    $category_id = 7; // general
                    $page_location = 'Genel Ayarlar';
                } elseif (strpos($setting_key, 'stat_') === 0) {
                    $category_id = 1; // homepage
                    $page_location = 'Ana Sayfa İstatistikler';
                } elseif (strpos($setting_key, 'blog_') === 0) {
                    $category_id = 6; // blog
                    $page_location = 'Blog Sayfası';
                }
                
                // İnsan dostu etiket oluştur
                $text_label = ucfirst(str_replace(['_', 'hero', 'site', 'stat', 'about', 'contact', 'footer', 'blog'], 
                                                  [' ', 'Ana', 'Site', 'İstatistik', 'Hakkımda', 'İletişim', 'Footer', 'Blog'], 
                                                  $setting_key));
                
                // Özel etiketler
                $special_labels = [
                    'hero_title' => 'Ana Başlık',
                    'hero_subtitle' => 'Alt Başlık', 
                    'hero_description' => 'Ana Açıklama',
                    'hero_greeting' => 'Karşılama Metni',
                    'site_brand' => 'Site Markası',
                    'site_title' => 'Site Başlığı',
                    'site_description' => 'Site Açıklaması',
                    'stat_projects' => 'Proje Sayısı',
                    'stat_clients' => 'Müşteri Sayısı',
                    'stat_years' => 'Deneyim Yılı',
                    'stat_awards' => 'Ödül Sayısı',
                    'contact_email' => 'İletişim E-postası',
                    'footer_description' => 'Footer Açıklama',
                    'about_image' => 'Hakkımda Profil Resmi',
                    'blog_enabled' => 'Blog Aktif Mi'
                ];
                
                if (isset($special_labels[$setting_key])) {
                    $text_label = $special_labels[$setting_key];
                }
                
                // Zorunlu alanları belirle
                $is_required = in_array($setting_key, ['site_title', 'hero_title', 'hero_subtitle']) ? 1 : 0;
                
                // Max length belirle
                $max_length = null;
                if ($text_type == 'text' && !in_array($setting_key, ['hero_description', 'footer_description', 'site_description'])) {
                    $max_length = 255;
                }
                
                $stmt = $pdo->prepare("INSERT INTO site_texts (category_id, text_key, text_label, text_value, text_type, page_location, is_required, max_length, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))");
                $stmt->execute([
                    $category_id,
                    $setting_key,
                    $text_label,
                    $setting_value,
                    $text_type,
                    $page_location,
                    $is_required,
                    $max_length
                ]);
            }
            
            $success_message = count($existing_settings) . ' adet mevcut ayar başarıyla metin yönetimine aktarıldı! (Developer: BERAT K)';
        }
        
    } catch(PDOException $e) {
        $error_message = 'Hata: ' . $e->getMessage();
    }
}

// Mevcut durumu kontrol et
try {
    $settings_count = $pdo->query("SELECT COUNT(*) FROM settings")->fetchColumn();
    $texts_count = $pdo->query("SELECT COUNT(*) FROM site_texts")->fetchColumn();
    
    $sample_settings = $pdo->query("SELECT * FROM settings LIMIT 10")->fetchAll();
    $sample_texts = $pdo->query("SELECT * FROM site_texts LIMIT 10")->fetchAll();
} catch(PDOException $e) {
    $settings_count = 0;
    $texts_count = 0;
    $sample_settings = [];
    $sample_texts = [];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 Metin Yönetimi Düzeltme - BERAT K</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: white;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .container {
            max-width: 1100px;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
        }
        .btn-fix {
            background: linear-gradient(45deg, #27ae60, #2ecc71);
            border: none;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1><i class="fas fa-tools"></i> Metin Yönetimi Düzeltme</h1>
            <p class="lead">Mevcut ayarlarınız metin yönetimi sistemine aktarılıyor</p>
            <small>Developer: BERAT K</small>
        </div>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <div class="row">
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5><i class="fas fa-database text-info me-2"></i>Mevcut Settings Tablosu</h5>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-info"><?php echo $settings_count; ?> Ayar</span>
                        </div>
                        
                        <?php if (!empty($sample_settings)): ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-sm">
                                    <thead>
                                        <tr>
                                            <th>Anahtar</th>
                                            <th>Değer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sample_settings as $setting): ?>
                                        <tr>
                                            <td><small><?php echo htmlspecialchars($setting['setting_key']); ?></small></td>
                                            <td><small><?php echo htmlspecialchars(substr($setting['setting_value'], 0, 30)); ?><?php echo strlen($setting['setting_value']) > 30 ? '...' : ''; ?></small></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($settings_count > 10): ?>
                                <small class="text-muted">ve <?php echo $settings_count - 10; ?> tane daha...</small>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Settings tablosunda veri bulunamadı!
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5><i class="fas fa-font text-success me-2"></i>Site Texts Tablosu</h5>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-success"><?php echo $texts_count; ?> Metin</span>
                        </div>
                        
                        <?php if (!empty($sample_texts)): ?>
                            <div class="table-responsive">
                                <table class="table table-dark table-sm">
                                    <thead>
                                        <tr>
                                            <th>Etiket</th>
                                            <th>Değer</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sample_texts as $text): ?>
                                        <tr>
                                            <td><small><?php echo htmlspecialchars($text['text_label']); ?></small></td>
                                            <td><small><?php echo htmlspecialchars(substr($text['text_value'], 0, 30)); ?><?php echo strlen($text['text_value']) > 30 ? '...' : ''; ?></small></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php if ($texts_count > 10): ?>
                                <small class="text-muted">ve <?php echo $texts_count - 10; ?> tane daha...</small>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Site texts tablosunda henüz veri yok!
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5>🚀 Çözüm</h5>
                <p>Mevcut <strong>settings</strong> tablosundaki verileriniz <strong>site_texts</strong> tablosuna aktarılacak:</p>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6><i class="fas fa-crown text-warning me-2"></i>Ana Sayfa Metinleri</h6>
                        <small>hero_title, hero_subtitle, hero_description, stat_* vs.</small>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-cog text-info me-2"></i>Genel Ayarlar</h6>
                        <small>site_title, site_brand, site_description vs.</small>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="fas fa-envelope text-primary me-2"></i>İletişim Bilgileri</h6>
                        <small>contact_email ve diğer iletişim ayarları</small>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-link text-secondary me-2"></i>Footer Metinleri</h6>
                        <small>footer_description ve alt bilgi metinleri</small>
                    </div>
                </div>

                <form method="POST">
                    <button type="submit" name="fix_texts" class="btn btn-fix btn-lg" <?php echo $settings_count == 0 ? 'disabled' : ''; ?>>
                        <i class="fas fa-magic me-2"></i>Metinleri Aktiv Et (<?php echo $settings_count; ?> ayar)
                    </button>
                </form>
                
                <?php if ($settings_count == 0): ?>
                    <small class="text-muted d-block mt-2">
                        <i class="fas fa-info-circle me-1"></i>
                        Settings tablosunda veri olmadığı için aktarım yapılamaz.
                    </small>
                <?php endif; ?>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="text-management.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i>Metin Yönetimi'ne Dön
            </a>
            <a href="dashboard.php" class="btn btn-outline-light ms-2">
                <i class="fas fa-home me-2"></i>Dashboard
            </a>
        </div>
    </div>
</body>
</html>