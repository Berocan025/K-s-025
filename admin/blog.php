<?php
/**
 * Blog & Content Management
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 */

require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$page_title = 'Blog & İçerik Yönetimi';

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Blog yazısı silme
    if (isset($_POST['delete_post'])) {
        try {
            $post_id = (int)$_POST['post_id'];
            
            // Öne çıkan görseli sil
            $stmt = $pdo->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $post = $stmt->fetch();
            if ($post && $post['featured_image'] && file_exists('../' . $post['featured_image'])) {
                unlink('../' . $post['featured_image']);
            }
            
            // Yazıyı sil
            $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
            if ($stmt->execute([$post_id])) {
                $success_message = 'Blog yazısı başarıyla silindi.';
            } else {
                $error_message = 'Blog yazısı silinirken hata oluştu.';
            }
        } catch(PDOException $e) {
            $error_message = 'Blog yazısı silinirken hata oluştu: ' . $e->getMessage();
        }
    }
    // Toplu blog yazısı silme
    elseif (isset($_POST['bulk_delete_posts']) && isset($_POST['selected_post_ids'])) {
        try {
            $selected_ids = $_POST['selected_post_ids'];
            $deleted_count = 0;
            
            foreach ($selected_ids as $post_id) {
                $post_id = (int)$post_id;
                
                // Öne çıkan görseli sil
                $stmt = $pdo->prepare("SELECT featured_image FROM blog_posts WHERE id = ?");
                $stmt->execute([$post_id]);
                $post = $stmt->fetch();
                if ($post && $post['featured_image'] && file_exists('../' . $post['featured_image'])) {
                    unlink('../' . $post['featured_image']);
                }
                
                // Yazıyı sil
                $stmt = $pdo->prepare("DELETE FROM blog_posts WHERE id = ?");
                if ($stmt->execute([$post_id])) {
                    $deleted_count++;
                }
            }
            
            if ($deleted_count > 0) {
                $success_message = $deleted_count . ' adet blog yazısı başarıyla silindi.';
            } else {
                $error_message = 'Hiçbir blog yazısı silinemedi.';
            }
        } catch(PDOException $e) {
            $error_message = 'Blog yazıları silinirken hata oluştu: ' . $e->getMessage();
        }
    }
    // Blog yazısı güncelleme
    elseif (isset($_POST['update_blog_post'])) {
        try {
            createBlogTables();
            
            $post_id = (int)$_POST['post_id'];
            $title = clean($_POST['post_title']);
            $content = $_POST['post_content'];
            $excerpt = clean($_POST['post_excerpt']);
            $status = clean($_POST['post_status']);
            $category_id = (int)$_POST['category_id'];
            $tags = clean($_POST['post_tags']);
            $meta_title = clean($_POST['meta_title']);
            $meta_description = clean($_POST['meta_description']);
            
            // Mevcut yazıyı al
            $stmt = $pdo->prepare("SELECT * FROM blog_posts WHERE id = ?");
            $stmt->execute([$post_id]);
            $existing_post = $stmt->fetch();
            
            if (!$existing_post) {
                throw new Exception('Blog yazısı bulunamadı.');
            }
            
            // Slug güncelle (başlık değiştiyse)
            $slug = $existing_post['slug'];
            if ($title !== $existing_post['title']) {
                $new_slug = createBlogSlug($title);
                // Aynı slug var mı kontrol et (kendi yazısı hariç)
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE slug = ? AND id != ?");
                $stmt->execute([$new_slug, $post_id]);
                if ($stmt->fetchColumn() > 0) {
                    $new_slug .= '-' . time();
                }
                $slug = $new_slug;
            }
            
            // Görsel yükleme
            $featured_image = $existing_post['featured_image'];
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
                // Eski görseli sil
                if ($featured_image && file_exists('../' . $featured_image)) {
                    unlink('../' . $featured_image);
                }
                $featured_image = uploadFile($_FILES['featured_image'], 'uploads/blog/');
            }
            
            // Published_at güncelle
            $published_at = $existing_post['published_at'];
            if ($status == 'published' && $existing_post['status'] != 'published') {
                $published_at = date('Y-m-d H:i:s');
            } elseif ($status != 'published') {
                $published_at = null;
            }
            
            // Blog yazısını güncelle
            $stmt = $pdo->prepare("
                UPDATE blog_posts SET 
                title = ?, slug = ?, content = ?, excerpt = ?, featured_image = ?, 
                status = ?, category_id = ?, tags = ?, meta_title = ?, meta_description = ?, 
                updated_at = CURRENT_TIMESTAMP, published_at = ?
                WHERE id = ?
            ");
            
            if ($stmt->execute([
                $title, $slug, $content, $excerpt, $featured_image, 
                $status, $category_id, $tags, $meta_title, $meta_description, 
                $published_at, $post_id
            ])) {
                $success_message = 'Blog yazısı başarıyla güncellendi!';
            } else {
                $error_message = 'Blog yazısı güncellenirken hata oluştu.';
            }
        } catch(Exception $e) {
            $error_message = 'Blog yazısı güncellenirken hata oluştu: ' . $e->getMessage();
        }
    }
    // Blog yazısı kaydetme
    elseif (isset($_POST['save_blog_post'])) {
        try {
            createBlogTables(); // Tabloları kontrol et
            
            $title = clean($_POST['post_title']);
            $content = $_POST['post_content']; // HTML içerik olabilir, clean kullanmayız
            $excerpt = clean($_POST['post_excerpt']);
            $status = clean($_POST['post_status']);
            $category_id = (int)$_POST['category_id'];
            $tags = clean($_POST['post_tags']);
            $meta_title = clean($_POST['meta_title']);
            $meta_description = clean($_POST['meta_description']);
            
            // Slug oluştur
            $slug = createBlogSlug($title);
            
            // Aynı slug var mı kontrol et
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM blog_posts WHERE slug = ?");
            $stmt->execute([$slug]);
            if ($stmt->fetchColumn() > 0) {
                $slug .= '-' . time();
            }
            
            // Görsel yükleme
            $featured_image = '';
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] == 0) {
                $featured_image = uploadFile($_FILES['featured_image'], 'uploads/blog/');
            }
            
            // Blog yazısını kaydet
            $stmt = $pdo->prepare("
                INSERT INTO blog_posts (title, slug, content, excerpt, featured_image, status, category_id, tags, meta_title, meta_description, author_id, published_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $published_at = ($status == 'published') ? date('Y-m-d H:i:s') : null;
            
            if ($stmt->execute([
                $title, $slug, $content, $excerpt, $featured_image, 
                $status, $category_id, $tags, $meta_title, $meta_description, 
                $_SESSION['admin_id'], $published_at
            ])) {
                $success_message = 'Blog yazısı başarıyla kaydedildi!';
            } else {
                $error_message = 'Blog yazısı kaydedilirken hata oluştu.';
            }
        } catch(PDOException $e) {
            $error_message = 'Blog yazısı kaydedilirken hata oluştu: ' . $e->getMessage();
        }
    }
    elseif (isset($_POST['update_blog_settings'])) {
        try {
            // Blog Ayarları
            $blog_enabled = isset($_POST['blog_enabled']) ? '1' : '0';
            setSetting('blog_enabled', $blog_enabled);
            if (isset($_POST['blog_posts_per_page'])) {
                setSetting('blog_posts_per_page', clean($_POST['blog_posts_per_page']));
            }
            if (isset($_POST['blog_page_title'])) {
                setSetting('blog_page_title', clean($_POST['blog_page_title']));
            }
            if (isset($_POST['blog_description'])) {
                setSetting('blog_description', clean($_POST['blog_description']));
            }
            
            // Sayfa Oluşturucu
            $page_builder_enabled = isset($_POST['page_builder_enabled']) ? '1' : '0';
            setSetting('page_builder_enabled', $page_builder_enabled);
            if (isset($_POST['default_editor'])) {
                setSetting('default_editor', clean($_POST['default_editor']));
            }
            
            // İçerik Ayarları
            $comments_enabled = isset($_POST['comments_enabled']) ? '1' : '0';
            setSetting('comments_enabled', $comments_enabled);
            $auto_save_enabled = isset($_POST['auto_save_enabled']) ? '1' : '0';
            setSetting('auto_save_enabled', $auto_save_enabled);
            
            $success_message = 'Blog ve içerik yönetimi ayarları başarıyla güncellendi.';
        } catch(PDOException $e) {
            $error_message = 'Blog ayarları güncellenirken hata oluştu: ' . $e->getMessage();
        }
    }
    // Statik sayfa oluşturma
    elseif (isset($_POST['create_static_page'])) {
        try {
            $page_title = clean($_POST['page_title']);
            $page_slug = clean($_POST['page_slug']);
            $page_status = clean($_POST['page_status']);
            
            if (!$page_title || !$page_slug) {
                throw new Exception('Sayfa başlığı ve URL gerekli!');
            }
            
            // Dosya adını güvenli hale getir
            $filename = preg_replace('/[^a-z0-9\-_]/', '', strtolower($page_slug)) . '.php';
            $filepath = '../' . $filename;
            
            // Dosya zaten var mı kontrol et
            if (file_exists($filepath)) {
                throw new Exception('Bu isimde bir sayfa zaten mevcut!');
            }
            
            // Sayfa içeriği oluştur
            $page_content = '<?php
/**
 * ' . $page_title . '
 * Otomatik oluşturulan sayfa
 */

require_once __DIR__ . \'/includes/functions.php\';

$page_title = \'' . addslashes($page_title) . '\';
$meta_description = \'' . addslashes($page_title) . ' sayfası\';

include \'includes/header.php\';
?>

<main class="main">
    <!-- Hero Section -->
    <section class="hero-section bg-dark text-white py-5">
        <div class="container">
            <div class="row align-items-center min-vh-50">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title mb-4">' . htmlspecialchars($page_title) . '</h1>
                    <p class="hero-subtitle text-muted mb-4">
                        Bu sayfa otomatik olarak oluşturulmuştur. İçeriği admin panelden düzenleyebilirsiniz.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Content Section -->
    <section class="content-section py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="content-wrapper">
                        <h2 class="mb-4">Sayfa İçeriği</h2>
                        <p class="lead">
                            Bu bölümde sayfa içeriğinizi yazabilirsiniz. Bu dosya (<code>' . $filename . '</code>) 
                            admin panelden düzenlenebilir ve özelleştirilebilir.
                        </p>
                        
                        <div class="row mt-5">
                            <div class="col-md-6">
                                <div class="feature-box p-4 bg-light rounded">
                                    <h5>Özellik 1</h5>
                                    <p>Buraya özellik açıklaması yazabilirsiniz.</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="feature-box p-4 bg-light rounded">
                                    <h5>Özellik 2</h5>
                                    <p>Buraya özellik açıklaması yazabilirsiniz.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include \'includes/footer.php\'; ?>';

            // Dosyayı oluştur
            if (file_put_contents($filepath, $page_content) !== false) {
                // Veritabanına kaydet (opsiyonel)
                try {
                    $stmt = $pdo->prepare("INSERT INTO static_pages (title, slug, file_path, status) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$page_title, $page_slug, $filename, $page_status]);
                } catch(PDOException $e) {
                    // Tablo yoksa oluştur (SQLite uyumlu)
                    $pdo->exec("CREATE TABLE IF NOT EXISTS static_pages (
                        id INTEGER PRIMARY KEY AUTOINCREMENT,
                        title VARCHAR(255) NOT NULL,
                        slug VARCHAR(255) NOT NULL UNIQUE,
                        file_path VARCHAR(255) NOT NULL,
                        status TEXT CHECK(status IN ('draft', 'published')) DEFAULT 'draft',
                        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                    )");
                    
                    $stmt = $pdo->prepare("INSERT INTO static_pages (title, slug, file_path, status) VALUES (?, ?, ?, ?)");
                    $stmt->execute([$page_title, $page_slug, $filename, $page_status]);
                }
                
                $success_message = "Sayfa başarıyla oluşturuldu! <a href='../$filename' target='_blank'>Sayfayı görüntüle</a>";
            } else {
                throw new Exception('Sayfa dosyası oluşturulamadı!');
            }
            
        } catch(Exception $e) {
            $error_message = 'Sayfa oluşturulurken hata oluştu: ' . $e->getMessage();
        }
    }
}

