<?php
/**
 * Helper Functions
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

// Start session only if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/database.php';

function clean($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        // Doğru yönlendirme yolu
        if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
            header('Location: login.php');
            exit;
        }
    }
}

function checkMaintenanceMode() {
    // Admin panelindeyse bakım modu kontrolü yapma
    $request_uri = $_SERVER['REQUEST_URI'];
    if (strpos($request_uri, '/admin/') !== false || 
        strpos($request_uri, 'admin/') === 0 ||
        basename($_SERVER['PHP_SELF']) === 'maintenance.php') {
        return;
    }
    
    // Bakım modu aktif mi kontrol et
    $maintenance_mode = getSetting('maintenance_mode', '0');
    if ($maintenance_mode === '1') {
        // maintenance.php sayfasına yönlendir
        header('Location: maintenance.php');
        exit;
    }
}

function getSetting($key, $default = '') {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    } catch(PDOException $e) {
        return $default;
    }
}



function setSetting($key, $value) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        return $stmt->execute([$key, $value]);
    } catch(PDOException $e) {
        return false;
    }
}

function uploadFile($file, $directory = 'uploads/') {
    // Ensure directory ends with slash
    if (substr($directory, -1) !== '/') {
        $directory .= '/';
    }
    
    // Create absolute path
    $abs_directory = __DIR__ . '/../' . $directory;
    
    // Ensure the directory exists with correct permissions
    if (!is_dir($abs_directory)) {
        if (!mkdir($abs_directory, 0755, true)) {
            return false;
        }
    }
    
    // Set permissions if directory exists
    if (!is_writable($abs_directory)) {
        chmod($abs_directory, 0755);
    }
    
    // Check file upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    // Validate file type
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'mp4', 'avi', 'mov', 'wmv', 'pdf', 'doc', 'docx'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_types)) {
        return false;
    }
    
    // Generate safe filename
    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9.-]/', '_', $file['name']);
    $file_path = $abs_directory . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $file_path)) {
        // Return relative path for database storage
        return $directory . $filename;
    }
    
    return false;
}

function getProjects($limit = 0) {
    global $pdo;
    try {
        $sql = "SELECT * FROM projects WHERE status = 1 ORDER BY created_at DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

function getServices($limit = 0) {
    global $pdo;
    try {
        $sql = "SELECT * FROM services WHERE status = 1 ORDER BY created_at DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

function getProducts($limit = 0) {
    global $pdo;
    try {
        $sql = "SELECT * FROM products WHERE status = 1 ORDER BY created_at DESC";
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

function formatDate($date) {
    return date('d.m.Y', strtotime($date));
}

function truncateText($text, $length = 150) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

// Site ziyaretçi sayacı fonksiyonları
function createVisitorTables() {
    global $pdo;
    try {
        // Ziyaretçi tablosu
        $sql = "CREATE TABLE IF NOT EXISTS visitors (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            page_url VARCHAR(500),
            referrer VARCHAR(500),
            visit_date DATE NOT NULL,
            visit_time DATETIME DEFAULT CURRENT_TIMESTAMP
        );";
        
        $pdo->exec($sql);
        
        // Sayfa görüntülenme tablosu
        $sql2 = "CREATE TABLE IF NOT EXISTS page_views (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            page_url VARCHAR(500) NOT NULL,
            ip_address VARCHAR(45) NOT NULL,
            user_agent TEXT,
            view_time DATETIME DEFAULT CURRENT_TIMESTAMP
        );";
        
        $pdo->exec($sql2);
        
        return true;
    } catch(PDOException $e) {
        error_log("Visitor tables creation failed: " . $e->getMessage());
        return false;
    }
}

function trackVisitor($page_url = null) {
    global $pdo;
    
    // Admin paneli ziyaretlerini sayma
    if (strpos($_SERVER['REQUEST_URI'], '/admin/') !== false) {
        return;
    }
    
    try {
        createVisitorTables();
        
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
        $page_url = $page_url ?? $_SERVER['REQUEST_URI'];
        $referrer = $_SERVER['HTTP_REFERER'] ?? '';
        $visit_date = date('Y-m-d');
        
        // Bu IP bugün ziyaret etmiş mi?
        $stmt = $pdo->prepare("SELECT id FROM visitors WHERE ip_address = ? AND visit_date = ?");
        $stmt->execute([$ip_address, $visit_date]);
        
        if (!$stmt->fetch()) {
            // Yeni ziyaretçi kaydı
            $stmt = $pdo->prepare("INSERT INTO visitors (ip_address, user_agent, page_url, referrer, visit_date) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$ip_address, $user_agent, $page_url, $referrer, $visit_date]);
        }
        
        // Sayfa görüntülenme kaydı
        $stmt = $pdo->prepare("INSERT INTO page_views (page_url, ip_address, user_agent) VALUES (?, ?, ?)");
        $stmt->execute([$page_url, $ip_address, $user_agent]);
        
    } catch(PDOException $e) {
        error_log("Visitor tracking failed: " . $e->getMessage());
    }
}

function getVisitorStats() {
    global $pdo;
    try {
        createVisitorTables();
        
        $stats = [];
        
        // Bugünkü ziyaretçiler
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE visit_date = DATE('now')");
        $stmt->execute();
        $stats['today'] = $stmt->fetchColumn();
        
        // Bu haftaki ziyaretçiler
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE visit_date >= DATE('now', '-7 days')");
        $stmt->execute();
        $stats['week'] = $stmt->fetchColumn();
        
        // Bu ayki ziyaretçiler
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE visit_date >= DATE('now', '-30 days')");
        $stmt->execute();
        $stats['month'] = $stmt->fetchColumn();
        
        // Toplam ziyaretçiler
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors");
        $stmt->execute();
        $stats['total'] = $stmt->fetchColumn();
        
        // Bugünkü sayfa görüntülenmeleri
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM page_views WHERE DATE(view_time) = DATE('now')");
        $stmt->execute();
        $stats['page_views_today'] = $stmt->fetchColumn();
        
        // Toplam sayfa görüntülenmeleri
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM page_views");
        $stmt->execute();
        $stats['page_views_total'] = $stmt->fetchColumn();
        
        return $stats;
    } catch(PDOException $e) {
        error_log("Visitor stats failed: " . $e->getMessage());
        return [
            'today' => 0,
            'week' => 0,
            'month' => 0,
            'total' => 0,
            'page_views_today' => 0,
            'page_views_total' => 0
        ];
    }
}

function getSystemStats() {
    $stats = [];
    
    // Disk kullanımı - güvenli kontrol
    if (function_exists('disk_free_space')) {
        try {
            $bytes = disk_free_space(".");
            $stats['disk_free'] = $bytes ? formatBytes($bytes) : 'N/A';
            
            $bytes = disk_total_space(".");
            $stats['disk_total'] = $bytes ? formatBytes($bytes) : 'N/A';
        } catch (Exception $e) {
            $stats['disk_free'] = 'N/A';
            $stats['disk_total'] = 'N/A';
        }
    } else {
        $stats['disk_free'] = 'N/A';
        $stats['disk_total'] = 'N/A';
    }
    
    // Bellek kullanımı
    $stats['memory_usage'] = formatBytes(memory_get_usage(true));
    $stats['memory_peak'] = formatBytes(memory_get_peak_usage(true));
    
    // PHP bilgileri
    $stats['php_version'] = phpversion();
    
    return $stats;
}

function formatBytes($bytes, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
        $bytes /= 1024;
    }
    
    return round($bytes, $precision) . ' ' . $units[$i];
}

// SEO ve Sitemap fonksiyonları
function generateSitemap() {
    global $pdo;
    
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $sitemap_path = __DIR__ . '/../sitemap.xml';
    
    $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
    
    // Ana sayfalar
    $pages = [
        '' => ['priority' => '1.0', 'changefreq' => 'daily'],
        'about.php' => ['priority' => '0.8', 'changefreq' => 'monthly'],
        'services.php' => ['priority' => '0.9', 'changefreq' => 'weekly'],
        'portfolio.php' => ['priority' => '0.9', 'changefreq' => 'weekly'],
        'products.php' => ['priority' => '0.9', 'changefreq' => 'weekly'],
        'contact.php' => ['priority' => '0.7', 'changefreq' => 'monthly']
    ];
    
    foreach ($pages as $page => $config) {
        $url = $base_url . '/' . $page;
        $xml .= '  <url>' . "\n";
        $xml .= '    <loc>' . htmlspecialchars($url) . '</loc>' . "\n";
        $xml .= '    <lastmod>' . date('Y-m-d') . '</lastmod>' . "\n";
        $xml .= '    <changefreq>' . $config['changefreq'] . '</changefreq>' . "\n";
        $xml .= '    <priority>' . $config['priority'] . '</priority>' . "\n";
        $xml .= '  </url>' . "\n";
    }
    
    // Projeler
    try {
        $stmt = $pdo->prepare("SELECT id, created_at FROM projects WHERE status = 1 ORDER BY created_at DESC");
        $stmt->execute();
        $projects = $stmt->fetchAll();
        
        foreach ($projects as $project) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($base_url . '/portfolio.php?project=' . $project['id']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . date('Y-m-d', strtotime($project['created_at'])) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>monthly</changefreq>' . "\n";
            $xml .= '    <priority>0.6</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
    } catch(PDOException $e) {
        error_log("Sitemap project generation failed: " . $e->getMessage());
    }
    
    // Ürünler
    try {
        $stmt = $pdo->prepare("SELECT id, created_at FROM products WHERE status = 1 ORDER BY created_at DESC");
        $stmt->execute();
        $products = $stmt->fetchAll();
        
        foreach ($products as $product) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($base_url . '/products.php?product=' . $product['id']) . '</loc>' . "\n";
            $xml .= '    <lastmod>' . date('Y-m-d', strtotime($product['created_at'])) . '</lastmod>' . "\n";
            $xml .= '    <changefreq>monthly</changefreq>' . "\n";
            $xml .= '    <priority>0.6</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
    } catch(PDOException $e) {
        error_log("Sitemap product generation failed: " . $e->getMessage());
    }
    
    $xml .= '</urlset>';
    
    // Dosyaya yaz
    if (file_put_contents($sitemap_path, $xml)) {
        // Son güncelleme zamanını kaydet
        setSetting('sitemap_last_update', date('Y-m-d H:i:s'));
        return true;
    }
    
    return false;
}

function generateRobotsTxt() {
    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
    $robots_path = __DIR__ . '/../robots.txt';
    
    $robots_content = getSetting('robots_txt_content', '');
    
    // Varsayılan robots.txt içeriği
    if (empty($robots_content)) {
        $robots_content = "User-agent: *\n";
        $robots_content .= "Disallow: /admin/\n";
        $robots_content .= "Disallow: /config/\n";
        $robots_content .= "Disallow: /includes/\n";
        $robots_content .= "Disallow: /uploads/debug.log\n";
        $robots_content .= "Allow: /\n\n";
        $robots_content .= "Sitemap: " . $base_url . "/sitemap.xml\n";
    }
    
    if (file_put_contents($robots_path, $robots_content)) {
        setSetting('robots_last_update', date('Y-m-d H:i:s'));
        return true;
    }
    
    return false;
}

// Brute force koruması fonksiyonları
function createBruteForceTable() {
    global $pdo;
    try {
        $sql = "CREATE TABLE IF NOT EXISTS login_attempts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            ip_address VARCHAR(45) NOT NULL,
            username VARCHAR(100),
            attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP,
            success INTEGER DEFAULT 0
        );";
        
        $pdo->exec($sql);
        return true;
    } catch(PDOException $e) {
        error_log("Brute force table creation failed: " . $e->getMessage());
        return false;
    }
}

function checkBruteForce($ip_address, $max_attempts = 5, $time_window = 900) { // 15 dakika
    global $pdo;
    
    try {
        createBruteForceTable();
        
        $time_limit = date('Y-m-d H:i:s', time() - $time_window);
        
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE ip_address = ? AND attempt_time > ? AND success = 0");
        $stmt->execute([$ip_address, $time_limit]);
        $failed_attempts = $stmt->fetchColumn();
        
        return $failed_attempts >= $max_attempts;
    } catch(PDOException $e) {
        error_log("Brute force check failed: " . $e->getMessage());
        return false;
    }
}

function logLoginAttempt($ip_address, $username, $success = false) {
    global $pdo;
    
    try {
        createBruteForceTable();
        
        $stmt = $pdo->prepare("INSERT INTO login_attempts (ip_address, username, success) VALUES (?, ?, ?)");
        $stmt->execute([$ip_address, $username, $success ? 1 : 0]);
        
        return true;
    } catch(PDOException $e) {
        error_log("Login attempt logging failed: " . $e->getMessage());
        return false;
    }
}

function getBruteForceStats() {
    global $pdo;
    
    try {
        createBruteForceTable();
        
        $stats = [];
        
        // Bugünkü başarısız denemeler
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE DATE(attempt_time) = DATE('now') AND success = 0");
        $stmt->execute();
        $stats['today_failed'] = $stmt->fetchColumn();
        
        // Bugünkü başarılı girişler
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM login_attempts WHERE DATE(attempt_time) = DATE('now') AND success = 1");
        $stmt->execute();
        $stats['today_success'] = $stmt->fetchColumn();
        
        // Şu anda engellenmiş IP'ler
        $time_limit = date('Y-m-d H:i:s', time() - 900); // 15 dakika
        $stmt = $pdo->prepare("SELECT COUNT(DISTINCT ip_address) FROM login_attempts WHERE attempt_time > ? AND success = 0 GROUP BY ip_address HAVING COUNT(*) >= 5");
        $stmt->execute([$time_limit]);
        $stats['blocked_ips'] = $stmt->rowCount();
        
        // En çok deneme yapan IP'ler (bugün)
        $stmt = $pdo->prepare("SELECT ip_address, COUNT(*) as attempts FROM login_attempts WHERE DATE(attempt_time) = DATE('now') AND success = 0 GROUP BY ip_address ORDER BY attempts DESC LIMIT 5");
        $stmt->execute();
        $stats['top_ips'] = $stmt->fetchAll();
        
        return $stats;
    } catch(PDOException $e) {
        error_log("Brute force stats failed: " . $e->getMessage());
        return [
            'today_failed' => 0,
            'today_success' => 0,
            'blocked_ips' => 0,
            'top_ips' => []
        ];
    }
}

// Gelişmiş sayfa analizi
function getPageAnalytics($days = 7) {
    global $pdo;
    
    try {
        createVisitorTables();
        
        $stats = [];
        
        // Popüler sayfalar
        $stmt = $pdo->prepare("
            SELECT page_url, COUNT(*) as views 
            FROM page_views 
            WHERE view_time >= DATE('now', '-' || ? || ' days') 
            GROUP BY page_url 
            ORDER BY views DESC 
            LIMIT 10
        ");
        $stmt->execute([$days]);
        $stats['popular_pages'] = $stmt->fetchAll();
        
        // Günlük sayfa görüntülenmeleri
        $daily_views = [];
        for ($i = $days - 1; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $date_formatted = date('M d', strtotime($date));
            
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM page_views WHERE DATE(view_time) = ?");
            $stmt->execute([$date]);
            $count = $stmt->fetchColumn();
            
            $daily_views[] = [
                'date' => $date_formatted,
                'views' => $count
            ];
        }
        $stats['daily_views'] = $daily_views;
        
        // Referrer analizi
        $stmt = $pdo->prepare("
            SELECT referrer, COUNT(*) as visits 
            FROM visitors 
            WHERE visit_date >= DATE('now', '-' || ? || ' days') 
            AND referrer != '' 
            GROUP BY referrer 
            ORDER BY visits DESC 
            LIMIT 10
        ");
        $stmt->execute([$days]);
        $stats['top_referrers'] = $stmt->fetchAll();
        
        return $stats;
    } catch(PDOException $e) {
        error_log("Page analytics failed: " . $e->getMessage());
        return [
            'popular_pages' => [],
            'daily_views' => [],
            'top_referrers' => []
        ];
    }
}

// Blog tablosu oluşturma fonksiyonu
function createBlogTables() {
    global $pdo;
    try {
        // Blog posts tablosu
        $sql = "CREATE TABLE IF NOT EXISTS blog_posts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(255) NOT NULL,
            slug VARCHAR(255) UNIQUE NOT NULL,
            content TEXT NOT NULL,
            excerpt TEXT,
            featured_image VARCHAR(500),
            status VARCHAR(20) DEFAULT 'draft',
            author_id INTEGER,
            category_id INTEGER,
            tags TEXT,
            meta_title VARCHAR(255),
            meta_description TEXT,
            meta_keywords TEXT,
            views INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            published_at DATETIME
        );";
        $pdo->exec($sql);
        
        // Blog categories tablosu
        $sql2 = "CREATE TABLE IF NOT EXISTS blog_categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            slug VARCHAR(100) UNIQUE NOT NULL,
            description TEXT,
            image VARCHAR(500),
            status INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );";
        $pdo->exec($sql2);
        
        // Blog comments tablosu
        $sql3 = "CREATE TABLE IF NOT EXISTS blog_comments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            post_id INTEGER NOT NULL,
            author_name VARCHAR(100) NOT NULL,
            author_email VARCHAR(255) NOT NULL,
            author_website VARCHAR(255),
            content TEXT NOT NULL,
            status VARCHAR(20) DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES blog_posts(id) ON DELETE CASCADE
        );";
        $pdo->exec($sql3);
        
        // Varsayılan kategori ekle
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_categories");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            $stmt = $pdo->prepare("INSERT INTO blog_categories (name, slug, description) VALUES (?, ?, ?)");
            $stmt->execute(['Genel', 'genel', 'Genel blog yazıları']);
            $stmt->execute(['Teknoloji', 'teknoloji', 'Teknoloji ve yazılım geliştirme']);
            $stmt->execute(['Projeler', 'projeler', 'Proje geliştirme süreçleri']);
            $stmt->execute(['Web Tasarım', 'web-tasarim', 'Web tasarım ve UI/UX konuları']);
        }
        
        // Varsayılan blog yazıları ekle
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts");
        $stmt->execute();
        if ($stmt->fetchColumn() == 0) {
            // Kategorileri al
            $stmt = $pdo->prepare("SELECT id, slug FROM blog_categories");
            $stmt->execute();
            $categories = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
            
            $default_posts = [
                [
                    'title' => 'Modern Web Geliştirme Trendleri 2024',
                    'slug' => 'modern-web-gelistirme-trendleri-2024',
                    'content' => '<h2>Web Geliştirme Dünyasındaki Yenilikler</h2>

<p>2024 yılı web geliştirme dünyasında birçok önemli değişiklik ve yenilik getirdi. Bu yazıda, geliştiricilerin takip etmesi gereken en önemli trendleri inceliyoruz.</p>

<h3>1. Yapay Zeka Entegrasyonu</h3>
<p>Yapay zeka artık web uygulamalarının ayrılmaz bir parçası haline geldi. ChatGPT API\'si gibi hizmetlerle:</p>
<ul>
<li>Akıllı chatbot\'lar</li>
<li>İçerik üretimi</li>
<li>Kullanıcı deneyimi personalizasyonu</li>
<li>Otomatik kod tamamlama</li>
</ul>

<h3>2. Jamstack Mimarisi</h3>
<p>JavaScript, API\'ler ve Markup\'ın birleşiminden oluşan Jamstack mimarisi popülerliğini artırıyor. Avantajları:</p>
<ul>
<li>Yüksek performans</li>
<li>Güvenlik</li>
<li>Ölçeklenebilirlik</li>
<li>Geliştirici deneyimi</li>
</ul>

<h3>3. Micro Frontends</h3>
<p>Büyük uygulamaları küçük, bağımsız parçalara ayırma yaklaşımı olan micro frontends, takım verimliliğini artırıyor.</p>

<blockquote>
"Modern web geliştirme, sadece kod yazmak değil, kullanıcı deneyimini en üst seviyeye çıkarmaktır."
</blockquote>

<p>Bu trendleri takip ederek, 2024\'te daha etkili ve modern web uygulamaları geliştirebiliriz.</p>',
                    'excerpt' => '2024 yılının en önemli web geliştirme trendlerini keşfedin. Yapay zeka entegrasyonundan Jamstack mimarisine kadar detaylı inceleme.',
                    'category_id' => $categories['teknoloji'],
                    'tags' => 'web geliştirme, 2024, trendler, yapay zeka, jamstack',
                    'meta_title' => 'Modern Web Geliştirme Trendleri 2024 | BERAT K - R10',
                    'meta_description' => '2024 yılının en önemli web geliştirme trendlerini öğrenin. Yapay zeka, Jamstack ve micro frontends hakkında detaylı bilgiler.',
                    'status' => 'published',
                    'views' => rand(45, 120)
                ],
                [
                    'title' => 'PHP 8.3 ile Gelen Yenilikler',
                    'slug' => 'php-8-3-ile-gelen-yenilikler',
                    'content' => '<h2>PHP 8.3: Daha Hızlı, Daha Güvenli</h2>

<p>PHP 8.3 sürümü web geliştirme dünyasında önemli iyileştirmeler getirdi. Bu makalede yeni özellikleri detaylı olarak inceliyoruz.</p>

<h3>Öne Çıkan Özellikler</h3>

<h4>1. Typed Class Constants</h4>
<p>Artık sınıf sabitlerinin türlerini belirleyebiliyoruz:</p>

<pre><code>class MathConstants {
    public const int PI_PRECISION = 10;
    public const float PI = 3.14159;
}</code></pre>

<h4>2. Dynamic Class Constant Fetch</h4>
<p>Dinamik olarak sınıf sabitlerini çağırabiliyoruz:</p>

<pre><code>$constantName = \'PI\';
echo MathConstants::{$constantName};</code></pre>

<h4>3. Anonymous Readonly Classes</h4>
<p>Anonim sınıflar artık readonly olabilir:</p>

<pre><code>$point = new readonly class {
    public function __construct(
        public int $x,
        public int $y
    ) {}
};</code></pre>

<h3>Performans İyileştirmeleri</h3>
<ul>
<li>%8-15 arası performans artışı</li>
<li>Bellek kullanımında iyileştirmeler</li>
<li>JIT compiler optimizasyonları</li>
</ul>

<h3>Deprecated Özellikler</h3>
<p>Bazı eski özellikler deprecated oldu. Projelerinizi güncellerken dikkat edilmesi gerekenler:</p>
<ul>
<li>NumberFormatter::TYPE_CURRENCY</li>
<li>SQLite3::escapeString()</li>
<li>Uniqid() fonksiyonunun prefix parametresi</li>
</ul>

<p>PHP 8.3 ile projelerinizi güncellemeyi düşünüyorsanız, bu yenilikleri kullanarak hem performansı hem de kod kalitesini artırabilirsiniz.</p>',
                    'excerpt' => 'PHP 8.3 sürümünün getirdiği yenilikleri keşfedin. Typed class constants, performans iyileştirmeleri ve daha fazlası.',
                    'category_id' => $categories['teknoloji'],
                    'tags' => 'PHP, PHP 8.3, web geliştirme, programlama',
                    'meta_title' => 'PHP 8.3 Yenilikleri ve Özellikleri | BERAT K - R10',
                    'meta_description' => 'PHP 8.3 sürümünün getirdiği yeni özellikler, performans iyileştirmeleri ve deprecated özellikler hakkında detaylı rehber.',
                    'status' => 'published',
                    'views' => rand(65, 150)
                ],
                [
                    'title' => 'React vs Vue.js: 2024 Karşılaştırması',
                    'slug' => 'react-vs-vue-js-2024-karsilastirmasi',
                    'content' => '<h2>Frontend Framework Savaşı: React vs Vue.js</h2>

<p>Frontend geliştirme dünyasında en popüler iki framework olan React ve Vue.js\'i 2024 perspektifiyle karşılaştırıyoruz.</p>

<h3>React: Meta\'nın Güçlü Çocuğu</h3>
<p>Facebook (Meta) tarafından geliştirilen React, geniş topluluk desteğiyle öne çıkıyor.</p>

<h4>React\'ın Avantajları:</h4>
<ul>
<li><strong>Büyük Ekosistem:</strong> Binlerce hazır component ve kütüphane</li>
<li><strong>İş Fırsatları:</strong> En çok aranan frontend skill\'i</li>
<li><strong>Esneklik:</strong> Çok çeşitli projelerde kullanılabilir</li>
<li><strong>React Native:</strong> Mobil geliştirme desteği</li>
</ul>

<h4>React\'ın Zorlukları:</h4>
<ul>
<li>Steep learning curve</li>
<li>Hızlı değişen ekosistem</li>
<li>Configuration complexity</li>
</ul>

<h3>Vue.js: Geliştiriciye Dostluk</h3>
<p>Evan You tarafından oluşturulan Vue.js, basitlik ve öğrenme kolaylığıyla dikkat çekiyor.</p>

<h4>Vue\'nın Avantajları:</h4>
<ul>
<li><strong>Kolay Öğrenme:</strong> Gentle learning curve</li>
<li><strong>Mükemmel Dökümantasyon:</strong> Anlaşılır ve detaylı</li>
<li><strong>Template Syntax:</strong> HTML\'ye yakın syntax</li>
<li><strong>Vue CLI:</strong> Hızlı proje kurulumu</li>
</ul>

<h4>Vue\'nın Zorlukları:</h4>
<ul>
<li>Daha küçük iş pazarı</li>
<li>React\'e göre daha az third-party plugin</li>
<li>Büyük projeler için ecosystem eksikliği</li>
</ul>

<h3>2024 Önerileri</h3>

<table style="width:100%; border-collapse: collapse; margin: 20px 0;">
<tr style="background-color: #333;">
<th style="border: 1px solid #555; padding: 10px;">Durum</th>
<th style="border: 1px solid #555; padding: 10px;">Öneri</th>
</tr>
<tr>
<td style="border: 1px solid #555; padding: 10px;">Yeni başlayan</td>
<td style="border: 1px solid #555; padding: 10px;">Vue.js ile başla</td>
</tr>
<tr>
<td style="border: 1px solid #555; padding: 10px;">İş arıyor</td>
<td style="border: 1px solid #555; padding: 10px;">React öğren</td>
</tr>
<tr>
<td style="border: 1px solid #555; padding: 10px;">Hızlı prototip</td>
<td style="border: 1px solid #555; padding: 10px;">Vue.js ideal</td>
</tr>
<tr>
<td style="border: 1px solid #555; padding: 10px;">Büyük takım</td>
<td style="border: 1px solid #555; padding: 10px;">React ekosistemi</td>
</tr>
</table>

<p>Sonuç olarak, her iki framework de güçlü. Seçim yaparken proje gereksinimlerinizi ve takım deneyimini göz önünde bulundurun.</p>',
                    'excerpt' => '2024 yılında React ve Vue.js framework\'lerini karşılaştırıyoruz. Hangi durumda hangisini seçmelisiniz?',
                    'category_id' => $categories['teknoloji'],
                    'tags' => 'React, Vue.js, frontend, javascript, framework',
                    'meta_title' => 'React vs Vue.js 2024 Karşılaştırması | Hangisini Seçmeli?',
                    'meta_description' => 'React ve Vue.js framework\'lerinin 2024 karşılaştırması. Avantajlar, dezavantajlar ve hangi durumda hangisini seçeceğiniz.',
                    'status' => 'published',
                    'views' => rand(85, 200)
                ],
                [
                    'title' => 'Web Tasarımında Dark Mode Trendi',
                    'slug' => 'web-tasariminda-dark-mode-trendi',
                    'content' => '<h2>Dark Mode: Sadece Trend mi, Gereksinim mi?</h2>

<p>Dark mode artık sadece bir trend değil, kullanıcı deneyiminin önemli bir parçası haline geldi. Bu yazıda dark mode\'un faydalarını ve uygulama yöntemlerini inceliyoruz.</p>

<h3>Dark Mode\'un Faydaları</h3>

<h4>1. Göz Sağlığı</h4>
<p>Özellikle düşük ışık koşullarında kullanıcıların gözlerini yoruyor:</p>
<ul>
<li>Mavi ışık maruziyetini azaltır</li>
<li>Göz yorgunluğunu önler</li>
<li>Daha rahat okuma deneyimi</li>
</ul>

<h4>2. Batarya Tasarrufu</h4>
<p>OLED ekranlarda önemli batarya tasarrufu sağlar:</p>
<ul>
<li>Siyah pikseller enerji tüketmez</li>
<li>%30\'a kadar batarya tasarrufu</li>
<li>Mobil cihazlarda uzun kullanım</li>
</ul>

<h4>3. Görsel Estetik</h4>
<p>Modern ve şık görünüm:</p>
<ul>
<li>Premium hissi verir</li>
<li>Renkler daha canlı görünür</li>
<li>Odaklanmayı artırır</li>
</ul>

<h3>CSS ile Dark Mode Uygulaması</h3>

<pre><code>/* Sistem tercihi algılama */
@media (prefers-color-scheme: dark) {
  :root {
    --bg-color: #121212;
    --text-color: #ffffff;
    --accent-color: #6c5ce7;
  }
}

