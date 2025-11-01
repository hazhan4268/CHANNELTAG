<?php
/**
 * Webhook Test Page
 * تست وب‌هوک
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Load autoloader
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../src/autoload.php';
}

require_once __DIR__ . '/../src/Bootstrap.php';
require_once __DIR__ . '/../src/Config.php';
require_once __DIR__ . '/../src/Services/TelegramClient.php';
require_once __DIR__ . '/../src/Logger.php';

use TelegramBot\Bootstrap;
use TelegramBot\Config;
use TelegramBot\Services\TelegramClient;
use TelegramBot\Logger;

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تست وب‌هوک</title>
    <style>
        body { font-family: Tahoma, Arial, sans-serif; padding: 20px; background: #f5f5f5; }
        .container { max-width: 800px; margin: 0 auto; background: white; padding: 30px; border-radius: 8px; }
        h1 { color: #333; }
        .test-result { padding: 15px; margin: 10px 0; border-radius: 4px; }
        .success { background: #d4edda; color: #155724; border-right: 4px solid #28a745; }
        .error { background: #f8d7da; color: #721c24; border-right: 4px solid #dc3545; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔍 تست وب‌هوک</h1>
        
        <?php
        try {
            Bootstrap::init();
            
            echo '<div class="test-result success">✓ Bootstrap initialized</div>';
            
            // Test Config
            $config = Config::load();
            echo '<div class="test-result success">✓ Config loaded</div>';
            
            $botToken = Config::get('BOT_TOKEN');
            $webhookSlug = Config::get('WEBHOOK_SLUG');
            $secretToken = Config::get('SECRET_TOKEN');
            $baseUrl = Config::get('BASE_URL');
            
            if (empty($botToken)) {
                echo '<div class="test-result error">✗ Bot Token not set</div>';
            } else {
                echo '<div class="test-result success">✓ Bot Token: ' . substr($botToken, 0, 10) . '...' . '</div>';
            }
            
            if (empty($webhookSlug)) {
                echo '<div class="test-result error">✗ Webhook Slug not set</div>';
            } else {
                echo '<div class="test-result success">✓ Webhook Slug: ' . htmlspecialchars($webhookSlug) . '</div>';
            }
            
            $webhookUrl = rtrim($baseUrl, '/') . "/webhook/{$webhookSlug}";
            echo '<div class="test-result success"><strong>Webhook URL:</strong><br><code>' . htmlspecialchars($webhookUrl) . '</code></div>';
            
            // Test Telegram API
            if (!empty($botToken)) {
                $logger = new Logger();
                $telegram = new TelegramClient($botToken, $logger);
                
                $botInfo = $telegram->getMe();
                if ($botInfo && isset($botInfo['ok']) && $botInfo['ok']) {
                    echo '<div class="test-result success">✓ Bot Info: ' . htmlspecialchars($botInfo['result']['first_name'] ?? 'N/A') . ' (@' . htmlspecialchars($botInfo['result']['username'] ?? 'N/A') . ')</div>';
                } else {
                    echo '<div class="test-result error">✗ Failed to get bot info</div>';
                }
                
                $webhookInfo = $telegram->getWebhookInfo();
                if ($webhookInfo && isset($webhookInfo['ok']) && $webhookInfo['ok']) {
                    $info = $webhookInfo['result'];
                    echo '<div class="test-result success">';
                    echo '<strong>Webhook Info:</strong><br>';
                    echo 'URL: ' . htmlspecialchars($info['url'] ?? 'Not set') . '<br>';
                    echo 'Pending Updates: ' . ($info['pending_update_count'] ?? 0) . '<br>';
                    if (isset($info['last_error_date'])) {
                        echo 'Last Error: ' . date('Y-m-d H:i:s', $info['last_error_date']) . '<br>';
                        echo 'Error Message: ' . htmlspecialchars($info['last_error_message'] ?? '') . '<br>';
                    }
                    echo '</div>';
                } else {
                    echo '<div class="test-result error">✗ Failed to get webhook info</div>';
                }
            }
            
            // Test Settings
            require_once __DIR__ . '/../src/Repositories/SettingsRepo.php';
            $settingsRepo = new \TelegramBot\Repositories\SettingsRepo();
            $globalOn = $settingsRepo->get('global_on', '1');
            echo '<div class="test-result ' . ($globalOn === '1' ? 'success' : 'error') . '">';
            echo 'Global Toggle: ' . ($globalOn === '1' ? 'ON ✓' : 'OFF ✗');
            echo '</div>';
            
            // Test Channels
            require_once __DIR__ . '/../src/Repositories/ChannelsRepo.php';
            $channelsRepo = new \TelegramBot\Repositories\ChannelsRepo();
            $channels = $channelsRepo->getEnabled();
            echo '<div class="test-result ' . (count($channels) > 0 ? 'success' : 'error') . '">';
            echo 'Enabled Channels: ' . count($channels);
            if (count($channels) > 0) {
                echo '<br>';
                foreach ($channels as $ch) {
                    echo '  - Chat ID: ' . $ch['chat_id'] . ($ch['username'] ? ' (@' . $ch['username'] . ')' : '') . '<br>';
                }
            } else {
                echo '<br><small>هیچ کانالی فعال نیست! لطفاً از پنل مدیریت کانال اضافه کنید.</small>';
            }
            echo '</div>';
            
            // Test Template
            $template = $settingsRepo->get('default_template', '');
            if (empty($template)) {
                echo '<div class="test-result error">✗ Template not set! Please set a template in admin panel.</div>';
            } else {
                echo '<div class="test-result success">✓ Template is set</div>';
            }
            
            // Test Tags
            require_once __DIR__ . '/../src/Repositories/TagsRepo.php';
            $tagsRepo = new \TelegramBot\Repositories\TagsRepo();
            $tags = $tagsRepo->getEnabledByType('tag');
            $ids = $tagsRepo->getEnabledByType('id');
            echo '<div class="test-result success">';
            echo 'Tags: ' . count($tags) . ' enabled<br>';
            echo 'IDs: ' . count($ids) . ' enabled';
            echo '</div>';
            
        } catch (\Exception $e) {
            echo '<div class="test-result error">';
            echo '✗ Error: ' . htmlspecialchars($e->getMessage()) . '<br>';
            echo 'File: ' . htmlspecialchars($e->getFile()) . '<br>';
            echo 'Line: ' . $e->getLine() . '<br>';
            echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
            echo '</div>';
        }
        ?>
        
        <div style="margin-top: 30px;">
            <a href="/admin/">← بازگشت به پنل مدیریت</a>
        </div>
    </div>
</body>
</html>

