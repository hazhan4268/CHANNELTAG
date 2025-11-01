<?php
$pageTitle = 'وضعیت سیستم';
ob_start();
?>

<h1>💊 وضعیت سیستم</h1>

<div class="card">
    <h2>📊 اطلاعات سیستم</h2>
    
    <div style="margin-bottom: 20px;">
        <strong>نسخه:</strong> <?= htmlspecialchars($health['version'] ?? 'نامشخص') ?>
    </div>
    
    <div style="margin-bottom: 20px;">
        <strong>دیتابیس:</strong>
        <?php if ($health['db'] === 'ok'): ?>
            <span class="badge badge-success">✅ متصل</span>
        <?php else: ?>
            <span class="badge badge-danger">❌ خطا</span>
            <p style="color: #dc3545; margin-top: 5px;"><?= htmlspecialchars($health['db_error'] ?? '') ?></p>
        <?php endif; ?>
    </div>
    
    <div style="margin-bottom: 20px;">
        <strong>Webhook:</strong>
        <?php if ($health['webhook'] === 'ok' || is_array($health['webhook'])): ?>
            <span class="badge badge-success">✅ متصل</span>
            <?php if (is_array($health['webhook'])): ?>
                <div style="margin-top: 15px; padding: 15px; background: #f8f9fa; border-radius: 6px; border-right: 4px solid #28a745;">
                    <p><strong>آدرس Webhook:</strong> <code><?= htmlspecialchars($health['webhook']['url'] ?? 'تنظیم نشده') ?></code></p>
                    <p style="margin-top: 10px;"><strong>پست‌های در انتظار:</strong> <?= htmlspecialchars($health['webhook']['pending_update_count'] ?? '0') ?></p>
                    <?php if (isset($health['webhook']['last_error_date'])): ?>
                        <p style="margin-top: 10px;"><strong>آخرین خطا:</strong> <?= date('Y-m-d H:i:s', $health['webhook']['last_error_date']) ?></p>
                        <p><strong>پیام خطا:</strong> <?= htmlspecialchars($health['webhook']['last_error_message'] ?? '') ?></p>
                    <?php else: ?>
                        <p style="margin-top: 10px; color: #28a745;">✅ هیچ خطایی ثبت نشده است</p>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <span class="badge badge-danger">❌ خطا</span>
            <p style="color: #dc3545; margin-top: 5px;"><?= htmlspecialchars($health['webhook_error'] ?? '') ?></p>
        <?php endif; ?>
    </div>
    
    <div style="margin-top: 25px;">
        <a href="/public/set_webhook.php" onclick="setWebhook(); return false;" class="btn btn-success">🔄 تنظیم مجدد Webhook</a>
    </div>
</div>

<script>
function setWebhook() {
    if (confirm('آیا می‌خواهید Webhook را دوباره تنظیم کنید؟')) {
        fetch('/set_webhook.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                alert('✅ Webhook با موفقیت تنظیم شد!');
                location.reload();
            } else {
                alert('❌ خطا: ' + (data.result.description || 'خطای نامشخص'));
            }
        })
        .catch(e => alert('❌ خطا: ' + e.message));
    }
}
</script>

<?php
$content = ob_get_clean();
include __DIR__ . '/layout.php';
?>
