<?php

namespace TelegramBot\Repositories;

use TelegramBot\Config;

class ChannelsRepo
{
    private $db;
    
    public function __construct()
    {
        try {
            $this->db = Config::getDb();
        } catch (\Exception $e) {
            error_log("ChannelsRepo constructor error: " . $e->getMessage());
            throw new \RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Find channel by chat_id or username
     */
    public function findByChatOrUsername($chatId = null, $username = null)
    {
        if ($chatId) {
            $stmt = $this->db->prepare("SELECT * FROM channels WHERE chat_id = ?");
            $stmt->execute([$chatId]);
            $result = $stmt->fetch();
            if ($result) return $result;
        }
        
        if ($username) {
            $stmt = $this->db->prepare("SELECT * FROM channels WHERE username = ?");
            $stmt->execute([ltrim($username, '@')]);
            return $stmt->fetch();
        }
        
        return null;
    }
    
    /**
     * Get all channels
     */
    public function getAll()
    {
        $stmt = $this->db->query("SELECT * FROM channels ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Get enabled channels
     */
    public function getEnabled()
    {
        $stmt = $this->db->query("SELECT * FROM channels WHERE enabled = 1 ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
    
    /**
     * Create channel
     */
    public function create($chatId, $username = null, $enabled = true, $template = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO channels (chat_id, username, enabled, per_channel_template)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$chatId, $username, $enabled ? 1 : 0, $template]);
    }
    
    /**
     * Update channel
     */
    public function update($id, array $data)
    {
        $allowed = ['chat_id', 'username', 'enabled', 'per_channel_template'];
        $updates = [];
        $params = [];
        
        foreach ($data as $key => $value) {
            if (in_array($key, $allowed)) {
                $updates[] = "`{$key}` = ?";
                $params[] = $value;
            }
        }
        
        if (empty($updates)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE channels SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Delete channel
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM channels WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Toggle enabled status
     */
    public function toggle($id)
    {
        $channel = $this->find($id);
        if ($channel) {
            return $this->update($id, ['enabled' => !$channel['enabled']]);
        }
        return false;
    }
    
    /**
     * Find by ID
     */
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM channels WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
}

