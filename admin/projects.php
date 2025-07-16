<?php
/**
 * Admin Projects Management
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$page_title = 'Projeler';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_project'])) {
        $title = clean($_POST['title']);
        $description = clean($_POST['description']);
        $category = clean($_POST['category']);
        $technologies = clean($_POST['technologies']);
        $demo_url = clean($_POST['demo_url']);
        $github_url = clean($_POST['github_url']);
        $status = isset($_POST['status']) ? 1 : 0;
        
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_path = uploadFile($_FILES['image'], 'uploads/projects/');
            if (!$image_path) {
                $error_message = 'Resim yüklenirken hata oluştu. Lütfen dosya formatını ve boyutunu kontrol edin.';
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO projects (title, description, image, demo_url, github_url, category, technologies, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $description, $image_path, $demo_url, $github_url, $category, $technologies, $status])) {
                $success_message = 'Proje başarıyla eklendi.';
            }
        } catch(PDOException $e) {
            $error_message = 'Proje eklenirken hata oluştu.';
        }
    } elseif (isset($_POST['edit_project'])) {
        $id = (int)$_POST['id'];
        $title = clean($_POST['title']);
        $description = clean($_POST['description']);
        $category = clean($_POST['category']);
        $technologies = clean($_POST['technologies']);
        $demo_url = clean($_POST['demo_url']);
        $github_url = clean($_POST['github_url']);
        $status = isset($_POST['status']) ? 1 : 0;
        
        $current_project = $pdo->prepare("SELECT image FROM projects WHERE id = ?");
        $current_project->execute([$id]);
        $current = $current_project->fetch();
        $image_path = $current['image'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $new_image = uploadFile($_FILES['image'], 'uploads/projects/');
            if ($new_image) {
                if ($image_path && file_exists('../' . $image_path)) {
                    unlink('../' . $image_path);
                }
                $image_path = $new_image;
            } else {
                $error_message = 'Resim yüklenirken hata oluştu. Lütfen dosya formatını ve boyutunu kontrol edin.';
            }
        }
        
        try {
            $stmt = $pdo->prepare("UPDATE projects SET title = ?, description = ?, image = ?, demo_url = ?, github_url = ?, category = ?, technologies = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$title, $description, $image_path, $demo_url, $github_url, $category, $technologies, $status, $id])) {
                $success_message = 'Proje başarıyla güncellendi.';
            }
        } catch(PDOException $e) {
            $error_message = 'Proje güncellenirken hata oluştu.';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $project = $pdo->prepare("SELECT image FROM projects WHERE id = ?");
        $project->execute([$id]);
        $proj = $project->fetch();
        
        $stmt = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        if ($stmt->execute([$id])) {
            if ($proj['image'] && file_exists($proj['image'])) {
                unlink($proj['image']);
            }
            $success_message = 'Proje başarıyla silindi.';
        }
    } catch(PDOException $e) {
        $error_message = 'Proje silinirken hata oluştu.';
    }
}

$edit_project = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_project = $stmt->fetch();
    } catch(PDOException $e) {
        $error_message = 'Proje bilgileri alınamadı.';
    }
}

try {
    $projects = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC")->fetchAll();
} catch(PDOException $e) {
    $projects = [];
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">Projeler</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#projectModal">
                <i class="fas fa-plus me-2"></i>Yeni Proje
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
    
    <?php if (!empty($projects)): ?>
        <div class="row">
            <?php foreach ($projects as $project): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card-custom h-100">
                        <div class="project-image-admin" style="<?php 
                            if ($project['image'] && file_exists('../' . $project['image'])) {
                                echo "background-image: url('../" . htmlspecialchars($project['image']) . "');";
                            } else {
                                echo "background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); display: flex; align-items: center; justify-content: center;";
                            }
                            ?> height: 200px; background-size: cover; background-position: center; border-radius: 15px 15px 0 0;">
                            <?php if (!$project['image'] || !file_exists('../' . $project['image'])): ?>
                                <i class="fas fa-project-diagram fa-3x text-white" style="opacity: 0.7;"></i>
                            <?php endif; ?>
                            <div class="project-overlay-admin">
                                <div class="project-actions">
                                    <button class="btn btn-sm btn-light" onclick="editProject(<?php echo $project['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $project['id']; ?>" class="btn btn-sm btn-danger delete-btn" data-confirm="Bu projeyi silmek istediğinizden emin misiniz?">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-light"><?php echo htmlspecialchars($project['title']); ?></h5>
                            <p class="text-light small"><?php echo truncateText($project['description'], 100); ?></p>
                            
                            <?php if($project['technologies']): ?>
                                <div class="mb-2">
                                    <?php 
                                    $techs = explode(',', $project['technologies']);
                                    foreach($techs as $tech): 
                                        $tech = trim($tech);
                                        if(!empty($tech)):
                                    ?>
                                        <span class="badge bg-secondary me-1"><?php echo htmlspecialchars($tech); ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="badge bg-gradient"><?php echo htmlspecialchars($project['category']); ?></span>
                                <span class="badge <?php echo $project['status'] ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $project['status'] ? 'Aktif' : 'Pasif'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-folder-open fa-5x text-muted mb-4"></i>
            <h3 class="text-light mb-3">Henüz proje eklenmemiş</h3>
            <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#projectModal">
                <i class="fas fa-plus me-2"></i>İlk Projeyi Ekle
            </button>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light" id="projectModalTitle">Yeni Proje Ekle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="projectForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="project_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Proje Başlığı</label>
                            <input type="text" class="form-control" name="title" id="project_title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Kategori</label>
                            <input type="text" class="form-control" name="category" id="project_category" placeholder="Web Sitesi, Mobil App, vb.">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-light">Açıklama</label>
                        <textarea class="form-control" name="description" id="project_description" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-light">Teknolojiler (virgülle ayırın)</label>
                        <input type="text" class="form-control" name="technologies" id="project_technologies" placeholder="HTML, CSS, JavaScript, PHP">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Demo URL</label>
                            <input type="url" class="form-control" name="demo_url" id="project_demo_url">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">GitHub URL</label>
                            <input type="url" class="form-control" name="github_url" id="project_github_url">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-light">Proje Resmi</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">JPG, PNG, GIF formatları desteklenir</small>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="project_status" checked>
                        <label class="form-check-label text-light" for="project_status">
                            Aktif (Sitede görüntülensin)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="add_project" id="submitBtn" class="btn btn-gradient">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.project-image-admin {
    position: relative;
    overflow: hidden;
}

.project-overlay-admin {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: all 0.3s ease;
}

.project-image-admin:hover .project-overlay-admin {
    opacity: 1;
}

.project-actions {
    display: flex;
    gap: 10px;
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
</style>

<script>
const projects = <?php echo json_encode($projects); ?>;

function editProject(id) {
    const project = projects.find(p => p.id == id);
    if (project) {
        document.getElementById('projectModalTitle').textContent = 'Proje Düzenle';
        document.getElementById('project_id').value = project.id;
        document.getElementById('project_title').value = project.title;
        document.getElementById('project_category').value = project.category;
        document.getElementById('project_description').value = project.description;
        document.getElementById('project_technologies').value = project.technologies;
        document.getElementById('project_demo_url').value = project.demo_url;
        document.getElementById('project_github_url').value = project.github_url;
        document.getElementById('project_status').checked = project.status == 1;
        document.getElementById('submitBtn').name = 'edit_project';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Güncelle';
        
        new bootstrap.Modal(document.getElementById('projectModal')).show();
    }
}

document.getElementById('projectModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('projectForm').reset();
    document.getElementById('projectModalTitle').textContent = 'Yeni Proje Ekle';
    document.getElementById('project_id').value = '';
    document.getElementById('submitBtn').name = 'add_project';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Kaydet';
});

document.addEventListener('DOMContentLoaded', function() {
    <?php if ($edit_project): ?>
        editProject(<?php echo $edit_project['id']; ?>);
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