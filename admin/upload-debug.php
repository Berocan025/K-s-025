<?php
/**
 * Upload Debug & Test Page
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once __DIR__ . '/../includes/functions.php';
requireLogin();

$page_title = 'Upload Debug';

$message = '';
$error = '';
$uploaded_file = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['test_file'])) {
    $upload_type = $_POST['upload_type'] ?? 'test';
    $directory = 'uploads/' . $upload_type . '/';
    
    $uploaded_file = uploadFile($_FILES['test_file'], $directory);
    if ($uploaded_file) {
        $message = 'Dosya başarıyla yüklendi: ' . $uploaded_file;
    } else {
        $error = 'Dosya yüklenirken hata oluştu. Debug log\'una bakın.';
    }
}

// Read debug log
$debug_log = '';
$debug_file = 'uploads/debug.log';
if (file_exists($debug_file)) {
    $debug_log = file_get_contents($debug_file);
}

// Clear debug log
if (isset($_POST['clear_log'])) {
    if (file_exists($debug_file)) {
        unlink($debug_file);
    }
    header('Location: upload-debug.php');
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom border-secondary">
        <h1 class="h2 text-gradient">Upload Debug & Test</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <form method="POST" style="display:inline;">
                <button type="submit" name="clear_log" class="btn btn-outline-light btn-sm me-2">
                    <i class="fas fa-trash me-1"></i>Clear Log
                </button>
            </form>
            <a href="php-info.php" class="btn btn-outline-gradient btn-sm" target="_blank">
                <i class="fas fa-info-circle me-1"></i>PHP Info
            </a>
        </div>
    </div>
    
    <?php if ($message): ?>
        <div class="alert alert-success" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i><?php echo $error; ?>
        </div>
    <?php endif; ?>
    
    <div class="row">
        <div class="col-md-4">
            <div class="card-custom mb-4">
                <div class="card-body">
                    <h5 class="card-title text-light">Test File Upload</h5>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="upload_type" class="form-label text-light">Upload Tipi</label>
                            <select class="form-control" id="upload_type" name="upload_type" required>
                                <option value="test">Test</option>
                                <option value="projects">Projects</option>
                                <option value="products">Products</option>
                                <option value="about">About</option>
                                <option value="services">Services</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="test_file" class="form-label text-light">Dosya Seç</label>
                            <input type="file" class="form-control" id="test_file" name="test_file" accept="image/*" required>
                            <small class="text-muted">JPG, PNG, GIF, WEBP formatları desteklenir (Max: 10MB)</small>
                        </div>
                        
                        <button type="submit" class="btn btn-gradient">
                            <i class="fas fa-upload me-2"></i>Dosyayı Yükle
                        </button>
                    </form>
                    
                    <?php if ($uploaded_file): ?>
                        <div class="mt-3">
                            <h6 class="text-light">Yüklenen Dosya:</h6>
                            <div class="text-center">
                                <img src="../<?php echo htmlspecialchars($uploaded_file); ?>" alt="Uploaded" class="img-fluid rounded" style="max-width: 200px; max-height: 200px;">
                                <br><small class="text-muted"><?php echo htmlspecialchars($uploaded_file); ?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card-custom mb-4">
                <div class="card-body">
                    <h5 class="card-title text-light">Server Info</h5>
                    
                    <ul class="list-unstyled text-light small">
                        <li><strong>PHP Version:</strong> <?php echo PHP_VERSION; ?></li>
                        <li><strong>Upload Max:</strong> <?php echo ini_get('upload_max_filesize'); ?></li>
                        <li><strong>Post Max:</strong> <?php echo ini_get('post_max_size'); ?></li>
                        <li><strong>Max File Uploads:</strong> <?php echo ini_get('max_file_uploads'); ?></li>
                        <li><strong>Memory Limit:</strong> <?php echo ini_get('memory_limit'); ?></li>
                        <li><strong>Max Execution Time:</strong> <?php echo ini_get('max_execution_time'); ?>s</li>
                    </ul>
                    
                    <h6 class="text-light mt-3">Directory Status:</h6>
                    <ul class="list-unstyled text-light small">
                        <?php
                        $dirs = ['uploads/', 'uploads/test/', 'uploads/projects/', 'uploads/products/', 'uploads/about/', 'uploads/services/'];
                        foreach ($dirs as $dir):
                            $abs_dir = __DIR__ . '/../' . $dir;
                            $exists = is_dir($abs_dir);
                            $writable = $exists && is_writable($abs_dir);
                            $perms = $exists ? substr(sprintf('%o', fileperms($abs_dir)), -4) : 'N/A';
                        ?>
                            <li>
                                <strong><?php echo $dir; ?></strong><br>
                                Exists: <span class="<?php echo $exists ? 'text-success' : 'text-danger'; ?>"><?php echo $exists ? 'Yes' : 'No'; ?></span> |
                                Writable: <span class="<?php echo $writable ? 'text-success' : 'text-danger'; ?>"><?php echo $writable ? 'Yes' : 'No'; ?></span> |
                                Perms: <span class="text-info"><?php echo $perms; ?></span>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card-custom mb-4">
                <div class="card-body">
                    <h5 class="card-title text-light">Recent Uploads</h5>
                    
                    <?php
                    $upload_dirs = ['uploads/test/', 'uploads/projects/', 'uploads/products/', 'uploads/about/', 'uploads/services/'];
                    $recent_files = [];
                    
                    foreach ($upload_dirs as $dir) {
                        $abs_dir = __DIR__ . '/../' . $dir;
                        if (is_dir($abs_dir)) {
                            $files = glob($abs_dir . 'berat_*');
                            foreach ($files as $file) {
                                if (is_file($file)) {
                                    $recent_files[] = [
                                        'path' => str_replace(__DIR__ . '/../', '', $file),
                                        'name' => basename($file),
                                        'time' => filemtime($file),
                                        'size' => filesize($file)
                                    ];
                                }
                            }
                        }
                    }
                    
                    // Sort by time, newest first
                    usort($recent_files, function($a, $b) { return $b['time'] - $a['time']; });
                    $recent_files = array_slice($recent_files, 0, 5);
                    ?>
                    
                    <?php if (!empty($recent_files)): ?>
                        <div class="recent-files">
                            <?php foreach ($recent_files as $file): ?>
                                <div class="mb-2 p-2 border border-secondary rounded">
                                    <div class="d-flex align-items-center">
                                        <img src="../<?php echo htmlspecialchars($file['path']); ?>" alt="" class="me-2 rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                        <div class="flex-grow-1">
                                            <div class="text-light small"><?php echo htmlspecialchars($file['name']); ?></div>
                                            <div class="text-muted" style="font-size: 0.7rem;">
                                                <?php echo date('Y-m-d H:i:s', $file['time']); ?> | 
                                                <?php echo round($file['size'] / 1024, 1); ?>KB
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="text-muted">No recent uploads found</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-12">
            <div class="card-custom">
                <div class="card-body">
                    <h5 class="card-title text-light">
                        Debug Log 
                        <small class="text-muted">(Last 50 lines)</small>
                    </h5>
                    
                    <div style="background: var(--dark-bg); padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto;">
                        <pre style="color: var(--text-light); font-size: 0.8rem; margin: 0; white-space: pre-wrap;"><?php 
                        if ($debug_log) {
                            $lines = explode("\n", $debug_log);
                            $lines = array_slice($lines, -50); // Last 50 lines
                            echo htmlspecialchars(implode("\n", $lines));
                        } else {
                            echo 'No debug log found. Try uploading a file to generate log entries.';
                        }
                        ?></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php if (isset($_POST) && !empty($_POST) && !isset($_POST['clear_log'])): ?>
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card-custom">
                <div class="card-body">
                    <h5 class="card-title text-light">Debug - POST Data</h5>
                    <pre style="background: var(--dark-bg); padding: 15px; border-radius: 5px; color: var(--text-light); font-size: 0.8rem;"><?php print_r($_POST); ?></pre>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card-custom">
                <div class="card-body">
                    <h5 class="card-title text-light">Debug - FILES Data</h5>
                    <pre style="background: var(--dark-bg); padding: 15px; border-radius: 5px; color: var(--text-light); font-size: 0.8rem;"><?php print_r($_FILES); ?></pre>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>