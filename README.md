# Telegram Channel Bot 🤖

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D8.1-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)
[![Version](https://img.shields.io/badge/version-2.1.0-brightgreen.svg)](https://github.com/hazhan4268/CHANNELTAG)

A production-grade Telegram bot for automatically replying to channel posts with templated comments. Designed for cPanel shared hosting with PHP 8.1+, MySQL (PDO), and webhook-based architecture.

## ✨ Features

- ✅ **Webhook-based** - Efficient webhook handling for channel posts
- ✅ **Modern Admin Panel** - Complete redesigned password-protected admin interface
- ✅ **Auto-Update System** - Automatic updates from GitHub repository
- ✅ **Template Engine** - Flexible template system with variables
- ✅ **Channel Management** - Allowlist channels with per-channel templates
- ✅ **Tag/ID Management** - CRUD interface for tags and IDs
- ✅ **Rate Limiting** - Prevents duplicates and manages rate limits
- ✅ **Retry Logic** - Automatic retry on API errors (429/5xx)
- ✅ **Security** - Secret token verification, CSRF protection, password hashing
- ✅ **Health Monitoring** - Health check page with system status
- ✅ **Installation Wizard** - Step-by-step installer
- ✅ **Backup & Restore** - Automatic backup before updates
- ✅ **Internationalization** - Support for EN/FA locales

## 🆕 New in Version 2.1.0

- 🎨 **Redesigned Admin Panel** - Modern, responsive UI with sidebar navigation
- 🔄 **Auto-Update System** - One-click updates from GitHub
- 📦 **Backup Management** - Automatic backups with restore functionality
- 📊 **Enhanced Dashboard** - Better statistics and system info
- ⚡ **Quick Actions** - Fast access to common tasks
- 🔗 **GitHub Integration** - Direct connection to main repository
- 📱 **Mobile Responsive** - Works perfectly on mobile devices

## 📋 Requirements

- PHP 8.1 or higher
- MySQL 5.7+ / MariaDB 10.2+
- cPanel shared hosting (or any PHP hosting)
- PHP Extensions: PDO, cURL, OpenSSL, JSON, mbstring
- Telegram Bot Token (from @BotFather)

## 🚀 Installation

### Step 1: Upload Files

Upload all files to your cPanel `public_html` directory (or your document root).

```
/public_html/
├── public/
├── src/
├── sql/
├── templates/
├── config/
├── logs/
└── install/
```

### Step 2: Create MySQL Database

1. Go to cPanel → MySQL Databases
2. Create a new database (e.g., `telegram_bot`)
3. Create a database user and assign full privileges
4. Note down: Database name, Username, Password

### Step 3: Run Installer

1. Visit `https://yourdomain.com/install/`
2. Follow the installation wizard:
   - **Step 1**: System check (PHP extensions)
   - **Step 2**: Database configuration
   - **Step 3**: Create admin user
   - **Step 4**: Bot configuration (token, base URL)
   - **Step 5**: Complete - Webhook URL displayed

### Step 4: Set Webhook

Click the "Set Webhook" button in the installer or manually call:

```
POST https://yourdomain.com/set_webhook.php
```

### Step 5: Configure Bot in Channel

1. Add your bot as **administrator** to your Telegram channel
2. Make sure the bot has permission to send messages
3. Go to Admin Panel → Channels
4. Add channel (Chat ID or username)

## 📖 Usage

### Admin Panel

Access: `https://yourdomain.com/admin/`

**Login**: Use the admin credentials created during installation.

**Sections**:
- **Dashboard**: Statistics and recent posts
- **Settings**: Global toggle, parse mode, locale, separators
- **Channels**: Manage allowed channels (add/remove/enable/disable)
- **Tags**: Manage tags and IDs (CRUD)
- **Template**: Edit auto-reply template
- **Health**: System status and webhook info

### Template Variables

Available variables in templates:

- `{tags}` - All enabled tags (joined by tag separator)
- `{ids}` - All enabled IDs (joined by ID separator)
- `{channel_username}` - Channel username (e.g., @channel)
- `{channel_id}` - Channel chat ID
- `{message_id}` - Post message ID
- `{date}` - Post date/time
- `{post_type}` - Type: text, photo, video, document, etc.
- `{permalink}` - Direct link to post (if username available)
- `{custom1}`, `{custom2}`, `{custom3}` - Custom placeholders

**Example Template**:
```
🔖 {tags}
🆔 {ids}

Post: {message_id} · {date}
{permalink}
```

### Adding Channels

1. Go to Admin Panel → Channels
2. Click "Add New Channel"
3. Enter:
   - **Chat ID**: Channel chat ID (e.g., `-1001234567890`)
   - **Username**: Optional (e.g., `@channel_username`)
   - **Enabled**: Toggle on/off
   - **Template**: Optional per-channel template (leave empty for default)

**Finding Chat ID**:
- Use @userinfobot in your channel
- Or check channel post JSON in webhook logs

### Managing Tags & IDs

1. Go to Admin Panel → Tags
2. Add new tag or ID:
   - **Type**: Tag or ID
   - **Value**: The actual tag/ID value
   - **Sort**: Sort order (0 = first)
   - **Enabled**: Toggle on/off

Tags and IDs are rendered in templates using `{tags}` and `{ids}` variables.

### Webhook Security

The webhook endpoint is protected by:
- **Secret Token**: Set during installation, verified on each request
- **X-Telegram-Bot-Api-Secret-Token** header verification
- **POST-only**: GET requests are blocked

Webhook URL format: `https://yourdomain.com/webhook/{RANDOM_SLUG}`

## 🔧 Configuration

### Environment File (`config/.env.php`)

Generated during installation. Contains:

```php
return [
    'DB_HOST' => 'localhost',
    'DB_NAME' => 'telegram_bot',
    'DB_USER' => 'db_user',
    'DB_PASS' => 'db_password',
    
    'BOT_TOKEN' => '123456:ABC-DEF...',
    'WEBHOOK_SLUG' => 'random32chars',
    'SECRET_TOKEN' => 'random64chars',
    'BASE_URL' => 'https://yourdomain.com',
    
    'PARSE_MODE' => 'MarkdownV2', // or 'HTML'
    'LOCALE' => 'fa', // 'en' or 'fa'
    'GLOBAL_ON' => true,
];
```

## 🛡️ Security Features

- **Password Hashing**: Uses PHP `password_hash()` (bcrypt)
- **CSRF Protection**: All admin forms use CSRF tokens
- **SQL Injection Prevention**: All queries use prepared statements
- **XSS Prevention**: All output is escaped with `htmlspecialchars()`
- **Secret Token**: Webhook endpoint requires secret token header
- **Session Security**: HTTP-only cookies, secure flag on HTTPS

## 📊 Database Schema

Tables:
- `settings` - Bot configuration (key-value)
- `channels` - Allowed channels list
- `tags` - Tags and IDs for templates
- `posts` - Post history (idempotency tracking)
- `admins` - Admin users
- `install_flag` - Installation status

See `/sql/schema.sql` for full schema.

## 🐛 Troubleshooting

### Bot Not Replying

1. **Check Global Toggle**: Admin Panel → Settings → Global Toggle
2. **Check Channel**: Ensure channel is added and enabled
3. **Check Bot Permissions**: Bot must be admin in channel
4. **Check Webhook**: Admin Panel → Health → Webhook status
5. **Check Logs**: `/logs/` directory for error messages

### Webhook Errors

- **401 Unauthorized**: Check `SECRET_TOKEN` in `.env.php`
- **404 Not Found**: Check webhook URL and slug
- **403 Forbidden**: Bot may not be admin in channel
- **429 Rate Limit**: Too many requests; wait and retry

### Database Errors

- Check database credentials in `.env.php`
- Ensure database user has CREATE, INSERT, UPDATE, DELETE privileges
- Check `/logs/` for detailed error messages

### cPanel Issues

- Ensure `public/` is your document root
- Check `.htaccess` is enabled (mod_rewrite)
- Verify PHP version is 8.1+
- Check file permissions: `logs/` must be writable (755)

## 📁 File Structure

```
/
├── public/              # Public web root
│   ├── index.php        # Webhook entry point
│   ├── set_webhook.php  # Webhook setup helper
│   ├── .htaccess        # Security & routing
│   └── admin/           # Admin panel entry points
├── src/
│   ├── Config.php       # Configuration loader
│   ├── Bootstrap.php    # Application bootstrap
│   ├── Router.php       # Request routing
│   ├── Logger.php       # Logging service
│   ├── Controllers/     # MVC controllers
│   ├── Services/        # Business logic
│   └── Repositories/    # Data access layer
├── templates/           # Admin panel templates
├── sql/                 # Database schema
│   ├── schema.sql       # Table definitions
│   └── seed.sql         # Initial data
├── config/              # Configuration files
│   ├── .env.example.php # Example config
│   └── .env.php         # Actual config (generated)
├── install/             # Installation wizard
├── logs/                # Application logs
├── composer.json        # PHP dependencies
└── README.md            # This file
```

## 🚀 Production Deployment

### Recommended Settings

1. **Remove Installer**: Delete or protect `/install/` directory
2. **Set Permissions**: 
   - `logs/` → 755 (writable)
   - `config/.env.php` → 600 (read-only, secure)
3. **Enable HTTPS**: Update `.htaccess` to force HTTPS
4. **Monitor Logs**: Regularly check `/logs/` for errors
5. **Backup Database**: Regular MySQL backups recommended

### Performance

- Webhook-based (no polling overhead)
- Database connection pooling
- Prepared statements for SQL efficiency
- Rate limiting prevents API abuse

## 📄 License

MIT License - See [LICENSE](LICENSE) file for details.

## 🤝 Contributing

Contributions welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 💬 Support

For issues and questions:
- Create an issue on GitHub
- Check `/admin/health.php` for system status
- Review `/logs/` for error details

## 🙏 Acknowledgments

Built with ❤️ for the Telegram community.

---

**Version**: 2.0  
**Last Updated**: 2024
