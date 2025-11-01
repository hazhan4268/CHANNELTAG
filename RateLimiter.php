<?php

namespace TelegramBot\Services;

use TelegramBot\Config;
use TelegramBot\Repositories\PostsRepo;
use TelegramBot\Repositories\SettingsRepo;

class RateLimiter
{
    private $postsRepo;
    private $settingsRepo;
    private $maxPerMinute;
    
    public function __construct(PostsRepo $postsRepo, SettingsRepo $settingsRepo = null)
    {
        $this->postsRepo = $postsRepo;
        $this->settingsRepo = $settingsRepo ?? new SettingsRepo();
        $this->maxPerMinute = (int)($this->settingsRepo->get('rate_limit_per_minute', '20'));
    }
    
    /**
     * Check if we can send a reply (rate limit + duplicate check)
     */
    public function canSend($chatId, $messageId)
    {
        // Check for duplicate first
        $existing = $this->postsRepo->findByChatAndMessage($chatId, $messageId);
        if ($existing && $existing['status'] === 'sent') {
            return ['allowed' => false, 'reason' => 'duplicate'];
        }
        
        // Check rate limit (leaky bucket in DB)
        $minuteAgo = date('Y-m-d H:i:s', strtotime('-1 minute'));
        $recentCount = $this->postsRepo->countRecentByChat($chatId, $minuteAgo);
        
        if ($recentCount >= $this->maxPerMinute) {
            return ['allowed' => false, 'reason' => 'rate_limit'];
        }
        
        return ['allowed' => true];
    }
    
    /**
     * Mark as sent
     */
    public function markSent($chatId, $messageId, $status = 'sent', $error = null)
    {
        return $this->postsRepo->createOrUpdate($chatId, $messageId, $status, $error);
    }
}

