<?php

namespace TelegramBot\Controllers;

use TelegramBot\Config;
use TelegramBot\Logger;
use TelegramBot\Services\TelegramClient;
use TelegramBot\Repositories\SettingsRepo;
use TelegramBot\Repositories\ChannelsRepo;
use TelegramBot\Repositories\TagsRepo;
use TelegramBot\Repositories\PostsRepo;
use TelegramBot\Services\TagService;
use TelegramBot\Services\TemplateRenderer;

class AdminController
{
    private $settingsRepo;
    private $channelsRepo;
    private $tagsRepo;
    private $postsRepo;
    private $tagService;
    private $logger;
    
    public function __construct()
    {
        try {
            $this->settingsRepo = new SettingsRepo();
            $this->channelsRepo = new ChannelsRepo();
            $this->tagsRepo = new TagsRepo();
            $this->postsRepo = new PostsRepo();
            $this->tagService = new TagService($this->tagsRepo);
            $this->logger = new Logger();
        } catch (\Exception $e) {
            error_log("AdminController constructor error: " . $e->getMessage());
            // Don't throw exception, let the individual methods handle it
            $this->logger = new Logger();
        }
    }
    
    /**
     * Dashboard
     */
    public function dashboard()
    {
        try {
            $stats = [
                'total_posts' => $this->postsRepo ? count($this->postsRepo->getRecent(1000)) : 0,
                'sent_posts' => $this->postsRepo ? count($this->postsRepo->getByStatus('sent', 1000)) : 0,
                'error_posts' => $this->postsRepo ? count($this->postsRepo->getByStatus('error', 1000)) : 0,
                'channels' => $this->channelsRepo ? count($this->channelsRepo->getAll()) : 0,
                'tags' => $this->tagsRepo ? count($this->tagsRepo->getAll('tag')) : 0,
                'ids' => $this->tagsRepo ? count($this->tagsRepo->getAll('id')) : 0,
            ];
            
            $recentPosts = $this->postsRepo ? $this->postsRepo->getRecent(20) : [];
            $globalOn = $this->settingsRepo ? (bool)$this->settingsRepo->get('global_on', '1') : true;
            
            $this->render('dashboard', [
                'stats' => $stats,
                'recentPosts' => $recentPosts,
                'globalOn' => $globalOn,
            ]);
        } catch (\Exception $e) {
            $this->logger->error("Dashboard error: " . $e->getMessage());
            $this->render('dashboard', [
                'stats' => ['total_posts' => 0, 'sent_posts' => 0, 'error_posts' => 0, 'channels' => 0, 'tags' => 0, 'ids' => 0],
                'recentPosts' => [],
                'globalOn' => true,
                'error' => 'خطا در بارگذاری داده‌ها: ' . $e->getMessage(),
            ]);
        }
    }
    
