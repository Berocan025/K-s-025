<?php
/**
 * Site Metin Yönetimi
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * 
 * Bu sayfa tüm site metinlerini kategoriler halinde yönetmeye yarar
 * Eksik metinleri tespit eder ve düzenleme imkanı sağlar
 */

require_once '../includes/functions.php';
requireLogin();

$page_title = 'Site Metin Yönetimi';
$success_message = '';
$error_message = '';

// Aktif kategori belirleme - Developer: BERAT K
$active_category = isset($_GET['category']) ? (int)$_GET['category'] : 0;

// Metin güncelleme - Developer: BERAT K
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_text'])) {
    $text_id = (int)$_POST['text_id'];
    $text_value = $_POST['text_value']; // HTML içerebileceği için clean kullanmıyoruz
    
    try {
        // Önce text_key'i al
        $stmt = $pdo->prepare("SELECT text_key FROM site_texts WHERE id = ?");
        $stmt->execute([$text_id]);
        $text_key = $stmt->fetchColumn();
        
        if ($text_key) {
            // site_texts tablosunu güncelle
            $stmt = $pdo->prepare("UPDATE site_texts SET text_value = ?, updated_at = datetime('now') WHERE id = ?");
            $stmt->execute([$text_value, $text_id]);
            
            // Aynı zamanda settings tablosunu da güncelle (geriye uyumluluk için)
            $stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
            $stmt->execute([$text_value, $text_key]);
            
            // Eğer settings'te yoksa ekle
            $stmt = $pdo->prepare("INSERT OR IGNORE INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $stmt->execute([$text_key, $text_value]);
            
            $success_message = 'Metin başarıyla güncellendi! (Developer: BERAT K)';
        } else {
            $error_message = 'Metin bulunamadı!';
        }
    } catch(PDOException $e) {
        $error_message = 'Veritabanı hatası: ' . $e->getMessage();
    }
}

// Yeni metin ekleme - Developer: BERAT K
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_text'])) {
    $category_id = (int)$_POST['category_id'];
    $text_key = clean($_POST['text_key']);
    $text_label = clean($_POST['text_label']);
    $text_value = $_POST['text_value'];
    $text_type = clean($_POST['text_type']);
    $page_location = clean($_POST['page_location']);
    $admin_notes = clean($_POST['admin_notes']);
    $is_required = isset($_POST['is_required']) ? 1 : 0;
    $is_html = isset($_POST['is_html']) ? 1 : 0;
    $max_length = !empty($_POST['max_length']) ? (int)$_POST['max_length'] : null;
    
    if (empty($text_key) || empty($text_label)) {
        $error_message = 'Metin anahtarı ve etiketi zorunludur!';
    } else {
        try {
            // Anahtar benzersizlik kontrolü
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM site_texts WHERE text_key = ?");
            $stmt->execute([$text_key]);
            
            if ($stmt->fetchColumn() > 0) {
                $error_message = 'Bu metin anahtarı zaten kullanılıyor!';
            } else {
                $stmt = $pdo->prepare("INSERT INTO site_texts (category_id, text_key, text_label, text_value, text_type, page_location, admin_notes, is_required, is_html, max_length, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))");
                
                if ($stmt->execute([$category_id, $text_key, $text_label, $text_value, $text_type, $page_location, $admin_notes, $is_required, $is_html, $max_length])) {
                    $success_message = "Yeni metin '$text_label' başarıyla eklendi! (Developer: BERAT K)";
                } else {
                    $error_message = 'Metin eklenirken hata oluştu!';
                }
            }
        } catch(PDOException $e) {
            $error_message = 'Veritabanı hatası: ' . $e->getMessage();
        }
    }
}

