<?php

namespace TelegramBot\Controllers;

use TelegramBot\Repositories\AdminsRepo;

class AuthController
{
    private $adminsRepo;
    
    public function __construct()
    {
        try {
            $this->adminsRepo = new AdminsRepo();
        } catch (\Exception $e) {
            // If database not available, handle gracefully
            error_log("AuthController: Database not available - " . $e->getMessage());
            $this->adminsRepo = null;
        }
    }
    
    /**
     * Login page
     */
    public function login()
    {
        $error = null;
        
        // Check if database is available
        if ($this->adminsRepo === null) {
            $error = 'سیستم هنوز نصب نشده است. لطفاً ابتدا نصب را انجام دهید.';
            $this->renderLogin($error);
            return;
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            
            if (empty($username) || empty($password)) {
                $error = 'نام کاربری و رمز عبور اجباری هستند';
            } else {
                try {
                    $admin = $this->adminsRepo->findByUsernameOrEmail($username);
                    
                    if ($admin && $this->adminsRepo->verifyPassword($admin, $password)) {
                        // Start session
                        if (session_status() === PHP_SESSION_NONE) {
                            session_start();
                        }
                        
                        $_SESSION['admin_id'] = $admin['id'];
                        $_SESSION['admin_username'] = $admin['username'];
                        $_SESSION['admin_role'] = $admin['role'];
                        
                        header('Location: /admin/');
                        exit;
                    } else {
                        $error = 'نام کاربری یا رمز عبور اشتباه است';
                    }
                } catch (\Exception $e) {
                    $error = 'خطا در اتصال به پایگاه داده: ' . $e->getMessage();
                }
            }
        }
        
        $this->renderLogin($error);
    }
    
    /**
     * Logout
     */
    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        session_destroy();
        
        header('Location: /admin/login.php');
        exit;
    }
    
    /**
     * Check if user is authenticated
     */
    public static function checkAuth()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Check if system is installed first
        try {
            \TelegramBot\Config::isInstalled();
        } catch (\Exception $e) {
            // System not installed, redirect to installer
            header('Location: /install/');
            exit;
        }
        
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login.php');
            exit;
        }
        
        return [
            'id' => $_SESSION['admin_id'],
            'username' => $_SESSION['admin_username'] ?? '',
            'role' => $_SESSION['admin_role'] ?? 'admin',
        ];
    }
    
    /**
     * Generate CSRF token
     */
    public static function getCsrfToken()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        
        return $_SESSION['csrf_token'];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken($token)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }
    
    /**
     * Render login page
     */
    private function renderLogin($error = null)
    {
        include __DIR__ . '/../../templates/admin/login.php';
    }
}

