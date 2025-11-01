<?php

namespace TelegramBot;

use TelegramBot\Config;

class Bootstrap
{
    /**
     * Initialize application
     */
    public static function init()
    {
        // Set error reporting
        error_reporting(E_ALL);
        ini_set('display_errors', 0);
        ini_set('log_errors', 1);
        
        // Set timezone
        date_default_timezone_set('UTC');
        
        // Set session settings
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
                ini_set('session.cookie_secure', 1);
            }
            session_start();
        }
        
        // Load configuration (only if .env.php exists)
        try {
            $envPath = __DIR__ . '/../config/.env.php';
            if (file_exists($envPath)) {
                Config::load();
            }
        } catch (\Exception $e) {
            // If not installed and not in installer/admin debug, redirect
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            $isInstaller = strpos($uri, '/install') !== false;
            $isAdminDebug = strpos($uri, '/admin/debug') !== false;
            $isAdminLogin = strpos($uri, '/admin/login') !== false;
            
            if (!$isInstaller && !$isAdminDebug && !$isAdminLogin) {
                // Only redirect if .env.php doesn't exist
                if (!file_exists(__DIR__ . '/../config/.env.php')) {
                    header('Location: /install/');
                    exit;
                }
            }
        }
    }
}

