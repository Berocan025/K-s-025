<?php
/**
 * Admin Dashboard
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$page_title = 'Dashboard';

$stats = [
    'projects' => $pdo->query("SELECT COUNT(*) FROM projects")->fetchColumn(),
    'services' => $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn(),
    'products' => $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn(),
    'messages' => $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn(),
    'new_messages' => $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn()
];

// Ziyaretçi istatistikleri
$visitor_stats = getVisitorStats();

// Sistem istatistikleri
$system_stats = getSystemStats();

// Brute force istatistikleri
$brute_force_stats = getBruteForceStats();

// Sayfa analizi
$page_analytics = getPageAnalytics(7);

$recent_projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC LIMIT 5")->fetchAll();
$recent_messages = $pdo->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetchAll();

// Son 7 günün ziyaretçi verileri (grafik için)
$visitor_chart_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $date_formatted = date('M d', strtotime($date));
    
    try {
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM visitors WHERE visit_date = ?");
        $stmt->execute([$date]);
        $count = $stmt->fetchColumn();
    } catch(PDOException $e) {
        $count = 0;
    }
    
    $visitor_chart_data[] = [
        'date' => $date_formatted,
        'visitors' => $count
    ];
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="../index.php" target="_blank" class="btn btn-outline-light btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i>Siteyi Görüntüle
                </a>
                <a href="settings.php" class="btn btn-outline-gradient btn-sm">
                    <i class="fas fa-cog me-1"></i>Ayarlar
                </a>
            </div>
        </div>
    </div>
    
    <div class="alert alert-info border-0" style="background: rgba(108, 92, 231, 0.1); border-left: 4px solid var(--primary-color) !important;">
        <div class="d-flex align-items-center">
            <i class="fas fa-info-circle me-2"></i>
            <div>
                <strong>Hoş geldiniz <?php echo $_SESSION['admin_username']; ?>!</strong> 
                Dashboard'unuzda sitenizin performansını takip edebilirsiniz.
            </div>
        </div>
    </div>
    
    <!-- İçerik İstatistikleri -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #6c5ce7, #74b9ff);">
                                <i class="fas fa-project-diagram"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-light">Toplam Proje</div>
                            <div class="h4 mb-0 text-light"><?php echo $stats['projects']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="projects.php" class="btn btn-outline-gradient btn-sm">Yönet</a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #fd79a8, #e84393);">
                                <i class="fas fa-cogs"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-light">Hizmetler</div>
                            <div class="h4 mb-0 text-light"><?php echo $stats['services']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="services.php" class="btn btn-outline-gradient btn-sm">Yönet</a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #00b894, #00cec9);">
                                <i class="fas fa-box"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-light">Ürünler</div>
                            <div class="h4 mb-0 text-light"><?php echo $stats['products']; ?></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="products.php" class="btn btn-outline-gradient btn-sm">Yönet</a>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #fdcb6e, #f39c12);">
                                <i class="fas fa-envelope"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-light">Mesajlar</div>
                            <div class="h4 mb-0 text-light">
                                <?php echo $stats['messages']; ?>
                                <?php if($stats['new_messages'] > 0): ?>
                                    <span class="badge bg-danger ms-2"><?php echo $stats['new_messages']; ?></span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <a href="messages.php" class="btn btn-outline-gradient btn-sm">Görüntüle</a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ziyaretçi İstatistikleri -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #a29bfe, #6c5ce7);">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-light">Bugünkü Ziyaretçi</div>
                            <div class="h4 mb-0 text-light"><?php echo number_format($visitor_stats['today']); ?></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <small class="text-muted">Bu hafta: <?php echo number_format($visitor_stats['week']); ?></small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #fd79a8, #fdcb6e);">
                                <i class="fas fa-eye"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-light">Sayfa Görüntülenme</div>
                            <div class="h4 mb-0 text-light"><?php echo number_format($visitor_stats['page_views_today']); ?></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <small class="text-muted">Toplam: <?php echo number_format($visitor_stats['page_views_total']); ?></small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #00b894, #55a3ff);">
                                <i class="fas fa-chart-line"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-light">Toplam Ziyaretçi</div>
                            <div class="h4 mb-0 text-light"><?php echo number_format($visitor_stats['total']); ?></div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <small class="text-muted">Bu ay: <?php echo number_format($visitor_stats['month']); ?></small>
                </div>
            </div>
        </div>
        
        <div class="col-xl-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="stat-icon" style="background: linear-gradient(45deg, #ff7675, #fd79a8);">
                                <i class="fas fa-server"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <div class="small text-light">Sistem Durumu</div>
                            <div class="h4 mb-0 text-light">
                                <span class="badge bg-success">Online</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent">
                    <small class="text-muted">PHP <?php echo $system_stats['php_version']; ?></small>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Ziyaretçi Grafiği ve Sistem Bilgileri -->
    <div class="row mb-4">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-light"><i class="fas fa-chart-area me-2"></i>Son 7 Günün Ziyaretçi Analizi</h5>
                </div>
                <div class="card-body">
                    <canvas id="visitorChart" height="100"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-light"><i class="fas fa-info-circle me-2"></i>Sistem Bilgileri</h5>
                </div>
                <div class="card-body">
                    <div class="system-info">
                        <div class="info-item mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-light">Disk Kullanımı</span>
                                <span class="text-muted"><?php echo $system_stats['disk_free']; ?> / <?php echo $system_stats['disk_total']; ?></span>
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-light">Bellek Kullanımı</span>
                                <span class="text-muted"><?php echo $system_stats['memory_usage']; ?></span>
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-light">PHP Versiyonu</span>
                                <span class="text-muted"><?php echo $system_stats['php_version']; ?></span>
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-light">Son Güncelleme</span>
                                <span class="text-muted"><?php echo date('d.m.Y H:i'); ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="text-center mt-4">
                        <a href="php-info.php" target="_blank" class="btn btn-outline-gradient btn-sm">
                            <i class="fas fa-info me-1"></i>PHP Info
                        </a>
                    </div>
                </div>
            </div>
        </div>
         </div>
 
     <!-- Güvenlik ve Analiz -->
     <div class="row mb-4">
         <div class="col-lg-8">
             <div class="card-custom">
                 <div class="card-header bg-transparent border-bottom border-secondary">
                     <h5 class="mb-0 text-light"><i class="fas fa-chart-bar me-2"></i>Popüler Sayfalar (Son 7 Gün)</h5>
                 </div>
                 <div class="card-body">
                     <?php if (!empty($page_analytics['popular_pages'])): ?>
                         <div class="table-responsive">
                             <table class="table table-dark table-hover">
                                 <thead>
                                     <tr>
                                         <th>Sayfa</th>
                                         <th>Görüntülenme</th>
                                         <th>Yüzde</th>
                                     </tr>
                                 </thead>
                                 <tbody>
                                     <?php 
                                     $total_views = array_sum(array_column($page_analytics['popular_pages'], 'views'));
                                     foreach ($page_analytics['popular_pages'] as $page): 
                                         $percentage = $total_views > 0 ? round(($page['views'] / $total_views) * 100, 1) : 0;
                                     ?>
                                         <tr>
                                             <td>
                                                 <i class="fas fa-file-alt me-2 text-muted"></i>
                                                 <?php echo htmlspecialchars($page['page_url'] ?: '/'); ?>
                                             </td>
                                             <td>
                                                 <span class="badge bg-gradient"><?php echo number_format($page['views']); ?></span>
                                             </td>
                                             <td>
                                                 <div class="progress" style="height: 10px;">
                                                     <div class="progress-bar bg-gradient" style="width: <?php echo $percentage; ?>%"></div>
                                                 </div>
                                                 <small class="text-muted"><?php echo $percentage; ?>%</small>
                                             </td>
                                         </tr>
                                     <?php endforeach; ?>
                                 </tbody>
                             </table>
                         </div>
                     <?php else: ?>
                         <div class="text-center py-4">
                             <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                             <p class="text-light">Henüz sayfa analizi verisi yok.</p>
                         </div>
                     <?php endif; ?>
                 </div>
             </div>
         </div>
         
         <div class="col-lg-4">
             <div class="card-custom">
                 <div class="card-header bg-transparent border-bottom border-secondary">
                     <h5 class="mb-0 text-light"><i class="fas fa-shield-alt me-2"></i>Güvenlik Durumu</h5>
                 </div>
                 <div class="card-body">
                     <div class="security-stats">
                         <div class="security-item mb-3 p-3 border border-secondary rounded">
                             <div class="d-flex justify-content-between align-items-center">
                                 <div>
                                     <i class="fas fa-lock text-success me-2"></i>
                                     <span class="text-light">Başarılı Giriş</span>
                                 </div>
                                 <span class="badge bg-success"><?php echo $brute_force_stats['today_success']; ?></span>
                             </div>
                         </div>
                         
                         <div class="security-item mb-3 p-3 border border-secondary rounded">
                             <div class="d-flex justify-content-between align-items-center">
                                 <div>
                                     <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                                     <span class="text-light">Başarısız Deneme</span>
                                 </div>
                                 <span class="badge bg-warning"><?php echo $brute_force_stats['today_failed']; ?></span>
                             </div>
                         </div>
                         
                         <div class="security-item mb-3 p-3 border border-secondary rounded">
                             <div class="d-flex justify-content-between align-items-center">
                                 <div>
                                     <i class="fas fa-ban text-danger me-2"></i>
                                     <span class="text-light">Engellenmiş IP</span>
                                 </div>
                                 <span class="badge bg-danger"><?php echo $brute_force_stats['blocked_ips']; ?></span>
                             </div>
                         </div>
                     </div>
                     
                     <?php if (!empty($brute_force_stats['top_ips'])): ?>
                         <div class="mt-4">
                             <h6 class="text-light mb-3">Şüpheli IP'ler</h6>
                             <?php foreach (array_slice($brute_force_stats['top_ips'], 0, 3) as $ip): ?>
                                 <div class="d-flex justify-content-between align-items-center mb-2">
                                     <small class="text-muted"><?php echo htmlspecialchars($ip['ip_address']); ?></small>
                                     <span class="badge bg-outline-danger"><?php echo $ip['attempts']; ?> deneme</span>
                                 </div>
                             <?php endforeach; ?>
                         </div>
                     <?php endif; ?>
                     
                     <div class="text-center mt-4">
                         <div class="row text-center">
                             <div class="col-6">
                                 <div class="security-badge p-2 border border-success rounded">
                                     <i class="fas fa-check-circle text-success"></i>
                                     <div class="small text-light mt-1">Güvenli</div>
                                 </div>
                             </div>
                             <div class="col-6">
                                 <div class="security-badge p-2 border border-info rounded">
                                     <i class="fas fa-sync fa-spin text-info"></i>
                                     <div class="small text-light mt-1">Aktif</div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 
     <div class="row">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-light"><i class="fas fa-project-diagram me-2"></i>Son Projeler</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_projects)): ?>
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>Proje Adı</th>
                                        <th>Kategori</th>
                                        <th>Durum</th>
                                        <th>Tarih</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_projects as $project): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if($project['image']): ?>
                                                        <img src="../<?php echo htmlspecialchars($project['image']); ?>" alt="" class="rounded me-2" width="40" height="40" style="object-fit: cover;">
                                                    <?php endif; ?>
                                                    <div>
                                                        <div class="fw-bold text-light"><?php echo htmlspecialchars($project['title']); ?></div>
                                                        <small class="text-light"><?php echo truncateText($project['description'], 50); ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <span class="badge bg-gradient"><?php echo htmlspecialchars($project['category']); ?></span>
                                            </td>
                                            <td>
                                                <?php if($project['status']): ?>
                                                    <span class="badge bg-success">Aktif</span>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary">Pasif</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-light"><?php echo formatDate($project['created_at']); ?></td>
                                            <td>
                                                <a href="projects.php?edit=<?php echo $project['id']; ?>" class="btn btn-outline-light btn-sm">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="projects.php" class="btn btn-gradient">Tüm Projeleri Görüntüle</a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-folder-open fa-3x text-muted mb-3"></i>
                            <p class="text-light">Henüz proje eklenmemiş.</p>
                            <a href="projects.php?add=1" class="btn btn-gradient">İlk Projeyi Ekle</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-light"><i class="fas fa-envelope me-2"></i>Son Mesajlar</h5>
                </div>
                <div class="card-body">
                    <?php if (!empty($recent_messages)): ?>
                        <?php foreach ($recent_messages as $message): ?>
                            <div class="d-flex mb-3 pb-3 <?php echo $message !== end($recent_messages) ? 'border-bottom border-secondary' : ''; ?>">
                                <div class="flex-shrink-0">
                                    <div class="avatar-circle <?php echo $message['status'] == 'new' ? 'bg-primary' : 'bg-secondary'; ?>">
                                        <?php echo strtoupper(substr($message['name'], 0, 1)); ?>
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mb-1 text-light"><?php echo htmlspecialchars($message['name']); ?></h6>
                                        <small class="text-light"><?php echo formatDate($message['created_at']); ?></small>
                                    </div>
                                    <p class="mb-1 small text-light"><?php echo truncateText($message['message'], 80); ?></p>
                                    <small class="text-light"><?php echo htmlspecialchars($message['email']); ?></small>
                                    <?php if($message['status'] == 'new'): ?>
                                        <span class="badge bg-danger ms-2">Yeni</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                        <div class="text-center">
                            <a href="messages.php" class="btn btn-outline-gradient btn-sm">Tüm Mesajları Görüntüle</a>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-2x text-muted mb-3"></i>
                            <p class="text-light">Henüz mesaj yok.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="card-custom mt-4">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-light"><i class="fas fa-chart-line me-2"></i>Hızlı İstatistikler</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-3">
                            <div class="border border-secondary rounded p-3">
                                <div class="h4 text-gradient mb-1"><?php echo getSetting('stat_projects', '150'); ?></div>
                                <small class="text-light">Tamamlanan Proje</small>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="border border-secondary rounded p-3">
                                <div class="h4 text-gradient mb-1"><?php echo getSetting('stat_clients', '85'); ?></div>
                                <small class="text-light">Mutlu Müşteri</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border border-secondary rounded p-3">
                                <div class="h4 text-gradient mb-1"><?php echo getSetting('stat_years', '5'); ?></div>
                                <small class="text-light">Yıllık Deneyim</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="border border-secondary rounded p-3">
                                <div class="h4 text-gradient mb-1"><?php echo getSetting('stat_awards', '12'); ?></div>
                                <small class="text-light">Ödül & Sertifika</small>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-3">
                        <a href="settings.php" class="btn btn-outline-gradient btn-sm">İstatistikleri Düzenle</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
}

.avatar-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
}

.table-dark {
    --bs-table-bg: var(--dark-card);
    border-color: var(--dark-border);
}

.table-dark th, .table-dark td {
    border-color: var(--dark-border);
}

.table-hover tbody tr:hover {
    background-color: rgba(108, 92, 231, 0.1);
}

.system-info .info-item {
    padding: 8px 0;
    border-bottom: 1px solid var(--dark-border);
}

.system-info .info-item:last-child {
    border-bottom: none;
}

canvas {
    max-height: 300px;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Ziyaretçi grafiği
const ctx = document.getElementById('visitorChart').getContext('2d');
const visitorChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?php echo json_encode(array_column($visitor_chart_data, 'date')); ?>,
        datasets: [{
            label: 'Günlük Ziyaretçi',
            data: <?php echo json_encode(array_column($visitor_chart_data, 'visitors')); ?>,
            borderColor: '#6c5ce7',
            backgroundColor: 'rgba(108, 92, 231, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4,
            pointBackgroundColor: '#6c5ce7',
            pointBorderColor: '#ffffff',
            pointBorderWidth: 2,
            pointRadius: 6,
            pointHoverRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                labels: {
                    color: '#f0f6fc'
                }
            }
        },
        scales: {
            x: {
                ticks: {
                    color: '#8b949e'
                },
                grid: {
                    color: '#21262d'
                }
            },
            y: {
                beginAtZero: true,
                ticks: {
                    color: '#8b949e',
                    stepSize: 1
                },
                grid: {
                    color: '#21262d'
                }
            }
        },
        elements: {
            point: {
                hoverBackgroundColor: '#fd79a8'
            }
        }
    }
});

// Gerçek zamanlı güncelleme
setInterval(() => {
    // Saati güncelle
    document.querySelector('.system-info .info-item:last-child .text-muted').textContent = 
        new Date().toLocaleString('tr-TR', {
            day: '2-digit',
            month: '2-digit', 
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
}, 60000); // Her dakika
</script>

<?php include 'includes/footer.php'; ?>