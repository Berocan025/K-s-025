<?php
/**
 * Content Management - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once '../includes/functions.php';
requireLogin();

$page_title = 'İçerik Yönetimi';

$success_message = '';
$error_message = '';

// Handle content updates
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_content'])) {
        $id = clean($_POST['content_id']);
        $content_text = $_POST['content_text']; // İçerik için clean kullanmıyoruz
        
        if (updateContent($id, $content_text)) {
            $success_message = 'İçerik başarıyla güncellendi!';
        } else {
            $error_message = 'İçerik güncellenirken bir hata oluştu.';
        }
    }
    
    if (isset($_POST['add_content'])) {
        $key = clean($_POST['content_key']);
        $title = clean($_POST['content_title']);
        $text = $_POST['content_text'];
        $type = clean($_POST['content_type']);
        $location = clean($_POST['page_location']);
        
        if (addSiteContent($key, $title, $text, $type, $location)) {
            $success_message = 'Yeni içerik başarıyla eklendi!';
        } else {
            $error_message = 'İçerik eklenirken bir hata oluştu.';
        }
    }
    
    if (isset($_POST['delete_content'])) {
        $id = clean($_POST['content_id']);
        if (deleteSiteContent($id)) {
            $success_message = 'İçerik başarıyla silindi!';
        } else {
            $error_message = 'İçerik silinirken bir hata oluştu.';
        }
    }
}

// Get all contents with optional filtering
$page_filter = isset($_GET['page']) ? clean($_GET['page']) : '';
if ($page_filter) {
    $contents = getPageContents($page_filter);
} else {
    $contents = getAllContents();
}

// Group contents by page location
$grouped_contents = [];
foreach ($contents as $content) {
    $grouped_contents[$content['page_location']][] = $content;
}

// Get all page locations for filter dropdown
$all_locations = [];
foreach (getAllContents() as $content) {
    if (!in_array($content['page_location'], $all_locations)) {
        $all_locations[] = $content['page_location'];
    }
}
sort($all_locations);

include 'includes/header.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1><i class="fas fa-edit me-2"></i>İçerik Yönetimi</h1>
                    <p class="text-muted">Site üzerindeki tüm yazıları ve içerikleri buradan düzenleyebilirsiniz.</p>
                    
                    <!-- Sayfa Filtresi -->
                    <div class="row mt-3">
                        <div class="col-md-3">
                            <select class="form-control" onchange="window.location.href='?page=' + this.value">
                                <option value="">🌐 Tüm Sayfalar</option>
                                <?php foreach ($all_locations as $location): ?>
                                    <option value="<?php echo htmlspecialchars($location); ?>" <?php echo ($page_filter === $location) ? 'selected' : ''; ?>>
                                        <?php 
                                        $location_names = [
                                            'index' => '🏠 Ana Sayfa',
                                            'about' => '👤 Hakkımda',
                                            'services' => '🔧 Hizmetler',
                                            'contact' => '📞 İletişim',
                                            'footer' => '⬇️ Footer',
                                            'stats' => '📊 İstatistikler',
                                            'general' => '🌟 Genel',
                                            'buttons' => '🔘 Butonlar',
                                            'games' => '🎮 Oyunlar',
                                            'bonus' => '🎁 Bonuslar',
                                            'meta' => '🔍 Meta Etiketleri'
                                        ];
                                        echo isset($location_names[$location]) ? $location_names[$location] : ucfirst($location);
                                        ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-9">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>İpucu:</strong> Bu sayfadan tüm metinleri düzenleyebilirsiniz. Değişiklikler anında siteye yansıyacaktır.
                            </div>
                        </div>
                    </div>
                </div>

                <?php if ($success_message): ?>
                    <div class="alert alert-success alert-dismissible fade show">
                        <i class="fas fa-check-circle me-2"></i><?php echo $success_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <?php if ($error_message): ?>
                    <div class="alert alert-danger alert-dismissible fade show">
                        <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error_message; ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Add New Content Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-plus me-2"></i>Yeni İçerik Ekle</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="content_key" class="form-label">İçerik Anahtarı</label>
                                        <input type="text" class="form-control" id="content_key" name="content_key" required>
                                        <small class="form-text text-muted">Örnek: about_intro, hero_title</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="content_title" class="form-label">İçerik Başlığı</label>
                                        <input type="text" class="form-control" id="content_title" name="content_title" required>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="content_type" class="form-label">İçerik Tipi</label>
                                        <select class="form-control" id="content_type" name="content_type">
                                            <option value="text">Kısa Metin</option>
                                            <option value="textarea">Uzun Metin</option>
                                            <option value="html">HTML</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="mb-3">
                                        <label for="page_location" class="form-label">Sayfa Konumu</label>
                                        <select class="form-control" id="page_location" name="page_location">
                                            <option value="general">Genel</option>
                                            <option value="home">Ana Sayfa</option>
                                            <option value="about">Hakkımda</option>
                                            <option value="portfolio">Platformlar</option>
                                            <option value="services">Hizmetler</option>
                                            <option value="products">Ürünler</option>
                                            <option value="contact">İletişim</option>
                                            <option value="footer">Footer</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-4 d-flex align-items-end">
                                    <button type="submit" name="add_content" class="btn btn-gradient">
                                        <i class="fas fa-plus me-2"></i>Ekle
                                    </button>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="content_text" class="form-label">İçerik Metni</label>
                                <textarea class="form-control" id="content_text" name="content_text" rows="3" required></textarea>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Contents by Page -->
                <?php foreach ($grouped_contents as $location => $location_contents): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-file-alt me-2"></i><?php echo ucfirst($location); ?> İçerikleri</h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($location_contents as $content): ?>
                                <div class="content-item mb-4 p-3 border rounded">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($content['content_title']); ?></h6>
                                            <small class="text-muted">
                                                <strong>Anahtar:</strong> <?php echo htmlspecialchars($content['content_key']); ?> | 
                                                <strong>Tip:</strong> <?php echo htmlspecialchars($content['content_type']); ?>
                                            </small>
                                        </div>
                                        <div>
                                            <button class="btn btn-sm btn-outline-danger" onclick="deleteContent(<?php echo $content['id']; ?>, '<?php echo htmlspecialchars($content['content_title']); ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <form method="POST" class="content-form">
                                        <input type="hidden" name="content_id" value="<?php echo $content['id']; ?>">
                                        <div class="mb-3">
                                            <?php if ($content['content_type'] == 'textarea' || $content['content_type'] == 'html'): ?>
                                                <textarea class="form-control" name="content_text" rows="4"><?php echo htmlspecialchars($content['content_text']); ?></textarea>
                                            <?php else: ?>
                                                <input type="text" class="form-control" name="content_text" value="<?php echo htmlspecialchars($content['content_text']); ?>">
                                            <?php endif; ?>
                                        </div>
                                        <button type="submit" name="update_content" class="btn btn-gradient btn-sm">
                                            <i class="fas fa-save me-1"></i>Güncelle
                                        </button>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">İçerik Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu içeriği silmek istediğinizden emin misiniz?</p>
                <p class="text-danger" id="contentName"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form method="POST" class="d-inline" id="deleteForm">
                    <input type="hidden" name="content_id" id="deleteContentId">
                    <button type="submit" name="delete_content" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteContent(id, name) {
    document.getElementById('deleteContentId').value = id;
    document.getElementById('contentName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>