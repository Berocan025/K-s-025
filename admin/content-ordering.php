<?php
/**
 * İçerik Sıralama Sistemi
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * 
 * Bu sayfa anasayfa bölümlerinin sırasını yönetmeye yarar
 * Platform Hizmetlerim, Platformlarım, Premium Ürünlerim vb. bölümlerin sıralaması
 */

require_once '../includes/functions.php';
requireLogin();

$page_title = 'İçerik Sıralama Yönetimi';
$success_message = '';
$error_message = '';

// AJAX sıralama güncelleme - Developer: BERAT K
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'update_order') {
    header('Content-Type: application/json');
    
    $sections = json_decode($_POST['sections'], true);
    
    if ($sections) {
        try {
            $pdo->beginTransaction();
            
            foreach ($sections as $index => $section_id) {
                $stmt = $pdo->prepare("UPDATE content_sections SET sort_order = ?, updated_at = datetime('now') WHERE id = ?");
                $stmt->execute([$index + 1, $section_id]);
            }
            
            $pdo->commit();
            echo json_encode(['success' => true, 'message' => 'Sıralama başarıyla güncellendi! (Developer: BERAT K)']);
        } catch(PDOException $e) {
            $pdo->rollBack();
            echo json_encode(['success' => false, 'message' => 'Hata: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Geçersiz veri!']);
    }
    exit;
}

// Bölüm görünürlüğü değiştirme - Developer: BERAT K
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_visibility'])) {
    $section_id = (int)$_POST['section_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT is_visible FROM content_sections WHERE id = ?");
        $stmt->execute([$section_id]);
        $current_visibility = $stmt->fetchColumn();
        
        $new_visibility = $current_visibility ? 0 : 1;
        
        $stmt = $pdo->prepare("UPDATE content_sections SET is_visible = ?, updated_at = datetime('now') WHERE id = ?");
        
        if ($stmt->execute([$new_visibility, $section_id])) {
            $success_message = 'Bölüm görünürlüğü güncellendi! (Developer: BERAT K)';
        } else {
            $error_message = 'Güncelleme başarısız!';
        }
    } catch(PDOException $e) {
        $error_message = 'Veritabanı hatası: ' . $e->getMessage();
    }
}

// Bölüm aktiflik durumu değiştirme - Developer: BERAT K
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle_active'])) {
    $section_id = (int)$_POST['section_id'];
    
    try {
        $stmt = $pdo->prepare("SELECT is_active FROM content_sections WHERE id = ?");
        $stmt->execute([$section_id]);
        $current_active = $stmt->fetchColumn();
        
        $new_active = $current_active ? 0 : 1;
        
        $stmt = $pdo->prepare("UPDATE content_sections SET is_active = ?, updated_at = datetime('now') WHERE id = ?");
        
        if ($stmt->execute([$new_active, $section_id])) {
            $success_message = 'Bölüm durumu güncellendi! (Developer: BERAT K)';
        } else {
            $error_message = 'Güncelleme başarısız!';
        }
    } catch(PDOException $e) {
        $error_message = 'Veritabanı hatası: ' . $e->getMessage();
    }
}

// Bölüm bilgileri güncelleme - Developer: BERAT K
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_section'])) {
    $section_id = (int)$_POST['section_id'];
    $section_title = clean($_POST['section_title']);
    $section_description = clean($_POST['section_description']);
    
    try {
        $stmt = $pdo->prepare("UPDATE content_sections SET section_title = ?, section_description = ?, updated_at = datetime('now') WHERE id = ?");
        
        if ($stmt->execute([$section_title, $section_description, $section_id])) {
            $success_message = 'Bölüm bilgileri güncellendi! (Developer: BERAT K)';
        } else {
            $error_message = 'Güncelleme başarısız!';
        }
    } catch(PDOException $e) {
        $error_message = 'Veritabanı hatası: ' . $e->getMessage();
    }
}

