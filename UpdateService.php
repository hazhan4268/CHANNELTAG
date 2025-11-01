<?php

namespace TelegramBot\Services;

use TelegramBot\Logger;
use TelegramBot\Config;

class UpdateService
{
    private $logger;
    private $githubRepo = 'hazhan4268/CHANNELTAG';
    private $projectRoot;
    private $currentVersion;
    
    public function __construct(Logger $logger = null)
    {
        $this->logger = $logger ?? new Logger();
        $this->projectRoot = dirname(dirname(__DIR__));
        $this->currentVersion = $this->getCurrentVersion();
    }
    
    /**
     * Check for updates from GitHub
     */
    public function checkForUpdates()
    {
        try {
            $latestRelease = $this->getLatestRelease();
            $latestVersion = $latestRelease['tag_name'] ?? null;
            
            if (!$latestVersion) {
                // Fallback to commit check
                $latestCommit = $this->getLatestCommit();
                return [
                    'has_update' => false,
                    'current_version' => $this->currentVersion,
                    'latest_commit' => $latestCommit['sha'] ?? null,
                    'commit_message' => $latestCommit['commit']['message'] ?? null,
                    'commit_date' => $latestCommit['commit']['committer']['date'] ?? null,
                ];
            }
            
            $hasUpdate = version_compare($this->currentVersion, $latestVersion, '<');
            
            return [
                'has_update' => $hasUpdate,
                'current_version' => $this->currentVersion,
                'latest_version' => $latestVersion,
                'release_notes' => $latestRelease['body'] ?? '',
                'published_at' => $latestRelease['published_at'] ?? null,
            ];
        } catch (\Exception $e) {
            $this->logger->error("Update check failed: " . $e->getMessage());
            return [
                'error' => $e->getMessage(),
                'has_update' => false,
                'current_version' => $this->currentVersion,
            ];
        }
    }
    
    /**
     * Perform update from GitHub
     */
    public function performUpdate($version = 'latest')
    {
        try {
            $this->logger->info("Starting update process...");
            
            // Create backup
            $backupPath = $this->createBackup();
            $this->logger->info("Backup created at: {$backupPath}");
            
            // Download latest files
            $downloadPath = $this->downloadUpdate($version);
            $this->logger->info("Update downloaded to: {$downloadPath}");
            
            // Extract and apply update
            $this->applyUpdate($downloadPath);
            $this->logger->info("Update applied successfully");
            
            // Update version info
            $this->updateVersionInfo($version);
            
            // Cleanup
            $this->cleanup($downloadPath);
            
            return [
                'success' => true,
                'message' => 'آپدیت با موفقیت انجام شد',
                'backup_path' => $backupPath,
                'new_version' => $version,
            ];
            
        } catch (\Exception $e) {
            $this->logger->error("Update failed: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * Get latest release from GitHub API
     */
    private function getLatestRelease()
    {
        $url = "https://api.github.com/repos/{$this->githubRepo}/releases/latest";
        $response = $this->makeGithubRequest($url);
        return json_decode($response, true);
    }
    
    /**
     * Get latest commit from GitHub API
     */
    private function getLatestCommit()
    {
        $url = "https://api.github.com/repos/{$this->githubRepo}/commits/main";
        $response = $this->makeGithubRequest($url);
        return json_decode($response, true);
    }
    
    /**
     * Make GitHub API request
     */
    private function makeGithubRequest($url)
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_USERAGENT => 'TelegramBot-ChannelManager/1.0',
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTPHEADER => [
                'Accept: application/vnd.github.v3+json',
            ],
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        
        if ($error) {
            throw new \Exception("cURL error: {$error}");
        }
        
        if ($httpCode !== 200) {
            throw new \Exception("GitHub API returned HTTP {$httpCode}");
        }
        
        return $response;
    }
    
    /**
     * Download update from GitHub
     */
    private function downloadUpdate($version = 'latest')
    {
        $downloadUrl = $version === 'latest' 
            ? "https://github.com/{$this->githubRepo}/archive/refs/heads/main.zip"
            : "https://github.com/{$this->githubRepo}/archive/refs/tags/{$version}.zip";
        
        $tempDir = sys_get_temp_dir();
        $zipPath = $tempDir . '/channeltag-update-' . time() . '.zip';
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $downloadUrl,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_FILE => fopen($zipPath, 'w'),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_TIMEOUT => 300,
            CURLOPT_USERAGENT => 'TelegramBot-ChannelManager/1.0',
        ]);
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if (!$result || $httpCode !== 200) {
            throw new \Exception("Failed to download update (HTTP {$httpCode})");
        }
        
        if (!file_exists($zipPath) || filesize($zipPath) === 0) {
            throw new \Exception("Downloaded file is empty or corrupted");
        }
        
        return $zipPath;
    }
    
    /**
     * Create backup of current installation
     */
    private function createBackup()
    {
        $backupDir = $this->projectRoot . '/backups';
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }
        
        $backupPath = $backupDir . '/backup-' . date('Y-m-d-H-i-s') . '.zip';
        
        $zip = new \ZipArchive();
        if ($zip->open($backupPath, \ZipArchive::CREATE) !== TRUE) {
            throw new \Exception("Cannot create backup file");
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($this->projectRoot)
        );
        
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filePath = $file->getRealPath();
                $relativePath = substr($filePath, strlen($this->projectRoot) + 1);
                
                // Skip backup and logs directories
                if (strpos($relativePath, 'backups/') === 0 || strpos($relativePath, 'logs/') === 0) {
                    continue;
                }
                
