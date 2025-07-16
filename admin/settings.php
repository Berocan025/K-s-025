<?php
/**
 * Admin Settings Management
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$page_title = 'Ayarlar';

$success_message = '';
$error_message = '';

// Success mesajını kontrol et (redirect'den geliyorsa)
if (isset($_GET['success']) && $_GET['success'] === 'password_changed') {
    $success_message = '✅ Şifreniz başarıyla değiştirildi! Yeni şifrenizle giriş yapabilirsiniz.';
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Genel Ayarlar
    if (isset($_POST['update_general_settings'])) {
        try {
            if (isset($_POST['site_title'])) {
                setSetting('site_title', clean($_POST['site_title']));
            }
            if (isset($_POST['site_description'])) {
                setSetting('site_description', clean($_POST['site_description']));
            }
            if (isset($_POST['site_keywords'])) {
                setSetting('site_keywords', clean($_POST['site_keywords']));
            }
            $success_message = 'Genel ayarlar başarıyla güncellendi.';
        } catch(PDOException $e) {
            $error_message = 'Genel ayarlar güncellenirken hata oluştu.';
        }
    }
    // İçerik Ayarları
    elseif (isset($_POST['update_content_settings'])) {
        try {
            if (isset($_POST['hero_title'])) {
                setSetting('hero_title', clean($_POST['hero_title']));
            }
            if (isset($_POST['hero_subtitle'])) {
                setSetting('hero_subtitle', clean($_POST['hero_subtitle']));
            }
            if (isset($_POST['hero_description'])) {
                setSetting('hero_description', clean($_POST['hero_description']));
            }
            if (isset($_POST['about_title'])) {
                setSetting('about_title', clean($_POST['about_title']));
            }
            if (isset($_POST['about_description'])) {
                setSetting('about_description', clean($_POST['about_description']));
            }
            $success_message = 'İçerik ayarları başarıyla güncellendi.';
        } catch(PDOException $e) {
            $error_message = 'İçerik ayarları güncellenirken hata oluştu.';
        }
    }
    // İletişim Ayarları
    elseif (isset($_POST['update_contact_settings'])) {
        try {
            if (isset($_POST['contact_email'])) {
                setSetting('contact_email', clean($_POST['contact_email']));
            }
            if (isset($_POST['contact_phone'])) {
                setSetting('contact_phone', clean($_POST['contact_phone']));
            }
            if (isset($_POST['contact_address'])) {
                setSetting('contact_address', clean($_POST['contact_address']));
            }
            if (isset($_POST['whatsapp_phone'])) {
                setSetting('whatsapp_phone', clean($_POST['whatsapp_phone']));
            }
            $success_message = 'İletişim ayarları başarıyla güncellendi.';
        } catch(PDOException $e) {
            $error_message = 'İletişim ayarları güncellenirken hata oluştu.';
        }
    }
    // Sosyal Medya Ayarları
    elseif (isset($_POST['update_social_settings'])) {
        try {
            if (isset($_POST['social_github'])) {
                setSetting('social_github', clean($_POST['social_github']));
            }
            if (isset($_POST['social_linkedin'])) {
                setSetting('social_linkedin', clean($_POST['social_linkedin']));
            }
            if (isset($_POST['social_twitter'])) {
                setSetting('social_twitter', clean($_POST['social_twitter']));
            }
            if (isset($_POST['social_instagram'])) {
                setSetting('social_instagram', clean($_POST['social_instagram']));
            }
            $success_message = 'Sosyal medya ayarları başarıyla güncellendi.';
        } catch(PDOException $e) {
            $error_message = 'Sosyal medya ayarları güncellenirken hata oluştu.';
        }
    }
    // İstatistik Ayarları
    elseif (isset($_POST['update_stats_settings'])) {
        try {
            if (isset($_POST['stat_projects'])) {
                setSetting('stat_projects', clean($_POST['stat_projects']));
            }
            if (isset($_POST['stat_clients'])) {
                setSetting('stat_clients', clean($_POST['stat_clients']));
            }
            if (isset($_POST['stat_years'])) {
                setSetting('stat_years', clean($_POST['stat_years']));
            }
            if (isset($_POST['stat_awards'])) {
                setSetting('stat_awards', clean($_POST['stat_awards']));
            }
            $success_message = 'İstatistik ayarları başarıyla güncellendi.';
        } catch(PDOException $e) {
            $error_message = 'İstatistik ayarları güncellenirken hata oluştu.';
        }
    }
    // Site Metinleri Ayarları
    elseif (isset($_POST['update_text_settings'])) {
        try {
            if (isset($_POST['site_brand'])) {
                setSetting('site_brand', clean($_POST['site_brand']));
            }
            if (isset($_POST['site_slogan'])) {
                setSetting('site_slogan', clean($_POST['site_slogan']));
            }
            if (isset($_POST['footer_text'])) {
                setSetting('footer_text', clean($_POST['footer_text']));
            }

            $success_message = 'Site metinleri başarıyla güncellendi.';
        } catch(PDOException $e) {
            $error_message = 'Site metinleri güncellenirken hata oluştu.';
        }
    }
    elseif (isset($_POST['update_visual_settings'])) {
        // Bakım modu ayarı
        $maintenance_mode = isset($_POST['maintenance_mode']) ? '1' : '0';
        setSetting('maintenance_mode', $maintenance_mode);
        
        // Favicon upload
        if (isset($_FILES['site_favicon']) && $_FILES['site_favicon']['error'] == 0) {
            $allowed_types = ['image/x-icon', 'image/vnd.microsoft.icon', 'image/png', 'image/jpeg'];
            if (in_array($_FILES['site_favicon']['type'], $allowed_types)) {
                $favicon_dir = '../uploads/favicon/';
                if (!file_exists($favicon_dir)) {
                    mkdir($favicon_dir, 0777, true);
                }
                
                $favicon_filename = 'favicon.' . pathinfo($_FILES['site_favicon']['name'], PATHINFO_EXTENSION);
                $favicon_path = $favicon_dir . $favicon_filename;
                
                if (move_uploaded_file($_FILES['site_favicon']['tmp_name'], $favicon_path)) {
                    setSetting('site_favicon', 'uploads/favicon/' . $favicon_filename);
                }
            }
        }
        
        // Logo upload
        if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] == 0) {
            $allowed_types = ['image/png', 'image/jpeg', 'image/jpg', 'image/svg+xml'];
            if (in_array($_FILES['site_logo']['type'], $allowed_types)) {
                $logo_dir = '../uploads/logo/';
                if (!file_exists($logo_dir)) {
                    mkdir($logo_dir, 0777, true);
                }
                
                $logo_filename = 'logo.' . pathinfo($_FILES['site_logo']['name'], PATHINFO_EXTENSION);
                $logo_path = $logo_dir . $logo_filename;
                
                if (move_uploaded_file($_FILES['site_logo']['tmp_name'], $logo_path)) {
                    setSetting('site_logo', 'uploads/logo/' . $logo_filename);
                }
            }
        }
        
        $success_message = 'Görsel ayarlar başarıyla güncellendi.';
    } elseif (isset($_POST['change_password'])) {
        $current_password = trim($_POST['current_password']);
        $new_password = trim($_POST['new_password']);
        $confirm_password = trim($_POST['confirm_password']);
        
        // Debug için log
        error_log("Password change attempt for user ID: " . ($_SESSION['admin_id'] ?? 'unknown'));
        
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $error_message = 'Tüm şifre alanları doldurulmalıdır.';
        } elseif ($new_password !== $confirm_password) {
            $error_message = 'Yeni şifreler eşleşmiyor.';
        } elseif (strlen($new_password) < 6) {
            $error_message = 'Yeni şifre en az 6 karakter olmalıdır.';
        } else {
            try {
                // Admin kullanıcıyı bul
                $stmt = $pdo->prepare("SELECT id, username, password FROM admin_users WHERE id = ?");
                $stmt->execute([$_SESSION['admin_id']]);
                $admin = $stmt->fetch();
                
                if (!$admin) {
                    $error_message = 'Kullanıcı bulunamadı.';
                    error_log("Admin user not found for ID: " . $_SESSION['admin_id']);
                } else {
                    error_log("Found admin user: " . $admin['username']);
                    
                    // Mevcut şifre doğru mu kontrol et
                    if (password_verify($current_password, $admin['password'])) {
                        // Yeni şifreyi hash'le
                        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                        
                        // Şifreyi güncelle
                        $update_stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                        
                        if ($update_stmt->execute([$hashed_password, $_SESSION['admin_id']])) {
                            $success_message = '✅ Şifreniz başarıyla değiştirildi! Yeni şifrenizle giriş yapabilirsiniz.';
                            error_log("Password successfully changed for user: " . $admin['username']);
                            
                            // Formu temizlemek için redirect
                            header("Location: " . $_SERVER['PHP_SELF'] . "?success=password_changed#nav-security");
                            exit;
                        } else {
                            $error_message = 'Şifre güncellenirken bir hata oluştu.';
                            error_log("Failed to update password in database");
                        }
                    } else {
                        $error_message = 'Mevcut şifreniz yanlış. Lütfen doğru şifreyi girin.';
                        error_log("Current password verification failed");
                    }
                }
            } catch(PDOException $e) {
                $error_message = 'Veritabanı hatası: ' . $e->getMessage();
                error_log("Database error during password change: " . $e->getMessage());
            }
        }
    }
    // Tema ve Görünüm Ayarları
    elseif (isset($_POST['update_theme_settings'])) {
        try {
            if (isset($_POST['primary_color'])) {
                setSetting('primary_color', clean($_POST['primary_color']));
            }
            if (isset($_POST['secondary_color'])) {
                setSetting('secondary_color', clean($_POST['secondary_color']));
            }
            if (isset($_POST['accent_color'])) {
                setSetting('accent_color', clean($_POST['accent_color']));
            }
            if (isset($_POST['dark_mode'])) {
                setSetting('dark_mode', '1');
            } else {
                setSetting('dark_mode', '0');
            }
            if (isset($_POST['font_family'])) {
                setSetting('font_family', clean($_POST['font_family']));
            }
            if (isset($_POST['custom_css'])) {
                setSetting('custom_css', $_POST['custom_css']); // CSS'te clean kullanmayız
            }
            if (isset($_POST['custom_js'])) {
                setSetting('custom_js', $_POST['custom_js']); // JS'te clean kullanmayız
            }
            $success_message = 'Tema ayarları başarıyla güncellendi.';
        } catch(PDOException $e) {
            $error_message = 'Tema ayarları güncellenirken hata oluştu.';
        }
    }
    // SEO ve Arama Motoru Ayarları
    elseif (isset($_POST['update_seo_settings'])) {
        try {
            if (isset($_POST['google_analytics'])) {
                setSetting('google_analytics', clean($_POST['google_analytics']));
            }
            if (isset($_POST['google_search_console'])) {
                setSetting('google_search_console', clean($_POST['google_search_console']));
            }
            if (isset($_POST['robots_txt_content'])) {
                setSetting('robots_txt_content', $_POST['robots_txt_content']);
            }
            if (isset($_POST['meta_robots'])) {
                setSetting('meta_robots', clean($_POST['meta_robots']));
            }
            if (isset($_POST['canonical_url'])) {
                setSetting('canonical_url', clean($_POST['canonical_url']));
            }
            
            // Sitemap ve robots.txt otomatik güncelle
            generateSitemap();
            generateRobotsTxt();
            
            $success_message = 'SEO ayarları başarıyla güncellendi ve sitemap.xml/robots.txt dosyaları yenilendi.';
        } catch(Exception $e) {
            $error_message = 'SEO ayarları güncellenirken hata oluştu.';
        }
    }
    // Sitemap ve Robots.txt Oluştur
    elseif (isset($_POST['generate_files'])) {
        try {
            $sitemap_success = generateSitemap();
            $robots_success = generateRobotsTxt();
            
            if ($sitemap_success && $robots_success) {
                $success_message = 'Sitemap.xml ve robots.txt dosyaları başarıyla oluşturuldu!';
            } elseif ($sitemap_success) {
                $success_message = 'Sitemap.xml oluşturuldu, ancak robots.txt oluşturulurken hata oluştu.';
            } elseif ($robots_success) {
                $success_message = 'Robots.txt oluşturuldu, ancak sitemap.xml oluşturulurken hata oluştu.';
            } else {
                $error_message = 'Dosyalar oluşturulurken hata oluştu.';
            }
        } catch(Exception $e) {
            $error_message = 'Dosya oluşturma işlemi başarısız oldu.';
        }
    }
    // E-posta Sistemi Ayarları
    elseif (isset($_POST['update_email_settings'])) {
        try {
            // SMTP Ayarları
            if (isset($_POST['smtp_host'])) {
                setSetting('smtp_host', clean($_POST['smtp_host']));
            }
            if (isset($_POST['smtp_port'])) {
                setSetting('smtp_port', clean($_POST['smtp_port']));
            }
            if (isset($_POST['smtp_security'])) {
                setSetting('smtp_security', clean($_POST['smtp_security']));
            }
            if (isset($_POST['smtp_username'])) {
                setSetting('smtp_username', clean($_POST['smtp_username']));
            }
            if (isset($_POST['smtp_password'])) {
                setSetting('smtp_password', $_POST['smtp_password']); // Şifreyi encode etmiyoruz
            }
            
            // Newsletter Ayarları
            if (isset($_POST['newsletter_title'])) {
                setSetting('newsletter_title', clean($_POST['newsletter_title']));
            }
            if (isset($_POST['newsletter_description'])) {
                setSetting('newsletter_description', clean($_POST['newsletter_description']));
            }
            $newsletter_enabled = isset($_POST['newsletter_enabled']) ? '1' : '0';
            setSetting('newsletter_enabled', $newsletter_enabled);
            
            // Otomatik Yanıt Ayarları
            if (isset($_POST['auto_reply_subject'])) {
                setSetting('auto_reply_subject', clean($_POST['auto_reply_subject']));
            }
            if (isset($_POST['auto_reply_message'])) {
                setSetting('auto_reply_message', $_POST['auto_reply_message']); // Mesaj içeriği clean edilmez
            }
            $auto_reply_enabled = isset($_POST['auto_reply_enabled']) ? '1' : '0';
            setSetting('auto_reply_enabled', $auto_reply_enabled);
            
            $success_message = 'E-posta sistemi ayarları başarıyla güncellendi.';
        } catch(PDOException $e) {
            $error_message = 'E-posta ayarları güncellenirken hata oluştu.';
        }
    }
    // Performans Ayarları
    elseif (isset($_POST['update_performance_settings'])) {
        try {
            // Cache Ayarları
            $cache_enabled = isset($_POST['cache_enabled']) ? '1' : '0';
            setSetting('cache_enabled', $cache_enabled);
            if (isset($_POST['cache_duration'])) {
                setSetting('cache_duration', clean($_POST['cache_duration']));
            }
            
            // Görsel Optimizasyonu
            $image_compression = isset($_POST['image_compression']) ? '1' : '0';
            setSetting('image_compression', $image_compression);
            if (isset($_POST['image_quality'])) {
                setSetting('image_quality', clean($_POST['image_quality']));
            }
            
            // CSS/JS Optimizasyonu
            $css_minify = isset($_POST['css_minify']) ? '1' : '0';
            setSetting('css_minify', $css_minify);
            $js_minify = isset($_POST['js_minify']) ? '1' : '0';
            setSetting('js_minify', $js_minify);
            
            $success_message = 'Performans ayarları başarıyla güncellendi.';
        } catch(PDOException $e) {
            $error_message = 'Performans ayarları güncellenirken hata oluştu.';
        }
    }

}

// İstatistikler için varsayılan değerleri kontrol et ve ekle
$default_stats = [
    'stat_projects' => '150',
    'stat_clients' => '85', 
    'stat_years' => '5',
    'stat_awards' => '12'
];

foreach ($default_stats as $key => $default_value) {
    $current_value = getSetting($key, '');
    if (empty($current_value)) {
        setSetting($key, $default_value);
    }
}

$settings = [
    'site_title' => getSetting('site_title', 'BERAT K - R10'),
    'site_description' => getSetting('site_description', 'Full Stack Developer & UI/UX Designer'),
    'site_keywords' => getSetting('site_keywords', 'web development, php, javascript, react'),
    'hero_title' => getSetting('hero_title', 'BERAT K'),
    'hero_subtitle' => getSetting('hero_subtitle', 'R10'),
    'hero_description' => getSetting('hero_description', 'Full Stack Developer & UI/UX Designer olarak yaratıcı ve kullanıcı dostu dijital deneyimler tasarlıyorum.'),
    'about_title' => getSetting('about_title', 'Hakkımda'),
    'about_description' => getSetting('about_description', 'Yazılım geliştirme alanında uzmanlaşmış bir profesyonelim.'),
    'contact_email' => getSetting('contact_email', 'info@beratk.com'),
    'contact_phone' => getSetting('contact_phone', '+90 555 123 45 67'),
    'contact_address' => getSetting('contact_address', 'İstanbul, Türkiye'),
    'whatsapp_phone' => getSetting('whatsapp_phone', '05395115632'),
    'social_github' => getSetting('social_github', 'https://github.com/beratk'),
    'social_linkedin' => getSetting('social_linkedin', 'https://linkedin.com/in/beratk'),
    'social_twitter' => getSetting('social_twitter', 'https://twitter.com/beratk'),
    'social_instagram' => getSetting('social_instagram', 'https://instagram.com/beratk'),
    'stat_projects' => getSetting('stat_projects', '150'),
    'stat_clients' => getSetting('stat_clients', '85'),
    'stat_years' => getSetting('stat_years', '5'),
    'stat_awards' => getSetting('stat_awards', '12'),
    'site_favicon' => getSetting('site_favicon', 'assets/img/favicon.ico'),
    'site_logo' => getSetting('site_logo', ''),
    'maintenance_mode' => getSetting('maintenance_mode', '0'),
    'site_brand' => getSetting('site_brand', 'BERAT K - R10'),
    'site_slogan' => getSetting('site_slogan', 'Profesyonel Çözümler'),
    'footer_text' => getSetting('footer_text', 'Fikirlerinizi gerçeğe dönüştürmek için bir adım uzaktasınız. {site_brand} ile profesyonel çözümler keşfedin.'),

    'primary_color' => getSetting('primary_color', '#6c5ce7'),
    'secondary_color' => getSetting('secondary_color', '#fd79a8'),
    'accent_color' => getSetting('accent_color', '#74b9ff'),
    'dark_mode' => getSetting('dark_mode', '1'),
    'font_family' => getSetting('font_family', 'Poppins'),
    'custom_css' => getSetting('custom_css', ''),
    'custom_js' => getSetting('custom_js', ''),
    'google_analytics' => getSetting('google_analytics', ''),
    'google_search_console' => getSetting('google_search_console', ''),
    'robots_txt_content' => getSetting('robots_txt_content', ''),
    'meta_robots' => getSetting('meta_robots', 'index, follow'),
    'canonical_url' => getSetting('canonical_url', ''),
    'sitemap_last_update' => getSetting('sitemap_last_update', 'Henüz oluşturulmadı'),
    'robots_last_update' => getSetting('robots_last_update', 'Henüz oluşturulmadı'),
    
    // E-posta Sistemi Ayarları
    'smtp_host' => getSetting('smtp_host', 'mail.example.com'),
    'smtp_port' => getSetting('smtp_port', '587'),
    'smtp_security' => getSetting('smtp_security', 'tls'),
    'smtp_username' => getSetting('smtp_username', ''),
    'smtp_password' => getSetting('smtp_password', ''),
    'newsletter_title' => getSetting('newsletter_title', 'BERAT K - R10 Newsletter'),
    'newsletter_description' => getSetting('newsletter_description', 'En son projeler ve teknoloji haberleri'),
    'newsletter_enabled' => getSetting('newsletter_enabled', '1'),
    'auto_reply_subject' => getSetting('auto_reply_subject', 'Mesajınız alındı - BERAT K - R10'),
    'auto_reply_message' => getSetting('auto_reply_message', 'Merhaba,\n\nMesajınız başarıyla alınmıştır. En kısa sürede size geri dönüş yapacağım.\n\nTeşekkürler,\nBERAT K - R10'),
    'auto_reply_enabled' => getSetting('auto_reply_enabled', '1'),
    
    // Performans Ayarları
    'cache_enabled' => getSetting('cache_enabled', '1'),
    'cache_duration' => getSetting('cache_duration', '60'),
    'image_compression' => getSetting('image_compression', '1'),
    'image_quality' => getSetting('image_quality', '85'),
    'css_minify' => getSetting('css_minify', '1'),
    'js_minify' => getSetting('js_minify', '1'),

];
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">Ayarlar</h1>
    </div>
    
    <?php if ($success_message): ?>
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success']) && $_GET['success'] === 'password_changed'): ?>
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i>✅ Şifreniz başarıyla değiştirildi! Yeni şifrenizle giriş yapabilirsiniz.
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body">
                    <nav>
                        <div class="nav nav-tabs border-0 mb-4" id="nav-tab" role="tablist">
                            <button class="nav-link active" id="nav-general-tab" data-bs-toggle="tab" data-bs-target="#nav-general" type="button" role="tab">
                                <i class="fas fa-cog me-2"></i>Genel Ayarlar
                            </button>
                            <button class="nav-link" id="nav-content-tab" data-bs-toggle="tab" data-bs-target="#nav-content" type="button" role="tab">
                                <i class="fas fa-edit me-2"></i>İçerik Ayarları
                            </button>
                            <button class="nav-link" id="nav-contact-tab" data-bs-toggle="tab" data-bs-target="#nav-contact" type="button" role="tab">
                                <i class="fas fa-address-book me-2"></i>İletişim Bilgileri
                            </button>
                            <button class="nav-link" id="nav-social-tab" data-bs-toggle="tab" data-bs-target="#nav-social" type="button" role="tab">
                                <i class="fas fa-share-alt me-2"></i>Sosyal Medya
                            </button>
                            <button class="nav-link" id="nav-stats-tab" data-bs-toggle="tab" data-bs-target="#nav-stats" type="button" role="tab">
                                <i class="fas fa-chart-bar me-2"></i>İstatistikler
                            </button>
                            <button class="nav-link" id="nav-text-tab" data-bs-toggle="tab" data-bs-target="#nav-text" type="button" role="tab">
                                <i class="fas fa-font me-2"></i>Site Metinleri
                            </button>
                            <button class="nav-link" id="nav-theme-tab" data-bs-toggle="tab" data-bs-target="#nav-theme" type="button" role="tab">
                                <i class="fas fa-palette me-2"></i>Tema & Görünüm
                            </button>
                            <button class="nav-link" id="nav-seo-tab" data-bs-toggle="tab" data-bs-target="#nav-seo" type="button" role="tab">
                                <i class="fas fa-search me-2"></i>SEO & Arama
                            </button>
                            <button class="nav-link" id="nav-visual-tab" data-bs-toggle="tab" data-bs-target="#nav-visual" type="button" role="tab">
                                <i class="fas fa-images me-2"></i>Logo & Favicon
                            </button>
                            <button class="nav-link" id="nav-maintenance-tab" data-bs-toggle="tab" data-bs-target="#nav-maintenance" type="button" role="tab">
                                <i class="fas fa-tools me-2"></i>Bakım Modu
                            </button>
                            <button class="nav-link" id="nav-security-tab" data-bs-toggle="tab" data-bs-target="#nav-security" type="button" role="tab">
                                <i class="fas fa-shield-alt me-2"></i>Güvenlik
                            </button>
                            <button class="nav-link" id="nav-email-tab" data-bs-toggle="tab" data-bs-target="#nav-email" type="button" role="tab">
                                <i class="fas fa-envelope me-2"></i>E-posta Sistemi
                            </button>
                            <button class="nav-link" id="nav-performance-tab" data-bs-toggle="tab" data-bs-target="#nav-performance" type="button" role="tab">
                                <i class="fas fa-tachometer-alt me-2"></i>Performans
                            </button>

                        </div>
                    </nav>
                    
                    <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-general" role="tabpanel">
                                <form method="POST" id="generalForm">
                                    <h5 class="text-light mb-3">Genel Site Ayarları</h5>
                                    <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Site Başlığı</label>
                                        <input type="text" class="form-control" name="site_title" value="<?php echo htmlspecialchars($settings['site_title']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Site Açıklaması</label>
                                        <input type="text" class="form-control" name="site_description" value="<?php echo htmlspecialchars($settings['site_description']); ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-light">SEO Anahtar Kelimeleri</label>
                                    <input type="text" class="form-control" name="site_keywords" value="<?php echo htmlspecialchars($settings['site_keywords']); ?>">
                                    <small class="text-muted">Virgülle ayırarak yazın</small>
                                </div>
                                
                                <div class="text-end mt-4 pt-3 border-top border-secondary">
                                    <button type="reset" class="btn btn-outline-light me-2">
                                        <i class="fas fa-undo me-2"></i>Sıfırla
                                    </button>
                                    <button type="submit" name="update_general_settings" class="btn btn-gradient">
                                        <i class="fas fa-save me-2"></i>Genel Ayarları Kaydet
                                    </button>
                                </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nav-content" role="tabpanel">
                                <form method="POST" id="contentForm">
                                    <h5 class="text-light mb-3">Ana Sayfa İçerik Ayarları</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Ana Başlık</label>
                                        <input type="text" class="form-control" name="hero_title" value="<?php echo htmlspecialchars($settings['hero_title']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Alt Başlık</label>
                                        <input type="text" class="form-control" name="hero_subtitle" value="<?php echo htmlspecialchars($settings['hero_subtitle']); ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-light">Ana Açıklama</label>
                                    <textarea class="form-control" name="hero_description" rows="3"><?php echo htmlspecialchars($settings['hero_description']); ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Hakkımda Başlığı</label>
                                        <input type="text" class="form-control" name="about_title" value="<?php echo htmlspecialchars($settings['about_title']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Hakkımda Açıklaması</label>
                                        <textarea class="form-control" name="about_description" rows="3"><?php echo htmlspecialchars($settings['about_description']); ?></textarea>
                                    </div>
                                </div>
                                
                                <div class="text-end mt-4 pt-3 border-top border-secondary">
                                    <button type="reset" class="btn btn-outline-light me-2">
                                        <i class="fas fa-undo me-2"></i>Sıfırla
                                    </button>
                                    <button type="submit" name="update_content_settings" class="btn btn-gradient">
                                        <i class="fas fa-save me-2"></i>İçerik Ayarlarını Kaydet
                                    </button>
                                </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nav-contact" role="tabpanel">
                                <form method="POST" id="contactForm">
                                <h5 class="text-light mb-3">İletişim Bilgileri</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">E-posta</label>
                                        <input type="email" class="form-control" name="contact_email" value="<?php echo htmlspecialchars($settings['contact_email']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Telefon</label>
                                        <input type="text" class="form-control" name="contact_phone" value="<?php echo htmlspecialchars($settings['contact_phone']); ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-light">Adres</label>
                                    <textarea class="form-control" name="contact_address" rows="2"><?php echo htmlspecialchars($settings['contact_address']); ?></textarea>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-light">WhatsApp Telefon Numarası</label>
                                    <input type="text" class="form-control" name="whatsapp_phone" value="<?php echo htmlspecialchars($settings['whatsapp_phone']); ?>" placeholder="05395115632">
                                    <small class="text-muted">Ürünler sayfasında WhatsApp butonu için kullanılacak</small>
                                </div>
                                
                                <div class="text-end mt-4 pt-3 border-top border-secondary">
                                    <button type="reset" class="btn btn-outline-light me-2">
                                        <i class="fas fa-undo me-2"></i>Sıfırla
                                    </button>
                                    <button type="submit" name="update_contact_settings" class="btn btn-gradient">
                                        <i class="fas fa-save me-2"></i>İletişim Ayarlarını Kaydet
                                    </button>
                                </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nav-social" role="tabpanel">
                                <form method="POST" id="socialForm">
                                <h5 class="text-light mb-3">Sosyal Medya Linkleri</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">GitHub</label>
                                        <input type="url" class="form-control" name="social_github" value="<?php echo htmlspecialchars($settings['social_github']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">LinkedIn</label>
                                        <input type="url" class="form-control" name="social_linkedin" value="<?php echo htmlspecialchars($settings['social_linkedin']); ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Twitter</label>
                                        <input type="url" class="form-control" name="social_twitter" value="<?php echo htmlspecialchars($settings['social_twitter']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Instagram</label>
                                        <input type="url" class="form-control" name="social_instagram" value="<?php echo htmlspecialchars($settings['social_instagram']); ?>">
                                    </div>
                                </div>
                                
                                <div class="text-end mt-4 pt-3 border-top border-secondary">
                                    <button type="reset" class="btn btn-outline-light me-2">
                                        <i class="fas fa-undo me-2"></i>Sıfırla
                                    </button>
                                    <button type="submit" name="update_social_settings" class="btn btn-gradient">
                                        <i class="fas fa-save me-2"></i>Sosyal Medya Ayarlarını Kaydet
                                    </button>
                                </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nav-stats" role="tabpanel">
                                <form method="POST" id="statsForm">
                                <h5 class="text-light mb-3">Ana Sayfa İstatistikleri</h5>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Tamamlanan Proje</label>
                                        <input type="number" class="form-control" name="stat_projects" value="<?php echo htmlspecialchars($settings['stat_projects']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Mutlu Müşteri</label>
                                        <input type="number" class="form-control" name="stat_clients" value="<?php echo htmlspecialchars($settings['stat_clients']); ?>">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Yıllık Deneyim</label>
                                        <input type="number" class="form-control" name="stat_years" value="<?php echo htmlspecialchars($settings['stat_years']); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Ödül & Sertifika</label>
                                        <input type="number" class="form-control" name="stat_awards" value="<?php echo htmlspecialchars($settings['stat_awards']); ?>">
                                    </div>
                                </div>
                                
                                <div class="text-end mt-4 pt-3 border-top border-secondary">
                                    <button type="reset" class="btn btn-outline-light me-2">
                                        <i class="fas fa-undo me-2"></i>Sıfırla
                                    </button>
                                    <button type="submit" name="update_stats_settings" class="btn btn-gradient">
                                        <i class="fas fa-save me-2"></i>İstatistik Ayarlarını Kaydet
                                    </button>
                                </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nav-text" role="tabpanel">
                                <form method="POST" id="textForm">
                                    <h5 class="text-light mb-3">Site Metinleri ve Marka Ayarları</h5>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-light">Marka Adı</label>
                                            <input type="text" class="form-control" name="site_brand" value="<?php echo htmlspecialchars($settings['site_brand']); ?>">
                                            <small class="text-muted">Sitede kullanılan ana marka adı (örn: BERAT K - R10)</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-light">Marka Sloganı</label>
                                            <input type="text" class="form-control" name="site_slogan" value="<?php echo htmlspecialchars($settings['site_slogan']); ?>">
                                            <small class="text-muted">Marka slogan veya alt başlık</small>
                                        </div>
                                    </div>
                                    
                                    <!-- Content Management için İçerik Yönetimi bölümünü kullanın -->
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-light">Footer Metni</label>
                                        <textarea class="form-control" name="footer_text" rows="2"><?php echo htmlspecialchars($settings['footer_text']); ?></textarea>
                                        <small class="text-muted">Footer'daki genel metin. {site_brand} yazdığınız yerlere marka adı otomatik geçer</small>
                                    </div>
                                    
                                    <div class="alert alert-info border-0" style="background: rgba(116, 185, 255, 0.1);">
                                        <h6 class="text-info"><i class="fas fa-info-circle me-2"></i>Dinamik Değişkenler</h6>
                                        <p class="mb-0 small text-light">
                                            Metinlerde <strong>{site_brand}</strong> yazdığınız yerlere otomatik olarak marka adı geçer. 
                                            Bu sayede marka adını değiştirdiğinizde tüm metinler otomatik güncellenir.
                                        </p>
                                    </div>
                                    
                                    <div class="text-end mt-4 pt-3 border-top border-secondary">
                                        <button type="reset" class="btn btn-outline-light me-2">
                                            <i class="fas fa-undo me-2"></i>Sıfırla
                                        </button>
                                        <button type="submit" name="update_text_settings" class="btn btn-gradient">
                                            <i class="fas fa-save me-2"></i>Site Metinlerini Kaydet
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nav-theme" role="tabpanel">
                                <form method="POST" id="themeForm">
                                    <h5 class="text-light mb-3">Tema ve Görünüm Ayarları</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-light">Ana Renk (Primary)</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" name="primary_color" value="<?php echo htmlspecialchars($settings['primary_color']); ?>">
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($settings['primary_color']); ?>" readonly>
                                            </div>
                                            <small class="text-muted">Gradient'in başlangıç rengi</small>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-light">İkincil Renk (Secondary)</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" name="secondary_color" value="<?php echo htmlspecialchars($settings['secondary_color']); ?>">
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($settings['secondary_color']); ?>" readonly>
                                            </div>
                                            <small class="text-muted">Gradient'in bitiş rengi</small>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <label class="form-label text-light">Vurgu Rengi (Accent)</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control form-control-color" name="accent_color" value="<?php echo htmlspecialchars($settings['accent_color']); ?>">
                                                <input type="text" class="form-control" value="<?php echo htmlspecialchars($settings['accent_color']); ?>" readonly>
                                            </div>
                                            <small class="text-muted">Link ve vurgu elementleri</small>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-light">Font Ailesi</label>
                                            <select class="form-control" name="font_family">
                                                <option value="Poppins" <?php echo $settings['font_family'] == 'Poppins' ? 'selected' : ''; ?>>Poppins (Varsayılan)</option>
                                                <option value="Inter" <?php echo $settings['font_family'] == 'Inter' ? 'selected' : ''; ?>>Inter</option>
                                                <option value="Roboto" <?php echo $settings['font_family'] == 'Roboto' ? 'selected' : ''; ?>>Roboto</option>
                                                <option value="Montserrat" <?php echo $settings['font_family'] == 'Montserrat' ? 'selected' : ''; ?>>Montserrat</option>
                                                <option value="Open Sans" <?php echo $settings['font_family'] == 'Open Sans' ? 'selected' : ''; ?>>Open Sans</option>
                                                <option value="Lato" <?php echo $settings['font_family'] == 'Lato' ? 'selected' : ''; ?>>Lato</option>
                                                <option value="Source Sans Pro" <?php echo $settings['font_family'] == 'Source Sans Pro' ? 'selected' : ''; ?>>Source Sans Pro</option>
                                            </select>
                                            <small class="text-muted">Site genelinde kullanılacak font</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-light">Tema Modu</label>
                                            <div class="form-check form-switch mt-2">
                                                <input class="form-check-input" type="checkbox" name="dark_mode" id="dark_mode" <?php echo $settings['dark_mode'] == '1' ? 'checked' : ''; ?> style="transform: scale(1.2);">
                                                <label class="form-check-label text-light ms-2" for="dark_mode">
                                                    <strong>Dark Mode Aktif</strong>
                                                </label>
                                            </div>
                                            <small class="text-muted">Koyu tema kullanın</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-light">Özel CSS Kodları</label>
                                        <textarea class="form-control" name="custom_css" rows="8" placeholder="/* Özel CSS kodlarınızı buraya yazın */
