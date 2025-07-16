<?php
/**
 * 🚀 BERAT K - R10 Portfolio - TAM KURULUM SİSTEMİ
 * 
 * ❓ Bu dosya ne işe yarar?
 * - SQLite veritabanını oluşturur
 * - Gerekli tabloları kurar  
 * - 3 ÖRNEK PROJE ekler
 * - 3 ÖRNEK ÜRÜN ekler
 * - SAYAÇLARI ayarlar (Projeler: 150, Müşteri: 85, vb.)
 * - Admin hesabı oluşturur
 * - Site ayarlarını yapar
 * 
 * 🎯 Sonuç: Tamamen hazır, çalışan bir portfolio sitesi!
 */

session_start();

// Zaten kurulu mu kontrol et
if (file_exists('database/portfolio.db') && filesize('database/portfolio.db') > 1000) {
    $already_installed = true;
} else {
    $already_installed = false;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🚀 BERAT K - R10 Portfolio Kurulumu</title>
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
        
        .text-gradient {
            background: linear-gradient(45deg, #fff, #a8edea);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .btn-install {
            background: linear-gradient(45deg, #667eea, #764ba2);
            border: none;
            color: white;
            font-weight: bold;
            border-radius: 50px;
            padding: 15px 40px;
            font-size: 1.1rem;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            transition: all 0.3s ease;
        }
        
        .btn-install:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.4);
            color: white;
        }
        
        .form-control {
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 10px;
            padding: 12px 20px;
        }
        
        .feature-list {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 25px;
            margin: 20px 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        
        .feature-item i {
            color: #a8edea;
            margin-right: 15px;
            font-size: 1.2rem;
        }
        
        .alert-custom {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            color: white;
        }
        
        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border-color: rgba(40, 167, 69, 0.5);
        }
        
        .alert-danger {
            background: rgba(220, 53, 69, 0.2);
            border-color: rgba(220, 53, 69, 0.5);
        }
        
        .progress-custom {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            height: 8px;
        }
        
        .progress-bar-custom {
            background: linear-gradient(45deg, #a8edea, #fed6e3);
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="install-container">
                    <div class="text-center mb-4">
                        <h1 class="text-gradient mb-3">
                            <i class="fas fa-rocket"></i> BERAT K - R10
                        </h1>
                        <h3>Portfolio Kurulum Sihirbazı</h3>
                        <p class="lead">SQLite ile hızlı, hazır içerikli kurulum</p>
                    </div>

                    <?php if ($already_installed): ?>
                        <div class="alert alert-custom alert-success text-center">
                            <h4><i class="fas fa-check-circle"></i> SİSTEM ZATEN KURULU!</h4>
                            <p>Portfolio siteniz zaten çalışır durumda.</p>
                            <div class="row mt-4">
                                <div class="col-md-6">
                                    <a href="index.php" class="btn btn-install w-100 mb-2">
                                        <i class="fas fa-home me-2"></i>Ana Sayfa
                                    </a>
                                </div>
                                <div class="col-md-6">
                                    <a href="admin/login.php" class="btn btn-outline-light w-100 mb-2">
                                        <i class="fas fa-cog me-2"></i>Admin Panel
                                    </a>
                                </div>
                            </div>
                            <hr class="my-4">
                            <p class="mb-3"><strong>Yeniden kurmak istiyorsanız:</strong></p>
                            <form method="POST" class="d-inline">
                                <input type="hidden" name="force_reinstall" value="1">
                                <button type="submit" class="btn btn-outline-light" onclick="return confirm('Tüm veriler silinecek! Emin misiniz?')">
                                    <i class="fas fa-refresh me-2"></i>Yeniden Kur
                                </button>
                            </form>
                        </div>
                    <?php else: ?>

                    <div class="feature-list">
                        <h5><i class="fas fa-star"></i> Kurulum Sonrası Hazır Gelecekler:</h5>
                        <div class="feature-item">
                            <i class="fas fa-project-diagram"></i>
                            <span><strong>3 Örnek Proje</strong> (E-ticaret, Mobil App, Portfolio Sistemi)</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-shopping-bag"></i>
                            <span><strong>3 Örnek Ürün</strong> (CRM: 2500₺, Blog Script: 850₺, E-İmza: 1200₺)</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-chart-line"></i>
                            <span><strong>Çalışan Sayaçlar</strong> (150 Proje, 85 Müşteri, 5 Yıl, 12 Ödül)</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-tools"></i>
                            <span><strong>6 Hizmet Kategorisi</strong> (Web, Mobil, E-ticaret, SEO, vb.)</span>
                        </div>
                        <div class="feature-item">
                            <i class="fas fa-user-shield"></i>
                            <span><strong>Admin Paneli</strong> (Tüm içerikleri yönetebilirsiniz)</span>
                        </div>
                    </div>

                    <?php
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        try {
                            echo '<div class="progress-custom mb-4">
                                    <div class="progress-bar-custom" style="width: 0%" id="progressBar"></div>
                                  </div>';
                            
                            echo '<div id="installLog" class="alert alert-custom">';
                            echo '<h5><i class="fas fa-cog fa-spin"></i> Kurulum Başlatılıyor...</h5>';
                            
                            // Progress güncellemesi için JavaScript
                            echo '<script>
                                function updateProgress(percent, text) {
                                    document.getElementById("progressBar").style.width = percent + "%";
                                    document.getElementById("installLog").innerHTML += "<p>" + text + "</p>";
                                }
                            </script>';
                            
                            // Eski veritabanını sil (yeniden kurulum)
                            if (isset($_POST['force_reinstall'])) {
                                if (file_exists('database/portfolio.db')) {
                                    unlink('database/portfolio.db');
                                }
                                echo '<script>updateProgress(10, "🗑️ Eski veritabanı temizlendi");</script>';
                            }
                            
                            // 1. Veritabanı bağlantısı oluştur
                            require_once 'config/database.php';
                            echo '<script>updateProgress(20, "✅ SQLite veritabanı bağlantısı kuruldu");</script>';
                            
                            // 2. Tabloları oluştur
                            $tables_sql = file_get_contents('setup_tables.sql');
                            if (!$tables_sql) {
                                // Eğer setup_tables.sql yoksa, burada tanımla
                                $tables_sql = "
                                CREATE TABLE IF NOT EXISTS settings (
                                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                                    setting_key VARCHAR(255) NOT NULL UNIQUE,
                                    setting_value TEXT
                                );
                                
                                CREATE TABLE IF NOT EXISTS admin_users (
                                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                                    username VARCHAR(100) NOT NULL UNIQUE,
                                    password VARCHAR(255) NOT NULL,
                                    email VARCHAR(255) NOT NULL UNIQUE,
                                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                                );
                                
                                CREATE TABLE IF NOT EXISTS projects (
                                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                                    title VARCHAR(255) NOT NULL,
                                    description TEXT,
                                    image VARCHAR(500),
                                    demo_url VARCHAR(500),
                                    github_url VARCHAR(500),
                                    category VARCHAR(100),
                                    technologies TEXT,
                                    featured INTEGER DEFAULT 0,
                                    status INTEGER DEFAULT 1,
                                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                                );
                                
                                CREATE TABLE IF NOT EXISTS services (
                                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                                    title VARCHAR(255) NOT NULL,
                                    description TEXT,
                                    icon VARCHAR(100),
                                    status INTEGER DEFAULT 1,
                                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                                );
                                
                                CREATE TABLE IF NOT EXISTS products (
                                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                                    title VARCHAR(255) NOT NULL,
                                    description TEXT,
                                    features TEXT,
                                    category VARCHAR(100),
                                    price VARCHAR(100),
                                    currency VARCHAR(10),
                                    image VARCHAR(500),
                                    demo_url VARCHAR(500),
                                    admin_demo_url VARCHAR(500),
                                    download_url VARCHAR(500),
                                    documentation_url VARCHAR(500),
                                    featured INTEGER DEFAULT 0,
                                    status INTEGER DEFAULT 1,
                                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                                );
                                
                                CREATE TABLE IF NOT EXISTS contact_messages (
                                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                                    name VARCHAR(255) NOT NULL,
                                    email VARCHAR(255) NOT NULL,
                                    subject VARCHAR(255),
                                    message TEXT NOT NULL,
                                    status VARCHAR(20) DEFAULT 'new',
                                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                                );
                                
                                CREATE TABLE IF NOT EXISTS blog_categories (
                                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                                    name VARCHAR(255) NOT NULL,
                                    slug VARCHAR(255) NOT NULL UNIQUE,
                                    description TEXT,
                                    status INTEGER DEFAULT 1,
                                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                                );
                                
                                CREATE TABLE IF NOT EXISTS blog_posts (
                                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                                    title VARCHAR(255) NOT NULL,
                                    slug VARCHAR(255) NOT NULL UNIQUE,
                                    content TEXT,
                                    excerpt TEXT,
                                    featured_image VARCHAR(500),
                                    category_id INTEGER,
                                    status VARCHAR(20) DEFAULT 'draft',
                                    meta_title VARCHAR(255),
                                    meta_description TEXT,
                                    tags TEXT,
                                    views INTEGER DEFAULT 0,
                                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                                    FOREIGN KEY (category_id) REFERENCES blog_categories(id)
                                );";
                            }
                            
                            $pdo->exec($tables_sql);
                            echo '<script>updateProgress(40, "✅ Tüm veritabanı tabloları oluşturuldu");</script>';
                            
                            // 3. Admin kullanıcı oluştur
                            $admin_username = $_POST['admin_username'] ?? 'admin';
                            $admin_password = password_hash($_POST['admin_password'] ?? 'admin123', PASSWORD_DEFAULT);
                            $admin_email = $_POST['admin_email'] ?? 'admin@beratk.com';
                            
                            $stmt = $pdo->prepare("INSERT OR REPLACE INTO admin_users (username, password, email) VALUES (?, ?, ?)");
                            $stmt->execute([$admin_username, $admin_password, $admin_email]);
                            echo '<script>updateProgress(50, "✅ Admin kullanıcısı oluşturuldu: ' . $admin_username . '");</script>';
                            
                            // 4. Site ayarları ve SAYAÇLAR
                            $site_settings = [
                                'site_title' => 'BERAT K - R10 Portfolio',
                                'site_description' => 'Profesyonel yazılımcı BERAT K - R10 portföy sitesi',
                                'site_keywords' => 'BERAT K, R10, yazılımcı, portfolio, web geliştirme',
                                'hero_title' => 'BERAT K',
                                'hero_subtitle' => 'R10',
                                'hero_description' => 'Profesyonel yazılım geliştirici olarak modern web uygulamaları, mobil çözümler ve yaratıcı dijital deneyimler tasarlıyorum.',
                                'footer_description' => 'Profesyonel yazılım geliştirme hizmetleri ve yaratıcı çözümler sunuyorum.',
                                'contact_email' => $admin_email,
                                // ÖNEMLİ: SAYAÇLAR BURADA AYARLANIYOR!
                                'stat_projects' => '150',
                                'stat_clients' => '85',
                                'stat_years' => '5',
                                'stat_awards' => '12',
                                'blog_enabled' => '1',
                                'blog_posts_per_page' => '6',
                                'blog_page_title' => 'Blog',
                                'blog_description' => 'Teknoloji, yazılım geliştirme ve projelerim hakkında yazılar'
                            ];
                            
                            foreach ($site_settings as $key => $value) {
                                $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
                                $stmt->execute([$key, $value]);
                            }
                            echo '<script>updateProgress(60, "✅ Site ayarları ve SAYAÇLAR ayarlandı!");</script>';
                            
                            // 5. Örnek projeler ekle
                            $sample_projects = [
                                ['E-Ticaret Web Sitesi', 'Modern ve responsive e-ticaret platformu. Kullanıcı dostu arayüz, güvenli ödeme sistemi ve admin paneli ile tam özellikli online mağaza çözümü.', 'assets/img/projects/ecommerce.jpg', 'https://demo.ecommerce.com', 'https://github.com/user/ecommerce', 'Web Development', 'HTML5, CSS3, JavaScript, PHP, MySQL, Bootstrap', 1],
                                ['Mobil ToDo Uygulaması', 'React Native ile geliştirilmiş cross-platform görev yönetimi uygulaması. Offline çalışma, push notification ve senkronizasyon özellikleri.', 'assets/img/projects/todo-app.jpg', 'https://play.google.com/store/apps/details?id=com.todo', 'https://github.com/user/todo-app', 'Mobile App', 'React Native, Redux, Firebase, Node.js', 1],
                                ['Portföy Yönetim Sistemi', 'Finansal portföy takibi ve analizi için geliştirilmiş web uygulaması. Gerçek zamanlı veriler, grafikler ve raporlama modülleri.', 'assets/img/projects/portfolio-system.jpg', 'https://demo.portfolio.com', 'https://github.com/user/portfolio-system', 'Web Application', 'Vue.js, Laravel, MySQL, Chart.js, WebSocket', 1]
                            ];
                            
                            foreach ($sample_projects as $project) {
                                $stmt = $pdo->prepare("INSERT INTO projects (title, description, image, demo_url, github_url, category, technologies, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                                $stmt->execute($project);
                            }
                            echo '<script>updateProgress(70, "✅ 3 örnek proje eklendi!");</script>';
                            
                            // 6. Örnek ürünler ekle
                            $sample_products = [
                                ['CRM Yönetim Sistemi', 'Müşteri ilişkileri yönetimi için kapsamlı çözüm. Satış takibi, raporlama, e-posta entegrasyonu ve daha fazlası.', 'Müşteri Yönetimi, Satış Takibi, Raporlama, E-posta Entegrasyonu, Kullanıcı Yetkilendirme', 'Software', '2500', 'TL', 'assets/img/products/crm.jpg', 'https://demo.crm.com', 'https://admin.crm.com', 'https://downloads.com/crm.zip', 'https://docs.crm.com', 1],
                                ['Blog Yönetim Scripti', 'Modern ve SEO dostu blog scripti. Çoklu yazar desteği, kategori yönetimi, yorum sistemi ve admin paneli.', 'SEO Optimize, Çoklu Yazar, Kategori Yönetimi, Yorum Sistemi, Responsive Tasarım', 'Script', '850', 'TL', 'assets/img/products/blog-script.jpg', 'https://demo.blog.com', 'https://admin.blog.com', 'https://downloads.com/blog.zip', 'https://docs.blog.com', 1],
                                ['E-İmza Entegrasyon Modülü', 'Web uygulamaları için elektronik imza entegrasyon çözümü. Güvenli, hızlı ve kolay entegrasyon.', 'Elektronik İmza, API Entegrasyonu, Güvenlik, Sertifika Yönetimi, Log Sistemi', 'Module', '1200', 'TL', 'assets/img/products/e-signature.jpg', 'https://demo.esign.com', 'https://admin.esign.com', 'https://downloads.com/esign.zip', 'https://docs.esign.com', 1]
                            ];
                            
                            foreach ($sample_products as $product) {
                                $stmt = $pdo->prepare("INSERT INTO products (title, description, features, category, price, currency, image, demo_url, admin_demo_url, download_url, documentation_url, featured) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                                $stmt->execute($product);
                            }
                            echo '<script>updateProgress(80, "✅ 3 örnek ürün eklendi!");</script>';
                            
                            // 7. Hizmetler ekle
                            $services = [
                                ['Web Geliştirme', 'Modern ve responsive web siteleri tasarlıyor ve geliştiriyorum. HTML5, CSS3, JavaScript ve PHP teknolojileri kullanarak profesyonel çözümler sunuyorum.', 'fas fa-code'],
                                ['Mobil Uygulama', 'iOS ve Android platformları için native ve hybrid uygulamalar geliştiriyorum. React Native ve Flutter teknolojileri ile çapraz platform çözümler.', 'fas fa-mobile-alt'],
                                ['E-Ticaret', 'Güvenli ve kullanıcı dostu e-ticaret çözümleri sunuyorum. WooCommerce, Shopify ve özel e-ticaret platformları geliştiriyorum.', 'fas fa-shopping-cart'],
                                ['SEO Optimizasyon', 'Web sitenizin arama motorlarında üst sıralarda yer alması için teknik ve içerik SEO hizmetleri veriyorum.', 'fas fa-search'],
                                ['Sistem Yönetimi', 'Sunucu kurulumu, güvenlik yapılandırması ve sistem optimizasyonu hizmetleri sunuyorum. Linux ve Windows sunucu yönetimi.', 'fas fa-server'],
                                ['UI/UX Tasarım', 'Kullanıcı dostu arayüz tasarımları ve kullanıcı deneyimi optimizasyonu yapıyorum. Figma ve Adobe XD kullanarak modern tasarımlar oluşturuyorum.', 'fas fa-paint-brush']
                            ];
                            
                            foreach ($services as $service) {
                                $stmt = $pdo->prepare("INSERT INTO services (title, description, icon) VALUES (?, ?, ?)");
                                $stmt->execute($service);
                            }
                            echo '<script>updateProgress(90, "✅ 6 hizmet kategorisi eklendi!");</script>';
                            
                            // 8. Blog kategorileri
                            $blog_categories = [
                                ['Teknoloji', 'teknoloji', 'Teknoloji dünyasından haberler ve gelişmeler'],
                                ['Web Geliştirme', 'web-gelistirme', 'Web geliştirme konularında ipuçları ve rehberler'],
                                ['Mobil Geliştirme', 'mobil-gelistirme', 'Mobil uygulama geliştirme hakkında yazılar']
                            ];
                            
                            foreach ($blog_categories as $category) {
                                $stmt = $pdo->prepare("INSERT INTO blog_categories (name, slug, description) VALUES (?, ?, ?)");
                                $stmt->execute($category);
                            }
                            echo '<script>updateProgress(100, "✅ Blog kategorileri eklendi!");</script>';
                            
                            // BAŞARILI KURULUM MESAJI
                            echo '</div>';
                            echo '<div class="alert alert-custom alert-success text-center mt-4">
                                    <h3><i class="fas fa-check-circle"></i> 🎉 KURULUM BAŞARILI!</h3>
                                    <p class="lead">BERAT K - R10 Portfolio siteniz tamamen hazır!</p>
                                    
                                    <div class="row mt-4">
                                        <div class="col-md-6">
                                            <a href="index.php" class="btn btn-install w-100 mb-3">
                                                <i class="fas fa-home me-2"></i>Ana Sayfayı Gör
                                            </a>
                                        </div>
                                        <div class="col-md-6">
                                            <a href="admin/login.php" class="btn btn-outline-light w-100 mb-3">
                                                <i class="fas fa-cog me-2"></i>Admin Paneli
                                            </a>
                                        </div>
                                    </div>
                                    
                                    <div class="mt-4 p-3" style="background: rgba(255,255,255,0.1); border-radius: 10px;">
                                        <h5>📋 Admin Giriş Bilgileri:</h5>
                                        <p><strong>Kullanıcı Adı:</strong> ' . htmlspecialchars($admin_username) . '</p>
                                        <p><strong>Şifre:</strong> ' . htmlspecialchars($_POST['admin_password'] ?? 'admin123') . '</p>
                                        <p><strong>E-posta:</strong> ' . htmlspecialchars($admin_email) . '</p>
                                    </div>
                                    
                                    <div class="mt-3">
                                        <h6>✅ Kurulum Sonrası Hazır Gelenler:</h6>
                                        <div class="row text-start">
                                            <div class="col-md-6">
                                                <p>📊 <strong>Çalışan Sayaçlar:</strong><br>
                                                • 150 Tamamlanan Proje<br>
                                                • 85 Mutlu Müşteri<br>
                                                • 5 Yıllık Deneyim<br>
                                                • 12 Ödül & Sertifika</p>
                                            </div>
                                            <div class="col-md-6">
                                                <p>🎨 <strong>Örnek İçerikler:</strong><br>
                                                • 3 Proje (E-ticaret, Mobil, Portfolio)<br>
                                                • 3 Ürün (CRM, Blog, E-İmza)<br>
                                                • 6 Hizmet Kategorisi<br>
                                                • Blog sistemi hazır</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>';
                            
                            echo '<script>
                                setTimeout(function() {
                                    alert("🎉 Kurulum tamamlandı! Artık sayaçlar çalışacak ve örnek içerikler görünecek.");
                                }, 1000);
                            </script>';
                            
                            exit;
                            
                        } catch (Exception $e) {
                            echo '<div class="alert alert-custom alert-danger">
                                    <h4><i class="fas fa-exclamation-triangle"></i> Kurulum Hatası!</h4>
                                    <p>' . $e->getMessage() . '</p>
                                  </div>';
                        }
                    }
                    ?>

                    <form method="POST" id="installForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Admin Kullanıcı Adı</label>
                                <input type="text" class="form-control" name="admin_username" value="admin" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Admin Şifre</label>
                                <input type="password" class="form-control" name="admin_password" value="admin123" required>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">Admin E-posta</label>
                            <input type="email" class="form-control" name="admin_email" value="admin@beratk.com" required>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-install btn-lg">
                                <i class="fas fa-rocket me-2"></i>
                                Portfolio Sitemi Kur!
                            </button>
                        </div>
                        
                        <div class="text-center mt-3">
                            <small class="text-light opacity-75">
                                ⚡ 1-2 dakikada tamamlanır • SQLite (Hafif & Hızlı) • Örnek içeriklerle hazır gelir
                            </small>
                        </div>
                    </form>
                    
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('installForm')?.addEventListener('submit', function() {
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Kuruluyor...';
            btn.disabled = true;
        });
    </script>
</body>
</html>