<?php
$pageTitle = 'مدیریت کانال‌ها';
ob_start();
?>

<h1>📺 مدیریت کانال‌ها</h1>

<div class="card">
    <h2>➕ افزودن کانال جدید</h2>
    <p style="margin-bottom: 20px; color: #666;">برای اینکه ربات به پست‌های یک کانال پاسخ دهد، باید آن را به لیست کانال‌های مجاز اضافه کنید.</p>
    
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="csrf_token" value="<?= \TelegramBot\Controllers\AuthController::getCsrfToken() ?>">
        
        <div class="form-group">
            <label>شناسه چت (Chat ID): *</label>
            <input type="text" name="chat_id" required placeholder="-1001234567890">
            <small style="color: #666; display: block; margin-top: 5px;">شناسه کانال (برای کانال‌های عمومی منفی است، مثال: -1001234567890)</small>
        </div>
        
        <div class="form-group">
            <label>یوزرنیم کانال (Username):</label>
            <input type="text" name="username" placeholder="@channel_username">
            <small style="color: #666; display: block; margin-top: 5px;">یوزرنیم کانال (اختیاری - برای کانال‌های عمومی)</small>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="enabled" checked>
                فعال
            </label>
            <small style="color: #666; display: block; margin-top: 5px;">اگر خاموش باشد، ربات به این کانال پاسخ نمی‌دهد</small>
        </div>
        
        <div class="form-group">
            <label>قالب اختصاصی کانال (اختیاری):</label>
            <textarea name="template" placeholder="اگر خالی بگذارید، از قالب پیش‌فرض استفاده می‌شود"></textarea>
            <small style="color: #666; display: block; margin-top: 5px;">قالب خاص این کانال (در غیر این صورت از قالب پیش‌فرض استفاده می‌شود)</small>
        </div>
        
        <button type="submit" class="btn">➕ افزودن کانال</button>
    </form>
</div>

<div class="card">
    <h2>📋 لیست کانال‌ها</h2>
    <?php if (empty($channels)): ?>
        <p>هیچ کانالی ثبت نشده است.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>شناسه</th>
                    <th>شناسه چت</th>
                    <th>یوزرنیم</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($channels as $channel): ?>
                    <tr>
                        <td><?= htmlspecialchars($channel['id']) ?></td>
                        <td><?= htmlspecialchars($channel['chat_id']) ?></td>
                        <td><?= htmlspecialchars($channel['username'] ?? '-') ?></td>
                        <td>
                            <?php if ($channel['enabled']): ?>
                                <span class="badge badge-success">فعال</span>
                            <?php else: ?>
                                <span class="badge badge-danger">غیرفعال</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $channel['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= \TelegramBot\Controllers\AuthController::getCsrfToken() ?>">
                                <button type="submit" class="btn btn-sm">🔄 تغییر وضعیت</button>
                            </form>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('آیا مطمئن هستید که می‌خواهید این کانال را حذف کنید؟');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $channel['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= \TelegramBot\Controllers\AuthController::getCsrfToken() ?>">
                                <button type="submit" class="btn btn-danger btn-sm">🗑️ حذف</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
