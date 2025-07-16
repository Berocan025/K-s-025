<?php
/**
 * Sayaç İstatistikleri Başlatma Scripti
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once 'config/database.php';
require_once 'includes/functions.php';

// Sayaç değerlerini ayarla
$default_stats = [
    'stat_projects' => '150',
    'stat_clients' => '85',
    'stat_years' => '5',
    'stat_awards' => '12'
];

try {
    echo "<h2>BERAT K - R10 Portfolio - İstatistik Sayaçlarını Başlatıyor...</h2>\n";
    
    // Her bir sayaç değeri için veritabanını güncelle
    foreach ($default_stats as $key => $value) {
        if (setSetting($key, $value)) {
            echo "✅ $key = $value (Başarıyla ayarlandı)<br>\n";
        } else {
            echo "❌ $key ayarlanırken hata oluştu<br>\n";
        }
    }
    
    echo "<br><h3>Mevcut Sayaç Değerleri:</h3>\n";
    foreach ($default_stats as $key => $default_value) {
        $current_value = getSetting($key, '0');
        echo "$key: <strong>$current_value</strong><br>\n";
    }
    
    echo "<br><p style='color: green;'><strong>✅ Tüm sayaçlar başarıyla ayarlandı!</strong></p>\n";
    echo "<p><a href='index.php'>Ana Sayfaya Git</a> | <a href='admin/login.php'>Admin Paneli</a></p>\n";
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Hata: " . $e->getMessage() . "</p>\n";
}
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
h1 { color: #6c5ce7; }
h2 { color: #2d3436; border-bottom: 2px solid #6c5ce7; padding-bottom: 5px; }
ul { background: #f8f9fa; padding: 15px; border-radius: 5px; }
li { margin: 5px 0; }
</style>