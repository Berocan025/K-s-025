<?php
/**
 * Admin Kullanıcı Yönetimi
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * 
 * Bu sayfa yeni admin kullanıcılar ekleme ve yetki yönetimi sağlar
 */

require_once '../includes/functions.php';
requireLogin();

$page_title = 'Admin Kullanıcı Yönetimi';
$success_message = '';
$error_message = '';

// Yeni admin kullanıcı ekleme - Developer: BERAT K
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin'])) {
    $username = clean($_POST['username']);
    $email = clean($_POST['email']);
    $password = $_POST['password'];
    $role_id = (int)$_POST['role_id'];
    $status = isset($_POST['status']) ? 1 : 0;
    
    if (empty($username) || empty($email) || empty($password)) {
        $error_message = 'Tüm alanları doldurmanız gerekiyor!';
    } else {
        try {
            // Kullanıcı adı ve email benzersizlik kontrolü
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            
            if ($stmt->fetchColumn() > 0) {
                $error_message = 'Bu kullanıcı adı veya email zaten kullanılıyor!';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                $stmt = $pdo->prepare("INSERT INTO admin_users (username, email, password, role_id, status, created_at) VALUES (?, ?, ?, ?, ?, datetime('now'))");
                
                if ($stmt->execute([$username, $email, $hashed_password, $role_id, $status])) {
                    $success_message = "Yeni admin kullanıcı '$username' başarıyla eklendi! (Developer: BERAT K)";
                } else {
                    $error_message = 'Kullanıcı eklenirken hata oluştu!';
                }
            }
        } catch(PDOException $e) {
            $error_message = 'Veritabanı hatası: ' . $e->getMessage();
        }
    }
}

// Admin kullanıcı düzenleme - Developer: BERAT K
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_admin'])) {
    $user_id = (int)$_POST['user_id'];
    $username = clean($_POST['edit_username']);
    $email = clean($_POST['edit_email']);
    $role_id = (int)$_POST['edit_role_id'];
    $status = isset($_POST['edit_status']) ? 1 : 0;
    
    try {
        $stmt = $pdo->prepare("UPDATE admin_users SET username = ?, email = ?, role_id = ?, status = ? WHERE id = ?");
        
        if ($stmt->execute([$username, $email, $role_id, $status, $user_id])) {
            $success_message = "Admin kullanıcı bilgileri güncellendi! (Developer: BERAT K)";
        } else {
            $error_message = 'Güncelleme işlemi başarısız!';
        }
    } catch(PDOException $e) {
        $error_message = 'Veritabanı hatası: ' . $e->getMessage();
    }
}

// Şifre güncelleme - Developer: BERAT K
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_password'])) {
    $user_id = (int)$_POST['user_id'];
    $new_password = $_POST['new_password'];
    
    if (!empty($new_password)) {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
            
            if ($stmt->execute([$hashed_password, $user_id])) {
                $success_message = "Şifre başarıyla güncellendi! (Developer: BERAT K)";
            } else {
                $error_message = 'Şifre güncellenemedi!';
            }
        } catch(PDOException $e) {
            $error_message = 'Veritabanı hatası: ' . $e->getMessage();
        }
    }
}

