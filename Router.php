<?php

namespace TelegramBot;

use TelegramBot\Controllers\WebhookController;
use TelegramBot\Controllers\AdminController;
use TelegramBot\Controllers\AuthController;

class Router
{
    /**
     * Route webhook requests
     */
    public static function routeWebhook()
    {
        try {
            $path = $_GET['path'] ?? '';
            
            // Check if config is loaded
            try {
                $slug = Config::get('WEBHOOK_SLUG', '');
            } catch (\Exception $e) {
                // Config not available (system not installed)
                return false;
            }
            
            if (empty($slug)) {
                // Webhook slug not configured yet
                return false;
            }
            
            if ($path === "webhook/{$slug}") {
                $controller = new WebhookController();
                $controller->handle();
                exit;
            }
            
            return false;
        } catch (\Exception $e) {
            error_log("Webhook routing error: " . $e->getMessage());
            http_response_code(500);
            exit;
        }
    }
    
    /**
     * Route admin requests
     */
    public static function routeAdmin()
    {
        try {
            $path = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
            $adminBase = '/admin';
            
            if (strpos($path, $adminBase) !== 0) {
                return false;
            }
            
            // Remove admin base
            $route = substr($path, strlen($adminBase));
            
            if ($route === '' || $route === '/') {
                $route = '/index.php';
            }
            
            // Special routes that don't need controllers
            if ($route === '/debug.php') {
                return false; // Let it be handled by the actual file
            }
            
            // Route to appropriate controller/method
            // Don't create controller for login/logout
            switch ($route) {
                case '/login.php':
                    $auth = new AuthController();
                    $auth->login();
                    return true;
                    
                case '/logout.php':
                    $auth = new AuthController();
                    $auth->logout();
                    return true;
                    
                case '/index.php':
                case '/':
                    AuthController::checkAuth();
                    $controller = new AdminController();
                    $controller->dashboard();
                    return true;
                    
                case '/settings.php':
                    AuthController::checkAuth();
                    $controller = new AdminController();
                    $controller->settings();
                    return true;
                    
                case '/channels.php':
                    AuthController::checkAuth();
                    $controller = new AdminController();
                    $controller->channels();
                    return true;
                    
                case '/tags.php':
                    AuthController::checkAuth();
                    $controller = new AdminController();
                    $controller->tags();
                    return true;
                    
                case '/template.php':
                    AuthController::checkAuth();
                    $controller = new AdminController();
                    $controller->template();
                    return true;
                    
                case '/health.php':
                    AuthController::checkAuth();
                    $controller = new AdminController();
                    $controller->health();
                    return true;
                    
                case '/update.php':
                    AuthController::checkAuth();
                    // UpdateController is handled directly in the file
                    return false; // Let the actual file handle it
            }
            
            return false;
        } catch (\Exception $e) {
            // Log error but don't expose details
            error_log("Router error: " . $e->getMessage());
            http_response_code(500);
            echo "خطای داخلی سرور. لطفاً صفحه debug را بررسی کنید: /admin/debug.php";
            return false;
        }
    }
}