.custom-class {
    color: #6c5ce7;
}"><?php echo htmlspecialchars($settings['custom_css']); ?></textarea>
                                        <small class="text-muted">Bu CSS kodları sitenizin head bölümüne eklenecek</small>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-light">Özel JavaScript Kodları</label>
                                        <textarea class="form-control" name="custom_js" rows="6" placeholder="// Özel JavaScript kodlarınızı buraya yazın
console.log('Custom JS loaded!');"><?php echo htmlspecialchars($settings['custom_js']); ?></textarea>
                                        <small class="text-muted">Bu JavaScript kodları sitenizin footer bölümüne eklenecek</small>
                                    </div>
                                    
                                    <div class="alert alert-warning border-0" style="background: rgba(255, 193, 7, 0.1);">
                                        <h6 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Renk Önizlemesi</h6>
                                        <div class="color-preview mt-3">
                                            <div class="row">
                                                <div class="col-md-4 mb-2">
                                                    <div class="preview-box" id="primaryPreview" style="background: <?php echo $settings['primary_color']; ?>">
                                                        <span>Ana Renk</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="preview-box" id="secondaryPreview" style="background: <?php echo $settings['secondary_color']; ?>">
                                                        <span>İkincil Renk</span>
                                                    </div>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <div class="preview-box gradient-preview" id="gradientPreview" style="background: linear-gradient(135deg, <?php echo $settings['primary_color']; ?>, <?php echo $settings['secondary_color']; ?>)">
                                                        <span>Gradient</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end mt-4 pt-3 border-top border-secondary">
                                        <button type="reset" class="btn btn-outline-light me-2">
                                            <i class="fas fa-undo me-2"></i>Sıfırla
                                        </button>
                                        <button type="submit" name="update_theme_settings" class="btn btn-gradient">
                                            <i class="fas fa-save me-2"></i>Tema Ayarlarını Kaydet
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nav-seo" role="tabpanel">
                                <form method="POST" id="seoForm">
                                    <h5 class="text-light mb-3">SEO ve Arama Motoru Optimizasyonu</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-light">Google Analytics ID</label>
                                            <input type="text" class="form-control" name="google_analytics" value="<?php echo htmlspecialchars($settings['google_analytics']); ?>" placeholder="G-XXXXXXXXXX">
                                            <small class="text-muted">Google Analytics takip kodu (örn: G-XXXXXXXXXX)</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-light">Google Search Console Kodu</label>
                                            <input type="text" class="form-control" name="google_search_console" value="<?php echo htmlspecialchars($settings['google_search_console']); ?>" placeholder="google-site-verification=xxxx">
                                            <small class="text-muted">Site doğrulama meta etiketi</small>
                                        </div>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-light">Meta Robots</label>
                                            <select class="form-control" name="meta_robots">
                                                <option value="index, follow" <?php echo $settings['meta_robots'] == 'index, follow' ? 'selected' : ''; ?>>Index, Follow (Önerilen)</option>
                                                <option value="index, nofollow" <?php echo $settings['meta_robots'] == 'index, nofollow' ? 'selected' : ''; ?>>Index, NoFollow</option>
                                                <option value="noindex, follow" <?php echo $settings['meta_robots'] == 'noindex, follow' ? 'selected' : ''; ?>>NoIndex, Follow</option>
                                                <option value="noindex, nofollow" <?php echo $settings['meta_robots'] == 'noindex, nofollow' ? 'selected' : ''; ?>>NoIndex, NoFollow</option>
                                            </select>
                                            <small class="text-muted">Arama motoru tarama direktifleri</small>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label text-light">Canonical URL</label>
                                            <input type="url" class="form-control" name="canonical_url" value="<?php echo htmlspecialchars($settings['canonical_url']); ?>" placeholder="https://example.com">
                                            <small class="text-muted">Ana domain URL (SEO için)</small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label text-light">Robots.txt İçeriği</label>
                                        <textarea class="form-control" name="robots_txt_content" rows="10" placeholder="User-agent: *
