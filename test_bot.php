<?php
/**
 * Simple Bot Test - Tests if bot token works
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🤖 تست ربات تلگرام</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} .info{color:blue;}</style>";

// Get bot token
$tokenSources = [
    'از فایل config/.env.php',
    'از متغیر محیطی',
    'ورود دستی'
];

$botToken = null;

// Try to load from config
if (file_exists(__DIR__ . '/../config/.env.php')) {
    $config = require __DIR__ . '/../config/.env.php';
    $botToken = $config['BOT_TOKEN'] ?? null;
    if ($botToken) {
        echo "<p class='info'>✓ توکن از فایل config یافت شد</p>";
    }
}

// If not in config, check if provided in URL
if (!$botToken && isset($_GET['token'])) {
    $botToken = $_GET['token'];
    echo "<p class='info'>✓ توکن از URL دریافت شد</p>";
}

// Show form if no token
if (!$botToken) {
    echo "<h2>توکن ربات را وارد کنید:</h2>";
    echo "<form method='GET'>";
    echo "<input type='text' name='token' placeholder='123456:ABC-DEF...' style='width:400px;padding:10px;' required>";
    echo "<button type='submit' style='padding:10px 20px;'>تست کن</button>";
    echo "</form>";
    echo "<p><small>برای دریافت توکن به <a href='https://t.me/BotFather' target='_blank'>@BotFather</a> در تلگرام بروید</small></p>";
    exit;
}

echo "<h2>1️⃣ تست اتصال به API تلگرام</h2>";

// Test getMe
$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.telegram.org/bot{$botToken}/getMe",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "<p class='error'>❌ خطای cURL: " . htmlspecialchars($error) . "</p>";
    exit;
}

$data = json_decode($response, true);

if ($httpCode !== 200) {
    echo "<p class='error'>❌ HTTP Error {$httpCode}</p>";
    echo "<pre>" . htmlspecialchars($response) . "</pre>";
    exit;
}

if (!isset($data['ok']) || !$data['ok']) {
    echo "<p class='error'>❌ خطای API: " . htmlspecialchars($data['description'] ?? 'Unknown') . "</p>";
    exit;
}

echo "<p class='success'>✅ اتصال به API موفق!</p>";
echo "<div style='background:#f0f0f0;padding:15px;border-radius:8px;margin:10px 0;'>";
echo "<strong>اطلاعات ربات:</strong><br>";
echo "نام: <strong>" . htmlspecialchars($data['result']['first_name']) . "</strong><br>";
echo "Username: <strong>@" . htmlspecialchars($data['result']['username']) . "</strong><br>";
echo "ID: <strong>" . htmlspecialchars($data['result']['id']) . "</strong><br>";
echo "Can Join Groups: " . ($data['result']['can_join_groups'] ? 'بله ✓' : 'خیر ✗') . "<br>";
echo "Can Read All Group Messages: " . ($data['result']['can_read_all_group_messages'] ? 'بله ✓' : 'خیر ✗') . "<br>";
echo "</div>";

// Test webhook info
echo "<h2>2️⃣ بررسی وضعیت Webhook</h2>";

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => "https://api.telegram.org/bot{$botToken}/getWebhookInfo",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 10,
]);

$response = curl_exec($ch);
curl_close($ch);

$webhookData = json_decode($response, true);

if ($webhookData && isset($webhookData['result'])) {
    $info = $webhookData['result'];
    
    echo "<div style='background:#f0f0f0;padding:15px;border-radius:8px;margin:10px 0;'>";
    
    if (empty($info['url'])) {
        echo "<p class='error'>❌ Webhook تنظیم نشده است</p>";
        echo "<p>ربات در حالت polling نیست و webhook هم ندارد. باید webhook را تنظیم کنید.</p>";
    } else {
        echo "<p class='success'>✅ Webhook فعال است</p>";
        echo "<strong>URL:</strong> " . htmlspecialchars($info['url']) . "<br>";
        echo "<strong>Has Custom Certificate:</strong> " . ($info['has_custom_certificate'] ? 'بله' : 'خیر') . "<br>";
        echo "<strong>Pending Update Count:</strong> " . ($info['pending_update_count'] ?? 0) . "<br>";
        
        if (isset($info['last_error_date'])) {
            echo "<p class='error'><strong>آخرین خطا:</strong> " . date('Y-m-d H:i:s', $info['last_error_date']) . "<br>";
            echo "<strong>پیام خطا:</strong> " . htmlspecialchars($info['last_error_message'] ?? '') . "</p>";
        } else {
            echo "<p class='success'>✅ بدون خطا</p>";
        }
        
        if (isset($info['allowed_updates'])) {
            echo "<strong>Allowed Updates:</strong> " . implode(', ', $info['allowed_updates']) . "<br>";
        }
    }
    
    echo "</div>";
}

// Test send message
echo "<h2>3️⃣ تست ارسال پیام (اختیاری)</h2>";

if (isset($_POST['test_chat_id'])) {
    $testChatId = $_POST['test_chat_id'];
    $testMessage = "🤖 تست ربات\n\nزمان: " . date('Y-m-d H:i:s') . "\nربات با موفقیت کار می‌کند!";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => "https://api.telegram.org/bot{$botToken}/sendMessage",
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode([
            'chat_id' => $testChatId,
            'text' => $testMessage,
        ]),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
        CURLOPT_TIMEOUT => 10,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    $result = json_decode($response, true);
    
    if ($result && $result['ok']) {
        echo "<p class='success'>✅ پیام با موفقیت ارسال شد!</p>";
    } else {
        echo "<p class='error'>❌ خطا در ارسال: " . htmlspecialchars($result['description'] ?? 'Unknown') . "</p>";
    }
}

echo "<form method='POST'>";
echo "<p>برای تست ارسال پیام، Chat ID خود را وارد کنید:</p>";
echo "<input type='hidden' name='token' value='" . htmlspecialchars($botToken) . "'>";
echo "<input type='text' name='test_chat_id' placeholder='Chat ID یا @username' style='width:300px;padding:10px;'>";
echo "<button type='submit' style='padding:10px 20px;'>ارسال پیام تست</button>";
echo "</form>";
echo "<p><small>برای پیدا کردن Chat ID خود به <a href='https://t.me/userinfobot' target='_blank'>@userinfobot</a> بروید</small></p>";

echo "<hr>";
echo "<h2>📋 خلاصه</h2>";

if (isset($data['ok']) && $data['ok']) {
    echo "<p class='success'>✅ توکن ربات معتبر است</p>";
    echo "<p class='success'>✅ ربات آماده استفاده است</p>";
    
    if (empty($info['url'] ?? null)) {
        echo "<p class='error'>⚠️ Webhook تنظیم نشده - باید از پنل ادمین webhook را تنظیم کنید</p>";
        echo "<p>مراحل بعدی:</p>";
        echo "<ol>";
        echo "<li>پروژه را نصب کنید: <a href='/install/'>برو به صفحه نصب</a></li>";
        echo "<li>بعد از نصب، webhook خودکار تنظیم می‌شود</li>";
        echo "<li>ربات را به کانال خود اضافه کنید</li>";
        echo "<li>کانال را از پنل ادمین اضافه کنید</li>";
        echo "</ol>";
    } else {
        echo "<p class='success'>✅ همه چیز آماده است!</p>";
    }
} else {
    echo "<p class='error'>❌ توکن ربات معتبر نیست</p>";
}

echo "<hr>";
echo "<h2>🔗 لینک‌های مفید</h2>";
echo "<ul>";
echo "<li><a href='/'>صفحه اصلی</a></li>";
echo "<li><a href='/install/'>نصب سیستم</a></li>";
echo "<li><a href='/admin/'>پنل مدیریت</a></li>";
echo "<li><a href='/admin/debug.php'>صفحه Debug</a></li>";
echo "<li><a href='https://core.telegram.org/bots/api' target='_blank'>مستندات API تلگرام</a></li>";
echo "</ul>";
?>