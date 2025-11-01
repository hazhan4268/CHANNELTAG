# ربات تلگرام با PHP 🤖

[![PHP Version](https://img.shields.io/badge/PHP-%3E%3D7.4-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

یک ربات تلگرام ساده و قابل توسعه با زبان PHP برای مدیریت کانال.

A simple and extensible Telegram bot with PHP for channel management.

## ✨ ویژگی‌ها

- ✅ پاسخ به دستورات `/start`, `/help`, `/about`
- ✅ پاسخ به پیام‌های متنی
- ✅ ساختار کد تمیز و قابل توسعه
- ✅ پشتیبانی از فایل `.env` برای تنظیمات
- ✅ مدیریت کانال تلگرام

## 📋 پیش‌نیازها

- PHP 7.4 یا بالاتر
- cURL extension
- Composer (اختیاری)
- یک ربات تلگرام از [BotFather](https://t.me/BotFather)

## 🚀 نصب و راه‌اندازی

### 1. کلون کردن ریپازیتوری

```bash
git clone https://github.com/YOUR_USERNAME/telegram-bot-php.git
cd telegram-bot-php
```

### 2. دریافت توکن ربات

- به [@BotFather](https://t.me/BotFather) در تلگرام بروید
- دستور `/newbot` را ارسال کنید
- نام و username ربات را تعیین کنید
- توکن دریافتی را کپی کنید

### 3. تنظیم توکن

**روش 1: استفاده از فایل `.env` (پیشنهادی)**

یک فایل `.env` در ریشه پروژه ایجاد کنید:

```bash
BOT_TOKEN=your_bot_token_here
```

**روش 2: تنظیم مستقیم در کد**

توکن را مستقیماً در فایل `bot.php` وارد کنید (خط 148).

### 4. اجرای ربات

```bash
php bot.php
```

## 💻 توسعه ربات

می‌توانید با ویرایش متد `processMessage()` در کلاس `TelegramBot`، دستورات و پاسخ‌های جدید اضافه کنید.

### مثال اضافه کردن دستور جدید:

```php
case '/mycommand':
    $response = "پاسخ دستور جدید شما";
    $this->sendMessage($chatId, $response);
    break;
```

## 📜 دستورات موجود

- `/start` - شروع ربات و نمایش راهنما
- `/help` - راهنمای استفاده
- `/about` - اطلاعات درباره ربات

## 📝 ساختار پروژه

```
telegram-bot-php/
├── bot.php           # فایل اصلی ربات
├── composer.json     # فایل وابستگی‌های Composer
├── .gitignore       # فایل‌های نادیده‌گرفته شده در Git
├── LICENSE          # مجوز پروژه
└── README.md        # این فایل
```

## 🤝 مشارکت

مشارکت شما خوش‌آمد است! لطفاً:

1. این ریپازیتوری را Fork کنید
2. یک شاخه جدید ایجاد کنید (`git checkout -b feature/AmazingFeature`)
3. تغییرات خود را commit کنید (`git commit -m 'Add some AmazingFeature'`)
4. به شاخه push کنید (`git push origin feature/AmazingFeature`)
5. یک Pull Request باز کنید

## 📄 مجوز

این پروژه تحت مجوز MIT منتشر شده است. برای جزئیات بیشتر فایل [LICENSE](LICENSE) را مطالعه کنید.

## ⚠️ نکات مهم

- ربات باید همیشه در حال اجرا باشد (مثلاً روی سرور یا VPS)
- برای استفاده در production، از supervisor یا systemd برای مدیریت ربات استفاده کنید
- از webhook به جای polling برای بهینه‌سازی استفاده کنید
- **هرگز توکن ربات را در کد commit نکنید!** از فایل `.env` استفاده کنید

## 💬 پشتیبانی

برای سوالات و مشکلات، می‌توانید issue ایجاد کنید.

## 🙏 تشکر

از استفاده از این ربات متشکرم!