Disallow: /admin/
Disallow: /config/

Sitemap: https://example.com/sitemap.xml"><?php echo htmlspecialchars($settings['robots_txt_content']); ?></textarea>
                                        <small class="text-muted">Robots.txt dosyasının içeriği</small>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <div class="info-card p-3 border border-secondary rounded">
                                                <h6 class="text-light mb-2"><i class="fas fa-sitemap me-2"></i>Sitemap Durumu</h6>
                                                <p class="text-muted small mb-2">Son Güncelleme: <?php echo $settings['sitemap_last_update']; ?></p>
                                                <a href="../sitemap.xml" target="_blank" class="btn btn-outline-gradient btn-sm me-2">
                                                    <i class="fas fa-external-link-alt me-1"></i>Görüntüle
                                                </a>
                                                <a href="../generate_sitemap.php?action=sitemap" target="_blank" class="btn btn-outline-light btn-sm">
                                                    <i class="fas fa-sync me-1"></i>Yenile
                                                </a>
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <div class="info-card p-3 border border-secondary rounded">
                                                <h6 class="text-light mb-2"><i class="fas fa-robot me-2"></i>Robots.txt Durumu</h6>
                                                <p class="text-muted small mb-2">Son Güncelleme: <?php echo $settings['robots_last_update']; ?></p>
                                                <a href="../robots.txt" target="_blank" class="btn btn-outline-gradient btn-sm me-2">
                                                    <i class="fas fa-external-link-alt me-1"></i>Görüntüle
                                                </a>
                                                <a href="../generate_sitemap.php?action=robots" target="_blank" class="btn btn-outline-light btn-sm">
                                                    <i class="fas fa-sync me-1"></i>Yenile
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="alert alert-info border-0" style="background: rgba(116, 185, 255, 0.1);">
                                        <h6 class="text-info"><i class="fas fa-lightbulb me-2"></i>SEO İpuçları</h6>
                                        <ul class="mb-0 small text-light">
                                            <li><strong>Google Analytics:</strong> Ziyaretçi trafiğini takip edin</li>
                                            <li><strong>Search Console:</strong> Arama performansınızı analiz edin</li>
                                            <li><strong>Sitemap:</strong> Arama motorlarının sitenizi taramasını kolaylaştırır</li>
                                            <li><strong>Robots.txt:</strong> Hangi sayfaların taranacağını belirler</li>
                                        </ul>
                                    </div>
                                    
                                    <div class="text-end mt-4 pt-3 border-top border-secondary">
                                        <button type="submit" name="generate_files" class="btn btn-outline-light me-2">
                                            <i class="fas fa-cog me-2"></i>Sitemap & Robots Oluştur
                                        </button>
                                        <button type="submit" name="update_seo_settings" class="btn btn-gradient">
                                            <i class="fas fa-save me-2"></i>SEO Ayarlarını Kaydet
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nav-visual" role="tabpanel">
                                <h5 class="text-light mb-3">Logo ve Favicon Yönetimi</h5>
                                <form method="POST" enctype="multipart/form-data" id="visualForm">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-light">Site Favicon</label>
                                                <input type="file" class="form-control" name="site_favicon" accept=".ico,.png,.jpg,.jpeg">
                                                <small class="text-muted">Önerilen boyut: 32x32px veya 16x16px. Format: ICO, PNG</small>
                                            </div>
                                            
                                            <?php if($settings['site_favicon']): ?>
                                                <div class="current-favicon mb-3">
                                                    <label class="form-label text-light">Mevcut Favicon</label>
                                                    <div class="favicon-preview p-3 border border-secondary rounded">
                                                        <img src="../<?php echo htmlspecialchars($settings['site_favicon']); ?>" alt="Favicon" style="width: 32px; height: 32px;">
                                                        <span class="text-light ms-2"><?php echo basename($settings['site_favicon']); ?></span>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label text-light">Site Logo</label>
                                                <input type="file" class="form-control" name="site_logo" accept=".png,.jpg,.jpeg,.svg">
                                                <small class="text-muted">Önerilen boyut: 200x50px. Format: PNG, JPG, SVG</small>
                                            </div>
                                            
                                            <?php if($settings['site_logo']): ?>
                                                <div class="current-logo mb-3">
                                                    <label class="form-label text-light">Mevcut Logo</label>
                                                    <div class="logo-preview p-3 border border-secondary rounded">
                                                        <img src="../<?php echo htmlspecialchars($settings['site_logo']); ?>" alt="Logo" style="max-height: 50px; max-width: 200px;">
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end">
                                        <button type="submit" name="update_visual_settings" class="btn btn-gradient">
                                            <i class="fas fa-upload me-2"></i>Görselleri Kaydet
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nav-maintenance" role="tabpanel">
                                <h5 class="text-light mb-3">Bakım Modu Ayarları</h5>
                                <form method="POST" id="maintenanceForm">
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="alert alert-warning border-0" style="background: rgba(255, 193, 7, 0.1);">
                                                <h6 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Bakım Modu Nedir?</h6>
                                                <p class="mb-0 text-light small">
                                                    Bakım modu aktif edildiğinde sitenizi sadece admin paneli üzerinden görebilirsiniz. 
                                                    Normal ziyaretçiler "Site Bakımda" sayfasını görür. Site güncellemesi sırasında kullanışlıdır.
                                                </p>
                                            </div>
                                            
                                            <div class="form-check form-switch mb-4">
                                                <input class="form-check-input" type="checkbox" name="maintenance_mode" id="maintenance_mode" 
                                                       <?php echo $settings['maintenance_mode'] == '1' ? 'checked' : ''; ?>
                                                       style="transform: scale(1.5);">
                                                <label class="form-check-label text-light ms-3" for="maintenance_mode">
                                                    <strong>Bakım Modunu Aktifleştir</strong>
                                                </label>
                                            </div>
                                            
                                            <?php if($settings['maintenance_mode'] == '1'): ?>
                                                <div class="alert alert-danger border-0" style="background: rgba(220, 53, 69, 0.1);">
                                                    <h6 class="text-danger"><i class="fas fa-exclamation-circle me-2"></i>DİKKAT!</h6>
                                                    <p class="mb-0 text-light small">
                                                        Bakım modu şu anda <strong>AKTİF</strong>. Site ziyaretçileri bakım sayfasını görüyor.
                                                    </p>
                                                </div>
                                            <?php else: ?>
                                                <div class="alert alert-success border-0" style="background: rgba(40, 167, 69, 0.1);">
                                                    <h6 class="text-success"><i class="fas fa-check-circle me-2"></i>Normal Durum</h6>
                                                    <p class="mb-0 text-light small">
                                                        Site normal şekilde çalışıyor. Ziyaretçiler siteyi görebiliyor.
                                                    </p>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            <div class="maintenance-preview p-3 border border-secondary rounded text-center">
                                                <i class="fas fa-tools fa-3x text-warning mb-3"></i>
                                                <h6 class="text-light">Site Bakımda</h6>
                                                <p class="small text-muted mb-0">Ziyaretçilerin göreceği sayfa</p>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end mt-4">
                                        <button type="submit" name="update_visual_settings" class="btn btn-gradient">
                                            <i class="fas fa-save me-2"></i>Bakım Modu Ayarlarını Kaydet
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <div class="tab-pane fade" id="nav-security" role="tabpanel">
                                <form method="POST" id="securityForm">
                                    <h5 class="text-light mb-3">Şifre Değiştir</h5>
                                    <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label class="form-label text-light">Mevcut Şifre <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="current_password" id="current_password" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-light">Yeni Şifre <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="new_password" id="new_password" required minlength="6">
                                            <small class="text-muted">En az 6 karakter olmalıdır</small>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-light">Yeni Şifre (Tekrar) <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
                                        </div>
                                        <button type="submit" name="change_password" class="btn btn-warning">
                                            <i class="fas fa-key me-2"></i>Şifreyi Değiştir
                                        </button>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-info border-0" style="background: rgba(116, 185, 255, 0.1);">
                                            <h6 class="text-info">Güvenlik Önerileri</h6>
                                            <ul class="mb-0 small text-light">
                                                <li>En az 8 karakter kullanın</li>
                                                <li>Büyük ve küçük harf karışımı</li>
                                                <li>Rakam ve özel karakter ekleyin</li>
                                                <li>Düzenli olarak şifrenizi değiştirin</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                </form>
                            </div>
                            
                            <!-- E-posta Sistemi -->
                            <div class="tab-pane fade" id="nav-email" role="tabpanel">
                                <form method="POST" id="emailForm">
                                    <h5 class="text-light mb-3">E-posta Sistemi Ayarları</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <div class="alert alert-success border-0" style="background: rgba(40, 167, 69, 0.1);">
                                                <h6 class="text-success"><i class="fas fa-check-circle me-2"></i>E-posta Sistemi Özellikleri</h6>
                                                <ul class="mb-0 small text-light">
                                                    <li>✅ SMTP entegrasyonu</li>
                                                    <li>✅ E-posta şablonları</li>
                                                    <li>✅ Otomatik yanıt sistemi</li>
                                                    <li>✅ Newsletter sistemi</li>
                                                    <li>✅ İletişim formu özelleştirme</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- SMTP Ayarları -->
                                    <div class="mb-4">
                                        <h6 class="text-light mb-3">SMTP E-posta Sunucu Ayarları</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-light">SMTP Sunucu</label>
                                                <input type="text" class="form-control" name="smtp_host" value="<?php echo getSetting('smtp_host', 'mail.example.com'); ?>" placeholder="mail.example.com">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label text-light">Port</label>
                                                <input type="number" class="form-control" name="smtp_port" value="<?php echo getSetting('smtp_port', '587'); ?>" placeholder="587">
                                            </div>
                                            <div class="col-md-3 mb-3">
                                                <label class="form-label text-light">Güvenlik</label>
                                                <select class="form-control" name="smtp_security">
                                                    <option value="tls" <?php echo getSetting('smtp_security', 'tls') == 'tls' ? 'selected' : ''; ?>>TLS</option>
                                                    <option value="ssl" <?php echo getSetting('smtp_security', 'tls') == 'ssl' ? 'selected' : ''; ?>>SSL</option>
                                                    <option value="none" <?php echo getSetting('smtp_security', 'tls') == 'none' ? 'selected' : ''; ?>>Yok</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-light">E-posta Adresi</label>
                                                <input type="email" class="form-control" name="smtp_username" value="<?php echo getSetting('smtp_username', ''); ?>" placeholder="noreply@example.com">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-light">Şifre</label>
                                                <input type="password" class="form-control" name="smtp_password" value="<?php echo getSetting('smtp_password', ''); ?>" placeholder="••••••••">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Newsletter Ayarları -->
                                    <div class="mb-4">
                                        <h6 class="text-light mb-3">Newsletter Sistemi</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-light">Newsletter Başlığı</label>
                                                <input type="text" class="form-control" name="newsletter_title" value="<?php echo getSetting('newsletter_title', 'BERAT K - R10 Newsletter'); ?>" placeholder="BERAT K - R10 Newsletter">
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-light">Newsletter Açıklaması</label>
                                                <input type="text" class="form-control" name="newsletter_description" value="<?php echo getSetting('newsletter_description', 'En son projeler ve teknoloji haberleri'); ?>" placeholder="En son projeler ve teknoloji haberleri">
                                            </div>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="newsletter_enabled" id="newsletter_enabled" <?php echo getSetting('newsletter_enabled', '1') == '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label text-light" for="newsletter_enabled">
                                                Newsletter sistemini aktifleştir
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <!-- Otomatik Yanıt -->
                                    <div class="mb-4">
                                        <h6 class="text-light mb-3">Otomatik Yanıt Sistemi</h6>
                                        <div class="mb-3">
                                            <label class="form-label text-light">Otomatik Yanıt Konusu</label>
                                            <input type="text" class="form-control" name="auto_reply_subject" value="<?php echo getSetting('auto_reply_subject', 'Mesajınız alındı - BERAT K - R10'); ?>" placeholder="Mesajınız alındı - BERAT K - R10">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label text-light">Otomatik Yanıt Mesajı</label>
                                            <textarea class="form-control" name="auto_reply_message" rows="4" placeholder="Merhaba, mesajınız başarıyla alındı..."><?php echo getSetting('auto_reply_message', 'Merhaba,\n\nMesajınız başarıyla alınmıştır. En kısa sürede size geri dönüş yapacağım.\n\nTeşekkürler,\nBERAT K - R10'); ?></textarea>
                                        </div>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="auto_reply_enabled" id="auto_reply_enabled" <?php echo getSetting('auto_reply_enabled', '1') == '1' ? 'checked' : ''; ?>>
                                            <label class="form-check-label text-light" for="auto_reply_enabled">
                                                Otomatik yanıt sistemini aktifleştir
                                            </label>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end mt-4 pt-3 border-top border-secondary">
                                        <button type="button" class="btn btn-outline-light me-2" onclick="testEmailConnection()">
                                            <i class="fas fa-paper-plane me-2"></i>Test E-postası Gönder
                                        </button>
                                        <button type="submit" name="update_email_settings" class="btn btn-gradient">
                                            <i class="fas fa-save me-2"></i>E-posta Ayarlarını Kaydet
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Performans Optimizasyonu -->
                            <div class="tab-pane fade" id="nav-performance" role="tabpanel">
                                <form method="POST" id="performanceForm">
                                                                         <h5 class="text-light mb-3">Performans Optimizasyonu</h5>
                                    
                                    <div class="row">
                                        <div class="col-md-12 mb-4">
                                            <div class="alert alert-success border-0" style="background: rgba(40, 167, 69, 0.1);">
                                                <h6 class="text-success"><i class="fas fa-check-circle me-2"></i>Performans Özellikleri</h6>
                                                <ul class="mb-0 small text-light">
                                                    <li>✅ Cache yönetimi</li>
                                                    <li>✅ Site hızı optimizasyonu</li>
                                                    <li>✅ Görsel sıkıştırma</li>
                                                    <li>✅ CSS/JS minifikasyonu</li>
                                                    <li>✅ Database optimizasyonu</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Cache Ayarları -->
                                    <div class="mb-4">
                                        <h6 class="text-light mb-3">Cache Yönetimi</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="cache_enabled" id="cache_enabled" <?php echo getSetting('cache_enabled', '1') == '1' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-light" for="cache_enabled">
                                                        <strong>Cache Sistemini Aktifleştir</strong>
                                                    </label>
                                                </div>
                                                <small class="text-muted">Sayfa yükleme hızını artırır</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-light">Cache Süresi (dakika)</label>
                                                <input type="number" class="form-control" name="cache_duration" value="<?php echo getSetting('cache_duration', '60'); ?>" placeholder="60">
                                                <small class="text-muted">Cache dosyalarının ne kadar süre saklanacağı</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Görsel Optimizasyonu -->
                                    <div class="mb-4">
                                        <h6 class="text-light mb-3">Görsel Optimizasyonu</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="image_compression" id="image_compression" <?php echo getSetting('image_compression', '1') == '1' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-light" for="image_compression">
                                                        <strong>Otomatik Görsel Sıkıştırma</strong>
                                                    </label>
                                                </div>
                                                <small class="text-muted">Yüklenen görselleri otomatik sıkıştırır</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label text-light">Görsel Kalitesi (%)</label>
                                                <input type="range" class="form-range" name="image_quality" value="<?php echo getSetting('image_quality', '85'); ?>" min="10" max="100" step="5">
                                                <small class="text-muted">Mevcut: %<span id="quality-value"><?php echo getSetting('image_quality', '85'); ?></span></small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- CSS/JS Optimizasyonu -->
                                    <div class="mb-4">
                                        <h6 class="text-light mb-3">CSS/JS Optimizasyonu</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="css_minify" id="css_minify" <?php echo getSetting('css_minify', '1') == '1' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-light" for="css_minify">
                                                        <strong>CSS Minifikasyonu</strong>
                                                    </label>
                                                </div>
                                                <small class="text-muted">CSS dosyalarını sıkıştırır</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" name="js_minify" id="js_minify" <?php echo getSetting('js_minify', '1') == '1' ? 'checked' : ''; ?>>
                                                    <label class="form-check-label text-light" for="js_minify">
                                                        <strong>JavaScript Minifikasyonu</strong>
                                                    </label>
                                                </div>
                                                <small class="text-muted">JavaScript dosyalarını sıkıştırır</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Database Optimizasyonu -->
                                    <div class="mb-4">
                                        <h6 class="text-light mb-3">Database Optimizasyonu</h6>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <button type="button" class="btn btn-outline-light w-100" onclick="optimizeDatabase()">
                                                    <i class="fas fa-database me-2"></i>Database'i Optimize Et
                                                </button>
                                                <small class="text-muted">Veritabanını temizler ve optimize eder</small>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <button type="button" class="btn btn-outline-warning w-100" onclick="clearCache()">
                                                    <i class="fas fa-trash me-2"></i>Cache'i Temizle
                                                </button>
                                                <small class="text-muted">Tüm cache dosyalarını siler</small>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end mt-4 pt-3 border-top border-secondary">
                                        <button type="button" class="btn btn-outline-light me-2" onclick="performanceTest()">
                                            <i class="fas fa-tachometer-alt me-2"></i>Performans Testi
                                        </button>
                                        <button type="submit" name="update_performance_settings" class="btn btn-gradient">
                                            <i class="fas fa-save me-2"></i>Performans Ayarlarını Kaydet
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Blog Sistemi -->
                            <div class="tab-pane fade" id="nav-blog" role="tabpanel">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="text-light mb-0">Blog & İçerik Yönetimi</h5>
                                    <div>
                                        <a href="blog.php" class="btn btn-outline-light btn-sm me-2">
                                            <i class="fas fa-cogs me-1"></i>Detaylı Blog Ayarları
                                        </a>
                                        <?php if(getSetting('blog_enabled', '1') == '1'): ?>
                                        <a href="../blog.php" target="_blank" class="btn btn-outline-gradient btn-sm">
                                            <i class="fas fa-external-link-alt me-1"></i>Blog Sayfasını Görüntüle
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-8">
                                        <div class="card-dark mb-4">
                                            <div class="card-body">
                                                <h6 class="text-light mb-3"><i class="fas fa-blog me-2"></i>Blog Durumu</h6>
                                                <?php if(getSetting('blog_enabled', '1') == '1'): ?>
                                                    <div class="alert alert-success border-0" style="background: rgba(40, 167, 69, 0.1);">
                                                        <h6 class="text-success"><i class="fas fa-check-circle me-2"></i>Blog Sistemi Aktif</h6>
                                                        <p class="mb-0 small">Blog sayfası sitede görünür ve ziyaretçiler erişebilir.</p>
                                                    </div>
                                                <?php else: ?>
                                                    <div class="alert alert-warning border-0" style="background: rgba(255, 193, 7, 0.1);">
                                                        <h6 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Blog Sistemi Pasif</h6>
                                                        <p class="mb-0 small">Blog sistemi kapalı. Detaylı ayarlardan aktifleştirebilirsiniz.</p>
                                                    </div>
                                                <?php endif; ?>
                                                
                                                <div class="mt-3">
                                                    <small class="text-muted d-block">Sayfa Başlığı: <strong><?php echo getSetting('blog_page_title', 'Blog'); ?></strong></small>
                                                    <small class="text-muted d-block">Sayfa Başına Yazı: <strong><?php echo getSetting('blog_posts_per_page', '6'); ?></strong></small>
                                                    <small class="text-muted d-block">Editör: <strong><?php echo ucfirst(getSetting('default_editor', 'tinymce')); ?></strong></small>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card-dark">
                                            <div class="card-body">
                                                <h6 class="text-light mb-3"><i class="fas fa-plus me-2"></i>Hızlı Blog Yazısı Oluştur</h6>
                                                <p class="text-muted small">Blog yazılarını yönetmek için aşağıdaki butonları kullanın:</p>
                                                
                                                <div class="d-grid gap-2 d-md-flex">
                                                    <a href="blog.php?action=new" class="btn btn-gradient me-md-2">
                                                        <i class="fas fa-plus me-2"></i>Yeni Blog Yazısı
                                                    </a>
                                                    <a href="blog.php?action=manage" class="btn btn-outline-light me-md-2">
                                                        <i class="fas fa-list me-2"></i>Yazıları Yönet
                                                    </a>
                                                    <a href="blog.php?action=categories" class="btn btn-outline-light">
                                                        <i class="fas fa-tags me-2"></i>Kategoriler
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-4">
                                        <div class="card-dark mb-4">
                                            <div class="card-body">
                                                <h6 class="text-light mb-3"><i class="fas fa-chart-bar me-2"></i>Blog İstatistikleri</h6>
                                                <?php
                                                try {
                                                    // Blog istatistikleri
                                                    $blog_stats = [];
                                                    
                                                    // Toplam yazı sayısı
                                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts");
                                                    $stmt->execute();
                                                    $blog_stats['total_posts'] = $stmt->fetchColumn();
                                                    
                                                    // Yayınlanan yazı sayısı  
                                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE status = 'published'");
                                                    $stmt->execute();
                                                    $blog_stats['published_posts'] = $stmt->fetchColumn();
                                                    
                                                    // Taslak yazı sayısı
                                                    $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE status = 'draft'");
                                                    $stmt->execute();
                                                    $blog_stats['draft_posts'] = $stmt->fetchColumn();
                                                    
                                                } catch(PDOException $e) {
                                                    $blog_stats = [
                                                        'total_posts' => 0,
                                                        'published_posts' => 0,
                                                        'draft_posts' => 0
                                                    ];
                                                }
                                                ?>
                                                
                                                <div class="row text-center">
                                                    <div class="col-4">
                                                        <div class="stat-item">
                                                            <h4 class="text-gradient mb-1"><?php echo $blog_stats['total_posts']; ?></h4>
                                                            <small class="text-muted">Toplam</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="stat-item">
                                                            <h4 class="text-success mb-1"><?php echo $blog_stats['published_posts']; ?></h4>
                                                            <small class="text-muted">Yayında</small>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="stat-item">
                                                            <h4 class="text-warning mb-1"><?php echo $blog_stats['draft_posts']; ?></h4>
                                                            <small class="text-muted">Taslak</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card-dark">
                                            <div class="card-body">
                                                <h6 class="text-light mb-3"><i class="fas fa-tools me-2"></i>Blog Araçları</h6>
                                                <div class="d-grid gap-2">
                                                    <button class="btn btn-outline-light btn-sm" onclick="alert('Yakında aktif olacak!')">
                                                        <i class="fas fa-upload me-2"></i>Toplu İçe Aktar
                                                    </button>
                                                    <button class="btn btn-outline-light btn-sm" onclick="exportContent()">
                                                        <i class="fas fa-download me-2"></i>İçeriği Dışa Aktar
                                                    </button>
                                                    <button class="btn btn-outline-light btn-sm" onclick="alert('Yakında aktif olacak!')">
                                                        <i class="fas fa-share-alt me-2"></i>Sosyal Medya Paylaş
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.nav-tabs .nav-link {
    background: transparent;
    border: none;
    color: var(--text-muted);
    margin-right: 10px;
    border-radius: 25px;
    padding: 10px 20px;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    color: var(--text-light);
    background: rgba(108, 92, 231, 0.1);
}