                $zip->addFile($filePath, $relativePath);
            }
        }
        
        $zip->close();
        return $backupPath;
    }
    
    /**
     * Apply downloaded update
     */
    private function applyUpdate($zipPath)
    {
        $tempDir = sys_get_temp_dir() . '/channeltag-extract-' . time();
        
        $zip = new \ZipArchive();
        if ($zip->open($zipPath) !== TRUE) {
            throw new \Exception("Cannot extract update file");
        }
        
        $zip->extractTo($tempDir);
        $zip->close();
        
        // Find extracted directory
        $extractedDir = null;
        $items = scandir($tempDir);
        foreach ($items as $item) {
            if ($item !== '.' && $item !== '..' && is_dir($tempDir . '/' . $item)) {
                $extractedDir = $tempDir . '/' . $item;
                break;
            }
        }
        
        if (!$extractedDir) {
            throw new \Exception("Could not find extracted files");
        }
        
        // Copy files while preserving config
        $this->copyUpdateFiles($extractedDir, $this->projectRoot);
        
        // Cleanup temp directory
        $this->removeDirectory($tempDir);
    }
    
    /**
     * Copy update files while preserving configuration
     */
    private function copyUpdateFiles($sourceDir, $targetDir)
    {
        $preserveFiles = [
            'config/.env.php',
            'logs/',
            'backups/',
        ];
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $file) {
            $relativePath = substr($file->getRealPath(), strlen($sourceDir) + 1);
            $targetPath = $targetDir . '/' . $relativePath;
            
            // Skip preserved files
            $shouldPreserve = false;
            foreach ($preserveFiles as $preservePattern) {
                if (strpos($relativePath, $preservePattern) === 0) {
                    $shouldPreserve = true;
                    break;
                }
            }
            
            if ($shouldPreserve && file_exists($targetPath)) {
                continue;
            }
            
            if ($file->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                $targetDirPath = dirname($targetPath);
                if (!is_dir($targetDirPath)) {
                    mkdir($targetDirPath, 0755, true);
                }
                copy($file->getRealPath(), $targetPath);
            }
        }
    }
    
    /**
     * Get current version
     */
    private function getCurrentVersion()
    {
        $versionFile = $this->projectRoot . '/VERSION';
        if (file_exists($versionFile)) {
            return trim(file_get_contents($versionFile));
        }
        return '1.0.0';
    }
    
    /**
     * Update version information
     */
    private function updateVersionInfo($version)
    {
        $versionFile = $this->projectRoot . '/VERSION';
        file_put_contents($versionFile, $version);
        
        $updateInfoFile = $this->projectRoot . '/LAST_UPDATE';
        $updateInfo = [
            'version' => $version,
            'updated_at' => date('Y-m-d H:i:s'),
            'updated_by' => 'AutoUpdate',
        ];
        file_put_contents($updateInfoFile, json_encode($updateInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    
    /**
     * Cleanup temporary files
     */
    private function cleanup($zipPath)
    {
        if (file_exists($zipPath)) {
            unlink($zipPath);
        }
    }
    
    /**
     * Remove directory recursively
     */
    private function removeDirectory($dir)
    {
        if (!is_dir($dir)) {
            return;
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        
        foreach ($iterator as $file) {
            if ($file->isDir()) {
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }
        
        rmdir($dir);
    }
    
    /**
     * Get update history
     */
    public function getUpdateHistory()
    {
        $backupDir = $this->projectRoot . '/backups';
        $history = [];
        
        if (is_dir($backupDir)) {
            $files = glob($backupDir . '/backup-*.zip');
            foreach ($files as $file) {
                $filename = basename($file);
                if (preg_match('/backup-(\d{4}-\d{2}-\d{2}-\d{2}-\d{2}-\d{2})\.zip/', $filename, $matches)) {
                    $date = \DateTime::createFromFormat('Y-m-d-H-i-s', $matches[1]);
                    $history[] = [
                        'file' => $file,
                        'date' => $date->format('Y-m-d H:i:s'),
                        'size' => filesize($file),
                    ];
                }
            }
        }
        
        // Sort by date descending
        usort($history, function($a, $b) {
            return strcmp($b['date'], $a['date']);
        });
        
        return $history;
    }
    
    /**
     * Restore from backup
     */
    public function restoreFromBackup($backupFile)
    {
        try {
            if (!file_exists($backupFile)) {
                throw new \Exception("Backup file not found");
            }
            
            $this->logger->info("Starting restore from backup: " . basename($backupFile));
            
            // Create current backup before restore
            $currentBackup = $this->createBackup();
            $this->logger->info("Current state backed up to: {$currentBackup}");
            
            // Extract backup
            $tempDir = sys_get_temp_dir() . '/channeltag-restore-' . time();
            
            $zip = new \ZipArchive();
            if ($zip->open($backupFile) !== TRUE) {
                throw new \Exception("Cannot open backup file");
            }
            
            $zip->extractTo($tempDir);
            $zip->close();
            
            // Copy files back
            $this->copyUpdateFiles($tempDir, $this->projectRoot);
            
            // Cleanup
            $this->removeDirectory($tempDir);
            
            $this->logger->info("Restore completed successfully");
            
            return [
                'success' => true,
                'message' => 'بازگردانی با موفقیت انجام شد',
                'current_backup' => $currentBackup,
            ];
            
        } catch (\Exception $e) {
            $this->logger->error("Restore failed: " . $e->getMessage());
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
}