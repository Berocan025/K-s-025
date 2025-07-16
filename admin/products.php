<?php
/**
 * Admin Products Management
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$page_title = 'Ürünler';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        $title = clean($_POST['title']);
        $description = clean($_POST['description']);
        $demo_url = clean($_POST['demo_url']);
        $admin_demo_url = clean($_POST['admin_demo_url']);
        $price = clean($_POST['price']);
        $features = clean($_POST['features']);
        $status = isset($_POST['status']) ? 1 : 0;
        
        $image_path = '';
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $image_path = uploadFile($_FILES['image'], 'uploads/products/');
            if (!$image_path) {
                $error_message = 'Resim yüklenirken hata oluştu. Lütfen dosya formatını ve boyutunu kontrol edin.';
            }
        }
        
        try {
            $stmt = $pdo->prepare("INSERT INTO products (title, description, image, demo_url, admin_demo_url, price, features, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            if ($stmt->execute([$title, $description, $image_path, $demo_url, $admin_demo_url, $price, $features, $status])) {
                $success_message = 'Ürün başarıyla eklendi.';
            }
        } catch(PDOException $e) {
            $error_message = 'Ürün eklenirken hata oluştu.';
        }
    } elseif (isset($_POST['edit_product'])) {
        $id = (int)$_POST['id'];
        $title = clean($_POST['title']);
        $description = clean($_POST['description']);
        $demo_url = clean($_POST['demo_url']);
        $admin_demo_url = clean($_POST['admin_demo_url']);
        $price = clean($_POST['price']);
        $features = clean($_POST['features']);
        $status = isset($_POST['status']) ? 1 : 0;
        
        $current_product = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $current_product->execute([$id]);
        $current = $current_product->fetch();
        $image_path = $current['image'];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $new_image = uploadFile($_FILES['image'], 'uploads/products/');
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
            $stmt = $pdo->prepare("UPDATE products SET title = ?, description = ?, image = ?, demo_url = ?, admin_demo_url = ?, price = ?, features = ?, status = ? WHERE id = ?");
            if ($stmt->execute([$title, $description, $image_path, $demo_url, $admin_demo_url, $price, $features, $status, $id])) {
                $success_message = 'Ürün başarıyla güncellendi.';
            }
        } catch(PDOException $e) {
            $error_message = 'Ürün güncellenirken hata oluştu.';
        }
    }
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $product = $pdo->prepare("SELECT image FROM products WHERE id = ?");
        $product->execute([$id]);
        $prod = $product->fetch();
        
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        if ($stmt->execute([$id])) {
            if ($prod['image'] && file_exists($prod['image'])) {
                unlink($prod['image']);
            }
            $success_message = 'Ürün başarıyla silindi.';
        }
    } catch(PDOException $e) {
        $error_message = 'Ürün silinirken hata oluştu.';
    }
}

$edit_product = null;
if (isset($_GET['edit'])) {
    $edit_id = (int)$_GET['edit'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$edit_id]);
        $edit_product = $stmt->fetch();
    } catch(PDOException $e) {
        $error_message = 'Ürün bilgileri alınamadı.';
    }
}

try {
    $products = $pdo->query("SELECT * FROM products ORDER BY created_at DESC")->fetchAll();
} catch(PDOException $e) {
    $products = [];
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">Ürünler</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#productModal">
                <i class="fas fa-plus me-2"></i>Yeni Ürün
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
    
    <?php if (!empty($products)): ?>
        <div class="row">
            <?php foreach ($products as $product): ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card-custom h-100">
                        <div class="product-image-admin" style="<?php 
                            if ($product['image'] && file_exists('../' . $product['image'])) {
                                echo "background-image: url('../" . htmlspecialchars($product['image']) . "');";
                            } else {
                                echo "background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); display: flex; align-items: center; justify-content: center;";
                            }
                            ?> height: 200px; background-size: cover; background-position: center; border-radius: 15px 15px 0 0;">
                            <?php if (!$product['image'] || !file_exists('../' . $product['image'])): ?>
                                <i class="fas fa-box fa-3x text-white" style="opacity: 0.7;"></i>
                            <?php endif; ?>
                            <div class="product-overlay-admin">
                                <div class="product-actions">
                                    <?php if($product['demo_url']): ?>
                                        <a href="<?php echo htmlspecialchars($product['demo_url']); ?>" target="_blank" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if($product['admin_demo_url']): ?>
                                        <a href="<?php echo htmlspecialchars($product['admin_demo_url']); ?>" target="_blank" class="btn btn-sm btn-warning">
                                            <i class="fas fa-cog"></i>
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-sm btn-light" onclick="editProduct(<?php echo $product['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger delete-btn" data-confirm="Bu ürünü silmek istediğinizden emin misiniz?">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title text-light"><?php echo htmlspecialchars($product['title']); ?></h5>
                            <p class="text-light small"><?php echo truncateText($product['description'], 100); ?></p>
                            
                            <?php if($product['price']): ?>
                                <div class="mb-2">
                                    <span class="text-gradient fw-bold h5"><?php echo htmlspecialchars($product['price']); ?></span>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($product['features']): ?>
                                <div class="mb-2">
                                    <?php 
                                    $features = explode(',', $product['features']);
                                    foreach(array_slice($features, 0, 3) as $feature): 
                                        $feature = trim($feature);
                                        if(!empty($feature)):
                                    ?>
                                        <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars($feature); ?></span>
                                    <?php 
                                        endif;
                                    endforeach; 
                                    if(count($features) > 3):
                                    ?>
                                        <span class="badge bg-info">+<?php echo count($features) - 3; ?> daha</span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="demo-links">
                                    <?php if($product['demo_url']): ?>
                                        <a href="<?php echo htmlspecialchars($product['demo_url']); ?>" target="_blank" class="btn btn-outline-gradient btn-sm me-1">
                                            <i class="fas fa-eye me-1"></i>Demo
                                        </a>
                                    <?php endif; ?>
                                    <?php if($product['admin_demo_url']): ?>
                                        <a href="<?php echo htmlspecialchars($product['admin_demo_url']); ?>" target="_blank" class="btn btn-outline-warning btn-sm">
                                            <i class="fas fa-cog me-1"></i>Admin
                                        </a>
                                    <?php endif; ?>
                                </div>
                                <span class="badge <?php echo $product['status'] ? 'bg-success' : 'bg-secondary'; ?>">
                                    <?php echo $product['status'] ? 'Aktif' : 'Pasif'; ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-box fa-5x text-muted mb-4"></i>
            <h3 class="text-light mb-3">Henüz ürün eklenmemiş</h3>
            <button class="btn btn-gradient" data-bs-toggle="modal" data-bs-target="#productModal">
                <i class="fas fa-plus me-2"></i>İlk Ürünü Ekle
            </button>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="productModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light" id="productModalTitle">Yeni Ürün Ekle</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="productForm">
                <div class="modal-body">
                    <input type="hidden" name="id" id="product_id">
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Ürün Adı</label>
                            <input type="text" class="form-control" name="title" id="product_title" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Fiyat</label>
                            <input type="text" class="form-control" name="price" id="product_price" placeholder="$99, ₺500, Ücretsiz">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-light">Açıklama</label>
                        <textarea class="form-control" name="description" id="product_description" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-light">Özellikler (virgülle ayırın)</label>
                        <input type="text" class="form-control" name="features" id="product_features" placeholder="Responsive, SEO Uyumlu, Hızlı Yükleme">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Demo URL</label>
                            <input type="url" class="form-control" name="demo_url" id="product_demo_url">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-light">Admin Demo URL</label>
                            <input type="url" class="form-control" name="admin_demo_url" id="product_admin_demo_url">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label text-light">Ürün Resmi</label>
                        <input type="file" class="form-control" name="image" accept="image/*">
                        <small class="text-muted">JPG, PNG, GIF formatları desteklenir</small>
                    </div>
                    
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="status" id="product_status" checked>
                        <label class="form-check-label text-light" for="product_status">
                            Aktif (Sitede görüntülensin)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">İptal</button>
                    <button type="submit" name="add_product" id="submitBtn" class="btn btn-gradient">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.product-image-admin {
    position: relative;
    overflow: hidden;
}

.product-overlay-admin {
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

.product-image-admin:hover .product-overlay-admin {
    opacity: 1;
}

.product-actions {
    display: flex;
    gap: 10px;
}

.demo-links {
    display: flex;
    gap: 5px;
}

.demo-links .btn {
    font-size: 0.8rem;
    padding: 4px 8px;
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

.btn-outline-warning {
    border-color: #f39c12;
    color: #f39c12;
}

.btn-outline-warning:hover {
    background-color: #f39c12;
    color: white;
}
</style>

<script>
const products = <?php echo json_encode($products); ?>;

function editProduct(id) {
    const product = products.find(p => p.id == id);
    if (product) {
        document.getElementById('productModalTitle').textContent = 'Ürün Düzenle';
        document.getElementById('product_id').value = product.id;
        document.getElementById('product_title').value = product.title;
        document.getElementById('product_description').value = product.description;
        document.getElementById('product_demo_url').value = product.demo_url;
        document.getElementById('product_admin_demo_url').value = product.admin_demo_url;
        document.getElementById('product_price').value = product.price;
        document.getElementById('product_features').value = product.features;
        document.getElementById('product_status').checked = product.status == 1;
        document.getElementById('submitBtn').name = 'edit_product';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Güncelle';
        
        new bootstrap.Modal(document.getElementById('productModal')).show();
    }
}

document.getElementById('productModal').addEventListener('hidden.bs.modal', function () {
    document.getElementById('productForm').reset();
    document.getElementById('productModalTitle').textContent = 'Yeni Ürün Ekle';
    document.getElementById('product_id').value = '';
    document.getElementById('submitBtn').name = 'add_product';
    document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-2"></i>Kaydet';
});

document.addEventListener('DOMContentLoaded', function() {
    <?php if ($edit_product): ?>
        editProduct(<?php echo $edit_product['id']; ?>);
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