.nav-tabs .nav-link.active {
    background: var(--gradient);
    color: white;
    border: none;
}

.tab-content {
    min-height: 400px;
}

.form-control {
    background-color: var(--dark-bg);
    border: 1px solid var(--dark-border);
    color: var(--text-light);
    border-radius: 8px;
}

.form-control:focus {
    background-color: var(--dark-bg);
    border-color: var(--primary-color);
    color: var(--text-light);
    box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25);
}

.form-control::placeholder {
    color: var(--text-muted);
}

.alert-info {
    background-color: rgba(116, 185, 255, 0.1);
    border-left: 4px solid #74b9ff;
    color: #74b9ff;
}

@media (max-width: 768px) {
    .nav-tabs {
        flex-direction: column;
    }
    
    .nav-tabs .nav-link {
        margin-bottom: 5px;
        margin-right: 0;
        text-align: center;
    }
}

.form-control-color {
    width: 60px;
    border: none;
    border-radius: 8px;
    height: 45px;
    cursor: pointer;
}

.preview-box {
    height: 80px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    border: 2px solid rgba(255,255,255,0.2);
    transition: all 0.3s ease;
}

.preview-box:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.3);
}

.color-preview {
    border-radius: 12px;
    padding: 15px;
    background: rgba(0,0,0,0.1);
}

