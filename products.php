<?php
/**
 * Products Page - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once 'includes/functions.php';
checkMaintenanceMode();
trackVisitor(); // Ziyaretçi sayacı
$page_title = 'Premium Ürünler';

$products = getProducts();
?>

<?php include 'includes/header.php'; ?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h1 class="display-4 mb-4">
                    <span class="text-gradient">Premium Ürünlerim</span>
                </h1>
                <p class="lead text-muted">
                    <?php echo getContentWithVariables('products_intro', 'BERAT K - R10 tarafından özel olarak tasarlanan premium kumar ürünleri. Lüks, güvenilir ve karlı çözümler.'); ?>
                </p>
            </div>
        </div>
        
        <?php if (!empty($products)): ?>
            <div class="row">
                <?php foreach ($products as $product): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="product-card animate-on-scroll">
                            <div class="product-image" style="background-image: url('<?php echo $product['image'] ? htmlspecialchars($product['image']) : 'assets/img/product-placeholder.jpg'; ?>');"></div>
                            <div class="product-content">
                                <h5 class="product-title"><?php echo htmlspecialchars($product['title']); ?></h5>
                                <p class="product-description"><?php echo htmlspecialchars($product['description']); ?></p>
                                
                                <?php if($product['price']): ?>
                                <div class="product-price mb-3">
                                    <span class="price-text text-gradient fw-bold">
                                        <i class="fas fa-tag me-1"></i>
                                        <?php echo htmlspecialchars($product['price']); ?> ₺
                                    </span>
                                </div>
                                <?php endif; ?>
                                
                                <div class="product-links mb-3">
                                    <?php if($product['demo_url']): ?>
                                        <a href="<?php echo htmlspecialchars($product['demo_url']); ?>" target="_blank" class="btn btn-outline-gradient btn-sm">
                                            <i class="fas fa-eye me-1"></i>Demo
                                        </a>
                                    <?php endif; ?>
                                    <?php if($product['admin_demo_url']): ?>
                                        <a href="<?php echo htmlspecialchars($product['admin_demo_url']); ?>" target="_blank" class="btn btn-gradient btn-sm">
                                            <i class="fas fa-cog me-1"></i>Admin Demo
                                        </a>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="whatsapp-contact mb-3">
                                    <a href="https://wa.me/<?php echo str_replace([' ', '+', '-', '(', ')'], '', getSetting('whatsapp_phone', '05395115632')); ?>?text=Merhaba! <?php echo urlencode($product['title']); ?> ürünü hakkında bilgi almak istiyorum.%0A%0AÜrün Linki: <?php echo urlencode("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" 
                                       target="_blank" class="btn btn-success btn-sm w-100">
                                        <i class="fab fa-whatsapp me-1"></i>WhatsApp ile İletişime Geç
                                    </a>
                                </div>
                                
                                <div class="product-footer pt-3 border-top border-secondary">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i>
                                        <?php echo formatDate($product['created_at']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-box-open fa-5x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">Henüz ürün eklenmemiş</h3>
                        <p class="text-muted mb-4">Yeni ürünler yakında piyasaya sürülecek.</p>
                        <a href="contact.php" class="btn btn-gradient">Özel Çözüm İste</a>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section class="py-5" style="background: rgba(108, 92, 231, 0.05);">
    <div class="container">
        <h2 class="section-title animate-on-scroll">Neden <?php echo getSetting('site_brand', 'BERAT K - R10'); ?> Ürünlerini Seçmelisiniz?</h2>
        
        <div class="row">
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box animate-on-scroll">
                    <div class="feature-icon">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <h5>Güvenli & Stabil</h5>
                    <p>Tüm ürünlerimiz en yüksek güvenlik standartlarında geliştirilir ve sürekli güncellenir.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box animate-on-scroll">
                    <div class="feature-icon">
                        <i class="fas fa-mobile-alt"></i>
                    </div>
                    <h5>Responsive Tasarım</h5>
                    <p>Tüm cihazlarda mükemmel çalışan, kullanıcı dostu arayüzler tasarlıyoruz.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box animate-on-scroll">
                    <div class="feature-icon">
                        <i class="fas fa-headset"></i>
                    </div>
                    <h5>Sürekli Destek</h5>
                    <p>Ürün satın alma sonrası teknik destek ve güncellemeler garantilidir.</p>
                </div>
            </div>
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="feature-box animate-on-scroll">
                    <div class="feature-icon">
                        <i class="fas fa-rocket"></i>
                    </div>
                    <h5>Hızlı Performans</h5>
                    <p>Optimize edilmiş kodlar ile yüksek performans ve hızlı yükleme süreleri.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: var(--dark-card);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="text-gradient mb-3">Özel Bir Ürün mü İstiyorsunuz?</h3>
                <p class="mb-0">İhtiyaçlarınıza özel yazılım çözümleri geliştirmek için benimle iletişime geçin. <?php echo getSetting('site_brand', 'BERAT K - R10'); ?> ile fark yaratın!</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="contact.php" class="btn btn-gradient btn-lg">
                    <i class="fas fa-lightbulb me-2"></i>Özel Çözüm
                </a>
            </div>
        </div>
    </div>
</section>

<style>
.feature-box {
    text-align: center;
    padding: 30px 20px;
    background: var(--dark-card);
    border-radius: 15px;
    border: 1px solid var(--dark-border);
    height: 100%;
    transition: all 0.3s ease;
}

.feature-box:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-hover);
}

.feature-icon {
    width: 70px;
    height: 70px;
    background: var(--gradient);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    color: white;
    margin: 0 auto 20px;
}

.empty-state {
    padding: 60px 20px;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.product-price {
    background: rgba(108, 92, 231, 0.1);
    border-radius: 8px;
    padding: 8px 12px;
    border: 1px solid rgba(108, 92, 231, 0.3);
}

.price-text {
    font-size: 1.1rem;
}

.whatsapp-contact .btn {
    background: #25D366;
    border-color: #25D366;
    transition: all 0.3s ease;
}

.whatsapp-contact .btn:hover {
    background: #128C7E;
    border-color: #128C7E;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(37, 211, 102, 0.3);
}
</style>

<?php include 'includes/footer.php'; ?>