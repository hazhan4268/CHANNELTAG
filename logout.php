<?php
/**
 * Admin Logout Entry Point
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

use TelegramBot\Bootstrap;
use TelegramBot\Controllers\AuthController;

try {
    Bootstrap::init();
    
    $controller = new AuthController();
    $controller->logout();
} catch (\Exception $e) {
    http_response_code(500);
    echo "خطای داخلی: " . htmlspecialchars($e->getMessage()) . "<br>";
    echo '<br><a href="/admin/login.php">بازگشت به صفحه ورود</a>';
}