// İçerik bölümlerini getir - Developer: BERAT K
try {
    $content_sections = $pdo->query("SELECT * FROM content_sections ORDER BY sort_order ASC, created_at ASC")->fetchAll();
} catch(PDOException $e) {
    $error_message = 'Veri yükleme hatası: ' . $e->getMessage();
    $content_sections = [];
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">
            <i class="fas fa-sort me-2"></i>İçerik Sıralama Yönetimi
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group">
                <button type="button" class="btn btn-outline-light btn-sm" onclick="resetOrder()">
                    <i class="fas fa-undo me-1"></i>Sıralamayı Sıfırla
                </button>
                <a href="../index.php" target="_blank" class="btn btn-outline-success btn-sm">
                    <i class="fas fa-external-link-alt me-1"></i>Anasayfayı Görüntüle
                </a>
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
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-grip-vertical me-2"></i>Anasayfa Bölüm Sıralaması
                    </h5>
                    <p class="text-light mb-3">
                        Bölümleri sürükleyip bırakarak anasayfadaki sıralarını değiştirebilirsiniz.
                        <br><small class="text-muted">Developer: BERAT K tarafından geliştirilmiştir.</small>
                    </p>
                    
                    <div id="sortable-sections" class="sortable-list">
                        <?php foreach ($content_sections as $section): ?>
                        <div class="sortable-item" data-id="<?php echo $section['id']; ?>">
                            <div class="d-flex align-items-center">
                                <div class="drag-handle me-3">
                                    <i class="fas fa-grip-vertical text-muted"></i>
                                </div>
                                
                                <div class="section-info flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1">
                                                <span class="section-order badge bg-primary me-2"><?php echo $section['sort_order']; ?></span>
                                                <?php echo htmlspecialchars($section['section_title']); ?>
                                            </h6>
                                            <small class="text-muted">
                                                <?php echo htmlspecialchars($section['section_key']); ?>
                                                <?php if ($section['section_description']): ?>
                                                    - <?php echo htmlspecialchars($section['section_description']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        
                                        <div class="section-controls">
                                            <!-- Görünürlük Toggle -->
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="section_id" value="<?php echo $section['id']; ?>">
                                                <button type="submit" name="toggle_visibility" 
                                                        class="btn btn-sm <?php echo $section['is_visible'] ? 'btn-outline-success' : 'btn-outline-secondary'; ?>"
                                                        title="<?php echo $section['is_visible'] ? 'Gizle' : 'Göster'; ?>">
                                                    <i class="fas <?php echo $section['is_visible'] ? 'fa-eye' : 'fa-eye-slash'; ?>"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Aktiflik Toggle -->
                                            <form method="POST" style="display: inline;">
                                                <input type="hidden" name="section_id" value="<?php echo $section['id']; ?>">
                                                <button type="submit" name="toggle_active" 
                                                        class="btn btn-sm <?php echo $section['is_active'] ? 'btn-outline-info' : 'btn-outline-warning'; ?>"
                                                        title="<?php echo $section['is_active'] ? 'Pasif Yap' : 'Aktif Yap'; ?>">
                                                    <i class="fas <?php echo $section['is_active'] ? 'fa-toggle-on' : 'fa-toggle-off'; ?>"></i>
                                                </button>
                                            </form>
                                            
                                            <!-- Düzenle -->
                                            <button type="button" class="btn btn-outline-warning btn-sm" 
                                                    onclick="editSection(<?php echo htmlspecialchars(json_encode($section)); ?>)">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Durum Göstergeleri -->
                            <div class="section-status mt-2">
                                <?php if (!$section['is_active']): ?>
                                    <span class="badge bg-warning">Pasif</span>
                                <?php endif; ?>
                                
                                <?php if (!$section['is_visible']): ?>
                                    <span class="badge bg-secondary">Gizli</span>
                                <?php endif; ?>
                                
                                <?php if ($section['is_active'] && $section['is_visible']): ?>
                                    <span class="badge bg-success">Aktif & Görünür</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-body">
                    <h5 class="card-title">
                        <i class="fas fa-info-circle me-2"></i>Kullanım Bilgileri
                    </h5>
                    
                    <div class="info-item mb-3">
                        <h6><i class="fas fa-grip-vertical text-primary me-2"></i>Sürükle & Bırak</h6>
                        <p class="small text-muted">Bölümleri tutup sürükleyerek yeni sıraya yerleştirebilirsiniz.</p>
                    </div>
                    
                    <div class="info-item mb-3">
                        <h6><i class="fas fa-eye text-success me-2"></i>Görünürlük</h6>
                        <p class="small text-muted">Göz ikonuyla bölümü anasayfada gösterip gizleyebilirsiniz.</p>
                    </div>
                    
                    <div class="info-item mb-3">
                        <h6><i class="fas fa-toggle-on text-info me-2"></i>Aktiflik</h6>
                        <p class="small text-muted">Toggle ile bölümü tamamen aktif/pasif yapabilirsiniz.</p>
                    </div>
                    
                    <div class="info-item mb-3">
                        <h6><i class="fas fa-edit text-warning me-2"></i>Düzenleme</h6>
                        <p class="small text-muted">Kalem ikonuyla bölüm başlığı ve açıklamasını düzenleyebilirsiniz.</p>
                    </div>
                    
                    <div class="developer-info mt-4 p-3" style="background: rgba(108, 92, 231, 0.1); border-radius: 10px; border-left: 4px solid var(--primary-color);">
                        <small class="text-muted">
                            <i class="fas fa-code me-1"></i>
                            <strong>Developer: BERAT K</strong><br>
                            Gelişmiş sürükle-bırak sistemi ile kolay içerik yönetimi
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bölüm Düzenleme Modal - Developer: BERAT K -->
<div class="modal fade" id="editSectionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">
                    <i class="fas fa-edit me-2"></i>Bölüm Düzenle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" id="edit_section_id" name="section_id">
                    
                    <div class="mb-3">
                        <label for="section_title" class="form-label">Bölüm Başlığı</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" 
                               id="section_title" name="section_title" required>
                        <div class="form-text">Anasayfada gösterilecek başlık</div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="section_description" class="form-label">Bölüm Açıklaması</label>
                        <textarea class="form-control bg-dark text-light border-secondary" 
                                  id="section_description" name="section_description" rows="3"></textarea>
                        <div class="form-text">Admin paneli için açıklama (opsiyonel)</div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Bölüm Anahtarı</label>
                        <input type="text" class="form-control bg-secondary text-light" 
                               id="section_key" readonly>
                        <div class="form-text">Sistem anahtarı (değiştirilemez)</div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="update_section" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Sürükle & Bırak Stilleri - Developer: BERAT K */
.sortable-list {
    min-height: 200px;
}

.sortable-item {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    padding: 15px;
    margin-bottom: 10px;
    cursor: move;
    transition: all 0.3s ease;
}

.sortable-item:hover {
    background: rgba(255, 255, 255, 0.1);
    border-color: rgba(108, 92, 231, 0.5);
}

.sortable-item.ui-sortable-helper {
    background: rgba(108, 92, 231, 0.2);
    border-color: var(--primary-color);
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.3);
    transform: rotate(2deg);
}

.sortable-item.ui-sortable-placeholder {
    background: rgba(108, 92, 231, 0.1);
    border: 2px dashed var(--primary-color);
    height: 80px;
    visibility: visible !important;
}

.drag-handle {
    cursor: grab;
    font-size: 1.2em;
}

.drag-handle:active {
    cursor: grabbing;
}

.section-controls .btn {
    margin-left: 5px;
}

.section-order {
    min-width: 30px;
    text-align: center;
}

.info-item h6 {
    color: var(--light-color);
    margin-bottom: 8px;
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<script>
$(document).ready(function() {
    // Sortable işlevselliği - Developer: BERAT K
    $("#sortable-sections").sortable({
        handle: '.drag-handle',
        placeholder: 'ui-sortable-placeholder',
        helper: 'clone',
        update: function(event, ui) {
            updateSectionOrder();
        },
        start: function(event, ui) {
            ui.helper.addClass('ui-sortable-helper');
        },
        stop: function(event, ui) {
            ui.item.removeClass('ui-sortable-helper');
        }
    });
    
    $("#sortable-sections").disableSelection();
});

// Sıralama güncelleme fonksiyonu - Developer: BERAT K
function updateSectionOrder() {
    var sections = [];
    
    $('#sortable-sections .sortable-item').each(function(index) {
        var sectionId = $(this).data('id');
        sections.push(sectionId);
        
        // Sıra numarasını güncelle
        $(this).find('.section-order').text(index + 1);
    });
    
    // AJAX ile sunucuya gönder
    $.ajax({
        url: 'content-ordering.php',
        method: 'POST',
        data: {
            action: 'update_order',
            sections: JSON.stringify(sections)
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                // Başarı mesajı göster
                showMessage('success', response.message);
            } else {
                showMessage('error', response.message);
            }
        },
        error: function() {
            showMessage('error', 'Güncelleme sırasında hata oluştu!');
        }
    });
}

// Bölüm düzenleme fonksiyonu - Developer: BERAT K
function editSection(section) {
    document.getElementById('edit_section_id').value = section.id;
    document.getElementById('section_title').value = section.section_title;
    document.getElementById('section_description').value = section.section_description || '';
    document.getElementById('section_key').value = section.section_key;
    
    new bootstrap.Modal(document.getElementById('editSectionModal')).show();
}

// Sıralamayı sıfırla - Developer: BERAT K
function resetOrder() {
    if (confirm('Bölüm sıralamasını varsayılan haline getirmek istediğinizden emin misiniz?')) {
        // Varsayılan sıralama ile güncelle
        var defaultSections = [];
        $('#sortable-sections .sortable-item').each(function() {
            defaultSections.push($(this).data('id'));
        });
        
        // AJAX ile güncelle
        $.ajax({
            url: 'content-ordering.php',
            method: 'POST',
            data: {
                action: 'update_order',
                sections: JSON.stringify(defaultSections)
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    location.reload();
                } else {
                    showMessage('error', response.message);
                }
            }
        });
    }
}

// Mesaj gösterme fonksiyonu - Developer: BERAT K
function showMessage(type, message) {
    var alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
    var icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
    
    var alertHtml = `
        <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Mevcut alert'leri kaldır
    $('.alert').remove();
    
    // Yeni alert'i ekle
    $('.container-fluid').prepend(alertHtml);
    
    // 5 saniye sonra otomatik kapat
    setTimeout(function() {
        $('.alert').fadeOut();
    }, 5000);
}
</script>

<?php include 'includes/footer.php'; ?>