textarea[name="custom_css"], textarea[name="custom_js"] {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 13px;
    line-height: 1.5;
    background: #1a1a1a;
    border: 1px solid var(--dark-border);
    color: #f8f8f2;
}

.input-group .form-control:last-child {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('settingsForm');
    const navTabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    
    navTabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            if (this.getAttribute('data-bs-target') === '#nav-security') {
                form.setAttribute('action', '');
            } else {
                form.setAttribute('action', '');
            }
        });
    });
    
    // Şifre değiştirme formu - SİMPLE APPROACH
    const securityForm = document.getElementById('securityForm');
    
    if (securityForm) {
        // Form submit'i engelleme, direkt normal submit olsun
        console.log('✅ Security form found and ready');
    }
    
    // Renk önizlemesi
    const colorInputs = document.querySelectorAll('input[type="color"]');
    colorInputs.forEach(input => {
        input.addEventListener('change', function() {
            const colorValue = this.value;
            const textInput = this.closest('.input-group').querySelector('input[type="text"]');
            textInput.value = colorValue;
            
            // Önizleme güncelle
            if (this.name === 'primary_color') {
                document.getElementById('primaryPreview').style.background = colorValue;
                updateGradientPreview();
            } else if (this.name === 'secondary_color') {
                document.getElementById('secondaryPreview').style.background = colorValue;
                updateGradientPreview();
            } else if (this.name === 'accent_color') {
                // Accent color için başka bir önizleme eklenebilir
            }
        });
    });
    
    function updateGradientPreview() {
        const primaryColor = document.querySelector('input[name="primary_color"]').value;
        const secondaryColor = document.querySelector('input[name="secondary_color"]').value;
        const gradientPreview = document.getElementById('gradientPreview');
        gradientPreview.style.background = `linear-gradient(135deg, ${primaryColor}, ${secondaryColor})`;
    }
    
    // Font ailesi değişikliğinde önizleme
    const fontSelect = document.querySelector('select[name="font_family"]');
    if (fontSelect) {
        fontSelect.addEventListener('change', function() {
            // Google Fonts import ekle
            const selectedFont = this.value;
            let fontLink = document.getElementById('preview-font-link');
            
            if (!fontLink) {
                fontLink = document.createElement('link');
                fontLink.id = 'preview-font-link';
                fontLink.rel = 'stylesheet';
                document.head.appendChild(fontLink);
            }
            
            fontLink.href = `https://fonts.googleapis.com/css2?family=${selectedFont.replace(' ', '+')}:wght@300;400;500;600;700&display=swap`;
            
            // Sayfa fontunu geçici olarak değiştir
            document.body.style.fontFamily = `'${selectedFont}', sans-serif`;
        });
    }
    
    // Görsel kalitesi slider'ı için
    const qualitySlider = document.querySelector('input[name="image_quality"]');
    if (qualitySlider) {
        qualitySlider.addEventListener('input', function() {
            document.getElementById('quality-value').textContent = this.value;
        });
    }
});

