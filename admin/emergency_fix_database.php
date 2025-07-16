<?php
/**
 * ACİL DURUM: TAM VERİTABANI ONARIMI
 * Developer: BERAT K - R10
 * 
 * SORUN: content_sections ve text_categories tabloları yok
 * ÇÖZÜM: Tüm eksik tabloları oluştur ve doldur
 */

require_once '../includes/functions.php';
requireLogin();

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>🚨 ACİL DURUM TAMİR - BERAT K</title>";
echo "<style>body{background:#000;color:#0f0;font-family:monospace;padding:20px;}";
echo ".success{color:#0f0;}.error{color:#f00;}.warning{color:#ff0;}</style></head><body>";

echo "<h1>🚨 ACİL DURUM VERİTABANI TAMİRİ</h1>";
echo "<p>Developer: <strong>BERAT K - R10</strong></p>";
echo "<hr>";

try {
    // 1. Eksik tabloları kontrol et
    echo "<h2>1️⃣ Mevcut Tablolar Kontrol Ediliyor...</h2>";
    
    $tables_to_check = ['content_sections', 'text_categories', 'site_texts'];
    $missing_tables = [];
    
    foreach ($tables_to_check as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) FROM $table");
            echo "<span class='success'>✅ $table tablosu mevcut</span><br>";
        } catch (PDOException $e) {
            echo "<span class='error'>❌ $table tablosu YOK!</span><br>";
            $missing_tables[] = $table;
        }
    }
    
    if (empty($missing_tables)) {
        echo "<h2 class='success'>🎉 TÜM TABLOLAR MEVCUT!</h2>";
        echo "<p><a href='content-ordering.php'>İçerik Sıralama Test Et</a> | ";
        echo "<a href='text-management.php'>Metin Yönetimi Test Et</a></p>";
        exit;
    }
    
    echo "<h2>2️⃣ Eksik Tablolar Oluşturuluyor...</h2>";
    
    // 2. content_sections tablosu oluştur - Developer: BERAT K
    if (in_array('content_sections', $missing_tables)) {
        echo "<h3>📋 content_sections tablosu oluşturuluyor...</h3>";
        
        $sql = "CREATE TABLE IF NOT EXISTS content_sections (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            section_key TEXT NOT NULL UNIQUE,
            section_name TEXT NOT NULL,
            section_title TEXT,
            section_description TEXT,
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            is_visible INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        echo "<span class='success'>✅ content_sections tablosu oluşturuldu</span><br>";
        
        // Gerçek bölümleri ekle
        $sections = [
            ['hero_section', 'Ana Banner (Hero)', 'Ana Banner Bölümü', 'Anasayfa üst banner alanı', 1],
            ['stats_section', 'İstatistikler', 'Başarı İstatistikleri', 'Proje, müşteri sayıları', 2],
            ['services_section', 'Hizmetler', 'Platform Hizmetlerim', 'Sunduğunuz hizmetler', 3],
            ['projects_section', 'Platformlar', 'Platformlarım', 'Kumar platformları ve projeler', 4],
            ['products_section', 'Premium Ürünler', 'Premium Ürünlerim', 'Premium ürünler', 5],
            ['blog_section', 'Platform Haberleri', 'Platform Haberleri', 'Blog yazıları', 6],
            ['why_choose_section', 'Neden Biz', 'Neden BERAT K Platformları?', 'Avantajlar', 7],
            ['contact_section', 'İletişim', 'İş Birliği İçin İletişim', 'İletişim formu', 8]
        ];
        
        foreach ($sections as $section) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO content_sections (section_key, section_name, section_title, section_description, sort_order, is_active, is_visible) VALUES (?, ?, ?, ?, ?, 1, 1)");
            $stmt->execute($section);
        }
        
        echo "<span class='success'>✅ " . count($sections) . " bölüm eklendi</span><br>";
    }
    
    // 3. text_categories tablosu oluştur - Developer: BERAT K
    if (in_array('text_categories', $missing_tables)) {
        echo "<h3>📋 text_categories tablosu oluşturuluyor...</h3>";
        
        $sql = "CREATE TABLE IF NOT EXISTS text_categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_name TEXT NOT NULL,
            category_description TEXT,
            icon TEXT DEFAULT 'fas fa-font',
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )";
        
        $pdo->exec($sql);
        echo "<span class='success'>✅ text_categories tablosu oluşturuldu</span><br>";
        
        // Kategorileri ekle
        $categories = [
            ['Ana Sayfa', 'Ana sayfa metinleri', 'fas fa-home', 1],
            ['Hakkımda', 'Hakkımda sayfası metinleri', 'fas fa-user', 2],
            ['Hizmetler', 'Hizmetler sayfası metinleri', 'fas fa-cogs', 3],
            ['Portföy', 'Portföy sayfası metinleri', 'fas fa-briefcase', 4],
            ['İletişim', 'İletişim sayfası metinleri', 'fas fa-envelope', 5],
            ['Blog', 'Blog sayfası metinleri', 'fas fa-blog', 6],
            ['Genel', 'Genel site metinleri', 'fas fa-globe', 7],
            ['Footer', 'Footer metinleri', 'fas fa-footer', 8]
        ];
        
        foreach ($categories as $category) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO text_categories (category_name, category_description, icon, sort_order) VALUES (?, ?, ?, ?)");
            $stmt->execute($category);
        }
        
        echo "<span class='success'>✅ " . count($categories) . " kategori eklendi</span><br>";
    }
    
    // 4. site_texts tablosu oluştur - Developer: BERAT K  
    if (in_array('site_texts', $missing_tables)) {
        echo "<h3>📋 site_texts tablosu oluşturuluyor...</h3>";
        
        $sql = "CREATE TABLE IF NOT EXISTS site_texts (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            category_id INTEGER,
            text_key TEXT NOT NULL UNIQUE,
            text_label TEXT NOT NULL,
            text_value TEXT,
            text_type TEXT DEFAULT 'text',
            page_location TEXT,
            is_active INTEGER DEFAULT 1,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (category_id) REFERENCES text_categories(id)
        )";
        
        $pdo->exec($sql);
        echo "<span class='success'>✅ site_texts tablosu oluşturuldu</span><br>";
        
        // Mevcut settings verilerini aktar
        echo "<h4>⚠️ Mevcut settings verilerini aktarıyorum...</h4>";
        
        try {
            $existing_settings = $pdo->query("SELECT * FROM settings")->fetchAll();
            
            foreach ($existing_settings as $setting) {
                $setting_key = $setting['setting_key'];
                $setting_value = $setting['setting_value'];
                
                // Kategori belirle
                $category_id = 1; // Default: homepage
                $page_location = 'Ana Sayfa';
                $text_type = 'text';
                
                if (strpos($setting_key, 'hero_') === 0) {
                    $category_id = 1;
                    $page_location = 'Ana Sayfa Hero';
                    if ($setting_key == 'hero_description') $text_type = 'textarea';
                } elseif (strpos($setting_key, 'contact_') === 0) {
                    $category_id = 5;
                    $page_location = 'İletişim Sayfası';
                    if (strpos($setting_key, 'email') !== false) $text_type = 'email';
                } elseif (strpos($setting_key, 'footer_') === 0) {
                    $category_id = 8;
                    $page_location = 'Footer';
                } elseif (strpos($setting_key, 'site_') === 0) {
                    $category_id = 7;
                    $page_location = 'Genel Ayarlar';
                }
                
                $text_label = ucfirst(str_replace('_', ' ', $setting_key));
                
                $stmt = $pdo->prepare("INSERT OR IGNORE INTO site_texts (category_id, text_key, text_label, text_value, text_type, page_location) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$category_id, $setting_key, $text_label, $setting_value, $text_type, $page_location]);
            }
            
            echo "<span class='success'>✅ " . count($existing_settings) . " ayar aktarıldı</span><br>";
        } catch (Exception $e) {
            echo "<span class='warning'>⚠️ Settings aktarılırken hata: " . $e->getMessage() . "</span><br>";
        }
        
        // FAQ metinlerini ekle
        echo "<h4>❓ FAQ metinleri ekleniyor...</h4>";
        
        $faq_texts = [
            [5, 'contact_intro', 'İletişim Sayfası Giriş Metni', 'Kumar endüstrisinde iş birliği fırsatları için benimle iletişime geçin. BERAT K - R10 olarak güvenli ve karlı platformlar kurmanızda size yardımcı olmak için buradayım.', 'textarea', 'İletişim Sayfası'],
            [5, 'faq_title', 'SSS Başlığı', 'Sıkça Sorulan Sorular', 'text', 'İletişim Sayfası'],
            [5, 'faq_q1', 'FAQ Soru 1', 'Proje süreci nasıl işliyor?', 'text', 'İletişim Sayfası FAQ'],
            [5, 'faq_a1', 'FAQ Cevap 1', 'İlk olarak projenizi detaylı şekilde konuşuyoruz. Ardından teknik analiz yapıp size teklif sunuyorum. Onay sonrası tasarım ve geliştirme sürecine başlıyoruz. Her aşamada sizinle iletişim halindeyim.', 'textarea', 'İletişim Sayfası FAQ'],
            [5, 'faq_q2', 'FAQ Soru 2', 'Proje teslim süresi ne kadar?', 'text', 'İletişim Sayfası FAQ'],
            [5, 'faq_a2', 'FAQ Cevap 2', 'Proje karmaşıklığına göre değişmekle birlikte, basit web siteleri 1-2 hafta, e-ticaret siteleri 3-4 hafta, özel uygulamalar ise 6-8 hafta sürebilir.', 'textarea', 'İletişim Sayfası FAQ'],
            [5, 'faq_q3', 'FAQ Soru 3', 'Destek hizmeti veriyor musunuz?', 'text', 'İletişim Sayfası FAQ'],
            [5, 'faq_a3', 'FAQ Cevap 3', 'Evet! Tüm projelerimde 6 ay ücretsiz teknik destek veriyorum. Bu süre sonrasında uygun fiyatlarla destek hizmeti devam ediyor.', 'textarea', 'İletişim Sayfası FAQ'],
            [5, 'faq_q4', 'FAQ Soru 4', 'Hangi teknolojileri kullanıyorsunuz?', 'text', 'İletişim Sayfası FAQ'],
            [5, 'faq_a4', 'FAQ Cevap 4', 'HTML5, CSS3, JavaScript, PHP, React, Vue.js, Laravel, Node.js gibi modern teknolojileri kullanıyorum. Projenizin ihtiyacına göre en uygun teknoloji stack\'ini seçiyoruz.', 'textarea', 'İletişim Sayfası FAQ'],
            [5, 'cta_text', 'CTA Metni', 'Kumar endüstrisinde birlikte büyümek için benimle iletişime geç. {site_brand} ile güvenli ve karlı platformlar kuralım.', 'textarea', 'İletişim Sayfası'],
        ];
        
        foreach ($faq_texts as $text) {
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO site_texts (category_id, text_key, text_label, text_value, text_type, page_location) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute($text);
        }
        
        echo "<span class='success'>✅ " . count($faq_texts) . " FAQ metni eklendi</span><br>";
    }
    
    echo "<h2 class='success'>🎉 VERİTABANI TAMİRİ TAMAMLANDI!</h2>";
    echo "<hr>";
    
    echo "<h3>✅ BAŞARIYLA OLUŞTURULAN TABLOLAR:</h3>";
    foreach ($missing_tables as $table) {
        echo "<span class='success'>✅ $table</span><br>";
    }
    
    echo "<h3>🚀 ŞİMDİ TEST EDİN:</h3>";
    echo "<p><a href='content-ordering.php' style='color:#0f0;'>İçerik Sıralama Test Et</a></p>";
    echo "<p><a href='text-management.php' style='color:#0f0;'>Metin Yönetimi Test Et</a></p>";
    echo "<p><a href='../index.php' target='_blank' style='color:#0f0;'>Anasayfayı Görüntüle</a></p>";
    
    echo "<hr>";
    echo "<p><strong>Developer: BERAT K - R10</strong> - Sorunlarınız çözüldü! ✅</p>";
    
} catch (Exception $e) {
    echo "<h2 class='error'>❌ HATA OLUŞTU!</h2>";
    echo "<p class='error'>Hata: " . $e->getMessage() . "</p>";
    echo "<p class='warning'>Admin ile iletişime geçin.</p>";
}

echo "</body></html>";
?>