<?php

namespace TelegramBot\Repositories;

use TelegramBot\Config;

class SettingsRepo
{
    private $db;
    
    public function __construct()
    {
        try {
            $this->db = Config::getDb();
        } catch (\Exception $e) {
            error_log("SettingsRepo constructor error: " . $e->getMessage());
            throw new \RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get setting value
     */
    public function get($key, $default = null)
    {
        $stmt = $this->db->prepare("SELECT v FROM settings WHERE k = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['v'] : $default;
    }
    
    /**
     * Set setting value
     */
    public function set($key, $value)
    {
        $stmt = $this->db->prepare("
            INSERT INTO settings (k, v) VALUES (?, ?)
            ON DUPLICATE KEY UPDATE v = VALUES(v), updated_at = CURRENT_TIMESTAMP
        ");
        return $stmt->execute([$key, $value]);
    }
    
    /**
     * Get all settings
     */
    public function getAll()
    {
        $stmt = $this->db->query("SELECT k, v FROM settings");
        $results = $stmt->fetchAll();
        $settings = [];
        foreach ($results as $row) {
            $settings[$row['k']] = $row['v'];
        }
        return $settings;
    }
    
    /**
     * Delete setting
     */
    public function delete($key)
    {
        $stmt = $this->db->prepare("DELETE FROM settings WHERE k = ?");
        return $stmt->execute([$key]);
    }
}

