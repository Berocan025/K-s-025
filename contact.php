<?php
/**
 * Contact Page - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once 'includes/functions.php';
checkMaintenanceMode();
trackVisitor(); // Ziyaretçi sayacı
$page_title = 'İş Birliği';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = clean($_POST['name']);
    $email = clean($_POST['email']);
    $subject = clean($_POST['subject']);
    $message = clean($_POST['message']);
    
    if (!empty($name) && !empty($email) && !empty($message)) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)");
                if ($stmt->execute([$name, $email, $subject, $message])) {
                    // E-posta bildirimi gönder
                    $emailSent = sendContactNotification($name, $email, $subject, $message);
                    
                    if ($emailSent) {
                        $success_message = 'Mesajınız başarıyla gönderildi! En kısa sürede size dönüş yapacağım.';
                    } else {
                        $success_message = 'Mesajınız kaydedildi! E-posta bildirimi gönderilemedi ancak mesajınızı admin panelinden görebilirim.';
                    }
                } else {
                    $error_message = 'Mesaj gönderilirken bir hata oluştu. Lütfen tekrar deneyin.';
                }
            } catch(PDOException $e) {
                $error_message = 'Bir hata oluştu. Lütfen daha sonra tekrar deneyin.';
            }
        } else {
            $error_message = 'Geçerli bir e-posta adresi giriniz.';
        }
    } else {
        $error_message = 'Lütfen tüm zorunlu alanları doldurunuz.';
    }
}
?>

<?php include 'includes/header.php'; ?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h1 class="display-4 mb-4">
                    <span class="text-gradient">İş Birliği Yapalım</span>
                </h1>
                <p class="lead text-muted">
                    <?php echo getContentWithVariables('contact_intro', 'Kumar endüstrisinde iş birliği fırsatları için benimle iletişime geçin. BERAT K - R10 olarak güvenli ve karlı platformlar kurmanızda size yardımcı olmak için buradayım.'); ?>
                </p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-8 mb-5">
                <div class="contact-form animate-on-scroll">
                    <h3 class="mb-4"><i class="fas fa-paper-plane me-2 text-gradient"></i>Mesaj Gönder</h3>
                    
                    <?php if ($success_message): ?>
                        <div class="alert alert-success" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" id="contactForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Adınız <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required 
                                       value="<?php echo isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ''; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">E-posta Adresiniz <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email" name="email" required
                                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="subject" class="form-label">Konu</label>
                            <input type="text" class="form-control" id="subject" name="subject"
                                   value="<?php echo isset($_POST['subject']) ? htmlspecialchars($_POST['subject']) : ''; ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label for="message" class="form-label">Mesajınız <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="message" name="message" rows="6" required 
                                      placeholder="Projeniz hakkında detaylı bilgi verebilirsiniz..."><?php echo isset($_POST['message']) ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
                        </div>
                        
                        <button type="submit" class="btn btn-gradient btn-lg">
                            <i class="fas fa-paper-plane me-2"></i>Mesajı Gönder
                        </button>
                    </form>
                </div>
            </div>
            
            <div class="col-lg-4">
                <div class="contact-info animate-on-scroll">
                    <h3 class="mb-4"><i class="fas fa-address-card me-2 text-gradient"></i>İletişim Bilgileri</h3>
                    
                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div>
                                <h5>E-posta</h5>
                                <p class="text-muted mb-0">
                                    <?php echo getSetting('contact_email', 'berat@r10.dev'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <?php if(getSetting('contact_phone')): ?>
                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div>
                                <h5>Telefon</h5>
                                <p class="text-muted mb-0">
                                    <?php echo getSetting('contact_phone'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <?php if(getSetting('contact_address')): ?>
                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div>
                                <h5>Adres</h5>
                                <p class="text-muted mb-0">
                                    <?php echo getSetting('contact_address'); ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="contact-item mb-4">
                        <div class="d-flex align-items-start">
                            <div class="contact-icon me-3">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <h5>Çalışma Saatleri</h5>
                                <p class="text-muted mb-0">
                                    Pazartesi - Cuma: 09:00 - 18:00<br>
                                    Hafta Sonu: Randevuya Göre
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="social-links-contact mt-4">
                        <h5 class="mb-3">Sosyal Medya</h5>
                        <div class="d-flex gap-3">
                            <?php if(getSetting('social_github')): ?>
                                <a href="<?php echo getSetting('social_github'); ?>" target="_blank" class="social-link-contact">
                                    <i class="fab fa-github"></i>
                                </a>
                            <?php endif; ?>
                            <?php if(getSetting('social_linkedin')): ?>
                                <a href="<?php echo getSetting('social_linkedin'); ?>" target="_blank" class="social-link-contact">
                                    <i class="fab fa-linkedin"></i>
                                </a>
                            <?php endif; ?>
                            <?php if(getSetting('social_twitter')): ?>
                                <a href="<?php echo getSetting('social_twitter'); ?>" target="_blank" class="social-link-contact">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            <?php endif; ?>
                            <?php if(getSetting('social_instagram')): ?>
                                <a href="<?php echo getSetting('social_instagram'); ?>" target="_blank" class="social-link-contact">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5" style="background: rgba(108, 92, 231, 0.05);">
    <div class="container">
        <h2 class="section-title animate-on-scroll">Sıkça Sorulan Sorular</h2>
        
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <div class="accordion accordion-flush" id="faqAccordion">
                    <div class="accordion-item animate-on-scroll">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                                Proje süreci nasıl işliyor?
                            </button>
                        </h2>
                        <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                İlk olarak projenizi detaylı şekilde konuşuyoruz. Ardından teknik analiz yapıp size teklif sunuyorum. 
                                Onay sonrası tasarım ve geliştirme sürecine başlıyoruz. Her aşamada sizinle iletişim halindeyim.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item animate-on-scroll">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                                Proje teslim süresi ne kadar?
                            </button>
                        </h2>
                        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Proje karmaşıklığına göre değişmekle birlikte, basit web siteleri 1-2 hafta, 
                                e-ticaret siteleri 3-4 hafta, özel uygulamalar ise 6-8 hafta sürebilir.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item animate-on-scroll">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                                Destek hizmeti veriyor musunuz?
                            </button>
                        </h2>
                        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                Evet! Tüm projelerimde 6 ay ücretsiz teknik destek veriyorum. 
                                Bu süre sonrasında uygun fiyatlarla destek hizmeti devam ediyor.
                            </div>
                        </div>
                    </div>
                    
                    <div class="accordion-item animate-on-scroll">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                                Hangi teknolojileri kullanıyorsunuz?
                            </button>
                        </h2>
                        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                            <div class="accordion-body">
                                HTML5, CSS3, JavaScript, PHP, React, Vue.js, Laravel, Node.js gibi modern teknolojileri kullanıyorum. 
                                Projenizin ihtiyacına göre en uygun teknoloji stack'ini seçiyoruz.
                            </div>
                        </div>
                    </div>
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
                <p class="mb-0"><?php echo getContentWithVariables('cta_text', 'Kumar endüstrisinde birlikte büyümek için benimle iletişime geç. {site_brand} ile güvenli ve karlı platformlar kuralım.'); ?></p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="portfolio.php" class="btn btn-outline-gradient btn-lg me-3">Çalışmalarım</a>
                <a href="services.php" class="btn btn-gradient btn-lg">Hizmetlerim</a>
            </div>
        </div>
    </div>
</section>

<style>
.contact-icon {
    width: 50px;
    height: 50px;
    background: var(--gradient);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.contact-item h5 {
    font-weight: 600;
    margin-bottom: 5px;
}

.social-link-contact {
    width: 45px;
    height: 45px;
    background: var(--dark-border);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--text-light);
    text-decoration: none;
    transition: all 0.3s ease;
    font-size: 1.2rem;
}

.social-link-contact:hover {
    background: var(--gradient);
    color: white;
    transform: translateY(-2px);
}

.accordion-item {
    background-color: var(--dark-card);
    border: 1px solid var(--dark-border);
    border-radius: 10px !important;
    margin-bottom: 15px;
}

.accordion-button {
    background-color: transparent;
    color: var(--text-light);
    border: none;
    padding: 20px;
    font-weight: 500;
}

.accordion-button:not(.collapsed) {
    background: var(--gradient);
    color: white;
}

.accordion-button:focus {
    box-shadow: none;
    border: none;
}

.accordion-button::after {
    filter: invert(1);
}

.accordion-body {
    background-color: var(--dark-bg);
    color: var(--text-muted);
    border-top: 1px solid var(--dark-border);
}

.alert {
    border-radius: 10px;
    border: none;
    padding: 15px 20px;
}

.alert-success {
    background-color: rgba(0, 184, 148, 0.1);
    color: #00b894;
    border-left: 4px solid #00b894;
}

.alert-danger {
    background-color: rgba(231, 76, 60, 0.1);
    color: #e74c3c;
    border-left: 4px solid #e74c3c;
}

/* Mobile responsive fixes */
@media (max-width: 991px) {
    .contact-info {
        margin-top: 40px;
    }
    
    .contact-form {
        margin-bottom: 30px;
    }
}

@media (max-width: 768px) {
    .contact-item {
        margin-bottom: 30px !important;
    }
    
    .contact-icon {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }
    
    .social-link-contact {
        width: 40px;
        height: 40px;
        font-size: 1.1rem;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
    
    .lead {
        font-size: 1.1rem;
    }
}

@media (max-width: 576px) {
    .contact-form {
        padding: 25px 20px;
    }
    
    .contact-info {
        padding: 25px 20px;
        margin-top: 30px;
    }
    
    .btn-lg {
        padding: 12px 25px;
        font-size: 1rem;
    }
    
    .accordion-button {
        padding: 15px;
        font-size: 0.95rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>