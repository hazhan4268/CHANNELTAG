<?php

namespace TelegramBot\Controllers;

use TelegramBot\Services\UpdateService;
use TelegramBot\Logger;

class UpdateController
{
    private $updateService;
    private $logger;
    
    public function __construct()
    {
        $this->logger = new Logger();
        $this->updateService = new UpdateService($this->logger);
    }
    
    /**
     * Update management page
     */
    public function index()
    {
        $updateInfo = $this->updateService->checkForUpdates();
        $history = $this->updateService->getUpdateHistory();
        
        $this->render('updates', [
            'updateInfo' => $updateInfo,
            'history' => $history,
        ]);
    }
    
    /**
     * Check for updates (AJAX)
     */
    public function checkUpdates()
    {
        header('Content-Type: application/json');
        
        $updateInfo = $this->updateService->checkForUpdates();
        echo json_encode($updateInfo);
        exit;
    }
    
    /**
     * Perform update
     */
    public function performUpdate()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        $version = $_POST['version'] ?? 'latest';
        $result = $this->updateService->performUpdate($version);
        
        echo json_encode($result);
        exit;
    }
    
    /**
     * Restore from backup
     */
    public function restoreBackup()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            http_response_code(403);
            echo json_encode(['error' => 'Invalid CSRF token']);
            exit;
        }
        
        header('Content-Type: application/json');
        
        $backupFile = $_POST['backup_file'] ?? '';
        if (empty($backupFile) || !file_exists($backupFile)) {
            echo json_encode(['error' => 'فایل بکاپ معتبر نیست']);
            exit;
        }
        
        $result = $this->updateService->restoreFromBackup($backupFile);
        echo json_encode($result);
        exit;
    }
    
    /**
     * Download backup file
     */
    public function downloadBackup()
    {
        $file = $_GET['file'] ?? '';
        
        if (empty($file) || !file_exists($file)) {
            http_response_code(404);
            echo 'فایل پیدا نشد';
            exit;
        }
        
        // Security check - ensure file is in backups directory
        $backupDir = dirname(dirname(__DIR__)) . '/backups';
        $realBackupDir = realpath($backupDir);
        $realFile = realpath($file);
        
        if (!$realFile || strpos($realFile, $realBackupDir) !== 0) {
            http_response_code(403);
            echo 'دسترسی غیرمجاز';
            exit;
        }
        
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Content-Length: ' . filesize($file));
        
        readfile($file);
        exit;
    }
    
    /**
     * Render template
     */
    private function render($template, array $data = [])
    {
        extract($data);
        ob_start();
        include __DIR__ . "/../../templates/admin/{$template}.php";
        $content = ob_get_clean();
        include __DIR__ . "/../../templates/admin/layout.php";
    }
}