<?php
/**
 * Services Page - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once 'includes/functions.php';
checkMaintenanceMode();
trackVisitor(); // Ziyaretçi sayacı
$page_title = 'Platform Hizmetlerim';

$services = getServices();
?>

<?php include 'includes/header.php'; ?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h1 class="display-4 mb-4">
                    <span class="text-gradient">Platform Hizmetlerim</span>
                </h1>
                <p class="lead text-muted">
                    <?php echo getContentWithVariables('services_intro', 'BERAT K - R10 olarak sunduğum profesyonel kumar platform hizmetleri. Güvenli, karlı ve adil oyun deneyimleri.'); ?>
                </p>
            </div>
        </div>
        
        <?php if (!empty($services)): ?>
            <div class="row">
                <?php foreach ($services as $service): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="service-card animate-on-scroll">
                            <div class="service-icon">
                                <i class="<?php echo htmlspecialchars($service['icon']); ?>"></i>
                            </div>
                            <h4><?php echo htmlspecialchars($service['title']); ?></h4>
                            <p><?php echo htmlspecialchars($service['description']); ?></p>
                            <a href="contact.php" class="btn btn-outline-gradient">İş Birliği</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-cogs fa-5x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">Henüz hizmet eklenmemiş</h3>
                        <p class="text-muted mb-4">Yeni hizmetler yakında eklenecek.</p>
                        <a href="contact.php" class="btn btn-gradient">İletişime Geç</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="py-5" style="background: rgba(108, 92, 231, 0.05);">
    <div class="container">
        <h2 class="section-title animate-on-scroll"><?php echo getContent('services_process_title', 'Çalışma Sürecim'); ?></h2>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="process-step animate-on-scroll">
                    <div class="process-number">1</div>
                    <h5><?php echo getContent('process_step_1_title', 'Analiz & Planlama'); ?></h5>
                    <p><?php echo getContent('process_step_1_desc', 'Projenizin gereksinimlerini detaylı şekilde analiz ediyor ve en uygun çözümü planlıyorum.'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="process-step animate-on-scroll">
                    <div class="process-number">2</div>
                    <h5><?php echo getContent('process_step_2_title', 'Tasarım & Prototipler'); ?></h5>
                    <p><?php echo getContent('process_step_2_desc', 'Kullanıcı deneyimini ön planda tutarak modern ve etkileyici tasarımlar oluşturuyorum.'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="process-step animate-on-scroll">
                    <div class="process-number">3</div>
                    <h5><?php echo getContent('process_step_3_title', 'Geliştirme & Test'); ?></h5>
                    <p><?php echo getContent('process_step_3_desc', 'En son teknolojiler ile kodlama yapıyor ve kapsamlı testlerle kaliteyi garanti ediyorum.'); ?></p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="process-step animate-on-scroll">
                    <div class="process-number">4</div>
                    <h5><?php echo getContent('process_step_4_title', 'Teslim & Destek'); ?></h5>
                    <p><?php echo getContent('process_step_4_desc', 'Projenizi zamanında teslim ediyor ve sürekli teknik destek sağlıyorum.'); ?></p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: var(--dark-card);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="text-gradient mb-3">Projenize Hemen Başlayalım!</h3>
                <p class="mb-0">Hangi hizmete ihtiyacınız varsa, <?php echo getSetting('site_brand', 'BERAT K - R10'); ?> ile en kaliteli çözümü alacaksınız.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="contact.php" class="btn btn-gradient btn-lg">
                    <i class="fas fa-paper-plane me-2"></i>Teklif İste
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.process-step {
    text-align: center;
    padding: 30px 20px;
    background: var(--dark-card);
    border-radius: 15px;
    border: 1px solid var(--dark-border);
    height: 100%;
    transition: all 0.3s ease;
}

.process-step:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.process-number {
    width: 60px;
    height: 60px;
    background: var(--gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    font-weight: bold;
    color: white;
    margin: 0 auto 20px;
}

.empty-state {
    padding: 60px 20px;
}
</style>

<?php include 'includes/footer.php'; ?>