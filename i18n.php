<?php

namespace TelegramBot;

class i18n
{
    private static $locale = 'fa';
    private static $strings = [];
    
    private static $translations = [
        'fa' => [
            // General
            'app_name' => 'ربات مدیریت کانال تلگرام',
            'welcome' => 'خوش آمدید',
            'logout' => 'خروج',
            'save' => 'ذخیره',
            'cancel' => 'لغو',
            'delete' => 'حذف',
            'edit' => 'ویرایش',
            'add' => 'افزودن',
            'search' => 'جستجو',
            'loading' => 'در حال بارگذاری...',
            'success' => 'موفقیت',
            'error' => 'خطا',
            'warning' => 'هشدار',
            'info' => 'اطلاعات',
            
            // Admin Panel
            'admin_panel' => 'پنل مدیریت',
            'dashboard' => 'داشبورد',
            'settings' => 'تنظیمات',
            'channels' => 'کانال‌ها',
            'tags' => 'تگ‌ها و شناسه‌ها',
            'template' => 'قالب',
            'health' => 'وضعیت سیستم',
            'statistics' => 'آمار',
            
            // Dashboard
            'total_posts' => 'کل پست‌ها',
            'sent_posts' => 'ارسال شده',
            'error_posts' => 'خطاها',
            'channels_count' => 'کانال‌ها',
            'tags_count' => 'تگ‌ها',
            'ids_count' => 'شناسه‌ها',
            'recent_posts' => 'پست‌های اخیر',
            'global_status' => 'وضعیت کلی',
            'bot_on' => 'ربات فعال است',
            'bot_off' => 'ربات غیرفعال است',
            
            // Settings
            'global_toggle' => 'فعال/غیرفعال کردن ربات',
            'parse_mode' => 'حالت تجزیه',
            'locale' => 'زبان',
            'tag_separator' => 'جداکننده تگ‌ها',
            'id_separator' => 'جداکننده شناسه‌ها',
            
            // Channels
            'channel_list' => 'لیست کانال‌ها',
            'add_channel' => 'افزودن کانال جدید',
            'chat_id' => 'شناسه چت',
            'username' => 'نام کاربری',
            'enabled' => 'فعال',
            'disabled' => 'غیرفعال',
            'per_channel_template' => 'قالب مخصوص این کانال',
            'use_default_template' => 'استفاده از قالب پیش‌فرض',
            
            // Tags
            'tags_list' => 'لیست تگ‌ها',
            'ids_list' => 'لیست شناسه‌ها',
            'add_tag' => 'افزودن تگ',
            'add_id' => 'افزودن شناسه',
            'type' => 'نوع',
            'value' => 'مقدار',
            'sort_order' => 'ترتیب',
            
            // Template
            'template_editor' => 'ویرایشگر قالب',
            'available_variables' => 'متغیرهای موجود',
            'preview' => 'پیش‌نمایش',
            'default_template' => 'قالب پیش‌فرض',
            
            // Health
            'system_status' => 'وضعیت سیستم',
            'database' => 'پایگاه داده',
            'webhook' => 'وب‌هوک',
            'version' => 'نسخه',
            'pending_updates' => 'به‌روزرسانی‌های در انتظار',
            'last_error' => 'آخرین خطا',
            
            // Installer
            'installer_title' => 'نصب‌کننده ربات تلگرام',
            'step' => 'مرحله',
            'step_system_check' => 'بررسی سیستم',
            'step_database' => 'پیکربندی پایگاه داده',
            'step_admin' => 'ایجاد کاربر مدیر',
            'step_bot' => 'تنظیمات ربات',
            'step_complete' => 'نصب کامل شد',
            'next' => 'بعدی',
            'previous' => 'قبلی',
            'complete_installation' => 'اتمام نصب',
            
            'system_check_desc' => 'بررسی افزونه‌های PHP موردنیاز',
            'database_desc' => 'اطلاعات اتصال به پایگاه داده MySQL',
            'admin_desc' => 'ایجاد حساب کاربری مدیر برای ورود به پنل',
            'bot_desc' => 'تنظیم توکن ربات و آدرس وب‌سایت',
            'complete_desc' => 'نصب با موفقیت انجام شد',
            
            'all_extensions_installed' => '✓ تمام افزونه‌های موردنیاز نصب شده‌اند',
            'missing_extensions' => 'افزونه‌های مفقود',
            
            'db_host' => 'آدرس سرور پایگاه داده',
            'db_name' => 'نام پایگاه داده',
            'db_user' => 'نام کاربری پایگاه داده',
            'db_pass' => 'رمز عبور پایگاه داده',
            
            'admin_username' => 'نام کاربری مدیر',
            'admin_email' => 'ایمیل مدیر (اختیاری)',
            'admin_password' => 'رمز عبور مدیر',
            'admin_password_confirm' => 'تایید رمز عبور',
            
            'bot_token' => 'توکن ربات',
            'bot_token_desc' => 'توکن ربات را از @BotFather دریافت کنید',
            'base_url' => 'آدرس پایه وب‌سایت',
            'base_url_desc' => 'آدرس کامل وب‌سایت شما (با https://)',
            
            'installation_complete' => 'نصب با موفقیت انجام شد',
            'webhook_url' => 'آدرس وب‌هوک',
            'set_webhook' => 'تنظیم وب‌هوک',
            'go_to_admin' => 'رفتن به پنل مدیریت',
            
            'webhook_set_success' => 'وب‌هوک با موفقیت تنظیم شد',
            'webhook_set_error' => 'خطا در تنظیم وب‌هوک',
        ],
        'en' => [
            // General
            'app_name' => 'Telegram Channel Bot',
            'welcome' => 'Welcome',
            'logout' => 'Logout',
            'save' => 'Save',
            'cancel' => 'Cancel',
            'delete' => 'Delete',
            'edit' => 'Edit',
            'add' => 'Add',
            'search' => 'Search',
            'loading' => 'Loading...',
            'success' => 'Success',
            'error' => 'Error',
            'warning' => 'Warning',
            'info' => 'Information',
            
            // Admin Panel
            'admin_panel' => 'Admin Panel',
            'dashboard' => 'Dashboard',
            'settings' => 'Settings',
            'channels' => 'Channels',
            'tags' => 'Tags & IDs',
            'template' => 'Template',
            'health' => 'Health Check',
            'statistics' => 'Statistics',
            
            // Dashboard
            'total_posts' => 'Total Posts',
            'sent_posts' => 'Sent',
            'error_posts' => 'Errors',
            'channels_count' => 'Channels',
            'tags_count' => 'Tags',
            'ids_count' => 'IDs',
            'recent_posts' => 'Recent Posts',
            'global_status' => 'Global Status',
            'bot_on' => 'Bot is ON',
            'bot_off' => 'Bot is OFF',
            
            // Settings
            'global_toggle' => 'Enable/Disable Bot',
            'parse_mode' => 'Parse Mode',
            'locale' => 'Locale',
            'tag_separator' => 'Tag Separator',
            'id_separator' => 'ID Separator',
            
            // Channels
            'channel_list' => 'Channel List',
            'add_channel' => 'Add New Channel',
            'chat_id' => 'Chat ID',
            'username' => 'Username',
            'enabled' => 'Enabled',
            'disabled' => 'Disabled',
            'per_channel_template' => 'Per-Channel Template',
            'use_default_template' => 'Use Default Template',
            
            // Tags
            'tags_list' => 'Tags List',
            'ids_list' => 'IDs List',
            'add_tag' => 'Add Tag',
            'add_id' => 'Add ID',
            'type' => 'Type',
            'value' => 'Value',
            'sort_order' => 'Sort Order',
            
            // Template
            'template_editor' => 'Template Editor',
            'available_variables' => 'Available Variables',
            'preview' => 'Preview',
            'default_template' => 'Default Template',
            
            // Health
            'system_status' => 'System Status',
            'database' => 'Database',
            'webhook' => 'Webhook',
            'version' => 'Version',
            'pending_updates' => 'Pending Updates',
            'last_error' => 'Last Error',
            
            // Installer
            'installer_title' => 'Telegram Bot Installer',
            'step' => 'Step',
            'step_system_check' => 'System Check',
            'step_database' => 'Database Configuration',
            'step_admin' => 'Create Admin User',
            'step_bot' => 'Bot Configuration',
            'step_complete' => 'Installation Complete',
            'next' => 'Next',
            'previous' => 'Previous',
            'complete_installation' => 'Complete Installation',
            
            'system_check_desc' => 'Check required PHP extensions',
            'database_desc' => 'MySQL database connection information',
            'admin_desc' => 'Create administrator account for admin panel',
            'bot_desc' => 'Configure bot token and website URL',
            'complete_desc' => 'Installation completed successfully',
            
            'all_extensions_installed' => '✓ All required extensions are installed',
            'missing_extensions' => 'Missing Extensions',
            
            'db_host' => 'Database Host',
            'db_name' => 'Database Name',
            'db_user' => 'Database User',
            'db_pass' => 'Database Password',
            
            'admin_username' => 'Admin Username',
            'admin_email' => 'Admin Email (Optional)',
            'admin_password' => 'Admin Password',
            'admin_password_confirm' => 'Confirm Password',
            
            'bot_token' => 'Bot Token',
            'bot_token_desc' => 'Get your bot token from @BotFather',
            'base_url' => 'Base URL',
            'base_url_desc' => 'Your full website URL (with https://)',
            
            'installation_complete' => 'Installation completed successfully',
            'webhook_url' => 'Webhook URL',
            'set_webhook' => 'Set Webhook',
            'go_to_admin' => 'Go to Admin Panel',
            
            'webhook_set_success' => 'Webhook set successfully',
            'webhook_set_error' => 'Error setting webhook',
        ],
    ];
    
    public static function init($locale = 'fa')
    {
        self::$locale = $locale;
        
        if (isset(self::$translations[$locale])) {
            self::$strings = self::$translations[$locale];
        } else {
            self::$strings = self::$translations['fa'];
        }
    }
    
    public static function get($key, $default = null)
    {
        return self::$strings[$key] ?? $default ?? $key;
    }
    
    public static function setLocale($locale)
    {
        self::init($locale);
    }
    
    public static function getLocale()
    {
        return self::$locale;
    }
}

