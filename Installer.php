<?php

namespace TelegramBot\Services;

use TelegramBot\Config;
use TelegramBot\Logger;
use TelegramBot\Repositories\AdminsRepo;

class Installer
{
    private $logger;
    
    public function __construct()
    {
        try {
            $this->logger = new Logger();
        } catch (\Exception $e) {
            // Fallback if Logger fails
            $this->logger = null;
        }
    }
    
    /**
     * Check PHP extensions
     */
    public function checkExtensions()
    {
        $required = ['pdo_mysql', 'curl', 'openssl', 'json', 'mbstring'];
        $missing = [];
        
        foreach ($required as $ext) {
            if (!extension_loaded($ext)) {
                $missing[] = $ext;
            }
        }
        
        return [
            'ok' => empty($missing),
            'missing' => $missing,
        ];
    }
    
    /**
     * Create database tables
     */
    public function createTables($host, $db, $user, $pass)
    {
        try {
            $pdo = new \PDO(
                "mysql:host={$host};charset=utf8mb4",
                $user,
                $pass,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
            
            // Create database if not exists
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$db}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $pdo->exec("USE `{$db}`");
            
            // Read and execute schema
            $schemaFile = __DIR__ . '/../../sql/schema.sql';
            if (!file_exists($schemaFile)) {
                throw new \RuntimeException("Schema file not found: {$schemaFile}");
            }
            
            $schema = file_get_contents($schemaFile);
            
            // Remove comments but keep structure
            $lines = explode("\n", $schema);
            $cleanLines = [];
            foreach ($lines as $line) {
                $line = trim($line);
                // Skip empty lines and comment lines
                if (empty($line) || preg_match('/^--/', $line)) {
                    continue;
                }
                // Remove inline comments
                $line = preg_replace('/--.*$/', '', $line);
                if (!empty($line)) {
                    $cleanLines[] = $line;
                }
            }
            
            $cleanSchema = implode("\n", $cleanLines);
            
            // Split by semicolon but be careful with multi-line statements
            $statements = [];
            $currentStatement = '';
            
            foreach ($cleanLines as $line) {
                $currentStatement .= $line . "\n";
                if (preg_match('/;\s*$/', $line)) {
                    $stmt = trim($currentStatement);
                    if (!empty($stmt)) {
                        $statements[] = $stmt;
                    }
                    $currentStatement = '';
                }
            }
            
            // If there's a statement without trailing semicolon
            if (!empty(trim($currentStatement))) {
                $statements[] = trim($currentStatement);
            }
            
            $executedCount = 0;
            $errorCount = 0;
            $errors = [];
            
            foreach ($statements as $statement) {
                $statement = trim($statement);
                if (empty($statement)) {
                    continue;
                }
                
                // Skip SET commands that might fail
                if (stripos($statement, 'SET SQL_MODE') !== false || 
                    stripos($statement, 'SET time_zone') !== false) {
                    continue;
                }
                
                try {
                    $pdo->exec($statement);
                    $executedCount++;
                } catch (\PDOException $e) {
                    $errorMsg = $e->getMessage();
                    
                    // Only log real errors, not "already exists"
                    if (strpos($errorMsg, 'already exists') === false && 
                        strpos($errorMsg, 'Duplicate') === false &&
                        strpos($errorMsg, 'Duplicate entry') === false) {
                        $errors[] = substr($statement, 0, 100) . '... | Error: ' . $errorMsg;
                        $errorCount++;
                        
                        if ($this->logger) {
                            $this->logger->error("SQL execution error: {$errorMsg} | Statement: " . substr($statement, 0, 200));
                        }
                        
                        // For critical errors, throw
                        if (stripos($errorMsg, 'syntax') !== false || 
                            stripos($errorMsg, 'parse') !== false) {
                            throw $e;
                        }
                    }
                }
            }
            
            // Log summary
            if ($this->logger) {
                $this->logger->info("Schema execution: {$executedCount} statements executed, {$errorCount} errors");
            }
            
            // If too many errors, fail
            if ($errorCount > 0 && count($errors) > 3) {
                throw new \RuntimeException("Multiple SQL errors occurred. Check logs for details.");
            }
            
            // Execute seed
            $seedFile = __DIR__ . '/../../sql/seed.sql';
            if (file_exists($seedFile)) {
                $seed = file_get_contents($seedFile);
                
                // Remove comments
                $seed = preg_replace('/--.*$/m', '', $seed);
                $seed = preg_replace('/\/\*.*?\*\//s', '', $seed);
                $seedStatements = explode(';', $seed);
                
                foreach ($seedStatements as $statement) {
                    $statement = trim($statement);
                    if (!empty($statement)) {
                        try {
                            $pdo->exec($statement . ';');
                        } catch (\PDOException $e) {
                            // Ignore duplicate key errors for seed data
                            if (strpos($e->getMessage(), 'Duplicate') === false && 
                                strpos($e->getMessage(), 'already exists') === false) {
                                if ($this->logger) {
                                    $this->logger->warning("Seed execution warning: " . $e->getMessage());
                                }
                            }
                        }
                    }
                }
            }
            
            return ['ok' => true];
        } catch (\Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Create admin user
     */
    public function createAdmin($username, $email, $password, $role = 'owner')
    {
        try {
            $db = Config::getDb();
            $adminsRepo = new AdminsRepo();
            
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $adminsRepo->create($username, $email, $passwordHash, $role);
            
            return ['ok' => true];
        } catch (\Exception $e) {
            return ['ok' => false, 'error' => $e->getMessage()];
        }
    }
    
    /**
     * Write .env.php file
     */
    public function writeEnvFile($data)
    {
        $envPath = __DIR__ . '/../../config/.env.php';
        $envDir = dirname($envPath);
        
        if (!is_dir($envDir)) {
            @mkdir($envDir, 0755, true);
        }
        
        $content = "<?php\n";
        $content .= "/**\n";
        $content .= " * Environment Configuration\n";
        $content .= " * Generated by installer\n";
        $content .= " */\n\n";
        $content .= "return [\n";
        
        foreach ($data as $key => $value) {
            if (is_bool($value)) {
                $value = $value ? 'true' : 'false';
            } elseif (is_string($value)) {
                $value = "'" . addslashes($value) . "'";
            } else {
                $value = var_export($value, true);
            }
            $content .= "    '{$key}' => {$value},\n";
        }
        
        $content .= "];\n";
        
        return file_put_contents($envPath, $content) !== false;
    }
    
    /**
     * Mark installation as complete
     */
    public function markInstalled()
    {
        try {
            $db = Config::getDb();
            
            // Check if install_flag table exists and has a row
            $stmt = $db->query("SELECT COUNT(*) as cnt FROM install_flag");
            $result = $stmt->fetch();
            
            if ($result && $result['cnt'] > 0) {
                $stmt = $db->prepare("UPDATE install_flag SET installed = 1, installed_at = CURRENT_TIMESTAMP");
            } else {
                $stmt = $db->prepare("INSERT INTO install_flag (installed, installed_at) VALUES (1, CURRENT_TIMESTAMP)");
            }
            
            return $stmt->execute();
        } catch (\Exception $e) {
            if ($this->logger) {
                $this->logger->error("Failed to mark installation: " . $e->getMessage());
            }
            return false;
        }
    }
    
    /**
     * Check if already installed
     */
    public function isInstalled()
    {
        try {
            $envPath = __DIR__ . '/../../config/.env.php';
            if (!file_exists($envPath)) {
                return false;
            }
            
            // Try to load config without throwing exception
            try {
                Config::load();
                return Config::isInstalled();
            } catch (\Exception $e) {
                // Config exists but can't be loaded - not installed yet
                return false;
            }
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Generate random string
     */
    public static function generateRandomString($length = 32)
    {
        return bin2hex(random_bytes($length / 2));
    }
}

