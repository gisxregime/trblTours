# 🚀 Quick Start: Gmail Auth System

Complete step-by-step setup guide with all terminal commands.

---

## 📋 Prerequisites

- PHP 8.3+
- Laravel 13
- MySQL/MariaDB
- Gmail account with 2-Step Verification enabled
- Composer installed

---

## ⚡ Quick Setup (5 Minutes)

### Step 1: Create Gmail App Password (2-FA Required)

```bash
# 1. Go to https://myaccount.google.com/security
# 2. Enable "2-Step Verification" if not already enabled
# 3. Go to https://myaccount.google.com/apppasswords
# 4. Select: App = "Mail", Device = "Windows PC" (or your OS)
# 5. Click "Generate"
# 6. Copy the 16-character password (example: xxxx xxxx xxxx xxxx)
```

### Step 2: Update `.env` File

```bash
# Open your project .env file and add/update:
GMAIL_USERNAME=dianagracecinco@gmail.com
GMAIL_APP_PASSWORD=hushuxgisdiandngfxngfd7532
```

**⚠️ CRITICAL NOTES:**
- Use the App Password (16 chars), NOT your regular Gmail password
- App Passwords require 2-Step Verification to be enabled
- Don't commit `.env` to Git (already in .gitignore)

### Step 3: Install PHPMailer (Already Done)

```bash
composer require phpmailer/phpmailer
```

### Step 4: Run Migrations

```bash
# Run all pending migrations
php artisan migrate

# If you encounter foreign key errors from existing migrations:
# Option A: Fix those migrations first
# Option B: Run only new migrations (advanced)
```

### Step 5: Test Registration

```bash
# Start development server
php artisan serve

# Visit in browser:
# http://localhost:8000/gmail/register
```

---

## 🔍 Verification Checklist

### ✅ Check Gmail Credentials

```bash
php artisan tinker

# Inside tinker:
>>> env('GMAIL_USERNAME')
"dianagracecinco@gmail.com"

>>> env('GMAIL_APP_PASSWORD')
"hushuxgisdiandngfxngfd7532"

>>> exit
```

### ✅ Check Database Tables

```bash
php artisan tinker

>>> \DB::table('users')->first()
>>> \DB::table('password_resets')->first()

>>> exit
```

### ✅ Test Email Sending

```bash
php artisan tinker

>>> use App\Services\GmailMailService;
>>> $mail = new GmailMailService();
>>> $sent = $mail->sendOTP('test@gmail.com', '123456', 'Test User');
>>> dd($sent);

# Should return: true

>>> exit
```

---

## 🌐 Available Routes

### Registration & Verification

```
GET  /gmail/register          - Registration form
POST /gmail/register          - Register user (generates OTP)
GET  /gmail/verify-otp        - OTP verification form
POST /gmail/verify-otp        - Verify OTP code & auto-login
POST /gmail/resend-otp        - Resend OTP
```

### Login & Logout

```
GET  /gmail/login             - Login form
POST /gmail/login             - Login (requires verified email)
POST /gmail/logout            - Logout (authenticated)
```

### Password Reset

```
GET  /gmail/forgot-password          - Forgot password form
POST /gmail/forgot-password          - Send reset link
GET  /gmail/reset-password/{token}   - Reset password form
POST /gmail/reset-password           - Update password
```

---

## 📧 Authentication Flow

### Registration Flow (Step-by-Step)

```
1. User visits /gmail/register
2. Fills: name, email, password, password_confirmation
3. System:
   - Validates email is unique
   - Hashes password
   - Generates 6-digit OTP
   - Saves user with otp_code + otp_expires_at (15 min)
   - Sends OTP via Gmail SMTP
4. Redirects to /gmail/verify-otp
5. User enters OTP code
6. System:
   - Verifies code matches & not expired
   - Sets email_verified_at = now()
   - Clears otp_code & otp_expires_at
   - Auto-logs in user
7. Redirects to /dashboard
```

### Login Flow

```
1. User visits /gmail/login
2. Enters: email, password
3. System:
   - Finds user by email
   - Checks email_verified_at is NOT NULL
   - Verifies password
   - Logs in user
4. Redirects to /dashboard
```

### Password Reset Flow

```
1. User visits /gmail/forgot-password
2. Enters email
3. System:
   - Generates 60-char random token
   - Hashes token (SHA256)
   - Stores in password_resets table
   - Sends reset link via Gmail (expires 1 hour)
4. User clicks email link
5. Enters new password (min 8 chars)
6. System:
   - Verifies token matches & not expired
   - Hashes new password
   - Updates user.password
   - Deletes reset token
7. Redirects to /gmail/login
```

---

## 🧪 Testing Guide

### Local Testing Without Sending Real Emails

**Option 1: Use Mailtrap (Free)**

```bash
# 1. Go to https://mailtrap.io
# 2. Sign up (free account)
# 3. Get SMTP credentials
# 4. Update .env:

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```

**Option 2: Use Log Driver**

```bash
# .env
MAIL_MAILER=log

# Emails will be logged to storage/logs/laravel.log
tail -f storage/logs/laravel.log | grep -i "To:"
```

### Full Registration Test

