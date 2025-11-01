<?php

namespace TelegramBot\Repositories;

use TelegramBot\Config;

class TagsRepo
{
    private $db;
    
    public function __construct()
    {
        try {
            $this->db = Config::getDb();
        } catch (\Exception $e) {
            error_log("TagsRepo constructor error: " . $e->getMessage());
            throw new \RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Get all tags/ids
     */
    public function getAll($type = null)
    {
        if ($type) {
            $stmt = $this->db->prepare("SELECT * FROM tags WHERE type = ? ORDER BY sort ASC, id ASC");
            $stmt->execute([$type]);
        } else {
            $stmt = $this->db->query("SELECT * FROM tags ORDER BY type ASC, sort ASC, id ASC");
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Get enabled tags/ids by type
     */
    public function getEnabledByType($type = null)
    {
        if ($type) {
            $stmt = $this->db->prepare("SELECT * FROM tags WHERE type = ? AND enabled = 1 ORDER BY sort ASC, id ASC");
            $stmt->execute([$type]);
        } else {
            $stmt = $this->db->query("SELECT * FROM tags WHERE enabled = 1 ORDER BY type ASC, sort ASC, id ASC");
        }
        return $stmt->fetchAll();
    }
    
    /**
     * Find by ID
     */
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tags WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create tag/id
     */
    public function create($type, $value, $enabled = true, $sort = 0)
    {
        $stmt = $this->db->prepare("
            INSERT INTO tags (type, value, enabled, sort)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$type, $value, $enabled ? 1 : 0, $sort]);
    }
    
    /**
     * Update tag/id
     */
    public function update($id, array $data)
    {
        $allowed = ['type', 'value', 'enabled', 'sort'];
        $updates = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $updates[] = "`{$key}` = ?";
                if ($key === 'enabled') {
                    $params[] = $value ? 1 : 0;
                } else {
                    $params[] = $value;
                }
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE tags SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Delete tag/id
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM tags WHERE id = ?");
        return $stmt->execute([$id]);
    }
}

