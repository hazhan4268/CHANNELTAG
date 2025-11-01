<?php
$pageTitle = 'تنظیمات';
ob_start();
?>

<h1>⚙️ تنظیمات</h1>

<div class="card">
    <h2>تنظیمات عمومی</h2>
    <form method="POST">
        <input type="hidden" name="csrf_token" value="<?= \TelegramBot\Controllers\AuthController::getCsrfToken() ?>">
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="global_on" <?= ($settings['global_on'] ?? '0') === '1' ? 'checked' : '' ?>>
                فعال/غیرفعال کردن ربات (Global Toggle)
            </label>
            <small style="color: #666; display: block; margin-top: 5px;">اگر این گزینه خاموش باشد، ربات به هیچ کانالی پاسخ نمی‌دهد</small>
        </div>
        
        <div class="form-group">
            <label>حالت پارس (Parse Mode):</label>
            <select name="parse_mode">
                <option value="MarkdownV2" <?= ($settings['parse_mode'] ?? 'MarkdownV2') === 'MarkdownV2' ? 'selected' : '' ?>>MarkdownV2 (پیشنهادی)</option>
                <option value="HTML" <?= ($settings['parse_mode'] ?? '') === 'HTML' ? 'selected' : '' ?>>HTML</option>
            </select>
            <small style="color: #666; display: block; margin-top: 5px;">MarkdownV2 برای متن‌های فارسی مناسب‌تر است</small>
        </div>
        
        <div class="form-group">
            <label>زبان رابط (Locale):</label>
            <select name="locale">
                <option value="fa" <?= ($settings['locale'] ?? 'fa') === 'fa' ? 'selected' : '' ?>>فارسی (FA)</option>
                <option value="en" <?= ($settings['locale'] ?? '') === 'en' ? 'selected' : '' ?>>English (EN)</option>
            </select>
        </div>
        
        <div class="form-group">
            <label>جداکننده تگ‌ها (Tag Separator):</label>
            <input type="text" name="tag_separator" value="<?= htmlspecialchars($settings['tag_separator'] ?? ' · ') ?>">
            <small style="color: #666; display: block; margin-top: 5px;">نویسه‌ای که بین تگ‌ها نمایش داده می‌شود</small>
        </div>
        
        <div class="form-group">
            <label>جداکننده ID ها (ID Separator):</label>
            <input type="text" name="id_separator" value="<?= htmlspecialchars($settings['id_separator'] ?? ' | ') ?>">
            <small style="color: #666; display: block; margin-top: 5px;">نویسه‌ای که بین ID ها نمایش داده می‌شود</small>
        </div>
        
        <button type="submit" class="btn btn-success">💾 ذخیره تنظیمات</button>
    </form>
</div>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
