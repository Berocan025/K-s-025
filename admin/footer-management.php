<?php
/**
 * Footer Management - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once '../includes/functions.php';
requireLogin();

$page_title = 'Footer Yönetimi';

$success_message = '';
$error_message = '';

// Handle footer link operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_link'])) {
        $id = clean($_POST['link_id']);
        $title = clean($_POST['link_title']);
        $url = clean($_POST['link_url']);
        $section = clean($_POST['link_section']);
        $sort_order = (int)clean($_POST['sort_order']);
        
        if (updateFooterLink($id, $title, $url, $section, $sort_order)) {
            $success_message = 'Footer linki başarıyla güncellendi!';
        } else {
            $error_message = 'Footer linki güncellenirken bir hata oluştu.';
        }
    }
    
    if (isset($_POST['add_link'])) {
        $title = clean($_POST['link_title']);
        $url = clean($_POST['link_url']);
        $section = clean($_POST['link_section']);
        $sort_order = (int)clean($_POST['sort_order']);
        
        if (addFooterLink($title, $url, $section, $sort_order)) {
            $success_message = 'Yeni footer linki başarıyla eklendi!';
        } else {
            $error_message = 'Footer linki eklenirken bir hata oluştu.';
        }
    }
    
    if (isset($_POST['delete_link'])) {
        $id = clean($_POST['link_id']);
        if (deleteFooterLink($id)) {
            $success_message = 'Footer linki başarıyla silindi!';
        } else {
            $error_message = 'Footer linki silinirken bir hata oluştu.';
        }
    }
}

// Get all footer links
$footer_links = getAllFooterLinks();

// Group links by section
$grouped_links = [];
foreach ($footer_links as $link) {
    $grouped_links[$link['link_section']][] = $link;
}

include 'includes/header.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1><i class="fas fa-link me-2"></i>Footer Yönetimi</h1>
                    <p class="text-muted">Footer bölümündeki linkleri ve bölümleri buradan düzenleyebilirsiniz.</p>
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

                <!-- Add New Link Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-plus me-2"></i>Yeni Footer Linki Ekle</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="link_title" class="form-label">Link Başlığı</label>
                                        <input type="text" class="form-control" id="link_title" name="link_title" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="link_url" class="form-label">Link URL</label>
                                        <input type="text" class="form-control" id="link_url" name="link_url" required>
                                        <small class="form-text text-muted">Örnek: index.php, about.php</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="link_section" class="form-label">Footer Bölümü</label>
                                        <select class="form-control" id="link_section" name="link_section" required>
                                            <option value="quick_links">Hızlı Linkler</option>
                                            <option value="services">Platform Hizmetleri</option>
                                            <option value="social">Sosyal Medya</option>
                                            <option value="legal">Yasal</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sıra</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
                                    </div>
                                </div>
                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="submit" name="add_link" class="btn btn-gradient">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Footer Links by Section -->
                <?php foreach ($grouped_links as $section => $section_links): ?>
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5><i class="fas fa-folder me-2"></i>
                                <?php 
                                switch($section) {
                                    case 'quick_links': echo 'Hızlı Linkler'; break;
                                    case 'services': echo 'Platform Hizmetleri'; break;
                                    case 'social': echo 'Sosyal Medya'; break;
                                    case 'legal': echo 'Yasal'; break;
                                    default: echo ucfirst($section); break;
                                }
                                ?>
                            </h5>
                        </div>
                        <div class="card-body">
                            <?php foreach ($section_links as $link): ?>
                                <div class="link-item mb-3 p-3 border rounded">
                                    <form method="POST" class="row align-items-end">
                                        <input type="hidden" name="link_id" value="<?php echo $link['id']; ?>">
                                        <div class="col-md-3">
                                            <label class="form-label">Link Başlığı</label>
                                            <input type="text" class="form-control" name="link_title" value="<?php echo htmlspecialchars($link['link_title']); ?>" required>
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">Link URL</label>
                                            <input type="text" class="form-control" name="link_url" value="<?php echo htmlspecialchars($link['link_url']); ?>" required>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Footer Bölümü</label>
                                            <select class="form-control" name="link_section" required>
                                                <option value="quick_links" <?php echo $link['link_section'] == 'quick_links' ? 'selected' : ''; ?>>Hızlı Linkler</option>
                                                <option value="services" <?php echo $link['link_section'] == 'services' ? 'selected' : ''; ?>>Platform Hizmetleri</option>
                                                <option value="social" <?php echo $link['link_section'] == 'social' ? 'selected' : ''; ?>>Sosyal Medya</option>
                                                <option value="legal" <?php echo $link['link_section'] == 'legal' ? 'selected' : ''; ?>>Yasal</option>
                                            </select>
                                        </div>
                                        <div class="col-md-2">
                                            <label class="form-label">Sıra</label>
                                            <input type="number" class="form-control" name="sort_order" value="<?php echo $link['sort_order']; ?>">
                                        </div>
                                        <div class="col-md-2">
                                            <div class="btn-group w-100">
                                                <button type="submit" name="update_link" class="btn btn-gradient btn-sm">
                                                    <i class="fas fa-save"></i>
                                                </button>
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="deleteLink(<?php echo $link['id']; ?>, '<?php echo htmlspecialchars($link['link_title']); ?>')">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <?php if (empty($footer_links)): ?>
                    <div class="card">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-link fa-3x text-muted mb-3"></i>
                            <h5 class="text-muted">Henüz footer linki eklenmemiş</h5>
                            <p class="text-muted">Yukarıdaki formu kullanarak yeni footer linki ekleyebilirsiniz.</p>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Footer Linki Sil</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Bu footer linkini silmek istediğinizden emin misiniz?</p>
                <p class="text-danger" id="linkName"></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                <form method="POST" class="d-inline" id="deleteForm">
                    <input type="hidden" name="link_id" id="deleteLinkId">
                    <button type="submit" name="delete_link" class="btn btn-danger">Sil</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function deleteLink(id, name) {
    document.getElementById('deleteLinkId').value = id;
    document.getElementById('linkName').textContent = name;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>

<?php include 'includes/footer.php'; ?>