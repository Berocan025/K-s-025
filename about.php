<?php
/**
 * About Page - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once 'includes/functions.php';
checkMaintenanceMode();
trackVisitor(); // Ziyaretçi sayacı
$page_title = 'Hakkımda';

// Get about settings from admin
$about_title = getSetting('about_title', 'Hakkımda');
$about_subtitle = getSetting('about_subtitle', 'Kumar Platform CEO\'su & Endüstri Yayıncısı');
$about_description = getSetting('about_description', 'Kumar endüstrisinde uzmanlaşmış bir CEO ve yayıncıyım. Güvenli, adil ve karlı oyun platformları inşa etme konusunda geniş deneyime sahibim. Milyonlarca oyuncunun güvendiği platformların lideri olarak, endüstrinin en yüksek standartlarını belirliyorum.');
$about_skills = getSetting('about_skills', 'Platform Yönetimi, Risk Analizi, Oyun Güvenliği, Canlı Yayın, Kullanıcı Deneyimi, Finansal Planlama, Yasal Uyumluluk, Pazarlama Stratejileri, Veri Analizi, İş Geliştirme');
$about_experience = getSetting('about_experience', '2020 - Şu an: Kumar Platform CEO\'su\n2018 - 2020: Platform Geliştirme Direktörü\n2016 - 2018: Kumar Yayıncısı\n2014 - 2016: Oyun Analisti');
$about_education = getSetting('about_education', '2014: İşletme Yönetimi Lisans\n2019: Kumar Endüstrisi Liderlik Sertifikası\n2021: Finansal Risk Yönetimi Uzmanlığı\n2022: Dijital Pazarlama Uzmanlığı');
$about_image = getSetting('about_image', '');
?>

<?php include 'includes/header.php'; ?>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-4 text-center mb-5 mb-lg-0">
                <div class="about-image animate-on-scroll">
                    <?php if($about_image && file_exists($about_image)): ?>
                        <img src="<?php echo htmlspecialchars($about_image); ?>" alt="<?php echo htmlspecialchars($about_title); ?>" class="img-fluid rounded-circle" style="width: 300px; height: 300px; object-fit: cover; border: 5px solid var(--primary-color);">
                    <?php else: ?>
                        <div class="placeholder-image" style="width: 300px; height: 300px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto; border: 5px solid var(--primary-color);">
                            <i class="fas fa-user fa-5x text-white" style="opacity: 0.7;"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="col-lg-8">
                <div class="about-content animate-on-scroll">
                    <h1 class="display-4 mb-3">
                        <span class="text-gradient"><?php echo htmlspecialchars($about_title); ?></span>
                    </h1>
                    <h3 class="text-light mb-4"><?php echo htmlspecialchars($about_subtitle); ?></h3>
                    <p class="lead about-description"><?php echo nl2br(htmlspecialchars($about_description)); ?></p>
                    
                    <div class="about-stats mt-4 animate-on-scroll">
                        <div class="row">
                            <?php 
                            // Get stats from admin settings
                            $stat_projects = getSetting('stat_projects', '150');
                            $stat_clients = getSetting('stat_clients', '85'); 
                            $stat_years = getSetting('stat_years', '5');
                            $stat_awards = getSetting('stat_awards', '12');
                            ?>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="stat-box text-center">
                                    <div class="stat-number text-gradient h2" data-count="<?php echo htmlspecialchars($stat_projects); ?>">0+</div>
                                    <div class="stat-label"><?php echo getContent('stat_projects_label', 'Aktif Platform'); ?></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="stat-box text-center">
                                    <div class="stat-number text-gradient h2" data-count="<?php echo htmlspecialchars($stat_clients); ?>">0M+</div>
                                    <div class="stat-label"><?php echo getContent('stat_clients_label', 'Aktif Oyuncu'); ?></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="stat-box text-center">
                                    <div class="stat-number text-gradient h2" data-count="<?php echo htmlspecialchars($stat_years); ?>">0+</div>
                                    <div class="stat-label"><?php echo getContent('stat_years_label', 'Yıllık Deneyim'); ?></div>
                                </div>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <div class="stat-box text-center">
                                    <div class="stat-number text-gradient h2" data-count="<?php echo htmlspecialchars($stat_awards); ?>">0+</div>
                                    <div class="stat-label"><?php echo getContent('stat_awards_label', 'Endüstri Ödülü'); ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if($about_skills): ?>
<section class="py-5" style="background: rgba(220, 38, 38, 0.05);">
    <div class="container">
        <h2 class="section-title animate-on-scroll"><?php echo getContent('about_skills_title', 'Uzmanlık Alanlarım'); ?></h2>
        
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="skills-container animate-on-scroll">
                    <?php 
                    $skills = explode(',', $about_skills);
                    foreach($skills as $skill): 
                        $skill = trim($skill);
                        if(!empty($skill)):
                    ?>
                        <div class="skill-item">
                            <span class="skill-badge"><?php echo htmlspecialchars($skill); ?></span>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Kariyer Yolculuğu Bölümü -->
<?php if($about_experience): ?>
<section class="py-5" style="background: rgba(220, 38, 38, 0.03);">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="section-title animate-on-scroll">
                    <i class="fas fa-crown me-3"></i><?php echo getContent('about_experience_title', 'Kariyer Yolculuğum'); ?>
                </h2>
                <p class="lead text-muted"><?php echo getContent('about_experience_desc', 'Kumar endüstrisinde liderlik pozisyonlarında edindiğim deneyimler ve başarılarım.'); ?></p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="experience-section animate-on-scroll">
                    <div class="timeline">
                        <?php 
                        // Önce newline'a göre ayır, sonra virgül'e göre
                        $temp_experiences = explode("\n", $about_experience);
                        $experiences = array();
                        foreach($temp_experiences as $temp_exp) {
                            $temp_exp = trim($temp_exp);
                            if(!empty($temp_exp)) {
                                // Her satırı virgül'e göre de ayır
                                $comma_split = explode(",", $temp_exp);
                                foreach($comma_split as $exp) {
                                    $exp = trim($exp);
                                    if(!empty($exp)) {
                                        $experiences[] = $exp;
                                    }
                                }
                            }
                        }
                        foreach($experiences as $experience): 
                            $experience = trim($experience);
                            if(!empty($experience)):
                        ?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <p class="experience-text"><?php echo htmlspecialchars($experience); ?></p>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Eğitim & Sertifikalar Bölümü -->
<?php if($about_education): ?>
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h2 class="section-title animate-on-scroll">
                    <i class="fas fa-trophy me-3"></i><?php echo getContent('about_education_title', 'Eğitim & Sertifikalarım'); ?>
                </h2>
                <p class="lead text-muted"><?php echo getContent('about_education_desc', 'Kumar endüstrisinde aldığım eğitimler, uzmanlık sertifikaları ve sürekli gelişim yolculuğum.'); ?></p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-10 mx-auto">
                <div class="education-section animate-on-scroll">
                    <div class="timeline">
                        <?php 
                        // Önce newline'a göre ayır, sonra virgül'e göre
                        $temp_educations = explode("\n", $about_education);
                        $educations = array();
                        foreach($temp_educations as $temp_edu) {
                            $temp_edu = trim($temp_edu);
                            if(!empty($temp_edu)) {
                                // Her satırı virgül'e göre de ayır
                                $comma_split = explode(",", $temp_edu);
                                foreach($comma_split as $edu) {
                                    $edu = trim($edu);
                                    if(!empty($edu)) {
                                        $educations[] = $edu;
                                    }
                                }
                            }
                        }
                        foreach($educations as $education): 
                            $education = trim($education);
                            if(!empty($education)):
                        ?>
                            <div class="timeline-item">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <p class="education-text"><?php echo htmlspecialchars($education); ?></p>
                                </div>
                            </div>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="py-5" style="background: var(--dark-card);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="text-gradient mb-3">Benimle Çalışmaya Hazır mısınız?</h3>
                <p class="mb-0 cta-text">Projelerinizi hayata geçirmek için profesyonel destek arıyorsanız, doğru yerdesiniz!</p>
            </div>
            <div class="col-lg-4 text-lg-end text-center text-lg-start">
                <div class="cta-buttons">
                    <a href="contact.php" class="btn btn-gradient btn-lg mb-2 mb-lg-0 me-lg-3">İletişime Geç</a>
                    <a href="portfolio.php" class="btn btn-outline-gradient btn-lg">Projelerim</a>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
.about-description {
    color: var(--text-light) !important;
    opacity: 0.9;
    line-height: 1.8;
}

.stat-box {
    padding: 20px;
    border-radius: 15px;
    background: var(--dark-card);
    border: 1px solid var(--dark-border);
    transition: all 0.3s ease;
}

.stat-box:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow);
}

.stat-number {
    font-weight: 800;
    margin-bottom: 5px;
}

.stat-label {
    color: var(--text-light) !important;
    opacity: 0.85;
    font-size: 0.9rem;
}

.skills-container {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
    justify-content: center;
}

.skill-item {
    display: inline-block;
}

.skill-badge {
    background: var(--gradient);
    color: white;
    padding: 10px 20px;
    border-radius: 25px;
    font-weight: 500;
    font-size: 0.9rem;
    display: inline-block;
    transition: all 0.3s ease;
}

.skill-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(108, 92, 231, 0.3);
}

.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: var(--gradient);
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
}

.timeline-marker {
    position: absolute;
    left: -37px;
    top: 5px;
    width: 12px;
    height: 12px;
    background: var(--gradient);
    border-radius: 50%;
    border: 3px solid var(--dark-bg);
}

.timeline-content {
    background: var(--dark-card);
    padding: 20px;
    border-radius: 12px;
    border: 1px solid var(--dark-border);
    position: relative;
}

.timeline-content::before {
    content: '';
    position: absolute;
    left: -8px;
    top: 15px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid var(--dark-border);
}

.timeline-content::after {
    content: '';
    position: absolute;
    left: -7px;
    top: 15px;
    width: 0;
    height: 0;
    border-top: 8px solid transparent;
    border-bottom: 8px solid transparent;
    border-right: 8px solid var(--dark-card);
}

.experience-text, .education-text {
    color: var(--text-light) !important;
    opacity: 0.9;
    margin-bottom: 0;
    line-height: 1.6;
}

.cta-text {
    color: var(--text-light) !important;
    opacity: 0.9;
}

@media (max-width: 768px) {
    .about-image img, .placeholder-image {
        width: 250px !important;
        height: 250px !important;
    }
    
    .display-4 {
        font-size: 2.5rem;
    }
    
    .skills-container {
        justify-content: flex-start;
    }
    
    .skill-badge {
        font-size: 0.8rem;
        padding: 8px 16px;
    }
    
    .timeline {
        padding-left: 25px;
    }
    
    .timeline-marker {
        left: -32px;
    }
    
    .cta-buttons {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 10px;
    }
    
    .cta-buttons .btn {
        width: 100%;
        max-width: 250px;
    }
    

}

@media (min-width: 992px) {
    .cta-buttons {
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        align-items: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>