<?php

namespace TelegramBot\Repositories;

use TelegramBot\Config;

class AdminsRepo
{
    private $db;
    
    public function __construct()
    {
        try {
            $this->db = Config::getDb();
        } catch (\Exception $e) {
            error_log("AdminsRepo constructor error: " . $e->getMessage());
            throw new \RuntimeException("Database connection failed: " . $e->getMessage());
        }
    }
    
    /**
     * Find admin by username or email
     */
    public function findByUsernameOrEmail($usernameOrEmail)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM admins 
            WHERE username = ? OR email = ?
            LIMIT 1
        ");
        $stmt->execute([$usernameOrEmail, $usernameOrEmail]);
        return $stmt->fetch();
    }
    
    /**
     * Find by ID
     */
    public function find($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM admins WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create admin
     */
    public function create($username, $email, $passwordHash, $role = 'admin')
    {
        $stmt = $this->db->prepare("
            INSERT INTO admins (username, email, password_hash, role)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$username, $email, $passwordHash, $role]);
    }
    
    /**
     * Verify password
     */
    public function verifyPassword($admin, $password)
    {
        return password_verify($password, $admin['password_hash']);
    }
    
    /**
     * Get all admins
     */
    public function getAll()
    {
        $stmt = $this->db->query("SELECT id, username, email, role, created_at FROM admins ORDER BY created_at DESC");
        return $stmt->fetchAll();
    }
}

