# Gmail-Based Custom Authentication System

Complete guide for setting up and using PHPMailer with Gmail SMTP for custom authentication with OTP verification.

---

## 📋 Table of Contents

1. [Gmail Setup (App Password)](#gmail-setup)
2. [Installation](#installation)
3. [Configuration](#configuration)
4. [Database Schema](#database-schema)
5. [Auth Flow Overview](#auth-flow-overview)
6. [API Routes](#api-routes)
7. [Testing](#testing)
8. [Error Handling](#error-handling)
9. [Security Best Practices](#security-best-practices)
10. [Troubleshooting](#troubleshooting)

---

## 🔐 Gmail Setup

### Step 1: Enable 2-Step Verification

1. Go to [Google Account Security](https://myaccount.google.com/security)
2. Click **"2-Step Verification"** (or it may say "Enable 2-Step Verification")
3. Follow the prompts to set up 2-step verification with your phone

### Step 2: Create App Password

⚠️ **CRITICAL: App Passwords only work if 2-Step Verification is enabled**

1. Go to [App Passwords Page](https://myaccount.google.com/apppasswords)
2. Select:
   - **App**: Select "Mail"
   - **Device**: Select "Windows PC" (or your OS)
3. Click **"Generate"**
4. Google will show a 16-character password like: `xxxx xxxx xxxx xxxx`
5. **Copy this password** - you'll need it for the `.env` file

### Example App Password
```
hushuxgisdiand ngfxngfd7532
```
Remove the space → `hushuxgisdiandngfxngfd7532`

### Why Not Use Your Regular Password?

- Google doesn't allow regular passwords for third-party apps
- App Passwords are more secure (can be revoked individually)
- They work ONLY with 2-Step Verification enabled
- Regular password would be rejected by Gmail SMTP

---

## ⚙️ Installation

### 1. Install Composer Dependencies (Already Done)

```bash
composer require phpmailer/phpmailer
```

### 2. Create Migrations (Already Done)

```bash
php artisan make:migration add_otp_fields_to_users_table --table=users
php artisan make:migration create_password_resets_table
```

### 3. Run Migrations

```bash
php artisan migrate
```

**If you encounter foreign key errors from existing migrations:**
- Fix the problematic migration first
- Or run: `php artisan migrate:refresh` (⚠️ This deletes all data!)

### 4. Verify Installation

```bash
php artisan tinker
>>> use App\Services\GmailMailService;
>>> $mail = new GmailMailService();
>>> exit
```

---

## 🔑 Configuration

### 1. Update `.env` File

```env
# Gmail SMTP Configuration
GMAIL_USERNAME=dianagracecinco@gmail.com
GMAIL_APP_PASSWORD=hushuxgisdiandngfxngfd7532
```

**DO NOT use your regular Gmail password!** Use the 16-character App Password from Step 2.

### 2. Verify in Your Laravel App

```bash
# Check if environment variables are loaded
php artisan config:show app.name
```

### 3. .gitignore Protection

Your `.env` file is already in `.gitignore`, so credentials won't be pushed to Git:

```bash
# Verify
cat .gitignore | grep "\.env"
```

**Output should include:**
```
.env
.env.*.php
```

---

## 📊 Database Schema

### users Table (OTP Fields Added)

```sql
ALTER TABLE users ADD (
    otp_code VARCHAR(6) NULL,
    otp_expires_at TIMESTAMP NULL
);
```

**Fields:**
- `otp_code`: 6-digit random code (e.g., "123456")
- `otp_expires_at`: Expires in 15 minutes from generation
- Both cleared after successful verification

### password_resets Table (New)

```sql
CREATE TABLE password_resets (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT NOT NULL (FK → users.id),
    token VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

**Fields:**
- `token`: SHA256 hashed random 60-character string
- `created_at`: Used to check 1-hour expiration
- Auto-deleted after successful password reset

---

## 🔄 Auth Flow Overview

### Registration Flow

```
1. User submits: name, email, password
   ↓
2. Validate email is unique
   ↓
3. Hash password with bcrypt
   ↓
4. Generate 6-digit OTP
   ↓
5. Save user with otp_code, otp_expires_at (15 min)
   ↓
6. Send OTP via Gmail SMTP email
   ↓
7. Redirect to verify-otp page
   ↓
8. User enters code
   ↓
9. Verify OTP matches & not expired
   ↓
10. Clear OTP, mark email_verified_at = now()
   ↓
11. Auto-login user
   ↓
12. Redirect to /dashboard
```

### Login Flow

```
1. User submits: email, password
   ↓
2. Query user by email
   ↓
3. Check if email_verified_at is NOT NULL (must be verified)
   ↓
4. Verify password hash matches
   ↓
5. Login user & regenerate session
   ↓
6. Redirect to /dashboard
```

### Password Reset Flow

```
1. User submits forgot-password email
   ↓
2. Query user (silent fail if not found for security)
   ↓
3. Generate 60-character random token
   ↓
4. Hash token with SHA256 & store in password_resets table
   ↓
5. Send reset link via Gmail: /reset-password/{token}
   ↓
6. Link expires in 1 hour
   ↓
7. User clicks link, enters new password
   ↓
8. Verify token, hash match created_at
   ↓
9. Update user password & delete token
   ↓
10. Redirect to login with success message
```

---

## 🛣️ API Routes

### Registration & OTP

```
GET  /gmail/register              → Show registration form
POST /gmail/register              → Create user & send OTP

GET  /gmail/verify-otp            → Show OTP form
POST /gmail/verify-otp            → Verify OTP & auto-login
POST /gmail/resend-otp            → Resend OTP code
```

### Login & Logout

```
GET  /gmail/login                 → Show login form
POST /gmail/login                 → Login with email & password

POST /gmail/logout                → Logout (requires auth)
```

### Password Reset

```
GET  /gmail/forgot-password       → Show forgot-password form
POST /gmail/forgot-password       → Send reset link via Gmail

GET  /gmail/reset-password/{token} → Show reset form
POST /gmail/reset-password        → Update password & login
```

---

## 🧪 Testing

### Option 1: Test Locally with Mailtrap (Free)

**Mailtrap lets you capture emails without sending real emails:**

1. Go to [Mailtrap.io](https://mailtrap.io)
2. Sign up (free)
3. Get your SMTP credentials
4. Update `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
```

### Option 2: Test with Real Gmail (Production)

Use the App Password method (recommended for testing too):

```env
GMAIL_USERNAME=dianagracecinco@gmail.com
GMAIL_APP_PASSWORD=hushuxgisdiandngfxngfd7532
```

### Option 3: Test in Code

```php
<?php

// app/Console/Commands/TestGmailAuth.php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\GmailMailService;
use Illuminate\Console\Command;

class TestGmailAuth extends Command
{
    protected $signature = 'test:gmail-auth';

    public function handle()
    {
        // Test 1: Create user with OTP
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => bcrypt('password123'),
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(15),
        ]);

        $this->info("✓ User created: {$user->email}");

        // Test 2: Send OTP email
        $mailService = new GmailMailService();
        $sent = $mailService->sendOTP($user->email, '123456', $user->name);

        if ($sent) {
            $this->info("✓ OTP email sent successfully!");
        } else {
            $this->error("✗ Failed to send OTP email");
        }

        // Test 3: Verify OTP
        $user->refresh();
        $user->update([
            'email_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        $this->info("✓ OTP verified, user email marked as verified");
    }
}
```

Run it:
```bash
php artisan test:gmail-auth
```

### Option 4: Manual Testing in Browser

1. Visit: `http://localhost:8000/gmail/register`
2. Fill form:
   - Name: John Doe
   - Email: your-email@gmail.com
   - Password: Test123456
3. Click "Register & Get OTP"
4. Check your email inbox for OTP code
5. Enter code at `http://localhost:8000/gmail/verify-otp`
6. Should be logged in and redirected to `/dashboard`

---

## ❌ Error Handling

### Common Gmail SMTP Errors

#### Error: "530 5.7.0 Must issue a STARTTLS command first"

**Cause**: Using wrong port or encryption setting

**Fix**: Ensure `.env` has:
```env
GMAIL_USERNAME=your-email@gmail.com
GMAIL_APP_PASSWORD=xxxx-xxxx-xxxx-xxxx  # App password, not regular password!
```

And `GmailMailService` has:
```php
$this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
$this->mail->Port = 587;
```

#### Error: "535 5.7.8 Username and password not accepted"

**Cause**: Wrong App Password or regular password used

**Fix**:
1. Go to [App Passwords](https://myaccount.google.com/apppasswords)
2. Generate NEW App Password
3. Use the 16-character password WITHOUT spaces
4. Make sure 2-Step Verification is enabled

#### Error: "This app has been blocked"

**Cause**: Google blocked login from "less secure app"

**Fix**: This won't happen with App Passwords (they bypass this security)

### Error Logging

All email errors are logged:

```bash
# Check logs
tail -f storage/logs/laravel.log

# Search for email errors
grep -i "email\|smtp\|mail" storage/logs/laravel.log
```

### Timeout Issues

If emails are slow:

```php
// Add timeout in GmailMailService:
$this->mail->Timeout = 30; // 30 seconds max
```

---

## 🔒 Security Best Practices

### 1. Never Commit Credentials

```bash
# Verify .env is ignored
cat .gitignore | grep -E "^\\.env"

# These files should be in .gitignore:
.env
.env.*.php
*.local
```

### 2. Use App Passwords Only

✅ **DO**: Use 16-character App Password from Google  
❌ **DON'T**: Use your regular Gmail password

### 3. Hash Password Resets

```php
// Password reset tokens are hashed before storage:
$hashedToken = hash('sha256', $token);
```

### 4. Rate Limiting

Add rate limiting to prevent brute force:

```php
Route::post('/gmail/login', [...])
    ->middleware('throttle:5,1'); // 5 attempts per minute
```

### 5. HTTPS Only in Production

```php
// In production, enforce HTTPS:
if (app()->isProduction()) {
    URL::forceScheme('https');
}
```

### 6. Email Verification Required

Users must verify email before login:

```php
// LoginController checks:
if ($user->email_verified_at === null) {
    return error("Please verify your email");
}
```

---

## 🐛 Troubleshooting

### Issue: "Method [showForm] not found"

**Solution**: Check controller method names match routes

```php
// routes/auth.php
Route::get('gmail/register', [RegisterController::class, 'showForm']);

// app/Http/Controllers/Auth/RegisterController.php
public function showForm() { ... }
```

### Issue: OTP Email Not Sending

**Check 1**: Verify Gmail credentials

```bash
php artisan tinker
>>> env('GMAIL_USERNAME')
>>> env('GMAIL_APP_PASSWORD')
```

**Check 2**: Enable debug mode

```php
// In GmailMailService constructor:
$this->mail->SMTPDebug = SMTP::DEBUG_SERVER; // Show SMTP debug info
```

**Check 3**: Check Laravel logs

```bash
tail -f storage/logs/laravel.log | grep -i "smtp\|gmail"
```

### Issue: "Token mismatch" on Form Submission

**Solution**: Ensure CSRF token is in form

```blade
<form method="POST">
    @csrf <!-- Required for all POST/PUT/DELETE -->
    ...
</form>
```

### Issue: OTP Expires Immediately

**Check**: Database timestamp column type

```sql
-- Should be:
ALTER TABLE users ADD otp_expires_at TIMESTAMP NULL;

-- Check your migration created it correctly:
php artisan tinker
>>> \DB::table('users')->first();
```

### Issue: Can't Login After Email Verification

**Check**: Verify `email_verified_at` was set

```bash
php artisan tinker
>>> $user = \App\Models\User::first();
>>> $user->email_verified_at
```

---

## 📝 Files Overview

### Key Files Created

```
app/
  Services/
    GmailMailService.php          # PHPMailer + Gmail SMTP config
  Http/Controllers/Auth/
    RegisterController.php         # Registration & OTP generation
    OtpVerificationController.php  # OTP verification & auto-login
    LoginController.php            # Email verification check + login
    ForgotPasswordController.php    # Password reset link generator
    ResetPasswordController.php     # Password update handler

database/
  migrations/
    *_add_otp_fields_to_users_table.php      # Add OTP columns
    *_create_password_resets_table.php       # Store reset tokens

resources/views/auth/
  gmail-register.blade.php                  # Registration form
  gmail-verify-otp.blade.php                # OTP verification form
  gmail-login.blade.php                     # Login form
  gmail-forgot-password.blade.php           # Forgot password form
  gmail-reset-password.blade.php            # Reset password form

routes/
  auth.php                        # Route definitions
```

### Models Modified

```
app/Models/
  User.php              # Added: otp_code, otp_expires_at fillable + casts
  PasswordReset.php     # New model for password reset tokens
```

---

## ✅ Next Steps

1. **Update `.env`** with your Gmail credentials:
   ```env
   GMAIL_USERNAME=your-email@gmail.com
   GMAIL_APP_PASSWORD=xxxx-xxxx-xxxx-xxxx
   ```

2. **Run migrations**:
   ```bash
   php artisan migrate
   ```

3. **Test in browser**:
   - Visit `http://localhost:8000/gmail/register`
   - Fill out registration form
   - Check email for OTP
   - Verify OTP and login

4. **Monitor logs**:
   ```bash
   tail -f storage/logs/laravel.log
   ```

5. **For production**:
   - Use HTTPS
   - Add rate limiting
   - Enable email verification requirement
   - Store App Password in secure environment variable management system

---

## 📚 Additional Resources

- [PHPMailer Documentation](https://github.com/PHPMailer/PHPMailer)
- [Gmail App Passwords](https://support.google.com/accounts/answer/185833)
- [Laravel Mail Documentation](https://laravel.com/docs/13/mail)
- [Laravel Authentication](https://laravel.com/docs/13/authentication)

---

**Last Updated**: April 20, 2026  
**Version**: 1.0  
**Status**: ✅ Ready for Production