$blog_settings = [
    'blog_enabled' => getSetting('blog_enabled', '1'),
    'blog_posts_per_page' => getSetting('blog_posts_per_page', '6'),
    'blog_page_title' => getSetting('blog_page_title', 'Blog'),
    'blog_description' => getSetting('blog_description', 'Teknoloji, yazılım geliştirme ve projelerim hakkında yazılar'),
    'page_builder_enabled' => getSetting('page_builder_enabled', '1'),
    'default_editor' => getSetting('default_editor', 'tinymce'),
    'comments_enabled' => getSetting('comments_enabled', '1'),
    'auto_save_enabled' => getSetting('auto_save_enabled', '1')
];

// Blog yazılarını al
try {
    createBlogTables();
    
    // Sayfalama
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $per_page = 10;
    $offset = ($page - 1) * $per_page;
    
    // Filtreleme
    $status_filter = isset($_GET['status']) ? clean($_GET['status']) : '';
    $category_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
    
    // WHERE koşulları
    $where_conditions = [];
    $params = [];
    
    if ($status_filter) {
        $where_conditions[] = "bp.status = ?";
        $params[] = $status_filter;
    }
    
    if ($category_filter) {
        $where_conditions[] = "bp.category_id = ?";
        $params[] = $category_filter;
    }
    
    $where_sql = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';
    
    // Blog yazılarını çek
    $sql = "SELECT bp.*, bc.name as category_name 
            FROM blog_posts bp 
            LEFT JOIN blog_categories bc ON bp.category_id = bc.id 
            $where_sql 
            ORDER BY bp.created_at DESC 
            LIMIT $per_page OFFSET $offset";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $blog_posts = $stmt->fetchAll();
    
    // Toplam yazı sayısı
    $count_sql = "SELECT COUNT(*) FROM blog_posts bp $where_sql";
    $count_stmt = $pdo->prepare($count_sql);
    $count_stmt->execute($params);
    $total_posts = $count_stmt->fetchColumn();
    $total_pages = ceil($total_posts / $per_page);
    
} catch(PDOException $e) {
    $blog_posts = [];
    $total_posts = 0;
    $total_pages = 1;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">Blog & İçerik Yönetimi</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <?php if(getSetting('blog_enabled', '1') == '1'): ?>
            <a href="../blog.php" target="_blank" class="btn btn-outline-light btn-sm me-2">
                <i class="fas fa-external-link-alt me-1"></i>Blog Sayfasını Görüntüle
            </a>
            <?php endif; ?>
            <button type="button" class="btn btn-gradient btn-sm" onclick="exportContent()">
                <i class="fas fa-download me-1"></i>İçerik Dışa Aktar
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
    
    <!-- Blog Yazıları Yönetimi -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-light">
                            <i class="fas fa-list me-2"></i>Blog Yazıları 
                            <span class="badge bg-primary ms-2"><?php echo $total_posts; ?></span>
                        </h5>
                        <div>
                            <button type="button" class="btn btn-gradient btn-sm" onclick="showBlogEditor()">
                                <i class="fas fa-plus me-2"></i>Yeni Yazı
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Filtreler -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <select class="form-control" onchange="filterPosts()" id="statusFilter">
                                <option value="">Tüm Durumlar</option>
                                <option value="published" <?php echo $status_filter == 'published' ? 'selected' : ''; ?>>Yayınlanan</option>
                                <option value="draft" <?php echo $status_filter == 'draft' ? 'selected' : ''; ?>>Taslak</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <select class="form-control" onchange="filterPosts()" id="categoryFilter">
                                <option value="">Tüm Kategoriler</option>
                                <?php
                                $categories = getBlogCategories();
                                foreach($categories as $category):
                                ?>
                                    <option value="<?php echo $category['id']; ?>" <?php echo $category_filter == $category['id'] ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($category['name']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-outline-light w-100" onclick="clearFilters()">
                                <i class="fas fa-filter me-2"></i>Filtreleri Temizle
                            </button>
                        </div>
                    </div>
                    
                    <!-- Toplu İşlemler -->
                    <?php if ($blog_posts): ?>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <button type="button" class="btn btn-danger btn-sm" onclick="deleteSelectedPosts()" disabled id="bulkDeleteBtn">
                                    <i class="fas fa-trash me-2"></i>Seçilenleri Sil
                                </button>
                                <span class="text-muted ms-2" id="selectedCount">0 yazı seçildi</span>
                            </div>
                        </div>
                        
                        <!-- Blog Yazıları Tablosu -->
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">
                                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                                        </th>
                                        <th width="40%">Başlık</th>
                                        <th width="15%">Kategori</th>
                                        <th width="10%">Durum</th>
                                        <th width="10%">Görüntüleme</th>
                                        <th width="10%">Tarih</th>
                                        <th width="10%">İşlemler</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($blog_posts as $post): ?>
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="selected_posts[]" value="<?php echo $post['id']; ?>" class="post-checkbox">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if ($post['featured_image']): ?>
                                                        <img src="../<?php echo $post['featured_image']; ?>" 
                                                             class="rounded me-3" style="width: 50px; height: 35px; object-fit: cover;">
                                                    <?php endif; ?>
                                                    <div>
                                                        <h6 class="mb-1 text-light"><?php echo htmlspecialchars($post['title']); ?></h6>
                                                        <small class="text-muted">
                                                            <a href="../blog.php?post=<?php echo $post['slug']; ?>" target="_blank" class="text-decoration-none">
                                                                <i class="fas fa-external-link-alt"></i> Görüntüle
                                                            </a>
                                                        </small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($post['category_name']): ?>
                                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($post['category_name']); ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($post['status'] == 'published'): ?>
                                                    <span class="badge bg-success">Yayında</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Taslak</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="text-muted"><?php echo $post['views']; ?></span>
                                            </td>
                                            <td>
                                                <small class="text-muted">
                                                    <?php echo date('d.m.Y', strtotime($post['created_at'])); ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <button type="button" class="btn btn-outline-primary" 
                                                            onclick="editPost(<?php echo htmlspecialchars(json_encode($post)); ?>)"
                                                            title="Düzenle">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-outline-danger" 
                                                            onclick="deletePost(<?php echo $post['id']; ?>, '<?php echo htmlspecialchars($post['title']); ?>')"
                                                            title="Sil">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Sayfalama -->
                        <?php if ($total_pages > 1): ?>
                            <nav aria-label="Blog pagination" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&category=<?php echo $category_filter; ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&category=<?php echo $category_filter; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&category=<?php echo $category_filter; ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-blog fa-3x text-muted mb-3"></i>
                            <h5 class="text-light">Henüz blog yazısı yok</h5>
                            <p class="text-muted">İlk blog yazınızı oluşturmak için yukarıdaki "Yeni Yazı" butonuna tıklayın.</p>
                            <button type="button" class="btn btn-gradient" onclick="showBlogEditor()">
                                <i class="fas fa-plus me-2"></i>İlk Yazımı Oluştur
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card-custom">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-light"><i class="fas fa-cog me-2"></i>Blog Sistemi Ayarları</h5>
                </div>
                <div class="card-body">
                    <form method="POST">
                        <!-- Blog Genel Ayarları -->
                        <div class="mb-4">
                            <h6 class="text-light mb-3">Genel Ayarlar</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="blog_enabled" id="blog_enabled" <?php echo $blog_settings['blog_enabled'] == '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label text-light" for="blog_enabled">
                                            <strong>Blog Sistemini Aktifleştir</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">Blog sayfasını sitede gösterir</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-light">Sayfa Başına Yazı Sayısı</label>
                                    <input type="number" class="form-control" name="blog_posts_per_page" value="<?php echo htmlspecialchars($blog_settings['blog_posts_per_page']); ?>" placeholder="6">
                                    <small class="text-muted">Blog sayfasında gösterilecek yazı sayısı</small>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-light">Blog Sayfası Başlığı</label>
                                    <input type="text" class="form-control" name="blog_page_title" value="<?php echo htmlspecialchars($blog_settings['blog_page_title']); ?>" placeholder="Blog">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-light">Blog Açıklaması</label>
                                    <input type="text" class="form-control" name="blog_description" value="<?php echo htmlspecialchars($blog_settings['blog_description']); ?>" placeholder="Blog açıklaması">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Sayfa Oluşturucu -->
                        <div class="mb-4">
                            <h6 class="text-light mb-3">Sayfa Oluşturucu</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="page_builder_enabled" id="page_builder_enabled" <?php echo $blog_settings['page_builder_enabled'] == '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label text-light" for="page_builder_enabled">
                                            <strong>Sayfa Oluşturucuyu Aktifleştir</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">Drag & Drop ile sayfa oluşturma</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label text-light">Varsayılan Editör</label>
                                    <select class="form-control" name="default_editor">
                                        <option value="tinymce" <?php echo $blog_settings['default_editor'] == 'tinymce' ? 'selected' : ''; ?>>TinyMCE (Zengin Editör)</option>
                                        <option value="markdown" <?php echo $blog_settings['default_editor'] == 'markdown' ? 'selected' : ''; ?>>Markdown</option>
                                        <option value="html" <?php echo $blog_settings['default_editor'] == 'html' ? 'selected' : ''; ?>>HTML Editör</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- İçerik Ayarları -->
                        <div class="mb-4">
                            <h6 class="text-light mb-3">İçerik Ayarları</h6>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="comments_enabled" id="comments_enabled" <?php echo $blog_settings['comments_enabled'] == '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label text-light" for="comments_enabled">
                                            <strong>Yorum Sistemini Aktifleştir</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">Blog yazılarında yorum yapabilme</small>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" name="auto_save_enabled" id="auto_save_enabled" <?php echo $blog_settings['auto_save_enabled'] == '1' ? 'checked' : ''; ?>>
                                        <label class="form-check-label text-light" for="auto_save_enabled">
                                            <strong>Otomatik Kayıt</strong>
                                        </label>
                                    </div>
                                    <small class="text-muted">İçeriği otomatik olarak kaydeder</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-end mt-4 pt-3 border-top border-secondary">
                            <button type="reset" class="btn btn-outline-light me-2">
                                <i class="fas fa-undo me-2"></i>Sıfırla
                            </button>
                            <button type="submit" name="update_blog_settings" class="btn btn-gradient">
                                <i class="fas fa-save me-2"></i>Blog Ayarlarını Kaydet
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card-custom">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-light"><i class="fas fa-bolt me-2"></i>Hızlı İşlemler</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-3">
                                                        <button type="button" class="btn btn-outline-gradient" onclick="showBlogEditor()">
                                    <i class="fas fa-plus me-2"></i>Yeni Blog Yazısı
                                </button>
                        
                        <button type="button" class="btn btn-outline-gradient" onclick="showPageBuilder()">
                            <i class="fas fa-file-alt me-2"></i>Yeni Sayfa Oluştur
                        </button>
                        
                        <button type="button" class="btn btn-outline-gradient" onclick="showCategoryManager()">
                            <i class="fas fa-tags me-2"></i>Kategori Yönetimi
                        </button>
                        
                        <div class="dropdown">
                            <button class="btn btn-outline-warning dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-download me-2"></i>İçerik Dışa Aktar
                            </button>
                            <ul class="dropdown-menu w-100">
                                <li><a class="dropdown-item" href="includes/export_content.php?format=json&type=all">JSON Format</a></li>
                                <li><a class="dropdown-item" href="includes/export_content.php?format=csv&type=all">CSV Format</a></li>
                                <li><a class="dropdown-item" href="includes/export_content.php?format=xml&type=all">XML Format</a></li>
                                <li><a class="dropdown-item" href="includes/export_content.php?format=sql&type=all">SQL Format</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-custom mt-4">
                <div class="card-header bg-transparent border-bottom border-secondary">
                    <h5 class="mb-0 text-light"><i class="fas fa-info-circle me-2"></i>Blog Sistemi Durumu</h5>
                </div>
                <div class="card-body">
                    <?php if($blog_settings['blog_enabled'] == '1'): ?>
                        <div class="alert alert-success border-0" style="background: rgba(40, 167, 69, 0.1);">
                            <h6 class="text-success"><i class="fas fa-check-circle me-2"></i>Blog Sistemi Aktif</h6>
                            <p class="mb-0 small text-light">Blog sayfası sitede görünür ve ziyaretçiler erişebilir.</p>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning border-0" style="background: rgba(255, 193, 7, 0.1);">
                            <h6 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Blog Sistemi Pasif</h6>
                            <p class="mb-0 small text-light">Blog sayfası sitede görünmüyor. Aktifleştirmek için yukarıdaki ayarı açın.</p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <h6 class="text-light">Sistem Özellikleri:</h6>
                        <ul class="mb-0 small text-light">
                            <li>✅ Blog sistemi</li>
                            <li>✅ Sayfa oluşturucu</li>
                            <li>✅ İçerik editörü</li>
                            <li>✅ Kategori yönetimi</li>
                            <li>✅ SEO optimizasyonu</li>
                            <li>✅ Yorum sistemi</li>
                            <li>✅ Otomatik kayıt</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Blog Editör Modal -->
<div class="modal fade" id="blogEditorModal" tabindex="-1" aria-labelledby="blogEditorModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-gradient" id="blogEditorModalLabel">
                    <i class="fas fa-plus me-2"></i>Yeni Blog Yazısı
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" enctype="multipart/form-data" id="blogForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-lg-8">
                            <!-- Başlık -->
                            <div class="mb-3">
                                <label class="form-label text-light">Yazı Başlığı <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="post_title" id="post_title" required placeholder="Blog yazısı başlığı...">
                            </div>
                            
                            <!-- İçerik -->
                            <div class="mb-3">
                                <label class="form-label text-light">İçerik <span class="text-danger">*</span></label>
                                <textarea class="form-control" name="post_content" id="post_content" rows="15" required placeholder="Blog yazısı içeriği..."></textarea>
                                <small class="text-muted">HTML etiketleri kullanabilirsiniz</small>
                            </div>
                            
                            <!-- Özet -->
                            <div class="mb-3">
                                <label class="form-label text-light">Yazı Özeti</label>
                                <textarea class="form-control" name="post_excerpt" id="post_excerpt" rows="3" placeholder="Kısa açıklama (isteğe bağlı)"></textarea>
                                <small class="text-muted">Boş bırakılırsa otomatik oluşturulur</small>
                            </div>
                        </div>
                        
                        <div class="col-lg-4">
                            <!-- Yayın Durumu -->
                            <div class="card-dark mb-3">
                                <div class="card-body">
                                    <h6 class="text-light mb-3">Yayın Durumu</h6>
                                    <select class="form-control" name="post_status" id="post_status">
                                        <option value="draft">Taslak</option>
                                        <option value="published">Yayınla</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Kategori -->
                            <div class="card-dark mb-3">
                                <div class="card-body">
                                    <h6 class="text-light mb-3">Kategori</h6>
                                    <select class="form-control" name="category_id" id="category_id">
                                        <?php
                                        $categories = getBlogCategories();
                                        foreach($categories as $category):
                                        ?>
                                            <option value="<?php echo $category['id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Öne Çıkan Görsel -->
                            <div class="card-dark mb-3">
                                <div class="card-body">
                                    <h6 class="text-light mb-3">Öne Çıkan Görsel</h6>
                                    <input type="file" class="form-control" name="featured_image" accept="image/*">
                                    <small class="text-muted">JPG, PNG dosyaları kabul edilir</small>
                                </div>
                            </div>
                            
                            <!-- Etiketler -->
                            <div class="card-dark mb-3">
                                <div class="card-body">
                                    <h6 class="text-light mb-3">Etiketler</h6>
                                    <input type="text" class="form-control" name="post_tags" id="post_tags" placeholder="etiket1, etiket2, etiket3">
                                    <small class="text-muted">Virgülle ayırarak yazın</small>
                                </div>
                            </div>
                            
                            <!-- SEO -->
                            <div class="card-dark">
                                <div class="card-body">
                                    <h6 class="text-light mb-3">SEO Ayarları</h6>
                                    <div class="mb-3">
                                        <label class="form-label text-light">Meta Başlığı</label>
                                        <input type="text" class="form-control" name="meta_title" id="meta_title" maxlength="60" placeholder="SEO başlığı">
                                        <small class="text-muted">Boş bırakılırsa yazı başlığı kullanılır</small>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-light">Meta Açıklaması</label>
                                        <textarea class="form-control" name="meta_description" id="meta_description" rows="3" maxlength="160" placeholder="SEO açıklaması"></textarea>
                                        <small class="text-muted">160 karakter sınırı</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-secondary">
                    <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>İptal
                    </button>
                    <button type="submit" name="save_blog_post" class="btn btn-gradient">
                        <i class="fas fa-save me-2"></i>Kaydet
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Kategori Yönetimi Modal -->
<div class="modal fade" id="categoryManagerModal" tabindex="-1" aria-labelledby="categoryManagerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-gradient" id="categoryManagerModalLabel">
                    <i class="fas fa-tags me-2"></i>Kategori Yönetimi
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-5">
                        <div class="card-dark">
                            <div class="card-body">
                                <h6 class="text-light mb-3">Yeni Kategori Ekle</h6>
                                <form id="categoryForm" onsubmit="event.preventDefault(); addCategory();">
                                    <div class="mb-3">
                                        <label class="form-label text-light">Kategori Adı</label>
                                        <input type="text" class="form-control" id="categoryName" required placeholder="Kategori adı...">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label text-light">Açıklama</label>
                                        <textarea class="form-control" id="categoryDescription" rows="3" placeholder="Kategori açıklaması..."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-gradient">
                                        <i class="fas fa-plus me-2"></i>Kategori Ekle
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="card-dark">
                            <div class="card-body">
                                <h6 class="text-light mb-3">Mevcut Kategoriler</h6>
                                <div id="categoryList" style="max-height: 400px; overflow-y: auto;">
                                    <!-- Kategoriler buraya yüklenecek -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Sayfa Oluşturucu Modal -->
<div class="modal fade" id="pageBuilderModal" tabindex="-1" aria-labelledby="pageBuilderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content bg-dark border-secondary">
            <div class="modal-header border-secondary">
                <h5 class="modal-title text-gradient" id="pageBuilderModalLabel">
                    <i class="fas fa-file-alt me-2"></i>Sayfa Oluşturucu
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-0" style="background: rgba(116, 185, 255, 0.1);">
                    <h6 class="text-info"><i class="fas fa-info-circle me-2"></i>Sayfa Oluşturucu</h6>
                    <p class="mb-0 small text-light">
                        Bu özellik yakında aktif olacak. Şu an için blog yazıları kullanarak sayfa benzeri içerikler oluşturabilirsiniz.
                    </p>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card-dark mb-3">
                            <div class="card-body">
                                <h6 class="text-light mb-3">Sayfa Şablonları</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <div class="template-card p-3 border border-secondary rounded text-center">
                                            <i class="fas fa-home fa-2x text-primary mb-2"></i>
                                            <h6 class="text-light">Ana Sayfa</h6>
                                            <small class="text-muted">Kurumsal ana sayfa şablonu</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="template-card p-3 border border-secondary rounded text-center">
                                            <i class="fas fa-user fa-2x text-success mb-2"></i>
                                            <h6 class="text-light">Hakkımda</h6>
                                            <small class="text-muted">Kişisel bilgi sayfası</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="template-card p-3 border border-secondary rounded text-center">
                                            <i class="fas fa-envelope fa-2x text-warning mb-2"></i>
                                            <h6 class="text-light">İletişim</h6>
                                            <small class="text-muted">İletişim formu sayfası</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <div class="template-card p-3 border border-secondary rounded text-center">
                                            <i class="fas fa-cogs fa-2x text-info mb-2"></i>
                                            <h6 class="text-light">Hizmetler</h6>
                                            <small class="text-muted">Hizmet listesi sayfası</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-dark">
                            <div class="card-body">
                                <h6 class="text-light mb-3">Sayfa Ayarları</h6>
                                <div class="mb-3">
                                    <label class="form-label text-light">Sayfa Başlığı</label>
                                    <input type="text" class="form-control" placeholder="Sayfa başlığı...">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-light">URL Slug</label>
                                    <input type="text" class="form-control" placeholder="sayfa-url">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label text-light">Durum</label>
                                    <select class="form-control">
                                        <option value="draft">Taslak</option>
                                        <option value="published">Yayınla</option>
                                    </select>
                                </div>
                                <button class="btn btn-gradient w-100" onclick="createStaticPage()">
                                    <i class="fas fa-save me-2"></i>Sayfa Oluştur
                                </button>
                                <small class="text-success d-block mt-2">Aktif - Hazır!</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-secondary">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Kapat
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function exportContent() {
    if (confirm('Tüm içeriği dışa aktarmak istediğinizden emin misiniz?')) {
        window.open('includes/export_content.php', '_blank');
    }
}

function previewChanges() {
    alert('Değişiklikler önizleme modunda açılacak...');
    window.open('../index.php?preview=1', '_blank');
}

function showBlogEditor() {
    const modal = new bootstrap.Modal(document.getElementById('blogEditorModal'));
    modal.show();
}

// Missing editPost function - this is why edit buttons don't work
function editPost(post) {
    // Set modal title
    document.getElementById('blogEditorModalLabel').innerHTML = '<i class="fas fa-edit me-2"></i>Blog Yazısını Düzenle';
    
    // Populate form fields with existing post data
    document.getElementById('post_title').value = post.title || '';
    document.getElementById('post_content').value = post.content || '';
    document.getElementById('post_excerpt').value = post.excerpt || '';
    document.getElementById('post_status').value = post.status || 'draft';
    document.getElementById('category_id').value = post.category_id || '';
    document.getElementById('post_tags').value = post.tags || '';
    document.getElementById('meta_title').value = post.meta_title || '';
    document.getElementById('meta_description').value = post.meta_description || '';
    
    // Add hidden field for post ID
    let hiddenInput = document.getElementById('edit_post_id');
    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'post_id';
        hiddenInput.id = 'edit_post_id';
        document.getElementById('blogForm').appendChild(hiddenInput);
    }
    hiddenInput.value = post.id;
    
    // Change form action to update
    const submitBtn = document.querySelector('button[name="save_blog_post"]');
    submitBtn.name = 'update_blog_post';
    submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Güncelle';
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('blogEditorModal'));
    modal.show();
}

