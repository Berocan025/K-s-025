<?php
/**
 * Blog Page - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once 'includes/functions.php';
checkMaintenanceMode();
trackVisitor(); // Ziyaretçi sayacı

// Blog sistemi kontrol et
if (getSetting('blog_enabled', '1') != '1') {
    header('Location: index.php');
    exit;
}

$page_title = getSetting('blog_page_title', 'Blog');
$posts_per_page = (int)getSetting('blog_posts_per_page', '6');

// Sayfalama
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * $posts_per_page;

// Kategori filtresi
$category = isset($_GET['category']) ? clean($_GET['category']) : null;

// Tek yazı görüntüleme
$single_post = null;
if (isset($_GET['post'])) {
    $single_post = getBlogPost(clean($_GET['post']));
    if (!$single_post) {
        header('HTTP/1.0 404 Not Found');
        $page_title = '404 - Yazı Bulunamadı';
    } else {
        $page_title = $single_post['title'];
    }
}

// Blog yazılarını al
if (!$single_post) {
    $blog_posts = getBlogPosts($posts_per_page, $category);
    $categories = getBlogCategories();
    
    // Toplam yazı sayısını al (sayfalama için)
    global $pdo;
    try {
        createBlogTables();
        $count_sql = "SELECT COUNT(*) FROM blog_posts bp";
        $count_params = [];
        
        if ($category) {
            $count_sql .= " LEFT JOIN blog_categories bc ON bp.category_id = bc.id WHERE bp.status = 'published' AND bc.slug = ?";
            $count_params = [$category];
        } else {
            $count_sql .= " WHERE bp.status = 'published'";
        }
        
        $stmt = $pdo->prepare($count_sql);
        $stmt->execute($count_params);
        $total_posts = $stmt->fetchColumn();
        $total_pages = ceil($total_posts / $posts_per_page);
    } catch(PDOException $e) {
        $total_posts = 0;
        $total_pages = 1;
    }
}
?>

<?php include 'includes/header.php'; ?>

<?php if ($single_post): ?>
    <!-- Tek Blog Yazısı -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <article class="blog-post animate-on-scroll">
                        <!-- Blog Post Header -->
                        <div class="blog-post-header mb-4">
                            <?php if ($single_post['category_name']): ?>
                                <div class="blog-category mb-3">
                                    <a href="blog.php?category=<?php echo $single_post['category_slug']; ?>" class="badge badge-gradient">
                                        <?php echo htmlspecialchars($single_post['category_name']); ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            
                            <h1 class="blog-post-title"><?php echo htmlspecialchars($single_post['title']); ?></h1>
                            
                            <div class="blog-post-meta">
                                <span class="me-3">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?php echo formatDate($single_post['created_at']); ?>
                                </span>
                                <span class="me-3">
                                    <i class="fas fa-eye me-1"></i>
                                    <?php echo $single_post['views']; ?> görüntüleme
                                </span>
                                <?php if ($single_post['tags']): ?>
                                    <span>
                                        <i class="fas fa-tags me-1"></i>
                                        <?php 
                                        $tags = explode(',', $single_post['tags']);
                                        foreach($tags as $tag): 
                                        ?>
                                            <span class="badge badge-tag me-1"><?php echo trim(htmlspecialchars($tag)); ?></span>
                                        <?php endforeach; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Featured Image -->
                        <?php if ($single_post['featured_image']): ?>
                            <div class="blog-featured-image mb-4">
                                <img src="<?php echo $single_post['featured_image']; ?>" alt="<?php echo htmlspecialchars($single_post['title']); ?>" class="img-fluid rounded">
                            </div>
                        <?php endif; ?>
                        
                        <!-- Blog Content -->
                        <div class="blog-content">
                            <?php echo $single_post['content']; ?>
                        </div>
                        
                        <!-- Post Navigation -->
                        <div class="blog-navigation mt-5 pt-4 border-top border-secondary">
                            <div class="row">
                                <div class="col-md-6">
                                    <a href="blog.php" class="btn btn-outline-gradient">
                                        <i class="fas fa-arrow-left me-2"></i>Tüm Yazılar
                                    </a>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <?php if ($single_post['category_name']): ?>
                                        <a href="blog.php?category=<?php echo $single_post['category_slug']; ?>" class="btn btn-outline-light">
                                            <?php echo htmlspecialchars($single_post['category_name']); ?> Yazıları
                                            <i class="fas fa-arrow-right ms-2"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </div>
        </div>
    </section>

<?php else: ?>
    <!-- Blog Listesi -->
    <section class="py-5">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h1 class="display-4 mb-4">
                        <span class="text-gradient"><?php echo getSetting('blog_page_title', 'Blog'); ?></span>
                    </h1>
                    <p class="lead text-muted">
                        <?php echo getSetting('blog_description', 'Teknoloji, yazılım geliştirme ve projelerim hakkında yazılar'); ?>
                    </p>
                </div>
            </div>
            
            <!-- Kategoriler -->
            <?php if ($categories): ?>
                <div class="row mb-5">
                    <div class="col-12 text-center">
                        <div class="blog-categories">
                            <a href="blog.php" class="badge badge-category me-2 mb-2 <?php echo !$category ? 'active' : ''; ?>">
                                Tümü
                            </a>
                            <?php foreach($categories as $cat): ?>
                                <a href="blog.php?category=<?php echo $cat['slug']; ?>" 
                                   class="badge badge-category me-2 mb-2 <?php echo $category == $cat['slug'] ? 'active' : ''; ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Blog Posts -->
            <?php if ($blog_posts): ?>
                <div class="row">
                    <?php foreach($blog_posts as $post): ?>
                        <div class="col-lg-6 mb-4">
                            <article class="blog-card animate-on-scroll">
                                <?php if ($post['featured_image']): ?>
                                    <div class="blog-card-image">
                                        <a href="blog.php?post=<?php echo $post['slug']; ?>">
                                            <img src="<?php echo $post['featured_image']; ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" class="img-fluid">
                                        </a>
                                        <?php if ($post['category_name']): ?>
                                            <div class="blog-card-category">
                                                <a href="blog.php?category=<?php echo $post['category_slug']; ?>" class="badge badge-gradient">
                                                    <?php echo htmlspecialchars($post['category_name']); ?>
                                                </a>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="blog-card-content">
                                    <div class="blog-card-meta mb-2">
                                        <span class="me-3">
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo formatDate($post['created_at']); ?>
                                        </span>
                                        <span>
                                            <i class="fas fa-eye me-1"></i>
                                            <?php echo $post['views']; ?>
                                        </span>
                                    </div>
                                    
                                    <h3 class="blog-card-title">
                                        <a href="blog.php?post=<?php echo $post['slug']; ?>">
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </a>
                                    </h3>
                                    
                                    <p class="blog-card-excerpt">
                                        <?php echo $post['excerpt'] ? htmlspecialchars($post['excerpt']) : truncateText(strip_tags($post['content']), 120); ?>
                                    </p>
                                    
                                    <a href="blog.php?post=<?php echo $post['slug']; ?>" class="btn btn-outline-gradient btn-sm">
                                        Devamını Oku <i class="fas fa-arrow-right ms-1"></i>
                                    </a>
                                </div>
                            </article>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Sayfalama -->
                <?php if ($total_pages > 1): ?>
                    <div class="row mt-5">
                        <div class="col-12">
                            <nav aria-label="Blog pagination">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="blog.php?page=<?php echo $page - 1; ?><?php echo $category ? '&category=' . $category : ''; ?>">
                                                <i class="fas fa-chevron-left"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="blog.php?page=<?php echo $i; ?><?php echo $category ? '&category=' . $category : ''; ?>">
                                                <?php echo $i; ?>
                                            </a>
                                        </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $total_pages): ?>
                                        <li class="page-item">
                                            <a class="page-link" href="blog.php?page=<?php echo $page + 1; ?><?php echo $category ? '&category=' . $category : ''; ?>">
                                                <i class="fas fa-chevron-right"></i>
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                        </div>
                    </div>
                <?php endif; ?>
                
            <?php else: ?>
                <!-- Yazı Yok -->
                <div class="row">
                    <div class="col-lg-6 mx-auto text-center">
                        <div class="empty-state animate-on-scroll">
                            <i class="fas fa-blog fa-3x text-muted mb-3"></i>
                            <h3 class="text-light">Henüz blog yazısı yok</h3>
                            <p class="text-muted">
                                <?php echo $category ? 'Bu kategoride henüz yazı bulunmuyor.' : 'Henüz hiç blog yazısı eklenmemiş.'; ?>
                            </p>
                            <?php if ($category): ?>
                                <a href="blog.php" class="btn btn-outline-gradient">
                                    Tüm Yazıları Görüntüle
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<style>
/* Blog Styles */
.blog-card {
    background: var(--dark-card);
    border-radius: 15px;
    overflow: hidden;
    transition: all 0.3s ease;
    border: 1px solid var(--dark-border);
    height: 100%;
    display: flex;
    flex-direction: column;
}

