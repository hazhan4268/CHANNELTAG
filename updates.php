<?php
$pageTitle = 'مدیریت آپدیت';
?>

<div class="card">
    <h2>🔄 مدیریت آپدیت سیستم</h2>
    
    <!-- وضعیت فعلی -->
    <div class="update-status" id="update-status">
        <h3>وضعیت فعلی</h3>
        <div class="current-version">
            <strong>نسخه فعلی:</strong> 
            <span class="badge badge-primary"><?= htmlspecialchars($updateInfo['current_version'] ?? 'نامشخص') ?></span>
        </div>
        
        <?php if (isset($updateInfo['error'])): ?>
            <div class="alert alert-danger mt-3">
                ❌ خطا در بررسی آپدیت: <?= htmlspecialchars($updateInfo['error']) ?>
            </div>
        <?php elseif (isset($updateInfo['has_update']) && $updateInfo['has_update']): ?>
            <div class="alert alert-warning mt-3">
                🆕 آپدیت جدید موجود است!
                <br><strong>نسخه جدید:</strong> <?= htmlspecialchars($updateInfo['latest_version'] ?? 'latest') ?>
                <?php if (isset($updateInfo['published_at'])): ?>
                    <br><small>تاریخ انتشار: <?= date('Y-m-d H:i', strtotime($updateInfo['published_at'])) ?></small>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-success mt-3">
                ✅ سیستم به‌روز است
                <?php if (isset($updateInfo['latest_commit'])): ?>
                    <br><small>آخرین commit: <?= substr($updateInfo['latest_commit'], 0, 7) ?></small>
                    <?php if (isset($updateInfo['commit_message'])): ?>
                        <br><small><?= htmlspecialchars($updateInfo['commit_message']) ?></small>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- دکمه‌های عملیات -->
    <div class="update-actions mt-4">
        <button type="button" class="btn btn-primary" onclick="checkForUpdates()">
            🔍 بررسی آپدیت
        </button>
        
        <?php if (isset($updateInfo['has_update']) && $updateInfo['has_update']): ?>
            <button type="button" class="btn btn-success" onclick="performUpdate()">
                ⬇️ دانلود و نصب آپدیت
            </button>
        <?php endif; ?>
        
        <button type="button" class="btn btn-warning" onclick="performUpdate('latest')">
            🚀 نصب آخرین نسخه (اجباری)
        </button>
    </div>
    
    <!-- نمایش release notes -->
    <?php if (isset($updateInfo['release_notes']) && !empty($updateInfo['release_notes'])): ?>
        <div class="release-notes mt-4">
            <h4>یادداشت‌های انتشار</h4>
            <div class="alert alert-info">
                <?= nl2br(htmlspecialchars($updateInfo['release_notes'])) ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- تاریخچه بکاپ -->
<div class="card">
    <h2>📁 مدیریت بکاپ</h2>
    
    <?php if (empty($history)): ?>
        <p>هیچ بکاپ موجود نیست.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th>تاریخ</th>
                        <th>اندازه</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($history as $backup): ?>
                        <tr>
                            <td><?= htmlspecialchars($backup['date']) ?></td>
                            <td><?= formatBytes($backup['size']) ?></td>
                            <td>
                                <a href="/admin/update.php?action=download&file=<?= urlencode($backup['file']) ?>" 
                                   class="btn btn-sm btn-primary">دانلود</a>
                                <button type="button" class="btn btn-sm btn-warning" 
                                        onclick="restoreBackup('<?= htmlspecialchars($backup['file']) ?>')">
                                    بازگردانی
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- لودینگ مودال -->
<div id="loading-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); z-index: 9999;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 10px; text-align: center;">
        <div class="spinner" style="width: 40px; height: 40px; border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
        <p id="loading-text">در حال پردازش...</p>
    </div>
</div>

<style>
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.update-status {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 20px;
}

.current-version {
    font-size: 16px;
    margin-bottom: 10px;
}

.badge-primary {
    background: #667eea;
    color: white;
}

.update-actions button {
    margin-left: 10px;
    margin-bottom: 10px;
}

.release-notes {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
}

.table-responsive {
    margin-top: 15px;
}
</style>

<script>
function showLoading(text = 'در حال پردازش...') {
    document.getElementById('loading-text').textContent = text;
    document.getElementById('loading-modal').style.display = 'block';
}

function hideLoading() {
    document.getElementById('loading-modal').style.display = 'none';
}

function checkForUpdates() {
    showLoading('در حال بررسی آپدیت...');
    
    fetch('/admin/update.php?action=check', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.error) {
            alert('خطا: ' + data.error);
            return;
        }
        
        // بروزرسانی UI
        location.reload();
    })
    .catch(error => {
        hideLoading();
        alert('خطا در بررسی آپدیت: ' + error.message);
    });
}

function performUpdate(version = 'latest') {
    if (!confirm('آیا مطمئن هستید که می‌خواهید آپدیت را انجام دهید؟\n\nیک بکاپ خودکار ایجاد خواهد شد.')) {
        return;
    }
    
    showLoading('در حال دانلود و نصب آپدیت...\nلطفاً صبر کنید...');
    
    const formData = new FormData();
    formData.append('csrf_token', '<?= AuthController::getCsrfToken() ?>');
    formData.append('version', version);
    
    fetch('/admin/update.php?action=update', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            alert('✅ آپدیت با موفقیت انجام شد!\n\nنسخه جدید: ' + data.new_version);
            location.reload();
        } else {
            alert('❌ خطا در آپدیت: ' + data.error);
        }
    })
    .catch(error => {
        hideLoading();
        alert('خطا در آپدیت: ' + error.message);
    });
}

function restoreBackup(backupFile) {
    if (!confirm('آیا مطمئن هستید که می‌خواهید از این بکاپ بازگردانی کنید؟\n\nوضعیت فعلی نیز بکاپ خواهد شد.')) {
        return;
    }
    
    showLoading('در حال بازگردانی از بکاپ...');
    
    const formData = new FormData();
    formData.append('csrf_token', '<?= AuthController::getCsrfToken() ?>');
    formData.append('backup_file', backupFile);
    
    fetch('/admin/update.php?action=restore', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        hideLoading();
        
        if (data.success) {
            alert('✅ بازگردانی با موفقیت انجام شد!');
            location.reload();
        } else {
            alert('❌ خطا در بازگردانی: ' + data.error);
        }
    })
    .catch(error => {
        hideLoading();
        alert('خطا در بازگردانی: ' + error.message);
    });
}
</script>

<?php
// Helper function for formatting file sizes
function formatBytes($size, $precision = 2) {
    $base = log($size, 1024);
    $suffixes = array('B', 'KB', 'MB', 'GB', 'TB');
    return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}
?>