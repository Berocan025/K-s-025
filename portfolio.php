<?php
/**
 * Portfolio Page - BERAT K - R10 Portfolio
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once 'includes/functions.php';
checkMaintenanceMode();
trackVisitor(); // Ziyaretçi sayacı
$page_title = 'Platformlarım';

$filter = isset($_GET['filter']) ? clean($_GET['filter']) : 'all';
$search = isset($_GET['search']) ? clean($_GET['search']) : '';

$sql = "SELECT * FROM projects WHERE status = 1";
$params = [];

if ($filter !== 'all' && !empty($filter)) {
    $sql .= " AND category = ?";
    $params[] = $filter;
}

if (!empty($search)) {
    $sql .= " AND (title LIKE ? OR description LIKE ? OR technologies LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $params[] = $search_term;
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $projects = $stmt->fetchAll();
} catch(PDOException $e) {
    $projects = [];
}

$categories = [];
try {
    $stmt = $pdo->query("SELECT DISTINCT category FROM projects WHERE status = 1 AND category IS NOT NULL ORDER BY category");
    $categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch(PDOException $e) {
    $categories = [];
}
?>

<?php include 'includes/header.php'; ?>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center mb-5">
                <h1 class="display-4 mb-4">
                    <span class="text-gradient">Platformlarım</span>
                </h1>
                <p class="lead text-muted">
                    <?php echo getContentWithVariables('portfolio_intro', 'BERAT K - R10 olarak yönettiğim kumar platformları ve başarılı projeler. Her platform, güvenlik ve kullanıcı deneyiminin mükemmel birleşimi.'); ?>
                </p>
            </div>
        </div>
        
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto">
                <div class="portfolio-filters d-flex flex-wrap justify-content-center mb-4">
                    <button class="portfolio-filter btn <?php echo $filter === 'all' ? 'btn-gradient' : 'btn-outline-gradient'; ?> me-2 mb-2" 
                            data-filter="all" onclick="filterProjects('all')">
                        <?php echo getContent('portfolio_filter_all', 'Tümü'); ?>
                    </button>
                    <?php foreach ($categories as $category): ?>
                        <button class="portfolio-filter btn <?php echo $filter === $category ? 'btn-gradient' : 'btn-outline-gradient'; ?> me-2 mb-2" 
                                data-filter="<?php echo htmlspecialchars($category); ?>" 
                                onclick="filterProjects('<?php echo htmlspecialchars($category); ?>')">
                            <?php echo htmlspecialchars($category); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
                
                <div class="search-container">
                    <form method="GET" class="d-flex">
                        <input type="hidden" name="filter" value="<?php echo htmlspecialchars($filter); ?>">
                        <input type="text" name="search" class="form-control me-2" 
                               placeholder="<?php echo getContent('portfolio_search_placeholder', 'Platformlar içinde ara...'); ?>" 
                               value="<?php echo htmlspecialchars($search); ?>">
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        
        <?php if (!empty($projects)): ?>
            <div class="row" id="portfolioGrid">
                <?php foreach ($projects as $project): ?>
                    <div class="col-lg-4 col-md-6 mb-4 portfolio-item animate-on-scroll <?php echo htmlspecialchars($project['category']); ?>">
                        <div class="project-card">
                            <div class="project-image" style="background-image: url('<?php echo $project['image'] ? htmlspecialchars($project['image']) : 'assets/img/project-placeholder.jpg'; ?>');">
                                <div class="project-overlay">
                                    <div class="project-links">
                                        <?php if($project['demo_url']): ?>
                                            <a href="<?php echo htmlspecialchars($project['demo_url']); ?>" target="_blank" title="Demo İzle">
                                                <i class="fas fa-external-link-alt"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if($project['github_url']): ?>
                                            <a href="<?php echo htmlspecialchars($project['github_url']); ?>" target="_blank" title="GitHub">
                                                <i class="fab fa-github"></i>
                                            </a>
                                        <?php endif; ?>
                                        <a href="#" onclick="showProjectDetails(<?php echo $project['id']; ?>)" title="Detaylar">
                                            <i class="fas fa-info-circle"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="p-4">
                                <h5 class="project-title"><?php echo htmlspecialchars($project['title']); ?></h5>
                                <p class="text-muted mb-3"><?php echo truncateText($project['description']); ?></p>
                                
                                <?php if($project['technologies']): ?>
                                    <div class="project-technologies mb-3">
                                        <?php 
                                        $technologies = explode(',', $project['technologies']);
                                        foreach($technologies as $tech): 
                                            $tech = trim($tech);
                                            if(!empty($tech)):
                                        ?>
                                            <span class="badge bg-secondary me-1 mb-1"><?php echo htmlspecialchars($tech); ?></span>
                                        <?php 
                                            endif;
                                        endforeach; 
                                        ?>
                                    </div>
                                <?php endif; ?>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="badge bg-gradient"><?php echo htmlspecialchars($project['category']); ?></span>
                                    <small class="text-muted"><?php echo formatDate($project['created_at']); ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12 text-center py-5">
                    <div class="empty-state">
                        <i class="fas fa-folder-open fa-5x text-muted mb-4"></i>
                        <h3 class="text-muted mb-3">
                            <?php if(!empty($search) || $filter !== 'all'): ?>
                                Arama kriterlerinize uygun proje bulunamadı
                            <?php else: ?>
                                Henüz proje eklenmemiş
                            <?php endif; ?>
                        </h3>
                        <?php if(!empty($search) || $filter !== 'all'): ?>
                            <a href="portfolio.php" class="btn btn-gradient">Tüm Projeleri Görüntüle</a>
                        <?php else: ?>
                            <p class="text-muted mb-4">Yeni projeler yakında eklenecek.</p>
                            <a href="contact.php" class="btn btn-gradient">Proje Teklifi Ver</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<div class="modal fade" id="projectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="projectModalTitle">Proje Detayları</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="projectModalBody">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Yükleniyor...</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<section class="py-5" style="background: var(--dark-card);">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h3 class="text-gradient mb-3">Sizin Projeniz Bir Sonraki Olsun!</h3>
                <p class="mb-0">Benzer kalitede bir proje için benimle iletişime geçin. <?php echo getSetting('site_brand', 'BERAT K - R10'); ?> ile hayallerinizi gerçeğe dönüştürün.</p>
            </div>
            <div class="col-lg-4 text-lg-end">
                <a href="contact.php" class="btn btn-gradient btn-lg">
                    <i class="fas fa-rocket me-2"></i>Projeme Başla
                </a>
            </div>
        </div>
    </div>
</section>

<script>
function filterProjects(category) {
    const url = new URL(window.location);
    url.searchParams.set('filter', category);
    window.location.href = url.toString();
}

function showProjectDetails(projectId) {
    const modal = new bootstrap.Modal(document.getElementById('projectModal'));
    const modalBody = document.getElementById('projectModalBody');
    
    modalBody.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Yükleniyor...</span>
            </div>
        </div>
    `;
    
    modal.show();
    
    <?php if (!empty($projects)): ?>
    const projects = <?php echo json_encode($projects); ?>;
    const project = projects.find(p => p.id == projectId);
    
    if (project) {
        document.getElementById('projectModalTitle').textContent = project.title;
        
        let technologies = '';
        if (project.technologies) {
            const techArray = project.technologies.split(',');
            technologies = techArray.map(tech => 
                `<span class="badge bg-gradient me-1 mb-1">${tech.trim()}</span>`
            ).join('');
        }
        
        let links = '';
        if (project.demo_url) {
            links += `<a href="${project.demo_url}" target="_blank" class="btn btn-gradient me-2"><i class="fas fa-external-link-alt me-2"></i>Demo İzle</a>`;
        }
        if (project.github_url) {
            links += `<a href="${project.github_url}" target="_blank" class="btn btn-outline-gradient"><i class="fab fa-github me-2"></i>GitHub</a>`;
        }
        
        modalBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <img src="${project.image || 'assets/img/project-placeholder.jpg'}" 
                         alt="${project.title}" class="img-fluid rounded mb-3">
                </div>
                <div class="col-md-6">
                    <h5 class="text-gradient mb-3">${project.title}</h5>
                    <p class="text-muted mb-3">${project.description}</p>
                    
                    <div class="mb-3">
                        <h6>Kategori:</h6>
                        <span class="badge bg-gradient">${project.category}</span>
                    </div>
                    
                    ${technologies ? `
                    <div class="mb-3">
                        <h6>Teknolojiler:</h6>
                        ${technologies}
                    </div>
                    ` : ''}
                    
                    <div class="mb-3">
                        <h6>Tamamlanma Tarihi:</h6>
                        <small class="text-muted">${new Date(project.created_at).toLocaleDateString('tr-TR')}</small>
                    </div>
                    
                    ${links ? `
                    <div class="mt-4">
                        ${links}
                    </div>
                    ` : ''}
                </div>
            </div>
        `;
    }
    <?php endif; ?>
}
</script>

<style>
.portfolio-filter {
    border-radius: 25px;
    padding: 8px 20px;
    font-size: 0.9rem;
    font-weight: 500;
    transition: all 0.3s ease;
}

.search-container {
    max-width: 400px;
    margin: 0 auto;
}

.empty-state {
    padding: 60px 20px;
}

.project-technologies .badge {
    font-size: 0.75rem;
    font-weight: normal;
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

@media (max-width: 768px) {
    .portfolio-filters {
        justify-content: center !important;
    }
    
    .project-links a {
        width: 40px;
        height: 40px;
        font-size: 0.9rem;
    }
}
</style>

<?php include 'includes/footer.php'; ?>