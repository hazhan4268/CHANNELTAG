<?php

namespace TelegramBot;

class Config
{
    private static $config = null;
    private static $env = null;
    
    /**
     * Load configuration from .env.php
     */
    public static function load()
    {
        if (self::$env === null) {
            $envPath = __DIR__ . '/../config/.env.php';
            if (file_exists($envPath)) {
                self::$env = require $envPath;
            } else {
                throw new \RuntimeException('.env.php file not found. Please run the installer.');
            }
        }
        return self::$env;
    }
    
    /**
     * Get a configuration value
     */
    public static function get($key, $default = null)
    {
        if (self::$env === null) {
            try {
                self::load();
            } catch (\Exception $e) {
                // If .env.php doesn't exist, return default
                return $default;
            }
        }
        return self::$env[$key] ?? $default;
    }
    
    /**
     * Set a configuration value (runtime)
     */
    public static function set($key, $value)
    {
        if (self::$env === null) {
            self::load();
        }
        self::$env[$key] = $value;
    }
    
    /**
     * Get database PDO connection
     */
    public static function getDb()
    {
        static $pdo = null;
        
        if ($pdo === null) {
            // Ensure config is loaded
            if (self::$env === null) {
                self::load();
            }
            
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=utf8mb4',
                self::get('DB_HOST', 'localhost'),
                self::get('DB_NAME')
            );
            
            $options = [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $pdo = new \PDO(
                $dsn,
                self::get('DB_USER'),
                self::get('DB_PASS'),
                $options
            );
        }
        
        return $pdo;
    }
    
    /**
     * Check if installation is complete
     */
    public static function isInstalled()
    {
        try {
            // Check if .env.php exists first
            $envPath = __DIR__ . '/../config/.env.php';
            if (!file_exists($envPath)) {
                return false;
            }
            
            // Try to load config
            try {
                self::load();
            } catch (\Exception $e) {
                return false;
            }
            
            // Check database
            $db = self::getDb();
            $stmt = $db->query("SELECT installed FROM install_flag LIMIT 1");
            $result = $stmt->fetch();
            return $result && (bool)$result['installed'];
        } catch (\Exception $e) {
            return false;
        }
    }
}

