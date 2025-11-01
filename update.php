<?php
/**
 * Update Management Entry Point
 */

// Load autoloader
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../../src/autoload.php';
}

require_once __DIR__ . '/../../src/Bootstrap.php';
require_once __DIR__ . '/../../src/Config.php';
require_once __DIR__ . '/../../src/Controllers/AuthController.php';
require_once __DIR__ . '/../../src/Controllers/UpdateController.php';

use TelegramBot\Bootstrap;
use TelegramBot\Controllers\AuthController;
use TelegramBot\Controllers\UpdateController;

try {
    Bootstrap::init();
    
    // Check authentication first
    AuthController::checkAuth();
    
    $action = $_GET['action'] ?? 'index';
    $controller = new UpdateController();
    
    switch ($action) {
        case 'check':
            $controller->checkUpdates();
            break;
            
        case 'update':
            $controller->performUpdate();
            break;
            
        case 'restore':
            $controller->restoreBackup();
            break;
            
        case 'download':
            $controller->downloadBackup();
            break;
            
        default:
            $controller->index();
            break;
    }
    
} catch (\Exception $e) {
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
        header('Content-Type: application/json');
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        http_response_code(500);
        echo "خطای داخلی: " . htmlspecialchars($e->getMessage()) . "<br>";
        echo "فایل: " . htmlspecialchars($e->getFile()) . "<br>";
        echo "خط: " . $e->getLine() . "<br>";
        echo '<br><a href="/admin/debug.php">صفحه Debug</a>';
    }
}