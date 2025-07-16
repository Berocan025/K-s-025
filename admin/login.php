<?php
/**
 * Admin Login Page
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 */

session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean($_POST['username']);
    $password = $_POST['password'];
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Brute force kontrolü
    if (checkBruteForce($ip_address)) {
        $error = '🔒 Güvenlik: Çok fazla başarısız deneme! 15 dakika sonra tekrar deneyin.';
        logLoginAttempt($ip_address, $username, false);
    } elseif (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                $_SESSION['admin_email'] = $user['email'];
                
                // Başarılı giriş kaydı
                logLoginAttempt($ip_address, $username, true);
                
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Kullanıcı adı veya şifre hatalı!';
                // Başarısız giriş kaydı
                logLoginAttempt($ip_address, $username, false);
            }
        } catch(PDOException $e) {
            $error = 'Giriş sırasında bir hata oluştu!';
            logLoginAttempt($ip_address, $username, false);
        }
    } else {
        $error = 'Lütfen tüm alanları doldurun.';
        logLoginAttempt($ip_address, $username, false);
    }
}

// Function already defined in includes/functions.php
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Girişi - BERAT K - R10</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #fd79a8;
            --dark-bg: #0d1117;
            --dark-card: #161b22;
            --dark-border: #21262d;
            --text-light: #f0f6fc;
            --text-muted: #8b949e;
            --gradient: linear-gradient(135deg, #6c5ce7, #fd79a8);
        }
        
        body {
            background: var(--dark-bg);
            color: var(--text-light);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            background-image: 
                radial-gradient(circle at 20% 50%, rgba(108, 92, 231, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(253, 121, 168, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 80%, rgba(108, 92, 231, 0.05) 0%, transparent 50%);
        }
        
        .login-card {
            background-color: var(--dark-card);
            border: 1px solid var(--dark-border);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }
        
        .text-gradient {
            background: var(--gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
        }
        
        .btn-gradient {
            background: var(--gradient);
            border: none;
            color: white;
            font-weight: 500;
            border-radius: 25px;
            padding: 12px 30px;
            transition: all 0.3s ease;
        }
        
        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108, 92, 231, 0.3);
            color: white;
        }
        
        .form-control {
            background-color: var(--dark-bg);
            border: 2px solid var(--dark-border);
            color: var(--text-light);
            border-radius: 15px;
            padding: 15px 20px;
            font-size: 16px;
            transition: all 0.3s ease;
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
        
        .input-group {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-muted);
            z-index: 5;
        }
        
        .input-group .form-control {
            padding-left: 50px;
        }
        
        .alert {
            border-radius: 15px;
            border: none;
        }
        
        .alert-danger {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
            border-left: 4px solid #e74c3c;
        }
        
        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .logo-icon {
            width: 80px;
            height: 80px;
            background: var(--gradient);
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin-bottom: 20px;
        }
        
        .floating-elements {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 1;
        }
        
        .floating-element {
            position: absolute;
            opacity: 0.05;
            animation: float 8s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-30px); }
        }
    </style>
</head>
<body>
    <div class="floating-elements">
        <div class="floating-element" style="top: 10%; left: 10%; animation-delay: 0s;">
            <i class="fas fa-code" style="font-size: 60px; color: var(--primary-color);"></i>
        </div>
        <div class="floating-element" style="top: 20%; right: 10%; animation-delay: 3s;">
            <i class="fas fa-laptop-code" style="font-size: 50px; color: var(--secondary-color);"></i>
        </div>
        <div class="floating-element" style="bottom: 20%; left: 15%; animation-delay: 6s;">
            <i class="fas fa-cogs" style="font-size: 55px; color: var(--primary-color);"></i>
        </div>
    </div>

    <div class="container" style="position: relative; z-index: 10;">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="login-card p-5">
                    <div class="logo-section">
                        <div class="logo-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <h1 class="text-gradient mb-2">BERAT K - R10</h1>
                        <h4 class="mb-3" style="color: var(--text-light); font-weight: 500;">Admin Paneli</h4>
                        <p style="color: var(--text-light); opacity: 0.9; font-weight: 400;">Hoş geldiniz! Devam etmek için giriş yapın.</p>
                    </div>
                    
                    <?php if ($error): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" class="mt-4">
                        <div class="mb-4">
                            <div class="input-group">
                                <i class="fas fa-user input-icon"></i>
                                <input type="text" class="form-control" name="username" placeholder="Kullanıcı Adı" required autofocus>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="input-group">
                                <i class="fas fa-lock input-icon"></i>
                                <input type="password" class="form-control" name="password" placeholder="Şifre" required>
                            </div>
                        </div>
                        
                        <div class="d-grid mb-4">
                            <button type="submit" class="btn btn-gradient btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Giriş Yap
                            </button>
                        </div>
                        
                        <div class="text-center">
                            <a href="../index.php" style="color: var(--text-light); opacity: 0.7; text-decoration: none;">
                                <i class="fas fa-arrow-left me-2"></i>Ana Sayfaya Dön
                            </a>
                        </div>
                    </form>
                    
                    <div class="text-center mt-4 pt-4 border-top border-secondary">
                        <small style="color: var(--text-light); opacity: 0.6;">
                            © <?php echo date('Y'); ?> BERAT K - R10. Tüm hakları saklıdır.
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });
            
            const form = document.querySelector('form');
            form.addEventListener('submit', function() {
                const submitBtn = this.querySelector('button[type="submit"]');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Giriş yapılıyor...';
                submitBtn.disabled = true;
            });
        });
    </script>
</body>
</html>