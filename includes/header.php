<?php
/**
 * Header Template - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo getSetting('site_title', 'BERAT K - R10'); ?></title>
    <meta name="description" content="<?php echo getSetting('site_description', 'Full Stack Developer & UI/UX Designer'); ?>">
    <meta name="keywords" content="<?php echo getSetting('site_keywords', 'web development, php, javascript, react'); ?>">
    <meta name="author" content="<?php echo getSetting('site_brand', 'BERAT K - R10'); ?>">
    <meta name="robots" content="<?php echo getSetting('meta_robots', 'index, follow'); ?>">
    
    <!-- Canonical URL -->
    <?php 
    $canonical_url = getSetting('canonical_url', '');
    if ($canonical_url) {
        $current_page = basename($_SERVER['PHP_SELF']);
        $canonical = rtrim($canonical_url, '/') . '/' . ($current_page == 'index.php' ? '' : $current_page);
        echo '<link rel="canonical" href="' . htmlspecialchars($canonical) . '">' . "\n";
    }
    ?>
    
    <!-- Google Search Console Verification -->
    <?php 
    $google_search_console = getSetting('google_search_console', '');
    if ($google_search_console) {
        echo '<meta name="' . htmlspecialchars($google_search_console) . '" content="">' . "\n";
    }
    ?>
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="<?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo getSetting('site_title', 'BERAT K - R10'); ?>">
    <meta property="og:description" content="<?php echo getSetting('site_description', 'Full Stack Developer & UI/UX Designer'); ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">
    <meta property="og:image" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>/assets/img/og-image.jpg">
    
    <!-- Twitter Card Meta Tags -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?php echo isset($page_title) ? $page_title . ' - ' : ''; ?><?php echo getSetting('site_title', 'BERAT K - R10'); ?>">
    <meta name="twitter:description" content="<?php echo getSetting('site_description', 'Full Stack Developer & UI/UX Designer'); ?>">
    <meta name="twitter:image" content="<?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST']; ?>/assets/img/og-image.jpg">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Casino Theme Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <?php 
    $font_family = getSetting('font_family', 'Orbitron');
    $font_family_formatted = str_replace(' ', '+', $font_family);
    ?>
    <link href="https://fonts.googleapis.com/css2?family=<?php echo $font_family_formatted; ?>:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    
    <!-- Casino Theme Dynamic CSS -->
    <style>
    :root {
        --primary-color: <?php echo getSetting('primary_color', '#dc2626'); ?>;
        --secondary-color: <?php echo getSetting('secondary_color', '#fbbf24'); ?>;
        --accent-color: <?php echo getSetting('accent_color', '#059669'); ?>;
        --gradient: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        --font-family: '<?php echo getSetting('font_family', 'Orbitron'); ?>', 'Roboto', sans-serif;
    }
    
    body {
        font-family: var(--font-family) !important;
    }
    
    h1, h2, h3, h4, h5, h6 {
        font-family: 'Orbitron', serif !important;
    }
    
    <?php if(getSetting('dark_mode', '1') == '0'): ?>
    /* Casino Platform CEO Light mode styles */
    :root {
        --dark-bg: #f8f8f8;
        --dark-secondary: #ffffff;
        --dark-card: rgba(255, 255, 255, 0.95);
        --dark-border: #d1d5db;
        --text-light: #1f2937;
        --text-muted: #6b7280;
        --text-gold: #dc2626;
        --gradient-bg: linear-gradient(135deg, #f8f8f8, #ffffff);
        --gradient-bg-alt: linear-gradient(45deg, #ffffff, #f8f8f8);
    }
    
    body {
        background: 
            radial-gradient(circle at 20% 20%, rgba(220, 38, 38, 0.05) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(251, 191, 36, 0.05) 0%, transparent 50%),
            var(--gradient-bg) !important;
        background-attachment: fixed !important;
        color: var(--text-light) !important;
    }
    <?php endif; ?>
    </style>
    
    <!-- Özel CSS -->
    <?php if(getSetting('custom_css')): ?>
    <style>
    <?php echo getSetting('custom_css'); ?>
    </style>
    <?php endif; ?>
    
    <link rel="icon" type="image/x-icon" href="<?php echo getSetting('site_favicon', 'assets/img/favicon.ico'); ?>">
    <link rel="shortcut icon" href="<?php echo getSetting('site_favicon', 'assets/img/favicon.ico'); ?>">
</head>
<body class="dark-theme" data-default-theme="<?php echo getSetting('dark_mode', '1') == '1' ? 'dark' : 'light'; ?>">
    
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark-custom fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <?php 
                $site_logo = getSetting('site_logo', '');
                if($site_logo && file_exists($site_logo)): 
                ?>
                    <img src="<?php echo htmlspecialchars($site_logo); ?>" alt="<?php echo getSetting('site_title', 'BERAT K - R10'); ?>" style="height: 40px;">
                <?php else: ?>
                    <span class="text-gradient fw-bold"><?php echo getSetting('site_title', 'BERAT K - R10'); ?></span>
                <?php endif; ?>
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>" href="index.php">Ana Sayfa</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="about.php">Hakkımda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>" href="services.php">Platform Hizmetleri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'portfolio.php' ? 'active' : ''; ?>" href="portfolio.php">Platformlar</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gallery.php' ? 'active' : ''; ?>" href="gallery.php">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">Premium Ürünler</a>
                    </li>
                    <?php if(getSetting('blog_enabled', '1') == '1'): ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'blog.php' ? 'active' : ''; ?>" href="blog.php">Platform Haberleri</a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'contact.php' ? 'active' : ''; ?>" href="contact.php">İş Birliği</a>
                    </li>
                    <li class="nav-item">
                        <button class="theme-toggle-btn" id="themeToggle" title="Tema Değiştir">
                            <i class="fas fa-moon" id="themeIcon"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="content-wrapper">
    
    <?php echo getSetting('custom_header_code', ''); ?>