<?php
/**
 * Admin About Page Management
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$page_title = 'Hakkımda';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_about'])) {
        $about_title = clean($_POST['about_title']);
        $about_subtitle = clean($_POST['about_subtitle']);
        $about_description = clean($_POST['about_description']);
        $about_skills = clean($_POST['about_skills']);
        $about_experience = clean($_POST['about_experience']);
        $about_education = clean($_POST['about_education']);
        
        $about_image = getSetting('about_image', '');
        if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] == 0) {
            $new_image = uploadFile($_FILES['about_image'], 'uploads/about/');
            if ($new_image) {
                if ($about_image && file_exists('../' . $about_image)) {
                    unlink('../' . $about_image);
                }
                $about_image = $new_image;
            } else {
                $error_message = 'Profil fotoğrafı yüklenirken hata oluştu. Lütfen dosya formatını ve boyutunu kontrol edin.';
            }
        }
        
        $settings = [
            'about_title' => $about_title,
            'about_subtitle' => $about_subtitle,
            'about_description' => $about_description,
            'about_skills' => $about_skills,
            'about_experience' => $about_experience,
            'about_education' => $about_education,
            'about_image' => $about_image
        ];
        
        try {
            foreach ($settings as $key => $value) {
                setSetting($key, $value);
            }
            $success_message = 'Hakkımda bilgileri başarıyla güncellendi.';
        } catch(PDOException $e) {
            $error_message = 'Bilgiler güncellenirken hata oluştu: ' . $e->getMessage();
        }
    }
}

$about_settings = [
    'about_title' => getSetting('about_title', 'Hakkımda'),
    'about_subtitle' => getSetting('about_subtitle', 'Full Stack Developer & UI/UX Designer'),
    'about_description' => getSetting('about_description', 'Yazılım geliştirme alanında uzmanlaşmış bir profesyonelim. Modern web teknolojileri, mobil uygulama geliştirme ve kullanıcı deneyimi tasarımı konularında geniş deneyime sahibim.'),
    'about_skills' => getSetting('about_skills', 'HTML5, CSS3, JavaScript, React, Vue.js, PHP, Laravel, Node.js, Python, MySQL, PostgreSQL, MongoDB, Docker, Git, AWS'),
    'about_experience' => getSetting('about_experience', '2019 - Şu an: Senior Full Stack Developer
2017 - 2019: Frontend Developer
2015 - 2017: Junior Web Developer'),
    'about_education' => getSetting('about_education', '2015: Bilgisayar Mühendisliği Lisans
2020: AWS Solutions Architect Sertifikası
2021: Google Analytics Sertifikası'),
    'about_image' => getSetting('about_image', '')
];
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">Hakkımda Sayfası Yönetimi</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <a href="../about.php" target="_blank" class="btn btn-outline-light btn-sm">
                <i class="fas fa-external-link-alt me-1"></i>Sayfayı Görüntüle
            </a>
        </div>
    </div>
    
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
    
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Başlık</label>
                                        <input type="text" class="form-control" name="about_title" value="<?php echo htmlspecialchars($about_settings['about_title']); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Alt Başlık</label>
                                        <input type="text" class="form-control" name="about_subtitle" value="<?php echo htmlspecialchars($about_settings['about_subtitle']); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-light">Açıklama</label>
                                    <textarea class="form-control" name="about_description" rows="5" required><?php echo htmlspecialchars($about_settings['about_description']); ?></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label text-light">Yetenekler (virgülle ayırın)</label>
                                    <textarea class="form-control" name="about_skills" rows="3"><?php echo htmlspecialchars($about_settings['about_skills']); ?></textarea>
                                    <small class="text-muted">Örnek: HTML5, CSS3, JavaScript, React, PHP</small>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">İş Deneyimi</label>
                                        <textarea class="form-control" name="about_experience" rows="4"><?php echo htmlspecialchars($about_settings['about_experience']); ?></textarea>
                                        <small class="text-muted">Her satırda bir deneyim yazın</small>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label text-light">Eğitim</label>
                                        <textarea class="form-control" name="about_education" rows="4"><?php echo htmlspecialchars($about_settings['about_education']); ?></textarea>
                                        <small class="text-muted">Her satırda bir eğitim/sertifika yazın</small>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3">
                                    <label class="form-label text-light">Profil Fotoğrafı</label>
                                    <input type="file" class="form-control" name="about_image" accept="image/*">
                                    <small class="text-muted">JPG, PNG, GIF formatları desteklenir</small>
                                </div>
                                
                                <?php if($about_settings['about_image']): ?>
                                    <div class="current-image">
                                        <label class="form-label text-light">Mevcut Fotoğraf</label>
                                        <div class="about-image-preview">
                                            <img src="../<?php echo htmlspecialchars($about_settings['about_image']); ?>" alt="Profil" class="img-fluid rounded" style="max-height: 300px;">
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="no-image">
                                        <div class="text-center p-4 border border-secondary rounded">
                                            <i class="fas fa-user fa-3x text-muted mb-3"></i>
                                            <p class="text-muted">Henüz profil fotoğrafı yüklenmemiş</p>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="text-end mt-4 pt-3 border-top border-secondary">
                            <button type="reset" class="btn btn-outline-light me-2">
                                <i class="fas fa-undo me-2"></i>Sıfırla
                            </button>
                            <button type="submit" name="update_about" class="btn btn-gradient">
                                <i class="fas fa-save me-2"></i>Bilgileri Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-light"><i class="fas fa-eye me-2"></i>Önizleme</h5>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-4 text-center">
                            <?php if($about_settings['about_image']): ?>
                                <img src="../<?php echo htmlspecialchars($about_settings['about_image']); ?>" alt="<?php echo htmlspecialchars($about_settings['about_title']); ?>" class="img-fluid rounded-circle" style="width: 200px; height: 200px; object-fit: cover;">
                            <?php else: ?>
                                <div style="width: 200px; height: 200px; background: var(--dark-border); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                    <i class="fas fa-user fa-4x text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h2 class="text-gradient"><?php echo htmlspecialchars($about_settings['about_title']); ?></h2>
                            <h4 class="text-light mb-3"><?php echo htmlspecialchars($about_settings['about_subtitle']); ?></h4>
                            <p class="text-light"><?php echo nl2br(htmlspecialchars($about_settings['about_description'])); ?></p>
                            
                            <?php if($about_settings['about_skills']): ?>
                                <div class="mt-3">
                                    <h6 class="text-light">Yetenekler:</h6>
                                    <div class="skills-preview">
                                        <?php 
                                        $skills = explode(',', $about_settings['about_skills']);
                                        foreach($skills as $skill): 
                                            $skill = trim($skill);
                                            if(!empty($skill)):
                                        ?>
                                            <span class="badge bg-gradient me-2 mb-2"><?php echo htmlspecialchars($skill); ?></span>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.about-image-preview {
    border: 2px solid var(--dark-border);
    border-radius: 10px;
    padding: 10px;
    text-align: center;
}

.about-image-preview img {
    border-radius: 10px;
}

.skills-preview {
    max-height: 100px;
    overflow-y: auto;
}

.no-image {
    margin-top: 20px;
}

.form-control {
    background-color: var(--dark-bg);
    border: 1px solid var(--dark-border);
    color: var(--text-light);
    border-radius: 8px;
}

.form-control:focus {
    background-color: var(--dark-bg);
    border-color: var(--primary-color);
    color: var(--text-light);
    box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25);
}

.form-control::placeholder {
    color: var(--text-muted);
}
</style>

<?php include 'includes/footer.php'; ?>