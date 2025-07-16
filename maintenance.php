<?php
/**
 * Maintenance Mode Page
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Site Bakımda - <?php echo getSetting('site_title', 'BERAT K - R10'); ?></title>
    <meta name="description" content="Site geçici olarak bakımda. Kısa süre sonra tekrar hizmetinizde olacağız.">
    
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <link rel="icon" type="image/x-icon" href="<?php echo getSetting('site_favicon', 'assets/img/favicon.ico'); ?>">
    
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #fd79a8;
            --dark-bg: #0d1117;
            --dark-card: #161b22;
            --text-light: #f0f6fc;
            --gradient: linear-gradient(135deg, #6c5ce7, #fd79a8);
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            background: var(--dark-bg);
            color: var(--text-light);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .maintenance-container {
            text-align: center;
            max-width: 600px;
            padding: 40px;
        }
        
        .maintenance-icon {
            font-size: 100px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 30px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        .maintenance-title {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .maintenance-text {
            font-size: 1.1rem;
            color: var(--text-light);
            opacity: 0.8;
            margin-bottom: 30px;
            line-height: 1.6;
        }
        
        .maintenance-card {
            background: var(--dark-card);
            border: 1px solid rgba(108, 92, 231, 0.2);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .contact-info {
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid rgba(108, 92, 231, 0.2);
        }
        
        .contact-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .contact-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }
        
        .floating-elements {
            position: fixed;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: -1;
        }
        
        .floating-element {
            position: absolute;
            opacity: 0.05;
            animation: float 6s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        @media (max-width: 768px) {
            .maintenance-container {
                padding: 20px;
            }
            
            .maintenance-icon {
                font-size: 80px;
            }
            
            .maintenance-title {
                font-size: 2rem;
            }
            
            .maintenance-card {
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="floating-elements">
        <div class="floating-element" style="top: 20%; left: 10%; animation-delay: 0s;">
            <i class="fas fa-tools" style="font-size: 50px;"></i>
        </div>
        <div class="floating-element" style="top: 40%; right: 15%; animation-delay: 2s;">
            <i class="fas fa-cog" style="font-size: 40px;"></i>
        </div>
        <div class="floating-element" style="bottom: 30%; left: 20%; animation-delay: 4s;">
            <i class="fas fa-wrench" style="font-size: 45px;"></i>
        </div>
        <div class="floating-element" style="top: 60%; right: 30%; animation-delay: 1s;">
            <i class="fas fa-hammer" style="font-size: 35px;"></i>
        </div>
    </div>

    <div class="maintenance-container">
        <div class="maintenance-card">
            <div class="maintenance-icon">
                <i class="fas fa-tools"></i>
            </div>
            
            <h1 class="maintenance-title">Site Bakımda</h1>
            
            <p class="maintenance-text">
                Daha iyi hizmet verebilmek için sitemizde geçici bakım çalışması yapıyoruz. 
                Kısa süre içerisinde tekrar hizmetinizde olacağız.
            </p>
            
            <div class="maintenance-text">
                <i class="fas fa-clock me-2"></i>
                Tahmini süre: <strong>1-2 saat</strong>
            </div>
            
            <div class="contact-info">
                <p class="mb-2">
                    <i class="fas fa-envelope me-2"></i>
                    Acil durumlar için: 
                    <a href="mailto:<?php echo getSetting('contact_email', 'info@beratk.com'); ?>" class="contact-link">
                        <?php echo getSetting('contact_email', 'info@beratk.com'); ?>
                    </a>
                </p>
                
                <?php if(getSetting('contact_phone')): ?>
                <p class="mb-0">
                    <i class="fas fa-phone me-2"></i>
                    Telefon: 
                    <a href="tel:<?php echo getSetting('contact_phone'); ?>" class="contact-link">
                        <?php echo getSetting('contact_phone'); ?>
                    </a>
                </p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>