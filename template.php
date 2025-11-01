<?php
$pageTitle = 'ویرایشگر قالب';
ob_start();
?>

<h1>✏️ ویرایشگر قالب</h1>

<div class="card">
    <h2>📝 ویرایش قالب پیام</h2>
    <p style="margin-bottom: 15px;">قالب پیامی که به صورت خودکار زیر هر پست کانال ارسال می‌شود را ویرایش کنید.</p>
    
    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; margin-bottom: 20px; border-right: 4px solid #007bff;">
        <strong>📋 متغیرهای موجود:</strong>
        <ul style="margin-top: 10px; margin-right: 20px; line-height: 1.8;">
            <li><code>{tags}</code> - تمام تگ‌های فعال (با جداکننده)</li>
            <li><code>{ids}</code> - تمام ID های فعال (با جداکننده)</li>
            <li><code>{channel_username}</code> - یوزرنیم کانال (مثال: @channel)</li>
            <li><code>{channel_id}</code> - شناسه کانال</li>
            <li><code>{message_id}</code> - شناسه پیام</li>
            <li><code>{date}</code> - تاریخ و ساعت</li>
            <li><code>{post_type}</code> - نوع پست (text, photo, video, ...)</li>
            <li><code>{permalink}</code> - لینک مستقیم به پست (اگر یوزرنیم موجود باشد)</li>
            <li><code>{custom1}</code>, <code>{custom2}</code>, <code>{custom3}</code> - متغیرهای سفارشی</li>
        </ul>
    </div>
    
    <form method="POST">
        <input type="hidden" name="action" value="save">
        <input type="hidden" name="csrf_token" value="<?= \TelegramBot\Controllers\AuthController::getCsrfToken() ?>">
        
        <div class="form-group">
            <label>قالب پیام:</label>
            <textarea name="template" rows="10" required><?= htmlspecialchars($template ?? '') ?></textarea>
            <small style="color: #666; display: block; margin-top: 5px;">قالب را با متغیرهای بالا ویرایش کنید</small>
        </div>
        
        <button type="submit" class="btn btn-success">💾 ذخیره قالب</button>
        <button type="submit" name="action" value="preview" class="btn">👁️ پیش‌نمایش</button>
    </form>
</div>

<?php if (!empty($preview)): ?>
<div class="card">
    <h2>👁️ پیش‌نمایش</h2>
    <div style="background: #f8f9fa; padding: 15px; border-radius: 4px; white-space: pre-wrap; font-family: 'Courier New', monospace; border: 2px solid #ddd;">
<?= htmlspecialchars($preview) ?>
    </div>
</div>
<?php endif; ?>

<div class="card" style="margin-top: 20px;">
    <h2>💡 مثال قالب</h2>
    <div style="background: #f8f9fa; padding: 15px; border-radius: 6px; border-right: 4px solid #28a745;">
        <pre style="white-space: pre-wrap; font-family: 'Courier New', monospace; margin: 0;">🔖 {tags}

🆔 {ids}

📌 پست: {message_id} · {date}
🔗 {permalink}</pre>
    </div>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
