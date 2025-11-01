<?php
/**
 * Webhook Test Simulator
 * این فایل یک update تلگرامی جعلی می‌سازد و به webhook ارسال می‌کند
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>🔄 تست Webhook ربات</h1>";
echo "<style>body{font-family:Arial;margin:20px;} .success{color:green;} .error{color:red;} pre{background:#f0f0f0;padding:10px;border-radius:5px;}</style>";

// Load config if exists
$configPath = __DIR__ . '/../config/.env.php';
if (!file_exists($configPath)) {
    echo "<p class='error'>❌ سیستم نصب نشده است. لطفاً ابتدا نصب را انجام دهید: <a href='/install/'>نصب</a></p>";
    exit;
}

$config = require $configPath;
$webhookSlug = $config['WEBHOOK_SLUG'] ?? null;
$secretToken = $config['SECRET_TOKEN'] ?? null;
$baseUrl = $config['BASE_URL'] ?? '';

if (!$webhookSlug || !$secretToken) {
    echo "<p class='error'>❌ Webhook تنظیم نشده است</p>";
    exit;
}

$webhookUrl = rtrim($baseUrl, '/') . "/webhook/{$webhookSlug}";

echo "<h2>📍 اطلاعات Webhook</h2>";
echo "<div style='background:#f0f0f0;padding:15px;border-radius:8px;margin:10px 0;'>";
echo "<strong>URL:</strong> <code>" . htmlspecialchars($webhookUrl) . "</code><br>";
echo "<strong>Secret Token:</strong> <code>" . substr($secretToken, 0, 10) . "...</code><br>";
echo "</div>";

// Simulate webhook payload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'test') {
    
    echo "<h2>🚀 ارسال Update جعلی...</h2>";
    
    $testUpdate = [
        'update_id' => time(),
        'channel_post' => [
            'message_id' => rand(1000, 9999),
            'chat' => [
                'id' => -1001234567890,  // Chat ID جعلی
                'title' => 'Test Channel',
                'username' => 'testchannel',
                'type' => 'channel',
            ],
            'date' => time(),
            'text' => 'این یک پست تستی است 🧪',
        ],
    ];
    
    echo "<h3>📦 Payload:</h3>";
    echo "<pre>" . json_encode($testUpdate, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "</pre>";
    
    // Send to webhook
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $webhookUrl,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($testUpdate),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'X-Telegram-Bot-Api-Secret-Token: ' . $secretToken,
        ],
        CURLOPT_TIMEOUT => 30,
    ]);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $error = curl_error($ch);
    curl_close($ch);
    
    echo "<h3>📡 پاسخ Webhook:</h3>";
    
    if ($error) {
        echo "<p class='error'>❌ خطای cURL: " . htmlspecialchars($error) . "</p>";
    } else {
        echo "<div style='background:#f0f0f0;padding:15px;border-radius:8px;margin:10px 0;'>";
        echo "<strong>HTTP Status:</strong> {$httpCode}<br>";
        
        if ($httpCode === 200) {
            echo "<p class='success'>✅ Webhook با موفقیت پاسخ داد!</p>";
        } elseif ($httpCode === 401) {
            echo "<p class='error'>❌ خطای احراز هویت - Secret Token نامعتبر است</p>";
        } elseif ($httpCode === 404) {
            echo "<p class='error'>❌ Webhook پیدا نشد - URL اشتباه است</p>";
        } elseif ($httpCode === 500) {
            echo "<p class='error'>❌ خطای سرور - مشکل در پردازش webhook</p>";
        } else {
            echo "<p class='error'>❌ خطای غیرمنتظره: HTTP {$httpCode}</p>";
        }
        
        if (!empty($response)) {
            echo "<strong>پاسخ:</strong><br>";
            echo "<pre>" . htmlspecialchars($response) . "</pre>";
        }
        echo "</div>";
    }
    
    // Check logs
    echo "<h3>📋 بررسی لاگ‌ها</h3>";
    $logDir = __DIR__ . '/../logs';
    if (is_dir($logDir)) {
        $logFiles = glob($logDir . '/app-*.log');
        if (!empty($logFiles)) {
            // Get latest log file
            usort($logFiles, function($a, $b) {
                return filemtime($b) - filemtime($a);
            });
            $latestLog = $logFiles[0];
            
            // Read last 20 lines
            $lines = file($latestLog);
            $lastLines = array_slice($lines, -20);
            
            echo "<p>آخرین خطوط لاگ (<code>" . basename($latestLog) . "</code>):</p>";
            echo "<pre style='max-height:300px;overflow:auto;'>" . htmlspecialchars(implode('', $lastLines)) . "</pre>";
        } else {
            echo "<p class='error'>هیچ لاگی یافت نشد</p>";
        }
    }
}

// Form to test
echo "<h2>🧪 ارسال Update تستی</h2>";
echo "<p>با کلیک روی دکمه زیر، یک channel_post جعلی به webhook ارسال می‌شود:</p>";

echo "<form method='POST'>";
echo "<input type='hidden' name='action' value='test'>";
echo "<button type='submit' style='padding:15px 30px;background:#28a745;color:white;border:none;border-radius:5px;cursor:pointer;font-size:16px;'>📤 ارسال Update تستی</button>";
echo "</form>";

echo "<hr>";
echo "<h2>📖 راهنما</h2>";
echo "<p>برای تست کامل ربات:</p>";
echo "<ol>";
echo "<li>مطمئن شوید سیستم نصب شده است</li>";
echo "<li>کانال تست را از پنل ادمین اضافه کنید</li>";
echo "<li>تگ‌ها و ID های موردنیاز را تنظیم کنید</li>";
echo "<li>قالب پیام را تنظیم کنید</li>";
echo "<li>دکمه 'ارسال Update تستی' را کلیک کنید</li>";
echo "<li>نتیجه را در پاسخ و لاگ‌ها بررسی کنید</li>";
echo "</ol>";

echo "<h3>🔗 لینک‌های مفید</h3>";
echo "<ul>";
echo "<li><a href='/admin/'>پنل مدیریت</a></li>";
echo "<li><a href='/admin/channels.php'>مدیریت کانال‌ها</a></li>";
echo "<li><a href='/admin/tags.php'>مدیریت تگ‌ها</a></li>";
echo "<li><a href='/admin/template.php'>تنظیم قالب</a></li>";
echo "<li><a href='/admin/health.php'>وضعیت سیستم</a></li>";
echo "<li><a href='/test_bot.php'>تست توکن ربات</a></li>";
echo "</ul>";

echo "<h3>💡 نکته</h3>";
echo "<p style='background:#fff3cd;padding:15px;border-radius:5px;border-right:4px solid #ffc107;'>";
echo "<strong>توجه:</strong> این فقط یک تست داخلی است. برای تست واقعی، باید:<br>";
echo "1. Webhook را از طریق API تلگرام تنظیم کنید<br>";
echo "2. ربات را به کانال اضافه کنید<br>";
echo "3. یک پست واقعی در کانال منتشر کنید";
echo "</p>";
?>