/* Light mode */
@media (prefers-color-scheme: light) {
  :root {
    --bg-color: #ffffff;
    --text-color: #333333;
    --accent-color: #6c5ce7;
  }
}

body {
  background-color: var(--bg-color);
  color: var(--text-color);
  transition: all 0.3s ease;
}</code></pre>

<h3>JavaScript ile Toggle</h3>

<pre><code>// Dark mode toggle
function toggleDarkMode() {
  document.body.classList.toggle(\'dark-mode\');
  
  // Kullanıcı tercihini kaydet
  const isDark = document.body.classList.contains(\'dark-mode\');
  localStorage.setItem(\'dark-mode\', isDark);
}

// Sayfa yüklendiğinde kontrol et
document.addEventListener(\'DOMContentLoaded\', () => {
  const savedMode = localStorage.getItem(\'dark-mode\');
  if (savedMode === \'true\') {
    document.body.classList.add(\'dark-mode\');
  }
});</code></pre>

<h3>Dark Mode Tasarım İlkeleri</h3>

<ol>
<li><strong>Kontrast Oranı:</strong> En az 4.5:1 oranında kontrast</li>
<li><strong>Renk Paleti:</strong> Yeterince koyu ama saf siyah değil</li>
<li><strong>Elevation:</strong> Yüzey seviyelerini göstermek için gri tonları</li>
<li><strong>Renkli Öğeler:</strong> Accent renkleri daha desaturated</li>
</ol>

<h3>Popüler Dark Mode Örnekleri</h3>
<ul>
<li><strong>Discord:</strong> #36393f ana renk</li>
<li><strong>Slack:</strong> #1a1d21 koyu tema</li>
<li><strong>Twitter:</strong> #15202b gece modu</li>
<li><strong>GitHub:</strong> #0d1117 dark theme</li>
</ul>

<blockquote>
"İyi bir dark mode, sadece renkleri tersine çevirmek değil, kullanıcı deneyimini yeniden tasarlamaktır."
</blockquote>

<p>Dark mode uygulaması yaparken kullanıcıya seçim hakkı tanımayı unutmayın. Hem light hem dark seçenekleri sunarak, herkesin tercihine uygun bir deneyim yaratabilirsiniz.</p>',
                    'excerpt' => 'Dark mode neden bu kadar popüler? Faydaları, uygulama yöntemleri ve tasarım ipuçları ile kapsamlı rehber.',
                    'category_id' => $categories['web-tasarim'],
                    'tags' => 'dark mode, web tasarım, UI/UX, CSS, kullanıcı deneyimi',
                    'meta_title' => 'Dark Mode Tasarım Rehberi | Web Tasarımında Karanlık Tema',
                    'meta_description' => 'Dark mode\'un faydaları, CSS ve JavaScript ile uygulama yöntemleri ve tasarım ipuçları. Kullanıcı dostu karanlık tema rehberi.',
                    'status' => 'published',
                    'views' => rand(95, 180)
                ]
            ];
            
            $stmt = $pdo->prepare("
                INSERT INTO blog_posts (title, slug, content, excerpt, category_id, tags, meta_title, meta_description, status, views, author_id, published_at, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?, ?)
            ");
            
            foreach ($default_posts as $index => $post) {
                // Tarihler farklı olsun
                $created_date = date('Y-m-d H:i:s', strtotime('-' . (count($default_posts) - $index) . ' days'));
                
                $stmt->execute([
                    $post['title'],
                    $post['slug'],
                    $post['content'],
                    $post['excerpt'],
                    $post['category_id'],
                    $post['tags'],
                    $post['meta_title'],
                    $post['meta_description'],
                    $post['status'],
                    $post['views'],
                    $created_date, // published_at
                    $created_date  // created_at
                ]);
            }
        }
        
        return true;
    } catch(PDOException $e) {
        error_log("Blog tables creation failed: " . $e->getMessage());
        return false;
    }
}

