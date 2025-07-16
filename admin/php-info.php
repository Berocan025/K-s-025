<?php
/**
 * PHP Info Page for Debug
 * Developer: BERAT K - R10
 * Website: Portfolio Management System
 * Created by: BERAT K - R10
 */

require_once __DIR__ . '/../includes/functions.php';
requireLogin();

?>
<!DOCTYPE html>
<html>
<head>
    <title>PHP Info - Debug</title>
    <style>
        body { font-family: Arial, sans-serif; background: #0d1117; color: #f0f6fc; }
        .container { max-width: 1200px; margin: 20px auto; padding: 20px; }
        .back-link { display: inline-block; margin-bottom: 20px; padding: 10px 20px; background: #6c5ce7; color: white; text-decoration: none; border-radius: 5px; }
        .back-link:hover { background: #5f3dc4; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { padding: 8px; border: 1px solid #21262d; text-align: left; }
        th { background: #161b22; }
        .section-title { background: #6c5ce7; color: white; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <a href="upload-debug.php" class="back-link">← Back to Upload Debug</a>
        
        <h1>PHP Configuration Info</h1>
        
        <h2>Upload Related Settings</h2>
        <table>
            <tr class="section-title"><th colspan="2">File Upload Settings</th></tr>
            <tr><td>file_uploads</td><td><?php echo ini_get('file_uploads') ? 'Enabled' : 'Disabled'; ?></td></tr>
            <tr><td>upload_max_filesize</td><td><?php echo ini_get('upload_max_filesize'); ?></td></tr>
            <tr><td>post_max_size</td><td><?php echo ini_get('post_max_size'); ?></td></tr>
            <tr><td>max_file_uploads</td><td><?php echo ini_get('max_file_uploads'); ?></td></tr>
            <tr><td>upload_tmp_dir</td><td><?php echo ini_get('upload_tmp_dir') ?: 'System Default'; ?></td></tr>
            
            <tr class="section-title"><th colspan="2">Memory & Execution</th></tr>
            <tr><td>memory_limit</td><td><?php echo ini_get('memory_limit'); ?></td></tr>
            <tr><td>max_execution_time</td><td><?php echo ini_get('max_execution_time'); ?> seconds</td></tr>
            <tr><td>max_input_time</td><td><?php echo ini_get('max_input_time'); ?> seconds</td></tr>
            
            <tr class="section-title"><th colspan="2">Directory Permissions</th></tr>
            <?php
            $dirs = [
                'uploads/' => __DIR__ . '/../uploads/',
                'uploads/test/' => __DIR__ . '/../uploads/test/',
                'uploads/projects/' => __DIR__ . '/../uploads/projects/',
                'uploads/products/' => __DIR__ . '/../uploads/products/',
                'uploads/about/' => __DIR__ . '/../uploads/about/',
                'uploads/services/' => __DIR__ . '/../uploads/services/'
            ];
            
            foreach ($dirs as $rel => $abs):
                $exists = is_dir($abs);
                $readable = $exists && is_readable($abs);
                $writable = $exists && is_writable($abs);
                $perms = $exists ? substr(sprintf('%o', fileperms($abs)), -4) : 'N/A';
            ?>
                <tr>
                    <td><?php echo $rel; ?></td>
                    <td>
                        Exists: <?php echo $exists ? '✓' : '✗'; ?> | 
                        Readable: <?php echo $readable ? '✓' : '✗'; ?> | 
                        Writable: <?php echo $writable ? '✓' : '✗'; ?> | 
                        Permissions: <?php echo $perms; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            
            <tr class="section-title"><th colspan="2">Server Info</th></tr>
            <tr><td>PHP Version</td><td><?php echo PHP_VERSION; ?></td></tr>
            <tr><td>Server Software</td><td><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Unknown'; ?></td></tr>
            <tr><td>Document Root</td><td><?php echo $_SERVER['DOCUMENT_ROOT'] ?? 'Unknown'; ?></td></tr>
            <tr><td>Script Path</td><td><?php echo __DIR__; ?></td></tr>
            <tr><td>Temp Directory</td><td><?php echo sys_get_temp_dir(); ?></td></tr>
            <tr><td>Temp Dir Writable</td><td><?php echo is_writable(sys_get_temp_dir()) ? 'Yes' : 'No'; ?></td></tr>
        </table>
        
        <h2>Recent Error Log</h2>
        <div style="background: #161b22; padding: 15px; border-radius: 5px; max-height: 300px; overflow-y: auto;">
            <pre style="margin: 0; color: #f0f6fc; font-size: 12px;"><?php
                $error_log = error_get_last();
                if ($error_log) {
                    echo "Last Error:\n";
                    echo "Type: " . $error_log['type'] . "\n";
                    echo "Message: " . $error_log['message'] . "\n";
                    echo "File: " . $error_log['file'] . "\n";
                    echo "Line: " . $error_log['line'] . "\n";
                } else {
                    echo "No recent PHP errors found.";
                }
            ?></pre>
        </div>
        
        <h2>Full PHP Info</h2>
        <div style="margin-top: 20px;">
            <?php phpinfo(); ?>
        </div>
    </div>
</body>
</html>