.blog-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(108, 92, 231, 0.2);
    border-color: var(--primary-color);
}

.blog-card-image {
    position: relative;
    height: 250px;
    overflow: hidden;
}

.blog-card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.blog-card:hover .blog-card-image img {
    transform: scale(1.05);
}

.blog-card-category {
    position: absolute;
    top: 15px;
    left: 15px;
}

.blog-card-content {
    padding: 25px;
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.blog-card-meta {
    color: var(--text-muted);
    font-size: 0.9rem;
}

.blog-card-title {
    margin-bottom: 15px;
    font-size: 1.25rem;
    line-height: 1.4;
}

.blog-card-title a {
    color: var(--text-light);
    text-decoration: none;
    transition: color 0.3s ease;
}

.blog-card-title a:hover {
    color: var(--primary-color);
}

.blog-card-excerpt {
    color: var(--text-muted);
    margin-bottom: 20px;
    line-height: 1.6;
    flex-grow: 1;
}

.badge-gradient {
    background: var(--gradient);
    color: white;
    padding: 5px 12px;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 500;
    text-decoration: none;
}

.badge-category {
    background: rgba(108, 92, 231, 0.1);
    color: var(--text-light);
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid transparent;
}

.badge-category:hover,
.badge-category.active {
    background: var(--gradient);
    color: white;
    border-color: var(--primary-color);
}

.badge-tag {
    background: rgba(116, 185, 255, 0.1);
    color: #74b9ff;
    padding: 3px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
}

/* Single Post Styles */
.blog-post-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-light);
    margin-bottom: 20px;
    line-height: 1.2;
}

