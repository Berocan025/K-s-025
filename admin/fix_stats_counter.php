<?php
/**
 * İSTATİSTİK SAYACI TAMİRİ
 * Developer: BERAT K - R10
 * 
 * SORUN: Ana sayfada istatistikler null+ gösteriyor
 * ÇÖZÜM: Settings tablosuna eksik stat değerlerini ekle
 */

require_once '../includes/functions.php';
requireLogin();

echo "<!DOCTYPE html>";
echo "<html><head><meta charset='UTF-8'><title>📊 İSTATİSTİK TAMİRİ - BERAT K</title>";
echo "<style>body{background:#1a1a2e;color:#16d9e3;font-family:monospace;padding:20px;}";
echo ".success{color:#0f3460;background:#16d9e3;padding:5px;margin:5px 0;border-radius:3px;}";
echo ".error{color:#e94560;}.warning{color:#f5af19;}</style></head><body>";

echo "<h1>📊 İSTATİSTİK SAYACI TAMİRİ</h1>";
echo "<p>Developer: <strong>BERAT K - R10</strong></p>";
echo "<hr>";

try {
    echo "<h2>🔍 Mevcut İstatistikleri Kontrol Ediyorum...</h2>";
    
    // Mevcut stat değerlerini kontrol et
    $stat_keys = ['stat_projects', 'stat_clients', 'stat_years', 'stat_awards'];
    $missing_stats = [];
    
    foreach ($stat_keys as $key) {
        $stmt = $pdo->prepare("SELECT setting_value FROM settings WHERE setting_key = ?");
        $stmt->execute([$key]);
        $value = $stmt->fetchColumn();
        
        if ($value === false || $value === null || $value === '') {
            echo "<span class='error'>❌ $key eksik veya boş</span><br>";
            $missing_stats[] = $key;
        } else {
            echo "<span class='success'>✅ $key = $value</span><br>";
        }
    }
    
    if (empty($missing_stats)) {
        echo "<h2 class='success'>🎉 TÜM İSTATİSTİKLER MEVCUT!</h2>";
        echo "<p><a href='../index.php' target='_blank'>Ana Sayfayı Kontrol Et</a></p>";
        exit;
    }
    
    echo "<h2>⚠️ Eksik İstatistikleri Ekliyorum...</h2>";
    
    // Eksik istatistikleri ekle/güncelle
    $default_stats = [
        'stat_projects' => '150',
        'stat_clients' => '85',
        'stat_years' => '5', 
        'stat_awards' => '12'
    ];
    
    $labels = [
        'stat_projects' => 'Başarılı Platform',
        'stat_clients' => 'Mutlu Müşteri', 
        'stat_years' => 'Yıl Deneyim',
        'stat_awards' => 'Ödül & Başarı'
    ];
    
    foreach ($missing_stats as $key) {
        $value = $default_stats[$key];
        
        // INSERT OR REPLACE kullan
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute([$key, $value]);
        
        echo "<span class='success'>✅ $key = $value eklendi</span><br>";
        
        // Aynı zamanda site_texts'e de ekle
        $label = $labels[$key] ?? ucfirst(str_replace('_', ' ', $key));
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO site_texts (category_id, text_key, text_label, text_value, text_type, page_location) VALUES (1, ?, ?, ?, 'number', 'Ana Sayfa')");
        $stmt->execute([$key, $label, $value]);
    }
    
    // Ayrıca stat_*_label değerlerini de ekle
    echo "<h3>🏷️ İstatistik Etiketlerini Ekliyorum...</h3>";
    
    $label_defaults = [
        'stat_projects_label' => 'Başarılı Platform',
        'stat_clients_label' => 'Mutlu Müşteri',
        'stat_years_label' => 'Yıl Deneyim', 
        'stat_awards_label' => 'Ödül & Başarı'
    ];
    
    foreach ($label_defaults as $key => $value) {
        $stmt = $pdo->prepare("INSERT OR REPLACE INTO settings (setting_key, setting_value) VALUES (?, ?)");
        $stmt->execute([$key, $value]);
        
        $stmt = $pdo->prepare("INSERT OR IGNORE INTO site_texts (category_id, text_key, text_label, text_value, text_type, page_location) VALUES (1, ?, ?, ?, 'text', 'Ana Sayfa')");
        $stmt->execute([$key, str_replace('_label', ' Etiketi', $key), $value]);
        
        echo "<span class='success'>✅ $key = $value eklendi</span><br>";
    }
    
    echo "<h2>🔧 loadBulkSettings Fonksiyonunu Test Ediyorum...</h2>";
    
    // Test bulk loading
    $test_keys = ['stat_projects', 'stat_clients', 'stat_years', 'stat_awards'];
    $bulk_result = loadBulkSettings($test_keys);
    
    foreach ($test_keys as $key) {
        $value = $bulk_result[$key] ?? 'YOK';
        if ($value !== 'YOK') {
            echo "<span class='success'>✅ loadBulkSettings[$key] = $value</span><br>";
        } else {
            echo "<span class='error'>❌ loadBulkSettings[$key] çalışmıyor!</span><br>";
        }
    }
    
    echo "<h2>🎯 Manuel Test</h2>";
    echo "<p>Ana sayfa istatistikleri:</p>";
    
    // Manuel test
    $manual_stats = [
        'stat_projects' => getSetting('stat_projects', '150'),
        'stat_clients' => getSetting('stat_clients', '85'),
        'stat_years' => getSetting('stat_years', '5'),
        'stat_awards' => getSetting('stat_awards', '12')
    ];
    
    foreach ($manual_stats as $key => $value) {
        echo "<strong>$key:</strong> $value<br>";
    }
    
    echo "<h2 class='success'>🎉 İSTATİSTİK TAMİRİ TAMAMLANDI!</h2>";
    echo "<hr>";
    
    echo "<h3>✅ TAMİR EDİLEN DEĞERLER:</h3>";
    foreach ($missing_stats as $stat) {
        $value = $default_stats[$stat];
        echo "<span class='success'>✅ $stat = $value</span><br>";
    }
    
    echo "<h3>🚀 ŞİMDİ TEST EDİN:</h3>";
    echo "<p><a href='../index.php' target='_blank' style='color:#16d9e3;'>Ana Sayfayı Görüntüle</a></p>";
    echo "<p><a href='settings.php' style='color:#16d9e3;'>Ayarlar Sayfası</a></p>";
    
    echo "<hr>";
    echo "<p><strong>Developer: BERAT K - R10</strong> - İstatistikler düzeltildi! ✅</p>";
    
} catch (Exception $e) {
    echo "<h2 class='error'>❌ HATA OLUŞTU!</h2>";
    echo "<p class='error'>Hata: " . $e->getMessage() . "</p>";
}

echo "</body></html>";
?>