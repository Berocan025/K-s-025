<?php
/**
 * İçerik Bölümleri Düzeltme
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * 
 * Bu sayfa gerçek proje içerikleriyle uyumlu bölümler oluşturur
 */

require_once '../includes/functions.php';
requireLogin();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['fix_sections'])) {
    try {
        // Önce mevcut yanlış bölümleri temizle
        $pdo->exec("DELETE FROM content_sections");
        
        // Gerçek proje bölümlerini ekle - Developer: BERAT K
        $real_sections = [
            [
                'section_key' => 'hero_section',
                'section_name' => 'Ana Banner (Hero)',
                'section_title' => 'Ana Banner Bölümü',
                'section_description' => 'Anasayfa üst banner alanı - profil resmi ve açıklama',
                'sort_order' => 1
            ],
            [
                'section_key' => 'stats_section',
                'section_name' => 'İstatistikler',
                'section_title' => 'Başarı İstatistikleri',
                'section_description' => 'Proje sayısı, müşteri sayısı, deneyim yılı gibi sayısal veriler',
                'sort_order' => 2
            ],
            [
                'section_key' => 'services_section',
                'section_name' => 'Hizmetler',
                'section_title' => 'Platform Hizmetlerim',
                'section_description' => 'Sunduğunuz hizmetlerin listelendiği bölüm',
                'sort_order' => 3
            ],
            [
                'section_key' => 'projects_section',
                'section_name' => 'Platformlar',
                'section_title' => 'Platformlarım',
                'section_description' => 'Kumar platformları ve projelerinizin gösterildiği alan',
                'sort_order' => 4
            ],
            [
                'section_key' => 'products_section',
                'section_name' => 'Premium Ürünler',
                'section_title' => 'Premium Ürünlerim',
                'section_description' => 'Premium ürün ve hizmetlerinizin tanıtıldığı bölüm',
                'sort_order' => 5
            ],
            [
                'section_key' => 'blog_section',
                'section_name' => 'Platform Haberleri',
                'section_title' => 'Platform Haberleri',
                'section_description' => 'Blog yazıları ve platform haberlerinin gösterildiği alan',
                'sort_order' => 6
            ],
            [
                'section_key' => 'why_choose_section',
                'section_name' => 'Neden Biz',
                'section_title' => 'Neden BERAT K Platformlarını Seçmelisiniz?',
                'section_description' => 'Avantajlarınızın ve öne çıkan özelliklerinizin anlatıldığı bölüm',
                'sort_order' => 7
            ],
            [
                'section_key' => 'contact_section',
                'section_name' => 'İletişim',
                'section_title' => 'İş Birliği İçin İletişim',
                'section_description' => 'İletişim formu ve iletişim bilgilerinin bulunduğu alan',
                'sort_order' => 8
            ]
        ];
        
        foreach ($real_sections as $section) {
            $stmt = $pdo->prepare("INSERT INTO content_sections (section_key, section_name, section_title, section_description, sort_order, is_active, is_visible, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, 1, datetime('now'), datetime('now'))");
            $stmt->execute([
                $section['section_key'],
                $section['section_name'], 
                $section['section_title'],
                $section['section_description'],
                $section['sort_order']
            ]);
        }
        
        $success_message = 'İçerik bölümleri başarıyla gerçek proje içerikleriyle güncellendi! (Developer: BERAT K)';
        
    } catch(PDOException $e) {
        $error_message = 'Hata: ' . $e->getMessage();
    }
}

// Mevcut bölümleri kontrol et
try {
    $current_sections = $pdo->query("SELECT * FROM content_sections ORDER BY sort_order")->fetchAll();
} catch(PDOException $e) {
    $current_sections = [];
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🔧 İçerik Bölümleri Düzeltme - BERAT K</title>
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
            max-width: 900px;
        }
        .card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
        }
        .btn-fix {
            background: linear-gradient(45deg, #e74c3c, #c0392b);
            border: none;
            color: white;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="text-center mb-4">
            <h1><i class="fas fa-tools"></i> İçerik Bölümleri Düzeltme</h1>
            <p class="lead">Gerçek proje içeriklerinizle uyumlu bölümler oluşturuluyor</p>
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

        <div class="card mb-4">
            <div class="card-body">
                <h5>🔍 Mevcut Durum</h5>
                <?php if (empty($current_sections)): ?>
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Henüz hiç içerik bölümü yok!
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-dark table-striped">
                            <thead>
                                <tr>
                                    <th>Sıra</th>
                                    <th>Bölüm Adı</th>
                                    <th>Başlık</th>
                                    <th>Durum</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($current_sections as $section): ?>
                                <tr>
                                    <td><?php echo $section['sort_order']; ?></td>
                                    <td><?php echo htmlspecialchars($section['section_name']); ?></td>
                                    <td><?php echo htmlspecialchars($section['section_title']); ?></td>
                                    <td>
                                        <?php if ($section['is_active'] && $section['is_visible']): ?>
                                            <span class="badge bg-success">Aktif</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pasif</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <div class="card-body">
                <h5>🚀 Çözüm</h5>
                <p>Aşağıdaki buton ile içerik bölümlerini gerçek projenizle uyumlu hale getirebilirsiniz:</p>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6><i class="fas fa-crown text-warning me-2"></i>Ana Banner</h6>
                        <small>Profil resmi ve açıklama bölümü</small>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-chart-bar text-success me-2"></i>İstatistikler</h6>
                        <small>Başarı sayıları (Proje, müşteri, vs.)</small>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <h6><i class="fas fa-cogs text-info me-2"></i>Platform Hizmetleri</h6>
                        <small>Sunduğunuz hizmetler</small>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-project-diagram text-primary me-2"></i>Platformlarım</h6>
                        <small>Kumar platformları ve projeler</small>
                    </div>
                </div>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h6><i class="fas fa-gem text-warning me-2"></i>Premium Ürünlerim</h6>
                        <small>Premium ürün ve hizmetler</small>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-newspaper text-secondary me-2"></i>Platform Haberleri</h6>
                        <small>Blog yazıları ve haberler</small>
                    </div>
                </div>

                <form method="POST">
                    <button type="submit" name="fix_sections" class="btn btn-fix btn-lg">
                        <i class="fas fa-magic me-2"></i>İçerik Bölümlerini Düzelt
                    </button>
                </form>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="content-ordering.php" class="btn btn-outline-light">
                <i class="fas fa-arrow-left me-2"></i>İçerik Sıralama'ya Dön
            </a>
            <a href="dashboard.php" class="btn btn-outline-light ms-2">
                <i class="fas fa-home me-2"></i>Dashboard
            </a>
        </div>
    </div>
</body>
</html>