// E-posta sistemi fonksiyonları
function testEmailConnection() {
    const emailData = {
        smtp_host: document.querySelector('input[name="smtp_host"]').value,
        smtp_port: document.querySelector('input[name="smtp_port"]').value,
        smtp_username: document.querySelector('input[name="smtp_username"]').value,
        smtp_password: document.querySelector('input[name="smtp_password"]').value,
        smtp_security: document.querySelector('select[name="smtp_security"]').value
    };
    
    if (!emailData.smtp_host || !emailData.smtp_username) {
        alert('Lütfen SMTP sunucu ve e-posta adresini girin.');
        return;
    }
    
    // AJAX ile test e-postası gönder
    fetch('includes/test_email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(emailData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Test e-postası başarıyla gönderildi!');
        } else {
            alert('E-posta gönderilirken hata oluştu: ' + data.message);
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error);
    });
}

// Performans fonksiyonları
function optimizeDatabase() {
    if (confirm('Veritabanını optimize etmek istediğinizden emin misiniz? Bu işlem biraz zaman alabilir.')) {
        fetch('includes/optimize_database.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Veritabanı başarıyla optimize edildi!');
            } else {
                alert('Optimizasyon sırasında hata oluştu: ' + data.message);
            }
        })
        .catch(error => {
            alert('Bir hata oluştu: ' + error);
        });
    }
}