.blog-post-meta {
    color: var(--text-muted);
    font-size: 0.95rem;
    margin-bottom: 30px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--dark-border);
}

.blog-featured-image {
    border-radius: 15px;
    overflow: hidden;
}

.blog-featured-image img {
    width: 100%;
    height: auto;
    max-width: 100%;
    display: block;
}

.blog-content {
    color: var(--text-light);
    line-height: 1.8;
    font-size: 1.1rem;
    word-wrap: break-word;
    overflow-wrap: break-word;
}

.blog-content h1,
.blog-content h2,
.blog-content h3,
.blog-content h4,
.blog-content h5,
.blog-content h6 {
    color: var(--text-light);
    margin: 30px 0 20px 0;
    word-wrap: break-word;
}

.blog-content h2 {
    padding-bottom: 10px;
    border-bottom: 2px solid var(--primary-color);
}

.blog-content img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 20px 0;
    display: block;
}

.blog-content blockquote {
    background: rgba(108, 92, 231, 0.1);
    border-left: 4px solid var(--primary-color);
    padding: 20px;
    margin: 30px 0;
    border-radius: 0 10px 10px 0;
    font-style: italic;
}

.blog-content code {
    background: var(--dark-bg);
    color: #f8f8f2;
    padding: 2px 6px;
    border-radius: 4px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    word-break: break-all;
}

.blog-content pre {
    background: var(--dark-bg);
    color: #f8f8f2;
    padding: 20px;
    border-radius: 10px;
    overflow-x: auto;
    margin: 20px 0;
    white-space: pre-wrap;
    word-wrap: break-word;
}