// Kategori ve metinleri getir - Developer: BERAT K
try {
    // Tüm kategorileri getir
    $categories = $pdo->query("SELECT * FROM text_categories WHERE is_active = 1 ORDER BY sort_order, category_name")->fetchAll();
    
    // Aktif kategori metinlerini getir
    if ($active_category > 0) {
        $texts = $pdo->prepare("SELECT * FROM site_texts WHERE category_id = ? AND is_active = 1 ORDER BY text_label");
        $texts->execute([$active_category]);
        $texts = $texts->fetchAll();
        
        // Aktif kategori bilgisini al
        $current_category = $pdo->prepare("SELECT * FROM text_categories WHERE id = ?");
        $current_category->execute([$active_category]);
        $current_category = $current_category->fetch();
    } else {
        // Tüm metinleri getir
        $texts = $pdo->query("
            SELECT t.*, c.category_name 
            FROM site_texts t 
            LEFT JOIN text_categories c ON t.category_id = c.id 
            WHERE t.is_active = 1 
            ORDER BY c.sort_order, t.text_label
        ")->fetchAll();
        $current_category = null;
    }
    
    // İstatistikler
    $stats = [
        'total_texts' => $pdo->query("SELECT COUNT(*) FROM site_texts WHERE is_active = 1")->fetchColumn(),
        'total_categories' => $pdo->query("SELECT COUNT(*) FROM text_categories WHERE is_active = 1")->fetchColumn(),
        'empty_texts' => $pdo->query("SELECT COUNT(*) FROM site_texts WHERE (text_value IS NULL OR text_value = '') AND is_active = 1")->fetchColumn()
    ];
    
} catch(PDOException $e) {
    $error_message = 'Veri yükleme hatası: ' . $e->getMessage();
    $categories = [];
    $texts = [];
    $stats = ['total_texts' => 0, 'total_categories' => 0, 'empty_texts' => 0];
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">
            <i class="fas fa-edit me-2"></i>Site Metin Yönetimi
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTextModal">
                    <i class="fas fa-plus me-1"></i>Yeni Metin Ekle
                </button>
                <a href="../index.php" target="_blank" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i>Siteyi Görüntüle
                </a>
            </div>
        </div>
    </div>

    <!-- İstatistikler - Developer: BERAT K -->
    <div class="row mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2" style="background: linear-gradient(45deg, #6c5ce7, #74b9ff);">
                        <i class="fas fa-font"></i>
                    </div>
                    <h4 class="text-light mb-1"><?php echo $stats['total_texts']; ?></h4>
                    <small class="text-muted">Toplam Metin</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2" style="background: linear-gradient(45deg, #a29bfe, #fd79a8);">
                        <i class="fas fa-folder"></i>
                    </div>
                    <h4 class="text-light mb-1"><?php echo $stats['total_categories']; ?></h4>
                    <small class="text-muted">Kategori</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2" style="background: linear-gradient(45deg, #fdcb6e, #e17055);">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <h4 class="text-light mb-1"><?php echo $stats['empty_texts']; ?></h4>
                    <small class="text-muted">Eksik Metin</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card-custom h-100">
                <div class="card-body text-center">
                    <div class="stat-icon mb-2" style="background: linear-gradient(45deg, #00b894, #00cec9);">
                        <i class="fas fa-code"></i>
                    </div>
                    <h4 class="text-light mb-1">BERAT K</h4>
                    <small class="text-muted">Developer</small>
                </div>
            </div>
        </div>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <?php echo $error_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="row">
        <!-- Kategori Listesi - Developer: BERAT K -->
        <div class="col-lg-3">
            <div class="card-custom">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-list me-2"></i>Metin Kategorileri
                    </h5>
                    
                    <div class="list-group list-group-flush">
                        <a href="?category=0" 
                           class="list-group-item list-group-item-action bg-transparent text-light border-secondary <?php echo $active_category == 0 ? 'active' : ''; ?>">
                            <i class="fas fa-globe me-2"></i>Tüm Metinler
                            <span class="badge bg-primary rounded-pill float-end"><?php echo $stats['total_texts']; ?></span>
                        </a>
                        
                        <?php foreach ($categories as $category): ?>
                        <a href="?category=<?php echo $category['id']; ?>" 
                           class="list-group-item list-group-item-action bg-transparent text-light border-secondary <?php echo $active_category == $category['id'] ? 'active' : ''; ?>">
                            <i class="fas fa-folder me-2"></i><?php echo htmlspecialchars($category['category_name']); ?>
                            <?php
                            // Kategori metin sayısını al
                            $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM site_texts WHERE category_id = ? AND is_active = 1");
                            $count_stmt->execute([$category['id']]);
                            $count = $count_stmt->fetchColumn();
                            ?>
                            <span class="badge bg-secondary rounded-pill float-end"><?php echo $count; ?></span>
                        </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Metinler - Developer: BERAT K -->
        <div class="col-lg-9">
            <div class="card-custom">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="card-title mb-0">
                            <?php if ($current_category): ?>
                                <i class="fas fa-folder me-2"></i><?php echo htmlspecialchars($current_category['category_name']); ?> Metinleri
                            <?php else: ?>
                                <i class="fas fa-font me-2"></i>Tüm Site Metinleri
                            <?php endif; ?>
                        </h5>
                        
                        <?php if ($current_category): ?>
                            <small class="text-muted">
                                <?php echo htmlspecialchars($current_category['category_description']); ?>
                            </small>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (empty($texts)): ?>
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Bu kategoride metin bulunamadı</h5>
                            <p class="text-muted">Yeni metin eklemek için "Yeni Metin Ekle" butonunu kullanın.</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($texts as $text): ?>
                            <div class="col-lg-6 mb-3">
                                <div class="text-item-card">
                                    <div class="text-item-header">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($text['text_label']); ?></h6>
                                                <small class="text-muted"><?php echo htmlspecialchars($text['text_key']); ?></small>
                                                <?php if (isset($text['category_name'])): ?>
                                                    <br><small class="badge bg-info"><?php echo htmlspecialchars($text['category_name']); ?></small>
                                                <?php endif; ?>
                                            </div>
                                            <div class="text-item-status">
                                                <?php if (empty($text['text_value'])): ?>
                                                    <span class="badge bg-warning">Boş</span>
                                                <?php else: ?>
                                                    <span class="badge bg-success">Dolu</span>
                                                <?php endif; ?>
                                                
                                                <?php if ($text['is_required']): ?>
                                                    <span class="badge bg-danger">Zorunlu</span>
                                                <?php endif; ?>
                                                
                                                <?php if ($text['is_html']): ?>
                                                    <span class="badge bg-info">HTML</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-item-body">
                                        <form method="POST" class="text-form">
                                            <input type="hidden" name="text_id" value="<?php echo $text['id']; ?>">
                                            
                                            <?php if ($text['text_type'] == 'textarea' || $text['is_html']): ?>
                                                <textarea name="text_value" class="form-control bg-dark text-light border-secondary" 
                                                          rows="4" 
                                                          <?php echo $text['max_length'] ? 'maxlength="' . $text['max_length'] . '"' : ''; ?>
                                                          placeholder="<?php echo htmlspecialchars($text['text_label']); ?> metnini girin..."><?php echo htmlspecialchars($text['text_value'] ?? ''); ?></textarea>
                                            <?php else: ?>
                                                <input type="<?php echo $text['text_type'] == 'email' ? 'email' : ($text['text_type'] == 'url' ? 'url' : 'text'); ?>" 
                                                       name="text_value" 
                                                       class="form-control bg-dark text-light border-secondary" 
                                                       value="<?php echo htmlspecialchars($text['text_value'] ?? ''); ?>"
                                                       <?php echo $text['max_length'] ? 'maxlength="' . $text['max_length'] . '"' : ''; ?>
                                                       placeholder="<?php echo htmlspecialchars($text['text_label']); ?> metnini girin...">
                                            <?php endif; ?>
                                            
                                            <div class="text-item-actions mt-2">
                                                <button type="submit" name="update_text" class="btn btn-primary btn-sm">
                                                    <i class="fas fa-save me-1"></i>Kaydet
                                                </button>
                                                
                                                <?php if ($text['page_location']): ?>
                                                    <small class="text-muted ms-2">
                                                        <i class="fas fa-map-marker-alt me-1"></i><?php echo htmlspecialchars($text['page_location']); ?>
                                                    </small>
                                                <?php endif; ?>
                                                
                                                <?php if ($text['max_length']): ?>
                                                    <small class="text-muted ms-2">
                                                        <i class="fas fa-ruler me-1"></i>Max: <?php echo $text['max_length']; ?> karakter
                                                    </small>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <?php if ($text['admin_notes']): ?>
                                                <div class="text-item-notes mt-2">
                                                    <small class="text-info">
                                                        <i class="fas fa-info-circle me-1"></i><?php echo htmlspecialchars($text['admin_notes']); ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Metin Ekleme Modal - Developer: BERAT K -->
<div class="modal fade" id="addTextModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">
                    <i class="fas fa-plus me-2"></i>Yeni Metin Ekle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Kategori</label>
                                <select class="form-select bg-dark text-light border-secondary" id="category_id" name="category_id" required>
                                    <option value="">Kategori Seçin</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category['id']; ?>" <?php echo $active_category == $category['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($category['category_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="text_type" class="form-label">Metin Türü</label>
                                <select class="form-select bg-dark text-light border-secondary" id="text_type" name="text_type" required>
                                    <option value="text">Kısa Metin</option>
                                    <option value="textarea">Uzun Metin</option>
                                    <option value="email">Email Adresi</option>
                                    <option value="url">Web Adresi</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="text_key" class="form-label">Metin Anahtarı</label>
                                <input type="text" class="form-control bg-dark text-light border-secondary" 
                                       id="text_key" name="text_key" required
                                       placeholder="ornek_metin_anahtari">
                                <div class="form-text">Sistem için benzersiz anahtar (sadece harf, rakam ve alt çizgi)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="text_label" class="form-label">Metin Etiketi</label>
                                <input type="text" class="form-control bg-dark text-light border-secondary" 
                                       id="text_label" name="text_label" required
                                       placeholder="Örnek Metin Başlığı">
                                <div class="form-text">Admin panelinde görünecek başlık</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="text_value" class="form-label">Metin İçeriği</label>
                        <textarea class="form-control bg-dark text-light border-secondary" 
                                  id="text_value" name="text_value" rows="3"
                                  placeholder="Metin içeriğini buraya yazın..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="page_location" class="form-label">Sayfa Konumu</label>
                                <input type="text" class="form-control bg-dark text-light border-secondary" 
                                       id="page_location" name="page_location"
                                       placeholder="Ana Sayfa, Hakkımda, vb.">
                                <div class="form-text">Bu metnin hangi sayfada kullanıldığı</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="max_length" class="form-label">Maksimum Karakter</label>
                                <input type="number" class="form-control bg-dark text-light border-secondary" 
                                       id="max_length" name="max_length" min="1"
                                       placeholder="255">
                                <div class="form-text">Boş bırakılırsa sınır olmaz</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="admin_notes" class="form-label">Admin Notları</label>
                        <textarea class="form-control bg-dark text-light border-secondary" 
                                  id="admin_notes" name="admin_notes" rows="2"
                                  placeholder="Bu metin hakkında notlar..."></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_required" name="is_required">
                                <label class="form-check-label" for="is_required">
                                    Zorunlu Metin
                                </label>
                                <div class="form-text">Bu metin boş bırakılamaz</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="is_html" name="is_html">
                                <label class="form-check-label" for="is_html">
                                    HTML İçerik
                                </label>
                                <div class="form-text">Metin HTML etiketleri içerebilir</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="add_text" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Metin Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Metin Yönetimi Stilleri - Developer: BERAT K */
.text-item-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 15px;
    height: 100%;
    transition: all 0.3s ease;
}

.text-item-card:hover {
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(108, 92, 231, 0.5);
}

.text-item-header h6 {
    color: var(--light-color);
    margin-bottom: 5px;
}

.text-item-status .badge {
    margin-left: 5px;
}

.text-item-body {
    margin-top: 10px;
}

.text-item-actions {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
}

.text-item-notes {
    background: rgba(108, 92, 231, 0.1);
    border-radius: 5px;
    padding: 8px;
    border-left: 3px solid var(--primary-color);
}

.stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    color: white;
    font-size: 1.5rem;
}

.list-group-item.active {
    background: var(--primary-color) !important;
    border-color: var(--primary-color) !important;
}

.list-group-item:hover {
    background: rgba(108, 92, 231, 0.1) !important;
}
</style>

<script>
// Metin anahtarı otomatik oluşturma - Developer: BERAT K
document.getElementById('text_label').addEventListener('input', function() {
    const label = this.value;
    const key = label
        .toLowerCase()
        .replace(/ğ/g, 'g')
        .replace(/ü/g, 'u')
        .replace(/ş/g, 's')
        .replace(/ı/g, 'i')
        .replace(/ö/g, 'o')
        .replace(/ç/g, 'c')
        .replace(/[^a-z0-9]/g, '_')
        .replace(/_+/g, '_')
        .replace(/^_|_$/g, '');
    
    document.getElementById('text_key').value = key;
});

// Form validation - Developer: BERAT K
document.querySelectorAll('.text-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        const textarea = this.querySelector('textarea, input[name="text_value"]');
        if (textarea.hasAttribute('required') && !textarea.value.trim()) {
            e.preventDefault();
            alert('Bu alan zorunludur!');
            textarea.focus();
        }
    });
});
</script>

<?php include 'includes/footer.php'; ?>