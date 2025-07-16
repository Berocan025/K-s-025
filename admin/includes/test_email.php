<?php
/**
 * E-posta SMTP Test Sistemi
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 */

require_once '../../includes/functions.php';

if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
    $mail_class = '\PHPMailer\PHPMailer\PHPMailer';
} else {
    // Paylaşımlı hosting için manuel PHPMailer
    require_once __DIR__ . '/../../phpmailer/PHPMailer.php';
    require_once __DIR__ . '/../../phpmailer/SMTP.php';
    require_once __DIR__ . '/../../phpmailer/Exception.php';
    $mail_class = 'PHPMailer\PHPMailer\PHPMailer';
}

requireLogin();

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Geçersiz veri formatı');
    }
    
    $smtp_host = $input['smtp_host'] ?? '';
    $smtp_port = $input['smtp_port'] ?? '587';
    $smtp_username = $input['smtp_username'] ?? '';
    $smtp_password = $input['smtp_password'] ?? '';
    $smtp_security = $input['smtp_security'] ?? 'tls';
    
    if (empty($smtp_host) || empty($smtp_username)) {
        throw new Exception('SMTP sunucu ve e-posta adresi gerekli');
    }
    
    // PHPMailer ile SMTP test
    $mail = new $mail_class(true);
    
    try {
        // SMTP ayarları
        $mail->isSMTP();
        $mail->Host = $smtp_host;
        $mail->SMTPAuth = true;
        $mail->Username = $smtp_username;
        $mail->Password = $smtp_password;
        $mail->Port = (int)$smtp_port;
        
        // Güvenlik ayarı
        if ($smtp_security === 'ssl') {
            $mail->SMTPSecure = 'ssl';
        } elseif ($smtp_security === 'tls') {
            $mail->SMTPSecure = 'tls';
        }
        
        // Debug kapalı (production için)
        $mail->SMTPDebug = 0;
        $mail->Debugoutput = 'html';
        
        // Karakter seti
        $mail->CharSet = 'UTF-8';
        
        // Gönderen bilgileri
        $site_brand = getSetting('site_brand', 'BERAT K - R10');
        $mail->setFrom($smtp_username, $site_brand);
        $mail->addAddress($smtp_username);
        $mail->addReplyTo($smtp_username, $site_brand);
        
        // E-posta içeriği
        $mail->isHTML(false);
        $mail->Subject = 'SMTP Test - ' . $site_brand;
        $mail->Body = "SMTP Bağlantı Testi

Bu e-posta SMTP ayarlarınızı test etmek için gönderilmiştir.

SMTP Ayarları:
• Sunucu: $smtp_host
• Port: $smtp_port  
• Güvenlik: " . strtoupper($smtp_security) . "
• Kullanıcı: $smtp_username

Test Tarihi: " . date('d.m.Y H:i:s') . "

Bu e-postayı aldıysanız SMTP ayarlarınız doğru çalışıyor demektir.

Saygılarımla,
$site_brand
Otomatik Test Sistemi";
        
        // E-postayı gönder
        $mail->send();
        
        echo json_encode([
            'success' => true,
            'message' => 'Test e-postası başarıyla gönderildi! E-posta hesabınızı kontrol edin.',
            'details' => [
                'smtp_host' => $smtp_host,
                'smtp_port' => $smtp_port,
                'smtp_security' => $smtp_security,
                'to_address' => $smtp_username
            ]
        ]);
        
    } catch (Exception $e) {
        throw new Exception('SMTP Hatası: ' . $mail->ErrorInfo . ' (' . $e->getMessage() . ')');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'suggestion' => 'SMTP ayarlarınızı kontrol edin. Sunucu adresi, port, kullanıcı adı ve şifre doğru olmalı.'
    ]);
}
?>