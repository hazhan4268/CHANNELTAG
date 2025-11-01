<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ربات تلگرام کانال منیجر</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0;
            padding: 20px;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            text-align: center;
            max-width: 500px;
        }
        h1 { color: #333; margin-bottom: 20px; }
        p { color: #666; margin-bottom: 30px; line-height: 1.6; }
        .btn {
            display: inline-block;
            padding: 12px 25px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            margin: 5px;
            transition: transform 0.2s;
        }
        .btn:hover { transform: translateY(-2px); }
        .status { margin-top: 20px; padding: 15px; border-radius: 6px; }
        .status.not-installed { background: #fff3cd; color: #856404; border-right: 4px solid #ffc107; }
        .status.installed { background: #d4edda; color: #155724; border-right: 4px solid #28a745; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🤖 ربات تلگرام کانال منیجر</h1>
        <p>سیستم مدیریت خودکار پاسخ به پست‌های کانال تلگرام با پنل مدیریت کامل</p>
        
        <?php
        $configPath = __DIR__ . '/config/.env.php';
        if (file_exists($configPath)) {
            echo '<div class="status installed">✅ سیستم نصب شده است</div>';
            echo '<a href="/admin/" class="btn">ورود به پنل مدیریت</a>';
            echo '<a href="/admin/health.php" class="btn">وضعیت سیستم</a>';
        } else {
            echo '<div class="status not-installed">⚠️ سیستم هنوز نصب نشده است</div>';
            echo '<a href="/install/" class="btn">شروع نصب</a>';
        }
        ?>
        
        <div style="margin-top: 30px;">
            <a href="/test.php" class="btn" style="background: #6c757d;">تست سیستم</a>
            <a href="/admin/debug.php" class="btn" style="background: #dc3545;">صفحه Debug</a>
        </div>
    </div>
</body>
</html>