// Missing deletePost function - this is why delete buttons don't work
function deletePost(postId, postTitle) {
    if (confirm(`"${postTitle}" adlı blog yazısını silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz!`)) {
        // Create a form to submit the delete request
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        // Add post ID
        const postIdInput = document.createElement('input');
        postIdInput.type = 'hidden';
        postIdInput.name = 'post_id';
        postIdInput.value = postId;
        form.appendChild(postIdInput);
        
        // Add delete action
        const deleteInput = document.createElement('input');
        deleteInput.type = 'hidden';
        deleteInput.name = 'delete_post';
        deleteInput.value = '1';
        form.appendChild(deleteInput);
        
        // Submit form
        document.body.appendChild(form);
        form.submit();
    }
}

// Reset form when creating new post
function showBlogEditor() {
    // Reset modal title
    document.getElementById('blogEditorModalLabel').innerHTML = '<i class="fas fa-plus me-2"></i>Yeni Blog Yazısı';
    
    // Reset form
    document.getElementById('blogForm').reset();
    
    // Remove edit post ID if exists
    const hiddenInput = document.getElementById('edit_post_id');
    if (hiddenInput) {
        hiddenInput.remove();
    }
    
    // Reset submit button
    const submitBtn = document.querySelector('button[name="update_blog_post"], button[name="save_blog_post"]');
    if (submitBtn) {
        submitBtn.name = 'save_blog_post';
        submitBtn.innerHTML = '<i class="fas fa-save me-2"></i>Kaydet';
    }
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('blogEditorModal'));
    modal.show();
}

