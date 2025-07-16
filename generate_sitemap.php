<?php
/**
 * Sitemap and Robots.txt Generator
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once 'includes/functions.php';

// Header content type
header('Content-Type: text/plain; charset=utf-8');

$action = $_GET['action'] ?? 'sitemap';

if ($action === 'sitemap') {
    if (generateSitemap()) {
        echo "✅ Sitemap.xml başarıyla oluşturuldu!\n";
        echo "📍 Konum: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/sitemap.xml\n";
        echo "🕒 Güncelleme: " . date('Y-m-d H:i:s') . "\n";
    } else {
        echo "❌ Sitemap oluşturulurken hata oluştu!\n";
    }
} elseif ($action === 'robots') {
    if (generateRobotsTxt()) {
        echo "✅ Robots.txt başarıyla oluşturuldu!\n";
        echo "📍 Konum: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/robots.txt\n";
        echo "🕒 Güncelleme: " . date('Y-m-d H:i:s') . "\n";
    } else {
        echo "❌ Robots.txt oluşturulurken hata oluştu!\n";
    }
} elseif ($action === 'both') {
    $sitemap_success = generateSitemap();
    $robots_success = generateRobotsTxt();
    
    if ($sitemap_success && $robots_success) {
        echo "✅ Sitemap.xml ve Robots.txt başarıyla oluşturuldu!\n\n";
        echo "📍 Sitemap: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/sitemap.xml\n";
        echo "📍 Robots: " . (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . "/robots.txt\n";
        echo "🕒 Güncelleme: " . date('Y-m-d H:i:s') . "\n";
    } else {
        echo "❌ Dosyalar oluşturulurken hata oluştu!\n";
        if (!$sitemap_success) echo "- Sitemap hatası\n";
        if (!$robots_success) echo "- Robots.txt hatası\n";
    }
} else {
    echo "❌ Geçersiz işlem!\n";
    echo "Kullanım:\n";
    echo "- ?action=sitemap (Sadece sitemap)\n";
    echo "- ?action=robots (Sadece robots.txt)\n";
    echo "- ?action=both (Her ikisini)\n";
}
?>