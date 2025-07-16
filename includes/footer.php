    </div>
    
    <footer class="footer-section bg-dark-custom py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 mb-4">
                    <h5 class="text-gradient mb-3"><?php echo getContent('footer_about_title', getSetting('site_brand', 'BERAT K - R10')); ?></h5>
                    <p class="footer-desc"><?php echo getContentWithVariables('footer_about_desc', 'Kumar endüstrisinin lider CEO\'su ve yayıncısı olarak, güvenli ve karlı platformlar sunuyorum.'); ?></p>
                    <div class="social-links">
                        <?php if(getSetting('social_github')): ?>
                            <a href="<?php echo getSetting('social_github'); ?>" target="_blank" class="social-link">
                                <i class="fab fa-github"></i>
                            </a>
                        <?php endif; ?>
                        <?php if(getSetting('social_linkedin')): ?>
                            <a href="<?php echo getSetting('social_linkedin'); ?>" target="_blank" class="social-link">
                                <i class="fab fa-linkedin-in"></i>
                            </a>
                        <?php endif; ?>
                        <?php if(getSetting('social_twitter')): ?>
                            <a href="<?php echo getSetting('social_twitter'); ?>" target="_blank" class="social-link">
                                <i class="fab fa-twitter"></i>
                            </a>
                        <?php endif; ?>
                        <?php if(getSetting('social_instagram')): ?>
                            <a href="<?php echo getSetting('social_instagram'); ?>" target="_blank" class="social-link">
                                <i class="fab fa-instagram"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-6 mb-4">
                    <h6 class="text-light mb-3"><?php echo getContent('footer_links_title', 'Hızlı Linkler'); ?></h6>
                    <ul class="list-unstyled footer-links">
                        <?php 
                        $quick_links = getFooterLinks('quick_links');
                        foreach($quick_links as $link):
                        ?>
                            <li><a href="<?php echo htmlspecialchars($link['link_url']); ?>"><?php echo htmlspecialchars($link['link_title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="text-light mb-3"><?php echo getContent('footer_services_title', 'Platform Hizmetlerim'); ?></h6>
                    <ul class="list-unstyled footer-links">
                        <?php 
                        $service_links = getFooterLinks('services');
                        foreach($service_links as $link):
                        ?>
                            <li><a href="<?php echo htmlspecialchars($link['link_url']); ?>"><?php echo htmlspecialchars($link['link_title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                
                <div class="col-lg-3 col-md-6 mb-4">
                    <h6 class="text-light mb-3"><?php echo getContent('footer_contact_title', 'İletişim Bilgileri'); ?></h6>
                    <ul class="list-unstyled footer-links">
                        <?php if(getSetting('contact_email')): ?>
                            <li>
                                <i class="fas fa-envelope me-2"></i>
                                <a href="mailto:<?php echo getSetting('contact_email'); ?>"><?php echo getSetting('contact_email'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if(getSetting('contact_phone')): ?>
                            <li>
                                <i class="fas fa-phone me-2"></i>
                                <a href="tel:<?php echo getSetting('contact_phone'); ?>"><?php echo getSetting('contact_phone'); ?></a>
                            </li>
                        <?php endif; ?>
                        <?php if(getSetting('contact_address')): ?>
                            <li>
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <span class="footer-address"><?php echo getSetting('contact_address'); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <hr class="border-secondary my-4">
            
            <div class="row align-items-center">
                <div class="col-md-6">
                    <p class="mb-0 footer-copyright"><?php echo getContent('footer_copyright', '© ' . date('Y') . ' ' . getSetting('site_brand', 'BERAT K - R10') . '. Tüm hakları saklıdır.'); ?></p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="mb-0 footer-dev">
                        <small class="footer-dev-text">Tasarım & Geliştirme: <span class="text-gradient"><?php echo getSetting('site_brand', 'BERAT K - R10'); ?></span></small>
                    </p>
                </div>
            </div>
        </div>
    </footer>
    
    <button class="scroll-to-top" onclick="scrollToTop()">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <?php echo getSetting('custom_footer_code', ''); ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/main.js"></script>
    
    <!-- Google Analytics -->
    <?php 
    $google_analytics = getSetting('google_analytics', '');
    if ($google_analytics): 
    ?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?php echo htmlspecialchars($google_analytics); ?>"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());
      gtag('config', '<?php echo htmlspecialchars($google_analytics); ?>');
    </script>
    <?php endif; ?>
    
    <!-- Özel JavaScript -->
    <?php if(getSetting('custom_js')): ?>
    <script>
    <?php echo getSetting('custom_js'); ?>
    </script>
    <?php endif; ?>
    
</body>
</html>

<?php
/**
 * Footer Template - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */
?>