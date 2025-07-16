<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' : ''; ?>Admin Panel - BERAT K - R10</title>
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- Casino Theme Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            /* Casino Platform CEO Admin Theme Colors */
            --primary-color: #dc2626;
            --secondary-color: #fbbf24;
            --accent-color: #059669;
            --dark-bg: #161616;
            --dark-secondary: #021526;
            --dark-card: rgba(22, 22, 22, 0.95);
            --dark-border: #2d2d2d;
            --text-light: #f8fafc;
            --text-muted: #d1d5db;
            --text-gold: #fbbf24;
            --gradient: linear-gradient(135deg, #dc2626, #fbbf24);
            --gradient-bg: linear-gradient(135deg, #161616, #021526);
            --gradient-bg-alt: linear-gradient(45deg, #021526, #161616);
            --gradient-hover: linear-gradient(135deg, #b91c1c, #f59e0b);
            --gradient-gold: linear-gradient(135deg, #fbbf24, #f59e0b);
            --shadow: 0 8px 32px rgba(220, 38, 38, 0.2);
            --shadow-hover: 0 12px 48px rgba(220, 38, 38, 0.4);
            --glow: 0 0 20px rgba(220, 38, 38, 0.5);
            --glow-gold: 0 0 20px rgba(251, 191, 36, 0.5);
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Orbitron', 'Roboto', sans-serif;
            background: 
                radial-gradient(circle at 20% 20%, rgba(220, 38, 38, 0.08) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(251, 191, 36, 0.06) 0%, transparent 50%),
                var(--gradient-bg);
            background-attachment: fixed;
            color: var(--text-light);
            line-height: 1.6;
            overflow-x: hidden;
            position: relative;
        }
        
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 25% 25%, rgba(220, 38, 38, 0.03) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(251, 191, 36, 0.03) 0%, transparent 50%),
                var(--gradient-bg-alt);
            background-size: 400px 400px, 300px 300px, 100% 100%;
            pointer-events: none;
            z-index: -1;
            opacity: 0.4;
        }
        
        h1, h2, h3, h4, h5, h6 {
            color: var(--text-gold) !important;
            font-weight: 600;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.5);
            font-family: 'Orbitron', serif;
        }
        
        /* Sidebar Styles */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 70px;
            background-color: var(--dark-card);
            border-right: 1px solid var(--dark-border);
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        
        .sidebar.expanded {
            width: 260px;
        }
        
        .sidebar-header {
            padding: 15px;
            border-bottom: 1px solid var(--dark-border);
            text-align: center;
            position: relative;
            min-height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .sidebar-toggle {
            position: absolute;
            top: 15px;
            right: 15px;
            background: transparent;
            border: none;
            color: var(--text-light);
            font-size: 1.2rem;
            cursor: pointer;
            padding: 5px;
            border-radius: 50%;
            transition: all 0.3s ease;
        }
        
        .sidebar-toggle:hover {
            background: var(--dark-border);
        }
        
        .brand-content {
            display: flex;
            align-items: center;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar.expanded .brand-content {
            opacity: 1;
            visibility: visible;
        }
        
        .logo-icon {
            width: 35px;
            height: 35px;
            background: var(--gradient);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 10px;
            flex-shrink: 0;
        }
        
        .brand-text h5 {
            margin: 0;
            font-size: 1.1rem;
        }
        
        .brand-text small {
            font-size: 0.8rem;
        }
        
        .sidebar-nav {
            padding: 10px 0;
        }
        
        .nav-item {
            margin-bottom: 2px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-muted);
            text-decoration: none;
            transition: all 0.3s ease;
            border-radius: 0;
            position: relative;
            white-space: nowrap;
        }
        
        .nav-link:hover, .nav-link.active {
            background: var(--gradient);
            color: white;
            margin: 0 8px;
            border-radius: 8px;
        }
        
        .nav-link i {
            margin-right: 12px;
            width: 20px;
            text-align: center;
            font-size: 1rem;
            flex-shrink: 0;
        }
        
        .nav-link span {
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar.expanded .nav-link span {
            opacity: 1;
            visibility: visible;
        }
        
        .nav-link .badge {
            margin-left: auto;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .sidebar.expanded .nav-link .badge {
            opacity: 1;
            visibility: visible;
        }
        
        /* Main Content */
        .main-content {
            margin-left: 70px;
            min-height: 100vh;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar.expanded + .main-content {
            margin-left: 260px;
        }
        
        .topbar {
            background-color: var(--dark-card);
            border-bottom: 1px solid var(--dark-border);
            padding: 12px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
            min-height: 60px;
        }
        
        .content-area {
            flex: 1;
            padding: 0;
        }
        
        .container-fluid {
            padding: 20px !important;
        }
        
        /* Button Styles */
        .text-gradient {
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 600;
        }
        
        .btn-gradient {
            background: var(--gradient);
            border: none;
            color: white;
            font-weight: 500;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-gradient:hover {
            background: var(--gradient-hover);
            transform: translateY(-1px);
            color: white;
        }
        
        .btn-outline-gradient {
            background: transparent;
            border: 1px solid var(--primary-color);
            color: var(--primary-color);
            font-weight: 500;
            padding: 6px 14px;
            border-radius: 6px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        
        .btn-outline-gradient:hover {
            background: var(--gradient);
            color: white;
            transform: translateY(-1px);
            border-color: transparent;
        }
        
        /* Card Styles */
        .card-custom {
            background-color: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .card-custom:hover {
            box-shadow: var(--shadow);
        }
        
        /* Form Styles */
        .form-control {
            background-color: var(--dark-bg);
            border: 1px solid var(--dark-border);
            color: var(--text-light);
            border-radius: 6px;
            padding: 8px 12px;
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
        
        /* Dropdown Styles */
        .dropdown-menu {
            background-color: var(--dark-card);
            border: 1px solid var(--dark-border);
            box-shadow: var(--shadow);
        }
        
        .dropdown-item {
            color: var(--text-light);
        }
        
        .dropdown-item:hover {
            background-color: var(--dark-bg);
            color: var(--text-light);
        }
        
        /* Alert Styles */
        .alert {
            border-radius: 8px;
            border: none;
            padding: 12px 16px;
        }
        
        .alert-success {
            background-color: rgba(0, 184, 148, 0.1);
            color: #00b894;
            border-left: 4px solid #00b894;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
        }
        
        .alert-warning {
            background-color: rgba(241, 196, 15, 0.1);
            color: #f1c40f;
            border-left: 4px solid #f1c40f;
        }
        
        .alert-info {
            background-color: rgba(116, 185, 255, 0.1);
            color: #74b9ff;
            border-left: 4px solid #74b9ff;
        }
        
        /* Text Colors */
        .text-light {
            color: var(--text-light) !important;
        }
        
        .text-muted {
            color: var(--text-muted) !important;
        }
        
        /* Mobile Responsive */
        @media (max-width: 768px) {
            .sidebar {
                width: 60px;
            }
            
            .sidebar.expanded {
                width: 240px;
            }
            
            .main-content {
                margin-left: 60px;
            }
            
            .sidebar.expanded + .main-content {
                margin-left: 240px;
            }
            
            .topbar {
                padding: 10px 15px;
            }
            
            .container-fluid {
                padding: 15px !important;
            }
            
            .nav-link {
                padding: 10px 15px;
            }
            
            .sidebar-header {
                padding: 10px;
            }
            
            .btn {
                padding: 6px 12px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 576px) {
            .sidebar {
                width: 50px;
            }
            
            .sidebar.expanded {
                width: 220px;
            }
            
            .main-content {
                margin-left: 50px;
            }
            
            .sidebar.expanded + .main-content {
                margin-left: 220px;
            }
            
            .topbar {
                padding: 8px 12px;
            }
            
            .container-fluid {
                padding: 10px !important;
            }
            
            .nav-link {
                padding: 8px 12px;
            }
            
            .nav-link i {
                font-size: 0.9rem;
            }
            
            .card-custom {
                margin-bottom: 15px;
            }
            
            .btn {
                padding: 5px 10px;
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="sidebar-header">
            <button class="sidebar-toggle" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            <div class="brand-content">
                <div class="logo-icon">
                    <i class="fas fa-code"></i>
                </div>
                <div class="brand-text">
                    <h5 class="text-gradient">BERAT K</h5>
                    <small class="text-muted">R10 Admin</small>
                </div>
            </div>
        </div>
        
        <nav class="sidebar-nav">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'projects.php' ? 'active' : ''; ?>" href="projects.php">
                        <i class="fas fa-project-diagram"></i>
                        <span>Projeler</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'active' : ''; ?>" href="services.php">
                        <i class="fas fa-cogs"></i>
                        <span>Hizmetler</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>" href="products.php">
                        <i class="fas fa-box"></i>
                        <span>Ürünler</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'active' : ''; ?>" href="messages.php">
                        <i class="fas fa-envelope"></i>
                        <span>Mesajlar</span>
                        <?php
                        try {
                            $new_messages = $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn();
                            if ($new_messages > 0):
                        ?>
                                <span class="badge bg-danger"><?php echo $new_messages; ?></span>
                        <?php 
                            endif;
                        } catch(Exception $e) {
                            // Ignore
                        }
                        ?>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'about.php' ? 'active' : ''; ?>" href="about.php">
                        <i class="fas fa-user"></i>
                        <span>Hakkımda</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'blog.php' ? 'active' : ''; ?>" href="blog.php">
                        <i class="fas fa-blog"></i>
                        <span>Blog</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'content-management.php' ? 'active' : ''; ?>" href="content-management.php">
                        <i class="fas fa-edit"></i>
                        <span>İçerik Yönetimi</span>
                    </a>
                </li>
                
                <!-- Yeni Özellikler - Developer: BERAT K -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin-users.php' ? 'active' : ''; ?>" href="admin-users.php">
                        <i class="fas fa-users-cog"></i>
                        <span>Admin Kullanıcıları</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'content-ordering.php' ? 'active' : ''; ?>" href="content-ordering.php">
                        <i class="fas fa-sort"></i>
                        <span>İçerik Sıralama</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'text-management.php' ? 'active' : ''; ?>" href="text-management.php">
                        <i class="fas fa-font"></i>
                        <span>Metin Yönetimi</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'gallery-management.php' ? 'active' : ''; ?>" href="gallery-management.php">
                        <i class="fas fa-images"></i>
                        <span>Galeri Yönetimi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'footer-management.php' ? 'active' : ''; ?>" href="footer-management.php">
                        <i class="fas fa-link"></i>
                        <span>Footer Yönetimi</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>" href="settings.php">
                        <i class="fas fa-cog"></i>
                        <span>Ayarlar</span>
                    </a>
                </li>
                
                <!-- Kurulum - Developer: BERAT K -->
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'setup_new_features.php' ? 'active' : ''; ?>" href="setup_new_features.php">
                        <i class="fas fa-rocket text-warning"></i>
                        <span>Yeni Özellikler</span>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'upload-debug.php' ? 'active' : ''; ?>" href="upload-debug.php">
                        <i class="fas fa-bug"></i>
                        <span>Upload Debug</span>
                    </a>
                </li>
                <li class="nav-item mt-3">
                    <a class="nav-link" href="../index.php" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        <span>Siteyi Görüntüle</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-danger" href="logout.php">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Çıkış Yap</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
    
    <div class="main-content">
        <div class="topbar">
            <div class="d-flex justify-content-between align-items-center w-100">
                <div class="d-flex align-items-center">
                    <?php if(isset($page_title)): ?>
                        <h5 class="mb-0"><?php echo $page_title; ?></h5>
                    <?php endif; ?>
                </div>
                
                <div class="d-flex align-items-center">
                    <div class="dropdown">
                        <button class="btn btn-outline-light btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i><?php echo $_SESSION['admin_username'] ?? 'Admin'; ?>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="settings.php"><i class="fas fa-cog me-2"></i>Ayarlar</a></li>
                            <li><hr class="dropdown-divider" style="border-color: var(--dark-border);"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Çıkış Yap</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="content-area">
</body>
</html>