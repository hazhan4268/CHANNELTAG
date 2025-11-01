<?php
/**
 * Set Webhook Helper
 */

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

Bootstrap::init();

// Check if installed
if (!Config::isInstalled()) {
    http_response_code(403);
    die('Installation required');
}

// Only allow POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Method not allowed');
}

$token = Config::get('BOT_TOKEN');
$secretToken = Config::get('SECRET_TOKEN');
$baseUrl = Config::get('BASE_URL');
$webhookSlug = Config::get('WEBHOOK_SLUG');

$webhookUrl = rtrim($baseUrl, '/') . "/webhook/{$webhookSlug}";

$logger = new Logger();
$telegram = new TelegramClient($token, $logger);

$result = $telegram->setWebhook(
    $webhookUrl,
    $secretToken,
    ['channel_post', 'edited_channel_post']
);

header('Content-Type: application/json');
echo json_encode([
    'success' => isset($result['ok']) && $result['ok'],
    'result' => $result,
    'webhook_url' => $webhookUrl,
], JSON_PRETTY_PRINT);