// Blog fonksiyonları
function getBlogPosts($limit = 0, $category = null, $status = 'published') {
    global $pdo;
    try {
        createBlogTables();
        
        $sql = "SELECT bp.*, bc.name as category_name, bc.slug as category_slug 
                FROM blog_posts bp 
                LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
                WHERE bp.status = ?";
        
        $params = [$status];
        
        if ($category) {
            $sql .= " AND bc.slug = ?";
            $params[] = $category;
        }
        
        $sql .= " ORDER BY bp.created_at DESC";
        
        if ($limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Get blog posts failed: " . $e->getMessage());
        return [];
    }
}

function getBlogPost($slug) {
    global $pdo;
    try {
        createBlogTables();
        
        $stmt = $pdo->prepare("
            SELECT bp.*, bc.name as category_name, bc.slug as category_slug 
            FROM blog_posts bp 
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
            WHERE bp.slug = ? AND bp.status = 'published'
        ");
        $stmt->execute([$slug]);
        $post = $stmt->fetch();
        
        if ($post) {
            // View sayısını artır
            $update_stmt = $pdo->prepare("UPDATE blog_posts SET views = views + 1 WHERE id = ?");
            $update_stmt->execute([$post['id']]);
        }
        
        return $post;
    } catch(PDOException $e) {
        error_log("Get blog post failed: " . $e->getMessage());
        return null;
    }
}

function getBlogCategories() {
    global $pdo;
    try {
        createBlogTables();
        
        $stmt = $pdo->prepare("SELECT * FROM blog_categories WHERE status = 1 ORDER BY name");
        $stmt->execute();
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        error_log("Get blog categories failed: " . $e->getMessage());
        return [];
    }
}

function createBlogSlug($title) {
    // Türkçe karakterleri değiştir
    $slug = str_replace(
        ['ç', 'ğ', 'ı', 'ö', 'ş', 'ü', 'Ç', 'Ğ', 'I', 'İ', 'Ö', 'Ş', 'Ü'],
        ['c', 'g', 'i', 'o', 's', 'u', 'c', 'g', 'i', 'i', 'o', 's', 'u'],
        $title
    );
    
    // Küçük harfe çevir
    $slug = strtolower($slug);
    
    // Sadece harf, rakam ve tire bırak
    $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
    
    // Çoklu tireleri tek tire yap
    $slug = preg_replace('/-+/', '-', $slug);
    
    // Başındaki ve sonundaki tireleri temizle
    $slug = trim($slug, '-');
    
    return $slug;
}

// E-posta gönderme fonksiyonları
function sendContactNotification($name, $email, $subject, $message) {
    // SMTP ayarları kontrol et
    $smtp_host = getSetting('smtp_host');
    $smtp_username = getSetting('smtp_username');
    $smtp_password = getSetting('smtp_password');
    
    if (empty($smtp_host) || empty($smtp_username)) {
        error_log("SMTP settings not configured");
        return false;
    }
    
    // PHPMailer kullanarak bildirim gönder
    if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
        error_log("PHPMailer not installed");
        return false;
    }
    
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        $mail_class = '\PHPMailer\PHPMailer\PHPMailer';
    } else {
        // Paylaşımlı hosting için manuel PHPMailer
        require_once __DIR__ . '/../phpmailer/PHPMailer.php';
        require_once __DIR__ . '/../phpmailer/SMTP.php';
        require_once __DIR__ . '/../phpmailer/Exception.php';
        $mail_class = 'PHPMailer\PHPMailer\PHPMailer';
    }
    
    try {
        $mail = new $mail_class(true);
        
        // SMTP ayarları
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->Port = (int)getSetting('smtp_port', '587');
        
        // Güvenlik ayarı
        $smtp_security = getSetting('smtp_security', 'tls');
        if ($smtp_security === 'ssl') {
            $mail->SMTPSecure = 'ssl';
        } elseif ($smtp_security === 'tls') {
            $mail->SMTPSecure = 'tls';
        }
        
        $mail->SMTPDebug = 0;
        $mail->CharSet = 'UTF-8';
        
        // Gönderen ve alıcı bilgileri
        $site_brand = getSetting('site_brand', 'BERAT K - R10');
        $contact_email = getSetting('contact_email', $smtp_username);
        
        $mail->setFrom($smtp_username, 'Portfolio Website - ' . $site_brand);
        $mail->addAddress($contact_email);
        $mail->addReplyTo($email, $name);
        
        // E-posta içeriği
        $mail->isHTML(true);
        $mail->Subject = '📬 Yeni İletişim Mesajı: ' . ($subject ? $subject : 'Konu Belirtilmemiş');
        
        $mail->Body = "
        <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; background-color: #f8f9fa;'>
            <div style='background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 30px; border-radius: 10px 10px 0 0; text-align: center;'>
                <h1 style='color: white; margin: 0; font-size: 24px;'>📬 Yeni İletişim Mesajı</h1>
                <p style='color: rgba(255,255,255,0.9); margin: 10px 0 0 0;'>" . $site_brand . " Portfolio Website</p>
            </div>
            
            <div style='background: white; padding: 30px; border-radius: 0 0 10px 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);'>
                <h2 style='color: #333; margin-top: 0; border-bottom: 2px solid #667eea; padding-bottom: 10px;'>Mesaj Detayları</h2>
                
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;'>
                    <p style='margin: 8px 0;'><strong>👤 Gönderen:</strong> " . htmlspecialchars($name) . "</p>
                    <p style='margin: 8px 0;'><strong>📧 E-posta:</strong> <a href='mailto:" . htmlspecialchars($email) . "' style='color: #667eea;'>" . htmlspecialchars($email) . "</a></p>
                    <p style='margin: 8px 0;'><strong>📌 Konu:</strong> " . htmlspecialchars($subject ?: 'Konu belirtilmemiş') . "</p>
                    <p style='margin: 8px 0;'><strong>📅 Tarih:</strong> " . date('d.m.Y H:i:s') . "</p>
                </div>
                
                <h3 style='color: #333; margin: 25px 0 15px 0;'>💬 Mesaj İçeriği:</h3>
                <div style='background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #667eea;'>
                    <p style='margin: 0; line-height: 1.6; color: #555;'>" . nl2br(htmlspecialchars($message)) . "</p>
                </div>
                
                <div style='margin: 30px 0 20px 0; padding: 20px; background: #e8f2ff; border-radius: 8px; border: 1px solid #b8daff;'>
                    <p style='margin: 0; color: #0066cc; font-size: 14px;'>
                        <strong>💡 İpucu:</strong> Bu mesaja doğrudan yanıt vererek " . htmlspecialchars($name) . " ile iletişime geçebilirsiniz.
                    </p>
                </div>
                
                <div style='text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;'>
                    <a href='mailto:" . htmlspecialchars($email) . "?subject=Re: " . htmlspecialchars($subject ?: 'İletişim Mesajınız') . "' 
                       style='display: inline-block; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
                              color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; 
                              font-weight: bold; margin: 5px;'>
                        📧 Yanıtla
                    </a>
                    <a href='" . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . "://" . $_SERVER['HTTP_HOST'] . "/admin/messages.php' 
                       style='display: inline-block; background: #28a745; color: white; padding: 12px 30px; 
                              text-decoration: none; border-radius: 6px; font-weight: bold; margin: 5px;'>
                        🔗 Admin Panel
                    </a>
                </div>
            </div>
            
            <div style='text-align: center; margin-top: 20px; color: #999; font-size: 12px;'>
                <p>Bu e-posta " . $site_brand . " portfolio website iletişim formu tarafından otomatik olarak gönderilmiştir.</p>
            </div>
        </div>";
        
        $mail->AltBody = "
Yeni İletişim Mesajı - " . $site_brand . "

Gönderen: " . $name . "
E-posta: " . $email . "
Konu: " . ($subject ?: 'Konu belirtilmemiş') . "
Tarih: " . date('d.m.Y H:i:s') . "

Mesaj:
" . $message . "

---
Bu e-posta " . $site_brand . " portfolio website iletişim formu tarafından otomatik olarak gönderilmiştir.
        ";
        
        $mail->send();
        return true;
        
    } catch (Exception $e) {
        error_log("Contact notification failed: " . $mail->ErrorInfo . " - " . $e->getMessage());
        return false;
    }
}

