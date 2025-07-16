<?php
/**
 * Admin Messages Management
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$page_title = 'Mesajlar';

$success_message = '';
$error_message = '';

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'mark_read') {
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'read' WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success_message = 'Mesaj okundu olarak işaretlendi.';
            }
        } catch(PDOException $e) {
            $error_message = 'İşlem sırasında bir hata oluştu.';
        }
    } elseif ($action === 'mark_replied') {
        try {
            $stmt = $pdo->prepare("UPDATE contact_messages SET status = 'replied' WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success_message = 'Mesaj yanıtlandı olarak işaretlendi.';
            }
        } catch(PDOException $e) {
            $error_message = 'İşlem sırasında bir hata oluştu.';
        }
    } elseif ($action === 'delete') {
        try {
            $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
            if ($stmt->execute([$id])) {
                $success_message = 'Mesaj silindi.';
            }
        } catch(PDOException $e) {
            $error_message = 'Silme işlemi sırasında bir hata oluştu.';
        }
    }
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$sql = "SELECT * FROM contact_messages";
$params = [];

if ($filter !== 'all') {
    $sql .= " WHERE status = ?";
    $params[] = $filter;
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $messages = $stmt->fetchAll();
} catch(PDOException $e) {
    $messages = [];
}

$stats = [
    'total' => $pdo->query("SELECT COUNT(*) FROM contact_messages")->fetchColumn(),
    'new' => $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'new'")->fetchColumn(),
    'read' => $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'read'")->fetchColumn(),
    'replied' => $pdo->query("SELECT COUNT(*) FROM contact_messages WHERE status = 'replied'")->fetchColumn()
];
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">Mesajlar</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <div class="btn-group me-2">
                <a href="?filter=all" class="btn btn-outline-light btn-sm <?php echo $filter === 'all' ? 'active' : ''; ?>">
                    Tümü (<?php echo $stats['total']; ?>)
                </a>
                <a href="?filter=new" class="btn btn-outline-light btn-sm <?php echo $filter === 'new' ? 'active' : ''; ?>">
                    Yeni (<?php echo $stats['new']; ?>)
                </a>
                <a href="?filter=read" class="btn btn-outline-light btn-sm <?php echo $filter === 'read' ? 'active' : ''; ?>">
                    Okundu (<?php echo $stats['read']; ?>)
                </a>
                <a href="?filter=replied" class="btn btn-outline-light btn-sm <?php echo $filter === 'replied' ? 'active' : ''; ?>">
                    Yanıtlandı (<?php echo $stats['replied']; ?>)
                </a>
            </div>
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
    
    <?php if (!empty($messages)): ?>
        <div class="row">
            <?php foreach ($messages as $message): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card-custom h-100">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div class="message-avatar">
                                    <div class="avatar-circle <?php echo $message['status'] == 'new' ? 'bg-primary' : ($message['status'] == 'read' ? 'bg-warning' : 'bg-success'); ?>">
                                        <?php echo strtoupper(substr($message['name'], 0, 1)); ?>
                                    </div>
                                </div>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <?php if ($message['status'] === 'new'): ?>
                                            <li><a class="dropdown-item" href="?action=mark_read&id=<?php echo $message['id']; ?>&filter=<?php echo $filter; ?>">
                                                <i class="fas fa-eye me-2"></i>Okundu İşaretle
                                            </a></li>
                                        <?php endif; ?>
                                        <?php if ($message['status'] !== 'replied'): ?>
                                            <li><a class="dropdown-item" href="?action=mark_replied&id=<?php echo $message['id']; ?>&filter=<?php echo $filter; ?>">
                                                <i class="fas fa-reply me-2"></i>Yanıtlandı İşaretle
                                            </a></li>
                                        <?php endif; ?>
                                        <li><a class="dropdown-item" href="mailto:<?php echo htmlspecialchars($message['email']); ?>">
                                            <i class="fas fa-envelope me-2"></i>E-posta Gönder
                                        </a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><a class="dropdown-item text-danger delete-btn" href="?action=delete&id=<?php echo $message['id']; ?>&filter=<?php echo $filter; ?>" data-confirm="Bu mesajı silmek istediğinizden emin misiniz?">
                                            <i class="fas fa-trash me-2"></i>Sil
                                        </a></li>
                                    </ul>
                                </div>
                            </div>
                            
                            <h6 class="card-title text-light"><?php echo htmlspecialchars($message['name']); ?></h6>
                            <p class="text-light small mb-2"><?php echo htmlspecialchars($message['email']); ?></p>
                            
                            <?php if ($message['subject']): ?>
                                <p class="text-gradient mb-2"><strong><?php echo htmlspecialchars($message['subject']); ?></strong></p>
                            <?php endif; ?>
                            
                            <p class="text-light mb-3"><?php echo nl2br(htmlspecialchars(truncateText($message['message'], 150))); ?></p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <small class="text-light"><?php echo formatDate($message['created_at']); ?></small>
                                <span class="badge <?php 
                                    echo $message['status'] == 'new' ? 'bg-primary' : 
                                        ($message['status'] == 'read' ? 'bg-warning' : 'bg-success'); 
                                ?>">
                                    <?php 
                                    echo $message['status'] == 'new' ? 'Yeni' : 
                                        ($message['status'] == 'read' ? 'Okundu' : 'Yanıtlandı'); 
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="text-center py-5">
            <i class="fas fa-inbox fa-5x text-muted mb-4"></i>
            <h3 class="text-light mb-3">
                <?php if ($filter !== 'all'): ?>
                    Bu kategoride mesaj bulunamadı
                <?php else: ?>
                    Henüz mesaj alınmamış
                <?php endif; ?>
            </h3>
            <?php if ($filter !== 'all'): ?>
                <a href="?filter=all" class="btn btn-gradient">Tüm Mesajları Görüntüle</a>
            <?php else: ?>
                <p class="text-light">İletişim formu üzerinden gelen mesajlar burada görüntülenecek.</p>
                <a href="../contact.php" target="_blank" class="btn btn-gradient">İletişim Sayfasını Görüntüle</a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-light">Mesaj Detayları</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="messageModalBody">
                <!-- Content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-light" data-bs-dismiss="modal">Kapat</button>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-circle {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 1.2rem;
}

.message-avatar {
    margin-right: 15px;
}

.card-custom {
    transition: all 0.3s ease;
}

.card-custom:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(108, 92, 231, 0.15);
}

.btn-group .btn.active {
    background: var(--gradient);
    border-color: transparent;
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
    .btn-group {
        flex-direction: column;
        width: 100%;
    }
    
    .btn-group .btn {
        border-radius: 5px !important;
        margin-bottom: 5px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
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