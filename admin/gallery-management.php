<?php
/**
 * Gallery Management - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once '../includes/functions.php';
requireLogin();

$page_title = 'Galeri Yönetimi';

$success_message = '';
$error_message = '';

// Handle gallery operations
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_gallery_item'])) {
        $title = clean($_POST['title']);
        $description = $_POST['description']; // HTML içerebilir
        $type = clean($_POST['type']);
        $youtube_url = clean($_POST['youtube_url']);
        $sort_order = (int)clean($_POST['sort_order']);
        
        $file_path = '';
        
        // Dosya yükleme işlemi
        if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/gallery/';
            if (!file_exists($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    $error_message = 'Upload klasörü oluşturulamadı. Klasör izinlerini kontrol edin.';
                }
            }
            
            $file_extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $allowed_image_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
            $allowed_video_extensions = ['mp4', 'avi', 'mov', 'wmv'];
            
            if ($type === 'image' && in_array(strtolower($file_extension), $allowed_image_extensions)) {
                $file_name = 'gallery_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                $file_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
                    $file_path = 'uploads/gallery/' . $file_name;
                } else {
                    $error_message = 'Dosya yüklenirken bir hata oluştu.';
                }
            } elseif ($type === 'video' && in_array(strtolower($file_extension), $allowed_video_extensions)) {
                $file_name = 'gallery_video_' . time() . '_' . rand(1000, 9999) . '.' . $file_extension;
                $file_path = $upload_dir . $file_name;
                
                if (move_uploaded_file($_FILES['file']['tmp_name'], $file_path)) {
                    $file_path = 'uploads/gallery/' . $file_name;
                } else {
                    $error_message = 'Video yüklenirken bir hata oluştu.';
                }
            } else {
                $error_message = 'Desteklenmeyen dosya formatı.';
            }
        }
        
        if (empty($error_message)) {
            // Eğer dosya yüklenmedi ve YouTube URL de yoksa hata ver
            if (empty($file_path) && empty($youtube_url)) {
                $error_message = 'Lütfen bir dosya yükleyin veya YouTube URL\'si girin.';
            } else {
                if (addGalleryItem($title, $description, $type, $file_path, $youtube_url, $sort_order)) {
                    $success_message = 'Galeri öğesi başarıyla eklendi!';
                } else {
                    $error_message = 'Galeri öğesi eklenirken bir hata oluştu. Veritabanı bağlantısını kontrol edin.';
                }
            }
        }
    }
    
    if (isset($_POST['update_gallery_item'])) {
        $id = clean($_POST['gallery_id']);
        $title = clean($_POST['title']);
        $description = $_POST['description'];
        $type = clean($_POST['type']);
        $youtube_url = clean($_POST['youtube_url']);
        $sort_order = (int)clean($_POST['sort_order']);
        
        if (updateGalleryItem($id, $title, $description, $type, $youtube_url, $sort_order)) {
            $success_message = 'Galeri öğesi başarıyla güncellendi!';
        } else {
            $error_message = 'Galeri öğesi güncellenirken bir hata oluştu.';
        }
    }
    
    if (isset($_POST['delete_gallery_item'])) {
        $id = clean($_POST['gallery_id']);
        if (deleteGalleryItem($id)) {
            $success_message = 'Galeri öğesi başarıyla silindi!';
        } else {
            $error_message = 'Galeri öğesi silinirken bir hata oluştu.';
        }
    }
    
    if (isset($_POST['toggle_status'])) {
        $id = clean($_POST['gallery_id']);
        $status = clean($_POST['status']);
        if (toggleGalleryStatus($id, $status)) {
            $success_message = 'Galeri öğesi durumu güncellendi!';
        } else {
            $error_message = 'Durum güncellenirken bir hata oluştu.';
        }
    }
}

// Get all gallery items
$gallery_items = getAllGalleryItems();

include 'includes/header.php';
?>

<div class="main-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-header">
                    <h1><i class="fas fa-images me-2"></i>Galeri Yönetimi</h1>
                    <p class="text-muted">Fotoğraf ve video galerisi yönetimi. YouTube entegrasyonu ve dosya yükleme desteklenir.</p>
                    
                    <!-- Debug Bilgileri -->
                    <div class="alert alert-info">
                        <small>
                            <strong>Debug:</strong> 
                            Gallery tablosu: <?php 
                            try {
                                $check = $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
                                echo "✅ $check kayıt";
                            } catch (Exception $e) {
                                echo "❌ Tablo bulunamadı";
                            }
                            ?> | 
                            Upload klasörü: <?php 
                            echo file_exists('../uploads/gallery/') ? '✅ Mevcut' : '❌ Yok';
                            ?> |
                            Upload izni: <?php
                            echo is_writable('../uploads/') ? '✅ Yazılabilir' : '❌ Yazılamaz';
                            ?>
                        </small>
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

                <!-- Add New Gallery Item Card -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-plus me-2"></i>Yeni Galeri Öğesi Ekle</h5>
                    </div>
                    <div class="card-body">
                        <form method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Başlık <span class="text-danger">*</span></label>
                                        <input type="text" class="form-control" id="title" name="title" required>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="type" class="form-label">Tür <span class="text-danger">*</span></label>
                                        <select class="form-control" id="type" name="type" required onchange="toggleUploadOptions()">
                                            <option value="image">Fotoğraf</option>
                                            <option value="video">Video</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="mb-3">
                                        <label for="sort_order" class="form-label">Sıra</label>
                                        <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="description" class="form-label">Açıklama</label>
                                <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6" id="file_upload_section">
                                    <div class="mb-3">
                                        <label for="file" class="form-label">Dosya Yükle</label>
                                        <input type="file" class="form-control" id="file" name="file" 
                                               accept="image/*,video/*">
                                        <small class="form-text text-muted">
                                            Fotoğraf: JPG, PNG, GIF, WEBP | Video: MP4, AVI, MOV, WMV
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6" id="youtube_section" style="display: none;">
                                    <div class="mb-3">
                                        <label for="youtube_url" class="form-label">YouTube URL</label>
                                        <input type="url" class="form-control" id="youtube_url" name="youtube_url" 
                                               placeholder="https://www.youtube.com/watch?v=...">
                                        <small class="form-text text-muted">
                                            YouTube video bağlantısı (opsiyonel)
                                        </small>
                                    </div>
                                </div>
                            </div>
                            
                            <button type="submit" name="add_gallery_item" class="btn btn-gradient">
                                <i class="fas fa-plus me-1"></i>Galeri Öğesi Ekle
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Gallery Items List -->
                <div class="card">
                    <div class="card-header">
                        <h5><i class="fas fa-list me-2"></i>Galeri Öğeleri</h5>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($gallery_items)): ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Önizleme</th>
                                            <th>Başlık</th>
                                            <th>Tür</th>
                                            <th>Sıra</th>
                                            <th>Durum</th>
                                            <th>Tarih</th>
                                            <th>İşlemler</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($gallery_items as $item): ?>
                                            <tr>
                                                <td>
                                                    <?php if ($item['type'] === 'video'): ?>
                                                        <?php if (!empty($item['youtube_url'])): ?>
                                                            <?php 
                                                            $youtube_id = '';
                                                            if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/)([^&\n?#]+)/', $item['youtube_url'], $matches)) {
                                                                $youtube_id = $matches[1];
                                                            }
                                                            ?>
                                                            <img src="https://img.youtube.com/vi/<?php echo $youtube_id; ?>/default.jpg" 
                                                                 alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                                                 class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                                                        <?php elseif (!empty($item['file_path'])): ?>
                                                            <video style="width: 60px; height: 40px;">
                                                                <source src="<?php echo htmlspecialchars($item['file_path']); ?>" type="video/mp4">
                                                            </video>
                                                        <?php else: ?>
                                                            <i class="fas fa-video fa-2x text-muted"></i>
                                                        <?php endif; ?>
                                                    <?php else: ?>
                                                        <?php if (!empty($item['file_path']) && file_exists($item['file_path'])): ?>
                                                            <img src="<?php echo htmlspecialchars($item['file_path']); ?>" 
                                                                 alt="<?php echo htmlspecialchars($item['title']); ?>" 
                                                                 class="img-thumbnail" style="width: 60px; height: 40px; object-fit: cover;">
                                                        <?php else: ?>
                                                            <i class="fas fa-image fa-2x text-muted"></i>
                                                        <?php endif; ?>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($item['title']); ?></strong>
                                                    <?php if (!empty($item['description'])): ?>
                                                        <br><small class="text-muted"><?php echo truncateText($item['description'], 50); ?></small>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <span class="badge <?php echo $item['type'] === 'video' ? 'bg-primary' : 'bg-success'; ?>">
                                                        <i class="fas fa-<?php echo $item['type'] === 'video' ? 'play' : 'image'; ?> me-1"></i>
                                                        <?php echo $item['type'] === 'video' ? 'Video' : 'Fotoğraf'; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo $item['sort_order']; ?></td>
                                                <td>
                                                    <form method="POST" class="d-inline">
                                                        <input type="hidden" name="gallery_id" value="<?php echo $item['id']; ?>">
                                                        <input type="hidden" name="status" value="<?php echo $item['is_active'] ? '0' : '1'; ?>">
                                                        <button type="submit" name="toggle_status" 
                                                                class="btn btn-sm <?php echo $item['is_active'] ? 'btn-success' : 'btn-secondary'; ?>">
                                                            <i class="fas fa-<?php echo $item['is_active'] ? 'eye' : 'eye-slash'; ?>"></i>
                                                            <?php echo $item['is_active'] ? 'Aktif' : 'Pasif'; ?>
                                                        </button>
                                                    </form>
                                                </td>
                                                <td>
                                                    <small><?php echo date('d.m.Y H:i', strtotime($item['created_at'])); ?></small>
                                                </td>
                                                <td>
                                                    <button type="button" class="btn btn-sm btn-outline-primary me-1" 
                                                            onclick="editGalleryItem(<?php echo htmlspecialchars(json_encode($item)); ?>)">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <form method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Bu galeri öğesini silmek istediğinizden emin misiniz?')">
                                                        <input type="hidden" name="gallery_id" value="<?php echo $item['id']; ?>">
                                                        <button type="submit" name="delete_gallery_item" class="btn btn-sm btn-outline-danger">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-images fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Henüz galeri öğesi eklenmemiş.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Gallery Item Modal -->
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Galeri Öğesi Düzenle</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="editForm">
                <div class="modal-body">
                    <input type="hidden" name="gallery_id" id="edit_gallery_id">
                    
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="edit_title" class="form-label">Başlık</label>
                                <input type="text" class="form-control" id="edit_title" name="title" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="edit_sort_order" class="form-label">Sıra</label>
                                <input type="number" class="form-control" id="edit_sort_order" name="sort_order">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Açıklama</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3" id="edit_youtube_section">
                        <label for="edit_youtube_url" class="form-label">YouTube URL</label>
                        <input type="url" class="form-control" id="edit_youtube_url" name="youtube_url">
                    </div>
                    
                    <input type="hidden" name="type" id="edit_type">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="update_gallery_item" class="btn btn-gradient">Güncelle</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleUploadOptions() {
    const type = document.getElementById('type').value;
    const fileSection = document.getElementById('file_upload_section');
    const youtubeSection = document.getElementById('youtube_section');
    
    if (type === 'video') {
        youtubeSection.style.display = 'block';
        document.getElementById('file').setAttribute('accept', 'video/*');
    } else {
        youtubeSection.style.display = 'none';
        document.getElementById('file').setAttribute('accept', 'image/*');
    }
}

function editGalleryItem(item) {
    document.getElementById('edit_gallery_id').value = item.id;
    document.getElementById('edit_title').value = item.title;
    document.getElementById('edit_description').value = item.description || '';
    document.getElementById('edit_youtube_url').value = item.youtube_url || '';
    document.getElementById('edit_sort_order').value = item.sort_order;
    document.getElementById('edit_type').value = item.type;
    
    // YouTube section'ı video türünde göster
    const youtubeSection = document.getElementById('edit_youtube_section');
    if (item.type === 'video') {
        youtubeSection.style.display = 'block';
    } else {
        youtubeSection.style.display = 'none';
    }
    
    const modal = new bootstrap.Modal(document.getElementById('editModal'));
    modal.show();
}
</script>

<?php include 'includes/footer.php'; ?>