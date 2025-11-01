<?php

namespace TelegramBot\Controllers;

use TelegramBot\Config;
use TelegramBot\Logger;
use TelegramBot\Services\TelegramClient;
use TelegramBot\Services\TemplateRenderer;
use TelegramBot\Services\RateLimiter;
use TelegramBot\Repositories\SettingsRepo;
use TelegramBot\Repositories\ChannelsRepo;
use TelegramBot\Repositories\PostsRepo;
use TelegramBot\Repositories\TagsRepo;

class WebhookController
{
    private $telegram;
    private $templateRenderer;
    private $rateLimiter;
    private $settingsRepo;
    private $channelsRepo;
    private $postsRepo;
    private $logger;
    
    public function __construct()
    {
        $token = Config::get('BOT_TOKEN');
        $this->logger = new Logger();
        $this->telegram = new TelegramClient($token, $this->logger);
        $this->settingsRepo = new SettingsRepo();
        $this->channelsRepo = new ChannelsRepo();
        $this->postsRepo = new PostsRepo();
        $tagsRepo = new TagsRepo();
        $this->templateRenderer = new TemplateRenderer($tagsRepo, $this->settingsRepo);
        $this->rateLimiter = new RateLimiter($this->postsRepo, $this->settingsRepo);
    }
    
    /**
     * Handle webhook request
     */
    public function handle()
    {
        // Verify secret token
        $secretToken = $_SERVER['HTTP_X_TELEGRAM_BOT_API_SECRET_TOKEN'] ?? '';
        $expectedToken = Config::get('SECRET_TOKEN');
        
        if ($secretToken !== $expectedToken) {
            http_response_code(401);
            $this->logger->error("Invalid secret token");
            exit;
        }
        
        // Only allow POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            exit;
        }
        
        // Parse JSON
        $input = file_get_contents('php://input');
        $update = json_decode($input, true);
        
        if (!$update) {
            http_response_code(400);
            $this->logger->error("Invalid JSON payload");
            exit;
        }
        
        // Handle channel_post or edited_channel_post
        $post = $update['channel_post'] ?? $update['edited_channel_post'] ?? null;
        
        if (!$post) {
            // Not a channel post, ignore
            http_response_code(200);
            exit;
        }
        
        $this->processChannelPost($post);
        
        http_response_code(200);
        exit;
    }
    
    /**
     * Process channel post
     */
    private function processChannelPost($post)
    {
        $chatId = $post['chat']['id'] ?? null;
        $messageId = $post['message_id'] ?? null;
        $username = $post['chat']['username'] ?? null;
        
        if (!$chatId || !$messageId) {
            $this->logger->warning("Missing chat_id or message_id");
            return;
        }
        
        // Check global toggle
        $globalOn = (bool)$this->settingsRepo->get('global_on', '1');
        if (!$globalOn) {
            $this->logger->debug("Global toggle is off");
            $this->postsRepo->createOrUpdate($chatId, $messageId, 'skipped', 'Global toggle off');
            return;
        }
        
        // Check if channel is allowed
        $channel = $this->channelsRepo->findByChatOrUsername($chatId, $username);
        
        if (!$channel) {
            $this->logger->debug("Channel not in allowlist: {$chatId}");
            $this->postsRepo->createOrUpdate($chatId, $messageId, 'skipped', 'Channel not allowed');
            return;
        }
        
        if (!$channel['enabled']) {
            $this->logger->debug("Channel disabled: {$chatId}");
            $this->postsRepo->createOrUpdate($chatId, $messageId, 'skipped', 'Channel disabled');
            return;
        }
        
        // Check rate limit and duplicates
        $canSend = $this->rateLimiter->canSend($chatId, $messageId);
        if (!$canSend['allowed']) {
            $reason = $canSend['reason'];
            $this->logger->debug("Cannot send: {$reason}");
            $this->postsRepo->createOrUpdate($chatId, $messageId, 'skipped', $reason);
            return;
        }
        
        // Get template (per-channel or default)
        $template = $channel['per_channel_template'] ?? 
                   $this->settingsRepo->get('default_template', '');
        
        if (empty($template)) {
            $this->logger->warning("No template configured for channel {$chatId}");
            $this->postsRepo->createOrUpdate($chatId, $messageId, 'skipped', 'No template');
            return;
        }
        
        // Build context
        $context = $this->buildContext($post, $username);
        
        // Render template
        $comment = $this->templateRenderer->render($template, $context);
        
        // Check if comment is empty after rendering
        if (empty(trim($comment))) {
            $this->logger->warning("Empty comment after template rendering for channel {$chatId}");
            $this->postsRepo->createOrUpdate($chatId, $messageId, 'skipped', 'Empty template result');
            return;
        }
        
        // Escape for parse mode if needed
        $parseMode = $this->settingsRepo->get('parse_mode', 'MarkdownV2');
        if ($parseMode === 'MarkdownV2' && !empty($comment)) {
            // Escape after variable replacement
            $comment = $this->templateRenderer->escapeMarkdownV2($comment);
        }
        
        // Send message
        $result = $this->telegram->sendMessage($chatId, $comment, [
            'reply_to_message_id' => $messageId,
            'parse_mode' => $parseMode,
            'disable_web_page_preview' => true,
        ]);
        
        if ($result && isset($result['ok']) && $result['ok']) {
            $this->rateLimiter->markSent($chatId, $messageId, 'sent');
            $this->logger->info("Reply sent successfully", [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'comment_length' => strlen($comment)
            ]);
        } else {
            $error = $result['description'] ?? 'Unknown error';
            $this->rateLimiter->markSent($chatId, $messageId, 'error', $error);
            $this->logger->error("Failed to send reply: {$error}", [
                'chat_id' => $chatId,
                'message_id' => $messageId,
                'comment' => substr($comment, 0, 100)
            ]);
        }
    }
    
    /**
     * Build template context
     */
    private function buildContext($post, $username)
    {
        $chatId = $post['chat']['id'];
        $messageId = $post['message_id'];
        $date = isset($post['date']) ? date('Y-m-d H:i:s', $post['date']) : date('Y-m-d H:i:s');
        
        // Determine post type
        $postType = 'text';
        if (isset($post['photo'])) $postType = 'photo';
        elseif (isset($post['video'])) $postType = 'video';
        elseif (isset($post['document'])) $postType = 'document';
        elseif (isset($post['audio'])) $postType = 'audio';
        elseif (isset($post['voice'])) $postType = 'voice';
        
        // Build permalink
        $permalink = '';
        if ($username) {
            $permalink = "https://t.me/{$username}/{$messageId}";
        }
        
        return [
            'channel_id' => (string)$chatId,
            'channel_username' => $username ? "@{$username}" : '',
            'message_id' => (string)$messageId,
            'date' => $date,
            'post_type' => $postType,
            'permalink' => $permalink,
            'custom1' => '',
            'custom2' => '',
            'custom3' => '',
        ];
    }
}