// Form doğrulama
document.getElementById('blogForm').addEventListener('submit', function(e) {
    const title = document.getElementById('post_title').value.trim();
    const content = document.getElementById('post_content').value.trim();
    
    if (!title || !content) {
        e.preventDefault();
        alert('Lütfen başlık ve içerik alanlarını doldurun.');
        return false;
    }
    
    if (title.length < 5) {
        e.preventDefault();
        alert('Başlık en az 5 karakter olmalıdır.');
        return false;
    }
    
    if (content.length < 50) {
        e.preventDefault();
        alert('İçerik en az 50 karakter olmalıdır.');
        return false;
    }
});

// Karakter sayacı
document.getElementById('meta_title').addEventListener('input', function() {
    const remaining = 60 - this.value.length;
    const small = this.nextElementSibling;
    small.textContent = `${remaining} karakter kaldı`;
    small.className = remaining < 0 ? 'text-danger' : 'text-muted';
});

document.getElementById('meta_description').addEventListener('input', function() {
    const remaining = 160 - this.value.length;
    const small = this.nextElementSibling;
    small.textContent = `${remaining} karakter kaldı`;
    small.className = remaining < 0 ? 'text-danger' : 'text-muted';
});

// Başlık değiştiğinde meta başlığı güncelle
document.getElementById('post_title').addEventListener('input', function() {
    const metaTitle = document.getElementById('meta_title');
    if (!metaTitle.value) {
        metaTitle.value = this.value;
    }
});