    /**
     * Settings page
     */
    public function settings()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token';
            } else {
                // Update settings
                $this->settingsRepo->set('global_on', isset($_POST['global_on']) ? '1' : '0');
                $this->settingsRepo->set('parse_mode', $_POST['parse_mode'] ?? 'MarkdownV2');
                $this->settingsRepo->set('locale', $_POST['locale'] ?? 'fa');
                $this->settingsRepo->set('tag_separator', $_POST['tag_separator'] ?? ' · ');
                $this->settingsRepo->set('id_separator', $_POST['id_separator'] ?? ' | ');
                
                header('Location: /admin/settings.php?success=1');
                exit;
            }
        }
        
        $settings = [
            'global_on' => $this->settingsRepo->get('global_on', '1'),
            'parse_mode' => $this->settingsRepo->get('parse_mode', 'MarkdownV2'),
            'locale' => $this->settingsRepo->get('locale', 'fa'),
            'tag_separator' => $this->settingsRepo->get('tag_separator', ' · '),
            'id_separator' => $this->settingsRepo->get('id_separator', ' | '),
        ];
        
        $this->render('settings', [
            'settings' => $settings,
            'error' => $error ?? null,
            'success' => isset($_GET['success']),
        ]);
    }
    
    /**
     * Channels management
     */
    public function channels()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token';
            } else {
                $action = $_POST['action'] ?? '';
                
                if ($action === 'add') {
                    $chatId = $_POST['chat_id'] ?? '';
                    $username = $_POST['username'] ?? '';
                    $enabled = isset($_POST['enabled']);
                    $template = $_POST['template'] ?? null;
                    
                    if ($chatId) {
                        $this->channelsRepo->create($chatId, $username, $enabled, $template);
                        header('Location: /admin/channels.php?success=1');
                        exit;
                    }
                } elseif ($action === 'toggle') {
                    $id = $_POST['id'] ?? 0;
                    if ($id) {
                        $this->channelsRepo->toggle($id);
                    }
                    header('Location: /admin/channels.php');
                    exit;
                } elseif ($action === 'delete') {
                    $id = $_POST['id'] ?? 0;
                    if ($id) {
                        $this->channelsRepo->delete($id);
                    }
                    header('Location: /admin/channels.php?success=1');
                    exit;
                }
            }
        }
        
        $channels = $this->channelsRepo->getAll();
        
        $this->render('channels', [
            'channels' => $channels,
            'error' => $error ?? null,
            'success' => isset($_GET['success']),
        ]);
    }
    
    /**
     * Tags management
     */
    public function tags()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token';
            } else {
                $action = $_POST['action'] ?? '';
                
                if ($action === 'add') {
                    $type = $_POST['type'] ?? 'tag';
                    $value = $_POST['value'] ?? '';
                    $enabled = isset($_POST['enabled']);
                    $sort = (int)($_POST['sort'] ?? 0);
                    
                    if ($value) {
                        $this->tagService->create($type, $value, $enabled, $sort);
                        header('Location: /admin/tags.php?success=1');
                        exit;
                    }
                } elseif ($action === 'toggle') {
                    $id = $_POST['id'] ?? 0;
                    if ($id) {
                        $this->tagService->toggle($id);
                    }
                    header('Location: /admin/tags.php');
                    exit;
                } elseif ($action === 'delete') {
                    $id = $_POST['id'] ?? 0;
                    if ($id) {
                        $this->tagService->delete($id);
                    }
                    header('Location: /admin/tags.php?success=1');
                    exit;
                }
            }
        }
        
        $tags = $this->tagsRepo->getAll('tag');
        $ids = $this->tagsRepo->getAll('id');
        
        $this->render('tags', [
            'tags' => $tags,
            'ids' => $ids,
            'error' => $error ?? null,
            'success' => isset($_GET['success']),
        ]);
    }
    
    /**
     * Template editor
     */
    public function template()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!AuthController::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
                $error = 'Invalid CSRF token';
            } else {
                $template = $_POST['template'] ?? '';
                $this->settingsRepo->set('default_template', $template);
                header('Location: /admin/template.php?success=1');
                exit;
            }
        }
        
        $template = $this->settingsRepo->get('default_template', '');
        $preview = '';
        
        // Preview if POST with preview action
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'preview') {
            $previewTemplate = $_POST['template'] ?? $template;
            $tagsRepo = $this->tagsRepo;
            $templateRenderer = new TemplateRenderer($tagsRepo, $this->settingsRepo);
            
            $context = [
                'channel_username' => '@example',
                'channel_id' => '123456789',
                'message_id' => '42',
                'date' => date('Y-m-d H:i:s'),
                'post_type' => 'text',
                'permalink' => 'https://t.me/example/42',
            ];
            
            $preview = $templateRenderer->render($previewTemplate, $context);
            
            // Don't save, just show preview
            $this->render('template', [
                'template' => $template,
                'preview' => $preview,
                'error' => null,
                'success' => false,
            ]);
            return;
        }
        
        $this->render('template', [
            'template' => $template,
            'preview' => $preview,
            'error' => $error ?? null,
            'success' => isset($_GET['success']),
        ]);
    }
    
    /**
     * Health check page
     */
    public function health()
    {
        $health = [
            'db' => 'ok',
            'webhook' => 'unknown',
            'version' => '2.0',
        ];
        
        // Check DB
        try {
            Config::getDb();
            $health['db'] = 'ok';
        } catch (\Exception $e) {
            $health['db'] = 'error';
            $health['db_error'] = $e->getMessage();
        }
        
        // Check webhook
        try {
            $token = Config::get('BOT_TOKEN');
            $telegram = new TelegramClient($token, $this->logger);
            $webhookInfo = $telegram->getWebhookInfo();
            
            if ($webhookInfo && isset($webhookInfo['ok']) && $webhookInfo['ok']) {
                $health['webhook'] = $webhookInfo['result'] ?? [];
            } else {
                $health['webhook'] = 'error';
                $health['webhook_error'] = $webhookInfo['description'] ?? 'Unknown error';
            }
        } catch (\Exception $e) {
            $health['webhook'] = 'error';
            $health['webhook_error'] = $e->getMessage();
        }
        
        $this->render('health', [
            'health' => $health,
        ]);
    }
    
    /**
     * Render template
     */
    private function render($template, array $data = [])
    {
        extract($data);
        ob_start();
        include __DIR__ . "/../../templates/admin/{$template}.php";
        $content = ob_get_clean();
        include __DIR__ . "/../../templates/admin/layout.php";
    }
}