// Admin kullanıcıları ve rolleri getir - Developer: BERAT K
try {
    $admin_users = $pdo->query("
        SELECT u.*, r.role_display_name 
        FROM admin_users u 
        LEFT JOIN admin_roles r ON u.role_id = r.id 
        ORDER BY u.created_at DESC
    ")->fetchAll();
    
    $admin_roles = $pdo->query("SELECT * FROM admin_roles WHERE is_active = 1 ORDER BY role_name")->fetchAll();
} catch(PDOException $e) {
    $error_message = 'Veri yükleme hatası: ' . $e->getMessage();
    $admin_users = [];
    $admin_roles = [];
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">
            <i class="fas fa-users-cog me-2"></i>Admin Kullanıcı Yönetimi
        </h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addAdminModal">
                <i class="fas fa-user-plus me-1"></i>Yeni Admin Ekle
            </button>
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

    <div class="card-custom">
        <div class="card-body">
            <h5 class="card-title">Mevcut Admin Kullanıcılar</h5>
            <p class="text-light">Sistemimdeki tüm admin kullanıcıları buradan yönetebilirsiniz. Developer: BERAT K</p>
            
            <div class="table-responsive">
                <table class="table table-dark table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Kullanıcı Adı</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Durum</th>
                            <th>Son Giriş</th>
                            <th>Oluşturma</th>
                            <th>İşlemler</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($admin_users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td>
                                <strong><?php echo htmlspecialchars($user['username']); ?></strong>
                                <?php if ($user['is_super_admin']): ?>
                                    <span class="badge bg-danger ms-1">Süper Admin</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="badge bg-info">
                                    <?php echo $user['role_display_name'] ?? 'Rol Tanımlanmamış'; ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($user['status']): ?>
                                    <span class="badge bg-success">Aktif</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Pasif</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                echo $user['last_login'] 
                                    ? date('d.m.Y H:i', strtotime($user['last_login']))
                                    : 'Hiç giriş yapılmamış';
                                ?>
                            </td>
                            <td><?php echo date('d.m.Y H:i', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <button type="button" class="btn btn-outline-warning btn-sm" 
                                            onclick="editAdmin(<?php echo htmlspecialchars(json_encode($user)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-outline-info btn-sm"
                                            onclick="changePassword(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                        <i class="fas fa-key"></i>
                                    </button>
                                    <?php if ($user['id'] != $_SESSION['admin_id']): ?>
                                    <button type="button" class="btn btn-outline-danger btn-sm"
                                            onclick="deleteAdmin(<?php echo $user['id']; ?>, '<?php echo htmlspecialchars($user['username']); ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Yeni Admin Ekleme Modal - Developer: BERAT K -->
<div class="modal fade" id="addAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">
                    <i class="fas fa-user-plus me-2"></i>Yeni Admin Kullanıcı Ekle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">Kullanıcı Adı</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" 
                               id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control bg-dark text-light border-secondary" 
                               id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre</label>
                        <input type="password" class="form-control bg-dark text-light border-secondary" 
                               id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">Admin Rolü</label>
                        <select class="form-select bg-dark text-light border-secondary" id="role_id" name="role_id" required>
                            <option value="">Rol Seçin</option>
                            <?php foreach ($admin_roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>">
                                    <?php echo htmlspecialchars($role['role_display_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="status" name="status" checked>
                        <label class="form-check-label" for="status">
                            Aktif Kullanıcı
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="add_admin" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Admin Ekle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Admin Düzenleme Modal - Developer: BERAT K -->
<div class="modal fade" id="editAdminModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">
                    <i class="fas fa-user-edit me-2"></i>Admin Kullanıcı Düzenle
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" id="edit_user_id" name="user_id">
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Kullanıcı Adı</label>
                        <input type="text" class="form-control bg-dark text-light border-secondary" 
                               id="edit_username" name="edit_username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email</label>
                        <input type="email" class="form-control bg-dark text-light border-secondary" 
                               id="edit_email" name="edit_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_role_id" class="form-label">Admin Rolü</label>
                        <select class="form-select bg-dark text-light border-secondary" id="edit_role_id" name="edit_role_id" required>
                            <?php foreach ($admin_roles as $role): ?>
                                <option value="<?php echo $role['id']; ?>">
                                    <?php echo htmlspecialchars($role['role_display_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="edit_status" name="edit_status">
                        <label class="form-check-label" for="edit_status">
                            Aktif Kullanıcı
                        </label>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="edit_admin" class="btn btn-warning">
                        <i class="fas fa-save me-1"></i>Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Şifre Değiştirme Modal - Developer: BERAT K -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content bg-dark">
            <div class="modal-header border-secondary">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i>Şifre Değiştir
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <input type="hidden" id="password_user_id" name="user_id">
                    <p>Kullanıcı: <span id="password_username" class="fw-bold"></span></p>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Yeni Şifre</label>
                        <input type="password" class="form-control bg-dark text-light border-secondary" 
                               id="new_password" name="new_password" required>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="update_password" class="btn btn-info">
                        <i class="fas fa-save me-1"></i>Şifreyi Güncelle
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Admin düzenleme fonksiyonu - Developer: BERAT K
function editAdmin(user) {
    document.getElementById('edit_user_id').value = user.id;
    document.getElementById('edit_username').value = user.username;
    document.getElementById('edit_email').value = user.email;
    document.getElementById('edit_role_id').value = user.role_id;
    document.getElementById('edit_status').checked = user.status == 1;
    
    new bootstrap.Modal(document.getElementById('editAdminModal')).show();
}

// Şifre değiştirme fonksiyonu - Developer: BERAT K
function changePassword(userId, username) {
    document.getElementById('password_user_id').value = userId;
    document.getElementById('password_username').textContent = username;
    document.getElementById('new_password').value = '';
    
    new bootstrap.Modal(document.getElementById('passwordModal')).show();
}

// Admin silme fonksiyonu - Developer: BERAT K
function deleteAdmin(userId, username) {
    if (confirm(`'${username}' adlı admin kullanıcıyı silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz!`)) {
        // AJAX ile silme işlemi yapılabilir
        // Şimdilik alert ile bilgilendirme
        alert('Silme özelliği yakında eklenecek. Developer: BERAT K');
    }
}
</script>

<?php include 'includes/footer.php'; ?>