<?php
/**
 * Error Log Viewer
 * مشاهده لاگ خطاها
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load autoloader
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../../src/autoload.php';
}

require_once __DIR__ . '/../../src/Bootstrap.php';
require_once __DIR__ . '/../../src/Config.php';
require_once __DIR__ . '/../../src/Controllers/AuthController.php';

use TelegramBot\Bootstrap;
use TelegramBot\Controllers\AuthController;

try {
    Bootstrap::init();
    AuthController::checkAuth();
} catch (\Exception $e) {
    // Allow viewing error log even if auth fails
}

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>مشاهده لاگ خطاها</title>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 1200px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #333; margin-bottom: 20px; }
        pre { background: #f8f9fa; padding: 15px; border-radius: 4px; overflow-x: auto; }
        .log-file { margin-bottom: 30px; }
        .log-file h2 { color: #555; border-bottom: 2px solid #667eea; padding-bottom: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>📋 مشاهده لاگ خطاها</h1>
        
        <?php
        $logDir = __DIR__ . '/../../logs';
        if (is_dir($logDir)) {
            $files = glob($logDir . '/*.log');
            rsort($files); // Newest first
            
            foreach (array_slice($files, 0, 5) as $file) {
                echo '<div class="log-file">';
                echo '<h2>' . basename($file) . '</h2>';
                if (file_exists($file) && is_readable($file)) {
                    $content = file_get_contents($file);
                    if (!empty($content)) {
                        echo '<pre>' . htmlspecialchars($content) . '</pre>';
                    } else {
                        echo '<p>لاگی ثبت نشده است.</p>';
                    }
                } else {
                    echo '<p>فایل قابل خواندن نیست.</p>';
                }
                echo '</div>';
            }
        } else {
            echo '<p>پوشه logs پیدا نشد.</p>';
        }
        
        // Show PHP error log
        echo '<div class="log-file">';
        echo '<h2>PHP Error Log</h2>';
        $phpLog = ini_get('error_log');
        if ($phpLog && file_exists($phpLog)) {
            $phpLogContent = file_get_contents($phpLog);
            if (!empty($phpLogContent)) {
                echo '<pre>' . htmlspecialchars(substr($phpLogContent, -5000)) . '</pre>'; // Last 5000 chars
            } else {
                echo '<p>لاگی ثبت نشده است.</p>';
            }
        } else {
            echo '<p>PHP error log پیدا نشد یا تنظیم نشده است.</p>';
        }
        echo '</div>';
        ?>
        
        <div style="margin-top: 30px;">
            <a href="/admin/debug.php">← صفحه Debug</a> | 
            <a href="/admin/">← بازگشت به پنل</a>
        </div>
    </div>
</body>
</html>

