<?php

namespace TelegramBot\Repositories;

use TelegramBot\Config;

class PostsRepo
{
    private $db;
    
    public function __construct()
    {
        try {
            $this->db = Config::getDb();
        } catch (\Exception $e) {
            error_log("PostsRepo constructor error: " . $e->getMessage());
            throw new \RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Find post by chat_id and message_id
     */
    public function findByChatAndMessage($chatId, $messageId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM posts 
            WHERE chat_id = ? AND message_id = ?
            LIMIT 1
        ");
        $stmt->execute([$chatId, $messageId]);
        return $stmt->fetch();
    }
    
    /**
     * Create or update post record
     */
    public function createOrUpdate($chatId, $messageId, $status = 'sent', $error = null)
    {
        $existing = $this->findByChatAndMessage($chatId, $messageId);
        
        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE posts 
                SET status = ?, error_message = ?, updated_at = CURRENT_TIMESTAMP
                WHERE chat_id = ? AND message_id = ?
            ");
            return $stmt->execute([$status, $error, $chatId, $messageId]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO posts (chat_id, message_id, status, error_message)
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$chatId, $messageId, $status, $error]);
        }
    }
    
    /**
     * Count recent posts by chat_id (for rate limiting)
     */
    public function countRecentByChat($chatId, $since)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM posts
            WHERE chat_id = ? AND created_at >= ? AND status = 'sent'
        ");
        $stmt->execute([$chatId, $since]);
        $result = $stmt->fetch();
        return (int)($result['count'] ?? 0);
    }
    
    /**
     * Get recent posts
     */
    public function getRecent($limit = 20)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM posts 
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get posts by status
     */
    public function getByStatus($status, $limit = 50)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM posts 
            WHERE status = ?
            ORDER BY created_at DESC 
            LIMIT ?
        ");
        $stmt->execute([$status, $limit]);
        return $stmt->fetchAll();
    }
}

