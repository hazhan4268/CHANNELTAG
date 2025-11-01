<?php
$pageTitle = 'مدیریت تگ‌ها و ID ها';
ob_start();
?>

<h1>🏷️ مدیریت تگ‌ها و ID ها</h1>

<div class="card">
    <h2>➕ افزودن تگ یا ID جدید</h2>
    <p style="margin-bottom: 20px; color: #666;">تگ‌ها و ID ها در قالب پیام نمایش داده می‌شوند. می‌توانید برای هر کانال تگ‌ها و ID های مختلف تعریف کنید.</p>
    
    <form method="POST">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="csrf_token" value="<?= \TelegramBot\Controllers\AuthController::getCsrfToken() ?>">
        
        <div class="form-group">
            <label>نوع:</label>
            <select name="type">
                <option value="tag">تگ (Tag)</option>
                <option value="id">ID</option>
            </select>
            <small style="color: #666; display: block; margin-top: 5px;">تگ یا ID را انتخاب کنید</small>
        </div>
        
        <div class="form-group">
            <label>مقدار: *</label>
            <input type="text" name="value" required maxlength="64">
            <small style="color: #666; display: block; margin-top: 5px;">مقدار تگ یا ID (حداکثر 64 کاراکتر)</small>
        </div>
        
        <div class="form-group">
            <label>ترتیب نمایش (Sort Order):</label>
            <input type="number" name="sort" value="0">
            <small style="color: #666; display: block; margin-top: 5px;">عدد کمتر = نمایش اول (0 = اول)</small>
        </div>
        
        <div class="form-group">
            <label>
                <input type="checkbox" name="enabled" checked>
                فعال
            </label>
            <small style="color: #666; display: block; margin-top: 5px;">اگر خاموش باشد، این تگ/ID در قالب نمایش داده نمی‌شود</small>
        </div>
        
        <button type="submit" class="btn">➕ افزودن</button>
    </form>
</div>

<div class="card">
    <h2>🏷️ تگ‌ها</h2>
    <?php if (empty($tags)): ?>
        <p>تگی ثبت نشده است.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>شناسه</th>
                    <th>مقدار</th>
                    <th>ترتیب</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tags as $tag): ?>
                    <tr>
                        <td><?= htmlspecialchars($tag['id']) ?></td>
                        <td><?= htmlspecialchars($tag['value']) ?></td>
                        <td><?= htmlspecialchars($tag['sort']) ?></td>
                        <td>
                            <?php if ($tag['enabled']): ?>
                                <span class="badge badge-success">فعال</span>
                            <?php else: ?>
                                <span class="badge badge-danger">غیرفعال</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $tag['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= \TelegramBot\Controllers\AuthController::getCsrfToken() ?>">
                                <button type="submit" class="btn btn-sm">🔄 تغییر وضعیت</button>
                            </form>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('آیا مطمئن هستید؟');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $tag['id'] ?>">
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

<div class="card">
    <h2>🆔 ID ها</h2>
    <?php if (empty($ids)): ?>
        <p>ID ای ثبت نشده است.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>شناسه</th>
                    <th>مقدار</th>
                    <th>ترتیب</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ids as $id): ?>
                    <tr>
                        <td><?= htmlspecialchars($id['id']) ?></td>
                        <td><?= htmlspecialchars($id['value']) ?></td>
                        <td><?= htmlspecialchars($id['sort']) ?></td>
                        <td>
                            <?php if ($id['enabled']): ?>
                                <span class="badge badge-success">فعال</span>
                            <?php else: ?>
                                <span class="badge badge-danger">غیرفعال</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" style="display: inline;">
                                <input type="hidden" name="action" value="toggle">
                                <input type="hidden" name="id" value="<?= $id['id'] ?>">
                                <input type="hidden" name="csrf_token" value="<?= \TelegramBot\Controllers\AuthController::getCsrfToken() ?>">
                                <button type="submit" class="btn btn-sm">🔄 تغییر وضعیت</button>
                            </form>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('آیا مطمئن هستید؟');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?= $id['id'] ?>">
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