/**
 * Content Management Functions
 * Manages all site contents from database
 */

// Bulk content loader - loads multiple content keys in one query
function loadBulkContent($keys = []) {
    static $bulk_cache = [];
    
    if (empty($keys)) {
        return [];
    }
    
    global $pdo;
    if (!$pdo) {
        try {
            require_once __DIR__ . '/../config/database.php';
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Filter already cached keys
    $missing_keys = array_diff($keys, array_keys($bulk_cache));
    
    if (!empty($missing_keys)) {
        try {
            $placeholders = str_repeat('?,', count($missing_keys) - 1) . '?';
            $stmt = $pdo->prepare("SELECT content_key, content_text FROM site_contents WHERE content_key IN ($placeholders) AND is_active = 1");
            $stmt->execute($missing_keys);
            $results = $stmt->fetchAll();
            
            foreach ($results as $row) {
                $bulk_cache[$row['content_key']] = $row['content_text'];
            }
            
            // Cache missing keys as empty
            foreach ($missing_keys as $key) {
                if (!isset($bulk_cache[$key])) {
                    $bulk_cache[$key] = '';
                }
            }
        } catch(PDOException $e) {
            // Return empty array on error
            return [];
        }
    }
    
    $result = [];
    foreach ($keys as $key) {
        $result[$key] = $bulk_cache[$key] ?? '';
    }
    
    return $result;
}

// Bulk settings loader
function loadBulkSettings($keys = []) {
    static $settings_cache = [];
    
    if (empty($keys)) {
        return [];
    }
    
    global $pdo;
    if (!$pdo) {
        try {
            require_once __DIR__ . '/../config/database.php';
        } catch (Exception $e) {
            return [];
        }
    }
    
    // Filter already cached keys
    $missing_keys = array_diff($keys, array_keys($settings_cache));
    
    if (!empty($missing_keys)) {
        try {
            $placeholders = str_repeat('?,', count($missing_keys) - 1) . '?';
            $stmt = $pdo->prepare("SELECT setting_key, setting_value FROM settings WHERE setting_key IN ($placeholders)");
            $stmt->execute($missing_keys);
            $results = $stmt->fetchAll();
            
            foreach ($results as $row) {
                $settings_cache[$row['setting_key']] = $row['setting_value'];
            }
            
            // Cache missing keys as empty
            foreach ($missing_keys as $key) {
                if (!isset($settings_cache[$key])) {
                    $settings_cache[$key] = '';
                }
            }
        } catch(PDOException $e) {
            return [];
        }
    }
    
    $result = [];
    foreach ($keys as $key) {
        $result[$key] = $settings_cache[$key] ?? '';
    }
    
    return $result;
}

// Get content by key
function getContent($key, $default = '') {
    static $cache = [];
    
    // Cache kontrolü - sonsuz döngü önleme
    if (isset($cache[$key])) {
        return $cache[$key];
    }
    
    global $pdo;
    if (!$pdo) {
        try {
            require_once __DIR__ . '/../config/database.php';
        } catch (Exception $e) {
            $cache[$key] = $default;
            return $default;
        }
    }
    
    try {
        $stmt = $pdo->prepare("SELECT content_text FROM site_contents WHERE content_key = ? AND is_active = 1");
        $stmt->execute([$key]);
        $result = $stmt->fetchColumn();
        $cache[$key] = $result !== false ? $result : $default;
        return $cache[$key];
    } catch(PDOException $e) {
        $cache[$key] = $default;
        return $default;
    }
}

// Get content with variable replacement (like getSettingWithVariables)
function getContentWithVariables($key, $default = '') {
    static $var_cache = [];
    
    if (isset($var_cache[$key])) {
        return $var_cache[$key];
    }
    
    $content = getContent($key, $default);
    
    // Replace variables with actual settings
    $variables = [
        '{site_brand}' => getSetting('site_brand', 'BERAT K - R10'),
        '{site_title}' => getSetting('site_title', 'BERAT K - R10'),
        '{site_email}' => getSetting('contact_email', ''),
        '{site_phone}' => getSetting('contact_phone', ''),
        '{current_year}' => date('Y')
    ];
    
    $var_cache[$key] = str_replace(array_keys($variables), array_values($variables), $content);
    return $var_cache[$key];
}

// Get all contents for a specific page
function getPageContents($page_location) {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        $stmt = $pdo->prepare("SELECT * FROM site_contents WHERE page_location = ? AND is_active = 1 ORDER BY sort_order, content_title");
        $stmt->execute([$page_location]);
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

// Get all site contents for admin
function getAllContents() {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        $stmt = $pdo->query("SELECT * FROM site_contents ORDER BY page_location, sort_order, content_title");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

// Update content
function updateContent($id, $content_text) {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        $stmt = $pdo->prepare("UPDATE site_contents SET content_text = ?, updated_at = CURRENT_TIMESTAMP WHERE id = ?");
        return $stmt->execute([$content_text, $id]);
    } catch(PDOException $e) {
        return false;
    }
}

// Get footer links by section
function getFooterLinks($section = null) {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        if ($section) {
            $stmt = $pdo->prepare("SELECT * FROM footer_links WHERE link_section = ? AND is_active = 1 ORDER BY sort_order");
            $stmt->execute([$section]);
        } else {
            $stmt = $pdo->query("SELECT * FROM footer_links WHERE is_active = 1 ORDER BY link_section, sort_order");
        }
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

// Get all footer links for admin
function getAllFooterLinks() {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        $stmt = $pdo->query("SELECT * FROM footer_links ORDER BY link_section, sort_order");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

// Update footer link
function updateFooterLink($id, $title, $url, $section, $sort_order) {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        $stmt = $pdo->prepare("UPDATE footer_links SET link_title = ?, link_url = ?, link_section = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$title, $url, $section, $sort_order, $id]);
    } catch(PDOException $e) {
        return false;
    }
}

// Add new footer link
function addFooterLink($title, $url, $section, $sort_order = 0) {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        $stmt = $pdo->prepare("INSERT INTO footer_links (link_title, link_url, link_section, sort_order) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$title, $url, $section, $sort_order]);
    } catch(PDOException $e) {
        return false;
    }
}

// Delete footer link
function deleteFooterLink($id) {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        $stmt = $pdo->prepare("DELETE FROM footer_links WHERE id = ?");
        return $stmt->execute([$id]);
    } catch(PDOException $e) {
        return false;
    }
}

// Gallery Management Functions
function getAllGalleryItems() {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        $stmt = $pdo->query("SELECT * FROM gallery ORDER BY sort_order ASC, created_at DESC");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

function addGalleryItem($title, $description, $type, $file_path, $youtube_url, $sort_order = 0) {
    global $pdo;
    if (!$pdo) {
        try {
            require_once __DIR__ . '/../config/database.php';
        } catch (Exception $e) {
            return false;
        }
    }
    
    try {
        // Önce tablo var mı kontrol et
        $pdo->query("SELECT 1 FROM gallery LIMIT 1");
        
        $stmt = $pdo->prepare("INSERT INTO gallery (title, description, type, file_path, youtube_url, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([$title, $description, $type, $file_path, $youtube_url, $sort_order]);
    } catch(PDOException $e) {
        // Eğer tablo yoksa oluştur
        if (strpos($e->getMessage(), 'no such table') !== false) {
            try {
                $sql = "CREATE TABLE IF NOT EXISTS gallery (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    title VARCHAR(255) NOT NULL,
                    description TEXT,
                    type VARCHAR(20) NOT NULL DEFAULT 'image',
                    file_path VARCHAR(500),
                    youtube_url VARCHAR(500),
                    thumbnail VARCHAR(500),
                    sort_order INTEGER DEFAULT 0,
                    is_active INTEGER DEFAULT 1,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                )";
                $pdo->exec($sql);
                
                // Tabloyu oluşturduktan sonra tekrar dene
                $stmt = $pdo->prepare("INSERT INTO gallery (title, description, type, file_path, youtube_url, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
                return $stmt->execute([$title, $description, $type, $file_path, $youtube_url, $sort_order]);
            } catch (Exception $e2) {
                return false;
            }
        }
        return false;
    }
}

function updateGalleryItem($id, $title, $description, $type, $youtube_url, $sort_order) {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        $stmt = $pdo->prepare("UPDATE gallery SET title = ?, description = ?, type = ?, youtube_url = ?, sort_order = ? WHERE id = ?");
        return $stmt->execute([$title, $description, $type, $youtube_url, $sort_order, $id]);
    } catch(PDOException $e) {
        return false;
    }
}

function deleteGalleryItem($id) {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        // Önce dosya yolunu al
        $stmt = $pdo->prepare("SELECT file_path FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        $item = $stmt->fetch();
        
        // Dosyayı sil
        if ($item && !empty($item['file_path']) && file_exists($item['file_path'])) {
            unlink($item['file_path']);
        }
        
        // Veritabanından sil
        $stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        return $stmt->execute([$id]);
    } catch(PDOException $e) {
        return false;
    }
}

function toggleGalleryStatus($id, $status) {
    global $pdo;
    if (!$pdo) {
        require_once __DIR__ . '/../config/database.php';
    }
    try {
        $stmt = $pdo->prepare("UPDATE gallery SET is_active = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    } catch(PDOException $e) {
        return false;
    }
}

// Add new site content
function addSiteContent($key, $title, $text, $type = 'text', $location = 'general') {
    try {
        require_once __DIR__ . '/../config/database.php';
        $stmt = $pdo->prepare("INSERT INTO site_contents (content_key, content_title, content_text, content_type, page_location) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$key, $title, $text, $type, $location]);
    } catch(PDOException $e) {
        return false;
    }
}

// Delete site content
function deleteSiteContent($id) {
    try {
        require_once __DIR__ . '/../config/database.php';
        $stmt = $pdo->prepare("DELETE FROM site_contents WHERE id = ?");
        return $stmt->execute([$id]);
    } catch(PDOException $e) {
        return false;
    }
}

?>