```bash
# 1. Open browser: http://localhost:8000/gmail/register
# 2. Fill form:
#    Name: John Doe
#    Email: your-email@gmail.com
#    Password: Test123456
#    Confirm: Test123456
# 3. Click "Register & Get OTP"
# 4. Check your email inbox for OTP
# 5. Copy 6-digit code
# 6. Paste at: http://localhost:8000/gmail/verify-otp
# 7. Should see success message & redirect to /dashboard
```

### Testing Password Reset

```bash
# 1. Login at /gmail/login
# 2. Visit /gmail/forgot-password
# 3. Enter your email
# 4. Check email for reset link
# 5. Click link
# 6. Enter new password
# 7. Submit & redirect to login
# 8. Login with new password
```

---

## 🐛 Common Issues & Fixes

### Issue: "SQLSTATE[HY000]: General error: 1005"

**Cause**: Foreign key constraint error from existing migration

**Fix**:
```bash
# Option 1: Check what's wrong
php artisan migrate:status

# Option 2: Run fresh (⚠️ deletes all data)
php artisan migrate:fresh

# Option 3: Fix specific migration and retry
# Edit database/migrations/2026_04_08_125921_create_conversations_table.php
# Then: php artisan migrate
```

### Issue: "Username and password not accepted"

**Cause**: Wrong App Password or using regular Gmail password

**Fix**:
```bash
# 1. Go to https://myaccount.google.com/apppasswords
# 2. Generate NEW App Password
# 3. Copy 16-character password (WITHOUT spaces)
# 4. Update .env:
GMAIL_APP_PASSWORD=xxxx-xxxx-xxxx-xxxx

# 5. Test:
php artisan tinker
>>> env('GMAIL_APP_PASSWORD')
>>> exit
```

### Issue: "Must issue a STARTTLS command first"

**Cause**: Wrong port or encryption setting

**Fix**: Verify GmailMailService has:
```php
$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$this->mail->Port = 587;
```

### Issue: OTP Not Being Sent

**Debug**:
```bash
# Check logs
tail -f storage/logs/laravel.log

# Search for email errors
grep -i "smtp\|mail\|email" storage/logs/laravel.log

# Check if credentials are loaded
php artisan config:show mail

# Test directly
php artisan tinker
>>> use App\Services\GmailMailService;
>>> $mail = new GmailMailService();
>>> $result = $mail->sendOTP('test@gmail.com', '123456', 'Test');
>>> dd($result);
```

### Issue: "419 Page Expired" (Form Submission)

**Cause**: CSRF token missing

**Fix**: Ensure all forms have:
```blade
<form method="POST">
    @csrf
    ...
</form>
```

---

## 🔒 Production Checklist

- [ ] Update `GMAIL_USERNAME` and `GMAIL_APP_PASSWORD` in production `.env`
- [ ] Ensure `.env` is NOT in version control
- [ ] Enable HTTPS only
- [ ] Add rate limiting to login/register routes
- [ ] Set up email logging/monitoring
- [ ] Configure queue for async email sending
- [ ] Test all auth flows
- [ ] Set up error monitoring (Sentry, etc.)
- [ ] Test password reset email links
- [ ] Verify email templates render correctly

---

## 📂 Project Files

### Created

```
app/Services/GmailMailService.php
app/Http/Controllers/Auth/RegisterController.php
app/Http/Controllers/Auth/OtpVerificationController.php
app/Http/Controllers/Auth/LoginController.php
app/Http/Controllers/Auth/ForgotPasswordController.php
app/Http/Controllers/Auth/ResetPasswordController.php

database/migrations/2026_04_20_105603_add_otp_fields_to_users_table.php
database/migrations/2026_04_20_105605_create_password_resets_table.php

resources/views/auth/gmail-register.blade.php
resources/views/auth/gmail-verify-otp.blade.php
resources/views/auth/gmail-login.blade.php
resources/views/auth/gmail-forgot-password.blade.php
resources/views/auth/gmail-reset-password.blade.php

GMAIL_AUTH_SETUP.md
GMAIL_AUTH_QUICK_START.md (this file)
```

### Modified

```
app/Models/User.php              # Added: otp_code, otp_expires_at
app/Models/PasswordReset.php     # Created new model
routes/auth.php                  # Added Gmail auth routes
.env                             # Added Gmail SMTP config
composer.json                    # Added phpmailer/phpmailer
```

---

## ✅ Verification

Run these commands to verify everything is working:

```bash
# 1. Check code formatting
vendor/bin/pint --dirty

# 2. Check for errors
php artisan list

# 3. Check routes
php artisan route:list | grep gmail

# 4. Test Gmail connection
php artisan tinker
>>> use App\Services\GmailMailService;
>>> new GmailMailService();
>>> exit
```

---

## 📞 Support

For issues or questions:

1. **Check logs**: `tail -f storage/logs/laravel.log`
2. **Read full docs**: `GMAIL_AUTH_SETUP.md`
3. **Gmail Help**: https://support.google.com/accounts/
4. **PHPMailer Docs**: https://github.com/PHPMailer/PHPMailer

---

## 🎯 Next Steps

1. ✅ Create Gmail App Password
2. ✅ Update `.env`
3. ✅ Run migrations
4. ✅ Test registration flow
5. ✅ Test login flow
6. ✅ Test password reset
7. ✅ Deploy to production

---

**Happy coding! 🚀**