function clearCache() {
    if (confirm('Tüm cache dosyalarını silmek istediğinizden emin misiniz?')) {
        fetch('includes/clear_cache.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Cache başarıyla temizlendi!');
            } else {
                alert('Cache temizlenirken hata oluştu: ' + data.message);
            }
        })
        .catch(error => {
            alert('Bir hata oluştu: ' + error);
        });
    }
}

function performanceTest() {
    alert('Performans testi başlatılıyor...');
    fetch('includes/performance_test.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const results = `
Performans Testi Sonuçları:
• Sayfa Yükleme Süresi: ${data.page_load_time}ms
• Database Sorgu Süresi: ${data.db_query_time}ms
• Bellek Kullanımı: ${data.memory_usage}MB
• Cache Durumu: ${data.cache_status}
• Öneriler: ${data.recommendations.join(', ')}
            `;
            alert(results);
        } else {
            alert('Performans testi başarısız: ' + data.message);
        }
    })
    .catch(error => {
        alert('Bir hata oluştu: ' + error);
    });
}

// Blog sistemi fonksiyonları
function exportContent() {
    if (confirm('Tüm içeriği dışa aktarmak istediğinizden emin misiniz?')) {
        window.open('includes/export_content.php', '_blank');
    }
}

function previewChanges() {
    alert('Değişiklikler önizleme modunda açılacak...');
    // Yeni sekmede önizleme aç
    window.open('../index.php?preview=1', '_blank');
}
</script>

<?php include 'includes/footer.php'; ?>