<?php
/**
 * Admin Services Management
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$page_title = 'Hizmetler';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_service'])) {
        $title = clean($_POST['title']);
        $description = clean($_POST['description']);
        $icon = clean($_POST['icon']);
        $status = isset($_POST['status']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("INSERT INTO services (title, description, icon, status) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$title, $description, $icon, $status])) {
                $success_message = 'Hizmet başarıyla eklendi.';
            }
        } catch(PDOException $e) {
            $error_message = 'Hizmet eklenirken hata oluştu.';
        }
    } elseif (isset($_POST['edit_service'])) {
        $id = (int)$_POST['id'];
        $title = clean($_POST['title']);
        $description = clean($_POST['description']);
        $icon = clean($_POST['icon']);
        $status = isset($_POST['status']) ? 1 : 0;
        
        try {
            $stmt = $pdo->prepare("UPDATE services SET title = ?, description = ?, icon = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$title, $description, $icon, $status, $id])) {
                $success_message = 'Hizmet başarıyla güncellendi.';
            }
        } catch(PDOException $e) {
            $error_message = 'Hizmet güncellenirken hata oluştu.';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        if ($stmt->execute([$id])) {
            $success_message = 'Hizmet başarıyla silindi.';
        }
    } catch(PDOException $e) {
        $error_message = 'Hizmet silinirken hata oluştu.';
    }
}

$edit_service = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_service = $stmt->fetch();
    } catch(PDOException $e) {
        $error_message = 'Hizmet bilgileri alınamadı.';
    }
}

try {
    $services = $pdo->query("SELECT * FROM services ORDER BY created_at DESC")->fetchAll();
} catch(PDOException $e) {
    $services = [];
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">Hizmetler</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#serviceModal">
                <i class="fas fa-plus me-2"></i>Yeni Hizmet
            </button>
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
    
    <?php if (!empty($services)): ?>
        <div class="row">
            <?php foreach ($services as $service): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card-custom h-100">
                        <div class="card-body text-center">
                            <div class="service-icon mb-3">
                                <i class="<?php echo htmlspecialchars($service['icon']); ?> fa-3x text-gradient"></i>
                            </div>
                            <h5 class="card-title text-light"><?php echo htmlspecialchars($service['title']); ?></h5>
                            <p class="text-light"><?php echo htmlspecialchars($service['description']); ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <span class="badge <?php echo $service['status'] ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $service['status'] ? 'Aktif' : 'Pasif'; ?>
                                </span>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-outline-light" onclick="editService(<?php echo $service['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $service['id']; ?>" class="btn btn-sm btn-outline-danger delete-btn" data-confirm="Bu hizmeti silmek istediğinizden emin misiniz?">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-cogs fa-5x text-muted mb-4"></i>
            <h3 class="text-light mb-3">Henüz hizmet eklenmemiş</h3>
            <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#serviceModal">
                <i class="fas fa-plus me-2"></i>İlk Hizmeti Ekle
            </button>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="serviceModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light" id="serviceModalTitle">Yeni Hizmet Ekle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="serviceForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="service_id">
                    
                    <div class="mb-3">
                        <label class="form-label text-light">Hizmet Başlığı</label>
                        <input type="text" class="form-control" name="title" id="service_title" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-light">Açıklama</label>
                        <textarea class="form-control" name="description" id="service_description" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-light">İkon (FontAwesome)</label>
                        <input type="text" class="form-control" name="icon" id="service_icon" placeholder="fas fa-code" required>
                        <small class="text-muted">
                            Örnek: fas fa-code, fas fa-mobile-alt, fas fa-paint-brush
                            <a href="https://fontawesome.com/icons" target="_blank" class="text-primary">FontAwesome ikonları</a>
                        </small>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="service_status" checked>
                        <label class="form-check-label text-light" for="service_status">
                            Aktif (Sitede görüntülensin)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="add_service" id="submitBtn" class="btn btn-gradient">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.service-icon {
    margin-bottom: 20px;
}

.modal-content {
    background-color: var(--dark-card);
    border: 1px solid var(--dark-border);
}

.modal-header {
    border-bottom: 1px solid var(--dark-border);
}

.modal-footer {
    border-top: 1px solid var(--dark-border);
}

.btn-close-white {
    filter: invert(1) grayscale(100%) brightness(200%);
}

.card-custom:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(108, 92, 231, 0.2);
}
</style>

<script>
const services = <?php echo json_encode($services); ?>;

function editService(id) {
    const service = services.find(s => s.id == id);
    if (service) {
        document.getElementById('serviceModalTitle').textContent = 'Hizmet Düzenle';
        document.getElementById('service_id').value = service.id;
        document.getElementById('service_title').value = service.title;
        document.getElementById('service_description').value = service.description;
        document.getElementById('service_icon').value = service.icon;
        document.getElementById('service_status').checked = service.status == 1;
        document.getElementById('submitBtn').name = 'edit_service';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Güncelle';
        
        new bootstrap.Modal(document.getElementById('serviceModal')).show();
    }
}

document.getElementById('serviceModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('serviceForm').reset();
    document.getElementById('serviceModalTitle').textContent = 'Yeni Hizmet Ekle';
    document.getElementById('service_id').value = '';
    document.getElementById('submitBtn').name = 'add_service';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Kaydet';
});

document.addEventListener('DOMContentLoaded', function() {
    <?php if ($edit_service): ?>
        editService(<?php echo $edit_service['id']; ?>);
    <?php endif; ?>
    
    const deleteButtons = document.querySelectorAll('.delete-btn');
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const confirmMessage = this.getAttribute('data-confirm');
            if (confirm(confirmMessage)) {
                window.location.href = this.href;
            }
        });
    });
});
</script>

<?php include 'includes/footer.php'; ?>