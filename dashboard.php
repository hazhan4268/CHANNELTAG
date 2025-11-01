<?php
$pageTitle = 'داشبورد';
?>

<!-- Stats Overview -->
<div class="stats">
    <div class="stat-card">
        <i class="fas fa-envelope"></i>
        <h3><?= number_format($stats['total_posts'] ?? 0) ?></h3>
        <p>کل پست‌ها</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-check-circle"></i>
        <h3><?= number_format($stats['sent_posts'] ?? 0) ?></h3>
        <p>پست‌های ارسال شده</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-exclamation-triangle"></i>
        <h3><?= number_format($stats['error_posts'] ?? 0) ?></h3>
        <p>پست‌های خطا</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-broadcast-tower"></i>
        <h3><?= number_format($stats['channels'] ?? 0) ?></h3>
        <p>کانال‌ها</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-tags"></i>
        <h3><?= number_format($stats['tags'] ?? 0) ?></h3>
        <p>تگ‌ها</p>
    </div>
    <div class="stat-card">
        <i class="fas fa-id-card"></i>
        <h3><?= number_format($stats['ids'] ?? 0) ?></h3>
        <p>شناسه‌ها</p>
    </div>
</div>

<!-- Quick Actions -->
<div class="card">
    <h2><i class="fas fa-bolt"></i> عملیات سریع</h2>
    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
        <a href="/admin/channels.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> افزودن کانال
        </a>
        <a href="/admin/tags.php" class="btn btn-success">
            <i class="fas fa-tag"></i> مدیریت تگ‌ها
        </a>
        <a href="/admin/template.php" class="btn btn-info">
            <i class="fas fa-edit"></i> ویرایش قالب
        </a>
        <a href="/admin/update.php" class="btn btn-warning">
            <i class="fas fa-cloud-download-alt"></i> بررسی آپدیت
        </a>
        <a href="/admin/health.php" class="btn">
            <i class="fas fa-heartbeat"></i> وضعیت سیستم
        </a>
    </div>
</div>

<!-- Global Status -->
<div class="card">
    <h2><i class="fas fa-power-off"></i> وضعیت عمومی</h2>
    <div style="display: flex; align-items: center; gap: 15px;">
        <div style="flex: 1;">
            <strong>وضعیت ربات:</strong>
            <?php if ($globalOn ?? true): ?>
                <span class="badge badge-success">
                    <i class="fas fa-check"></i> فعال
                </span>
            <?php else: ?>
                <span class="badge badge-danger">
                    <i class="fas fa-times"></i> غیرفعال
                </span>
            <?php endif; ?>
        </div>
        <div>
            <a href="/admin/settings.php" class="btn btn-sm">
                <i class="fas fa-cog"></i> تنظیمات
            </a>
        </div>
    </div>
</div>

<!-- Recent Posts -->
<?php if (!empty($recentPosts)): ?>
<div class="card">
    <h2><i class="fas fa-history"></i> آخرین پست‌ها</h2>
    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>کانال</th>
                    <th>پیام</th>
                    <th>وضعیت</th>
                    <th>تاریخ</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($recentPosts, 0, 10) as $post): ?>
                    <tr>
                        <td>
                            <code><?= htmlspecialchars($post['chat_id']) ?></code>
                        </td>
                        <td>
                            <code><?= htmlspecialchars($post['message_id']) ?></code>
                        </td>
                        <td>
                            <?php if ($post['status'] === 'sent'): ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check"></i> ارسال شده
                                </span>
                            <?php elseif ($post['status'] === 'error'): ?>
                                <span class="badge badge-danger" title="<?= htmlspecialchars($post['error_message'] ?? '') ?>">
                                    <i class="fas fa-times"></i> خطا
                                </span>
                            <?php else: ?>
                                <span class="badge badge-warning">
                                    <i class="fas fa-clock"></i> رد شده
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <small><?= date('Y-m-d H:i', strtotime($post['created_at'])) ?></small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <?php if (count($recentPosts) > 10): ?>
        <div style="text-align: center; margin-top: 15px;">
            <p class="text-muted">
                <i class="fas fa-info-circle"></i>
                فقط 10 پست اخیر نمایش داده شده است. کل: <?= count($recentPosts) ?> پست
            </p>
        </div>
    <?php endif; ?>
</div>
<?php else: ?>
<div class="card">
    <h2><i class="fas fa-history"></i> آخرین پست‌ها</h2>
    <div style="text-align: center; padding: 40px;">
        <i class="fas fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
        <p style="color: #666; margin: 0;">هنوز هیچ پستی پردازش نشده است</p>
        <small style="color: #999;">پس از دریافت پست‌های کانال، آنها در اینجا نمایش داده خواهند شد</small>
    </div>
</div>
<?php endif; ?>

<!-- System Info -->
<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px;">
    <div class="card">
        <h2><i class="fas fa-info-circle"></i> اطلاعات سیستم</h2>
        <div style="line-height: 1.8;">
            <div><strong>نسخه PHP:</strong> <?= phpversion() ?></div>
            <div><strong>نسخه ربات:</strong> <?= file_exists(__DIR__ . '/../../VERSION') ? trim(file_get_contents(__DIR__ . '/../../VERSION')) : '1.0.0' ?></div>
            <div><strong>حافظه مصرفی:</strong> <?= formatBytes(memory_get_usage(true)) ?></div>
            <div><strong>زمان سرور:</strong> <?= date('Y-m-d H:i:s') ?></div>
        </div>
    </div>
    
    <div class="card">
        <h2><i class="fas fa-link"></i> لینک‌های مفید</h2>
        <div style="display: flex; flex-direction: column; gap: 10px;">
            <a href="https://github.com/hazhan4268/CHANNELTAG" target="_blank" class="btn btn-sm btn-info">
                <i class="fab fa-github"></i> مخزن GitHub
            </a>
            <a href="https://t.me/BotFather" target="_blank" class="btn btn-sm">
                <i class="fab fa-telegram"></i> BotFather
            </a>
            <a href="/admin/debug.php" class="btn btn-sm btn-warning">
                <i class="fas fa-bug"></i> صفحه Debug
            </a>
        </div>
    </div>
</div>

<script>
// Auto-refresh stats every 30 seconds
setTimeout(function() {
    location.reload();
}, 30000);

// Format numbers with animation
document.querySelectorAll('.stat-card h3').forEach(el => {
    const target = parseInt(el.textContent.replace(/,/g, ''));
    let current = 0;
    const increment = target / 30;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            el.textContent = target.toLocaleString('fa-IR');
            clearInterval(timer);
        } else {
            el.textContent = Math.floor(current).toLocaleString('fa-IR');
        }
    }, 50);
});
</script>

<?php
// Helper function for formatting file sizes
function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}
?>