// Blog filtreleme fonksiyonları
function filterPosts() {
    const statusFilter = document.getElementById('statusFilter').value;
    const categoryFilter = document.getElementById('categoryFilter').value;
    
    let url = window.location.pathname + '?';
    let params = [];
    
    if (statusFilter) {
        params.push('status=' + encodeURIComponent(statusFilter));
    }
    
    if (categoryFilter) {
        params.push('category=' + encodeURIComponent(categoryFilter));
    }
    
    if (params.length > 0) {
        url += params.join('&');
    }
    
    window.location.href = url;
}

function clearFilters() {
    window.location.href = window.location.pathname;
}

// Toplu seçim fonksiyonları
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.post-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

// Seçili yazı sayısını güncelle
function updateBulkActions() {
    const selectedCheckboxes = document.querySelectorAll('.post-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');
    
    if (selectedCheckboxes.length > 0) {
        bulkDeleteBtn.disabled = false;
        selectedCount.textContent = selectedCheckboxes.length + ' yazı seçildi';
    } else {
        bulkDeleteBtn.disabled = true;
        selectedCount.textContent = '0 yazı seçildi';
    }
}

// Checkbox değişikliklerini dinle
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.post-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActions);
    });
});

// Seçili yazıları toplu sil
function deleteSelectedPosts() {
    const selectedCheckboxes = document.querySelectorAll('.post-checkbox:checked');
    
    if (selectedCheckboxes.length === 0) {
        alert('Lütfen silinecek yazıları seçin.');
        return;
    }
    
    if (confirm(`${selectedCheckboxes.length} adet blog yazısını silmek istediğinizden emin misiniz?\n\nBu işlem geri alınamaz!`)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        // Seçili post ID'lerini ekle
        selectedCheckboxes.forEach(checkbox => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'selected_post_ids[]';
            input.value = checkbox.value;
            form.appendChild(input);
        });
        
        // Toplu silme action ekle
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'bulk_delete_posts';
        actionInput.value = '1';
        form.appendChild(actionInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Kategori yönetimi modal'ını göster
function showCategoryManager() {
    const modal = new bootstrap.Modal(document.getElementById('categoryManagerModal'));
    modal.show();
    loadCategories();
}

function showPageBuilder() {
    if (confirm('Yeni sayfa oluşturmak için blog yazı editörünü kullanacaksınız. Devam etmek istiyor musunuz?')) {
        window.open('blog.php?action=new&type=page', '_blank');
    }
}

// Sayfa oluşturucu modal'ını göster
function showPageBuilder() {
    const modal = new bootstrap.Modal(document.getElementById('pageBuilderModal'));
    modal.show();
}

// Statik sayfa oluştur fonksiyonu
function createStaticPage() {
    const title = document.querySelector('#pageBuilderModal input[placeholder="Sayfa başlığı..."]').value.trim();
    const slug = document.querySelector('#pageBuilderModal input[placeholder="sayfa-url"]').value.trim();
    const status = document.querySelector('#pageBuilderModal select').value;
    
    if (!title) {
        alert('Lütfen sayfa başlığı girin!');
        return;
    }
    
    // Auto-generate slug if empty
    let finalSlug = slug;
    if (!finalSlug) {
        finalSlug = title.toLowerCase()
            .replace(/[^a-z0-9\s]/gi, '')
            .replace(/\s+/g, '-')
            .replace(/-+/g, '-')
            .replace(/^-|-$/g, '');
    }
    
    if (confirm(`"${title}" adlı sayfa oluşturulsun mu?\nURL: ${finalSlug}.php`)) {
        // Form oluştur ve gönder
        const form = document.createElement('form');
        form.method = 'POST';
        form.style.display = 'none';
        
        // Veriler
        const titleInput = document.createElement('input');
        titleInput.type = 'hidden';
        titleInput.name = 'page_title';
        titleInput.value = title;
        form.appendChild(titleInput);
        
        const slugInput = document.createElement('input');
        slugInput.type = 'hidden';
        slugInput.name = 'page_slug';
        slugInput.value = finalSlug;
        form.appendChild(slugInput);
        
        const statusInput = document.createElement('input');
        statusInput.type = 'hidden';
        statusInput.name = 'page_status';
        statusInput.value = status;
        form.appendChild(statusInput);
        
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'create_static_page';
        actionInput.value = '1';
        form.appendChild(actionInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

// Kategorileri yükle
function loadCategories() {
    // Bu fonksiyon AJAX ile kategorileri yükleyecek
    fetch('includes/get_categories.php')
        .then(response => response.json())
        .then(data => {
            const categoryList = document.getElementById('categoryList');
            categoryList.innerHTML = '';
            
            if (data.success && data.categories.length > 0) {
                data.categories.forEach(category => {
                    const categoryItem = `
                        <div class="category-item d-flex justify-content-between align-items-center p-3 mb-2 bg-dark rounded">
                            <div>
                                <h6 class="mb-1 text-light">${category.name}</h6>
                                <small class="text-muted">${category.description || 'Açıklama yok'}</small>
                            </div>
                            <div>
                                <button class="btn btn-outline-primary btn-sm me-2" onclick="editCategory(${category.id}, '${category.name}', '${category.description || ''}')">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger btn-sm" onclick="deleteCategory(${category.id}, '${category.name}')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                    categoryList.innerHTML += categoryItem;
                });
            } else {
                categoryList.innerHTML = '<p class="text-muted text-center">Henüz kategori eklenmemiş.</p>';
            }
        })
        .catch(error => {
            console.error('Kategoriler yüklenirken hata:', error);
            document.getElementById('categoryList').innerHTML = '<p class="text-danger text-center">Kategoriler yüklenirken hata oluştu.</p>';
        });
}

// Yeni kategori ekle
function addCategory() {
    const name = document.getElementById('categoryName').value.trim();
    const description = document.getElementById('categoryDescription').value.trim();
    
    if (!name) {
        alert('Kategori adı gerekli!');
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'add_category');
    formData.append('name', name);
    formData.append('description', description);
    
    fetch('includes/manage_categories.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Kategori başarıyla eklendi!');
            document.getElementById('categoryForm').reset();
            loadCategories();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Kategori eklenirken hata:', error);
        alert('Kategori eklenirken hata oluştu.');
    });
}

// Kategori düzenle
function editCategory(id, name, description) {
    const newName = prompt('Kategori adı:', name);
    if (newName === null) return;
    
    const newDescription = prompt('Kategori açıklaması:', description);
    if (newDescription === null) return;
    
    const formData = new FormData();
    formData.append('action', 'edit_category');
    formData.append('id', id);
    formData.append('name', newName);
    formData.append('description', newDescription);
    
    fetch('includes/manage_categories.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Kategori başarıyla güncellendi!');
            loadCategories();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Kategori güncellenirken hata:', error);
        alert('Kategori güncellenirken hata oluştu.');
    });
}

// Kategori sil
function deleteCategory(id, name) {
    if (!confirm(`"${name}" kategorisini silmek istediğinizden emin misiniz?`)) {
        return;
    }
    
    const formData = new FormData();
    formData.append('action', 'delete_category');
    formData.append('id', id);
    
    fetch('includes/manage_categories.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Kategori başarıyla silindi!');
            loadCategories();
        } else {
            alert('Hata: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Kategori silinirken hata:', error);
        alert('Kategori silinirken hata oluştu.');
    });
}
</script>

<style>
.card-custom {
    background-color: var(--dark-card);
    border: 1px solid var(--dark-border);
    border-radius: 12px;
    box-shadow: var(--shadow);
}

.card-header {
    padding: 20px;
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

.dropdown-menu {
    background-color: var(--dark-card);
    border: 1px solid var(--dark-border);
}

.dropdown-item {
    color: var(--text-light);
}

.dropdown-item:hover {
    background-color: var(--dark-bg);
    color: var(--primary-color);
}
</style>

<?php include 'includes/footer.php'; ?>