.blog-content pre code {
    background: none;
    padding: 0;
}

/* Pagination */
.pagination .page-link {
    background: var(--dark-card);
    border: 1px solid var(--dark-border);
    color: var(--text-light);
    padding: 10px 16px;
    margin: 0 3px;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.pagination .page-link:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

.pagination .page-item.active .page-link {
    background: var(--gradient);
    border-color: var(--primary-color);
    color: white;
}

/* Mobile Responsive Styles */
@media (max-width: 768px) {
    .blog-post-title {
        font-size: 1.8rem;
        line-height: 1.3;
        margin-bottom: 15px;
    }
    
    .blog-content {
        font-size: 1rem;
        line-height: 1.7;
    }
    
    .blog-content h1 {
        font-size: 1.6rem;
    }
    
    .blog-content h2 {
        font-size: 1.4rem;
    }
    
    .blog-content h3 {
        font-size: 1.2rem;
    }
    
    .blog-card-content {
        padding: 20px;
    }
    
    .blog-card-image {
        height: 200px;
    }
    
    .blog-card-title {
        font-size: 1.1rem;
        margin-bottom: 12px;
    }
    
    .blog-post-meta {
        font-size: 0.85rem;
        margin-bottom: 20px;
        padding-bottom: 15px;
    }
    
    .blog-featured-image {
        margin-bottom: 15px;
    }
    
    .blog-content blockquote {
        padding: 15px;
        margin: 20px 0;
    }
    
    .blog-content pre {
        padding: 15px;
        font-size: 0.85rem;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .badge-category {
        padding: 6px 12px;
        font-size: 0.8rem;
        margin: 2px;
        display: inline-block;
    }
    
    .blog-categories {
        text-align: center;
        margin-bottom: 20px;
    }
    
    .pagination .page-link {
        padding: 8px 12px;
        margin: 0 2px;
        font-size: 0.9rem;
    }
    
    .pagination {
        justify-content: center;
        flex-wrap: wrap;
    }
    
    .pagination .page-item {
        margin: 2px;
    }
}

@media (max-width: 576px) {
    .blog-post-title {
        font-size: 1.5rem;
        line-height: 1.4;
    }
    
    .blog-content {
        font-size: 0.95rem;
        line-height: 1.6;
    }
    
    .blog-card-content {
        padding: 15px;
    }
    
    .blog-card-image {
        height: 180px;
    }
    
    .blog-card-title {
        font-size: 1rem;
    }
    
    .blog-card-meta {
        font-size: 0.8rem;
    }
    
    .blog-content h1 {
        font-size: 1.4rem;
    }
    
    .blog-content h2 {
        font-size: 1.2rem;
    }
    
    .blog-content h3 {
        font-size: 1.1rem;
    }
    
    .blog-content img {
        margin: 15px 0;
    }
    
    .blog-content blockquote {
        padding: 12px;
        margin: 15px 0;
        font-size: 0.9rem;
    }
    
    .blog-content pre {
        padding: 12px;
        font-size: 0.8rem;
        margin: 15px 0;
    }
    
    .badge-category {
        padding: 4px 8px;
        font-size: 0.75rem;
    }
    
    .pagination .page-link {
        padding: 6px 10px;
        font-size: 0.8rem;
    }
    
    .display-4 {
        font-size: 2rem;
    }
    
    .lead {
        font-size: 1rem;
    }
}

/* Extra Small Screens */
@media (max-width: 480px) {
    .container {
        padding-left: 10px;
        padding-right: 10px;
    }
    
    .blog-post-title {
        font-size: 1.3rem;
        word-break: break-word;
    }
    
    .blog-content {
        font-size: 0.9rem;
    }
    
    .blog-card-image {
        height: 160px;
    }
    
    .blog-card-content {
        padding: 12px;
    }
    
    .display-4 {
        font-size: 1.8rem;
    }
    
    .blog-categories .badge-category {
        display: block;
        width: 100%;
        margin: 5px 0;
        text-align: center;
    }
}
</style>

<?php include 'includes/footer.php'; ?>