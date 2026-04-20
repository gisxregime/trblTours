# Gmail Auth System - Code Examples & Integration Guide

Complete code examples for testing and extending the custom auth system.

---

## 📝 Code Snippets

### Testing Registration (Tinker)

```php
php artisan tinker

>>> use App\Models\User;
>>> use App\Services\GmailMailService;
>>> use Illuminate\Support\Facades\Hash;

>>> // Create test user
>>> $user = User::create([
>>>     'name' => 'Diana Grace',
>>>     'email' => 'diana@example.com',
>>>     'password' => Hash::make('SecurePass123'),
>>>     'otp_code' => '123456',
>>>     'otp_expires_at' => now()->addMinutes(15),
>>> ]);

>>> // Send OTP
>>> $mail = new GmailMailService();
>>> $mail->sendOTP($user->email, $user->otp_code, $user->name);

>>> // Verify OTP
>>> $user->update([
>>>     'email_verified_at' => now(),
>>>     'otp_code' => null,
>>>     'otp_expires_at' => null,
>>> ]);

>>> // Check user is verified
>>> $user->refresh();
>>> echo $user->email_verified_at;

>>> exit
```

### Testing Password Reset (Tinker)

```php
php artisan tinker

>>> use App\Models\User;
>>> use App\Models\PasswordReset;
>>> use Illuminate\Support\Str;
>>> use App\Services\GmailMailService;

>>> // Get verified user
>>> $user = User::where('email_verified_at', '!=', null)->first();

>>> // Generate reset token
>>> $token = Str::random(60);
>>> PasswordReset::create([
>>>     'user_id' => $user->id,
>>>     'token' => hash('sha256', $token),
>>> ]);

>>> // Create reset URL
>>> $resetUrl = url(route('password.reset.form', ['token' => $token], false));

>>> // Send reset link
>>> $mail = new GmailMailService();
>>> $mail->sendResetLink($user->email, $token, $user->name, $resetUrl);

>>> echo "Reset link: " . $resetUrl;
>>> exit
```

### Testing OTP Verification

```php
php artisan tinker

>>> use App\Models\User;

>>> // Get user with OTP
>>> $user = User::whereNotNull('otp_code')->first();

>>> // Check OTP details
>>> echo "OTP: " . $user->otp_code . "\n";
>>> echo "Expires: " . $user->otp_expires_at . "\n";
>>> echo "Expired? " . ($user->otp_expires_at->isPast() ? 'YES' : 'NO') . "\n";

>>> // Verify OTP
>>> if ($user->otp_code === '123456' && !$user->otp_expires_at->isPast()) {
>>>     $user->update([
>>>         'email_verified_at' => now(),
>>>         'otp_code' => null,
>>>         'otp_expires_at' => null,
>>>     ]);
>>>     echo "OTP Verified!\n";
>>> }

>>> exit
```

---

## 🔌 Custom Integration Examples

### Using GmailMailService in Your Own Controllers

```php
<?php

namespace App\Http\Controllers;

use App\Services\GmailMailService;
use Illuminate\Http\Request;

class MyController extends Controller
{
    public function sendCustomEmail(Request $request)
    {
        $mailService = new GmailMailService();
        
        // Send OTP
        $sent = $mailService->sendOTP(
            'user@gmail.com',
            '987654',
            'John Doe'
        );

        if ($sent) {
            return response()->json(['status' => 'Email sent']);
        } else {
            return response()->json(['error' => 'Failed to send email'], 500);
        }
    }
}
```

### Sending Custom HTML Email

```php
<?php

use PHPMailer\PHPMailer\PHPMailer;

class MyMailService
{
    public function sendCustom($to, $subject, $html)
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = env('GMAIL_USERNAME');
        $mail->Password = env('GMAIL_APP_PASSWORD');
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom(env('GMAIL_USERNAME'), env('APP_NAME'));
        $mail->addAddress($to);
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = $html;

        return $mail->send();
    }
}
```

---

## 🎯 Query Examples

### Find Users by Verification Status

```php
<?php

use App\Models\User;

// Get all verified users
$verified = User::whereNotNull('email_verified_at')->get();

// Get all unverified users
$unverified = User::whereNull('email_verified_at')->get();

// Get users with OTP (currently in verification process)
$withOtp = User::whereNotNull('otp_code')->get();

// Get expired OTPs
$expiredOtp = User::where('otp_expires_at', '<', now())->get();
```

### Find Password Reset Requests

```php
<?php

use App\Models\PasswordReset;

// Get active reset tokens (not expired)
$active = PasswordReset::where('created_at', '>', now()->subHour())->get();

// Get expired tokens
$expired = PasswordReset::where('created_at', '<', now()->subHour())->get();

// Find by user
$resetsByUser = PasswordReset::where('user_id', 1)->get();
```

---

## 📊 Database Queries

### Check User Verification

```sql
-- Count verified users
SELECT COUNT(*) as verified_users 
FROM users 
WHERE email_verified_at IS NOT NULL;

-- Count unverified users
SELECT COUNT(*) as unverified_users 
FROM users 
WHERE email_verified_at IS NULL;

-- List users with active OTP
SELECT id, name, email, otp_code, otp_expires_at 
FROM users 
WHERE otp_code IS NOT NULL 
AND otp_expires_at > NOW();

-- Cleanup expired OTPs
UPDATE users 
SET otp_code = NULL, otp_expires_at = NULL 
WHERE otp_expires_at < NOW() AND otp_code IS NOT NULL;
```

### Check Password Resets

```sql
-- Active password reset requests
SELECT pr.id, pr.user_id, u.name, u.email, pr.created_at
FROM password_resets pr
JOIN users u ON pr.user_id = u.id
WHERE pr.created_at > DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- Delete expired tokens
DELETE FROM password_resets 
WHERE created_at < DATE_SUB(NOW(), INTERVAL 1 HOUR);

-- Count resets per user
SELECT user_id, COUNT(*) as reset_count
FROM password_resets
GROUP BY user_id
ORDER BY reset_count DESC;
```

---

## 🧪 Unit Test Example

```php
<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GmailAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_creates_user_with_otp()
    {
        $response = $this->post('/gmail/register', [
            'name' => 'Test User',
            'email' => 'test@gmail.com',
            'password' => 'SecurePass123',
            'password_confirmation' => 'SecurePass123',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'test@gmail.com',
            'email_verified_at' => null,
        ]);

        $user = User::where('email', 'test@gmail.com')->first();
        $this->assertNotNull($user->otp_code);
        $this->assertNotNull($user->otp_expires_at);
    }

    public function test_otp_verification_marks_email_verified()
    {
        $user = User::factory()->create([
            'otp_code' => '123456',
            'otp_expires_at' => now()->addMinutes(15),
            'email_verified_at' => null,
        ]);

        $response = $this->post('/gmail/verify-otp', [
            'email' => $user->email,
            'otp_code' => '123456',
        ]);

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertNull($user->otp_code);
    }

    public function test_login_requires_verified_email()
    {
        $user = User::factory()->create([
            'password' => bcrypt('SecurePass123'),
            'email_verified_at' => null,
        ]);

        $response = $this->post('/gmail/login', [
            'email' => $user->email,
            'password' => 'SecurePass123',
        ]);

        $response->assertSessionHas('error');
        $this->assertGuest();
    }

    public function test_password_reset_sends_email()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $response = $this->post('/gmail/forgot-password', [
            'email' => $user->email,
        ]);

        $this->assertDatabaseHas('password_resets', [
            'user_id' => $user->id,
        ]);
    }
}
```

Run tests:
```bash
php artisan test tests/Feature/GmailAuthTest.php
```

---

## 🔐 Security Examples

### Rate Limiting

```php
// routes/auth.php
Route::post('/gmail/login', [LoginController::class, 'login'])
    ->middleware('throttle:5,1'); // 5 attempts per minute

Route::post('/gmail/register', [RegisterController::class, 'register'])
    ->middleware('throttle:3,1'); // 3 attempts per minute
```

### Checking User Roles/Permissions

```php
// Middleware example
Route::post('/gmail/logout', [LoginController::class, 'logout'])
    ->middleware('auth') // Must be logged in
    ->middleware('verified'); // Email must be verified

// In controller
public function logout(Request $request)
{
    if (!auth()->check()) {
        return redirect('/gmail/login');
    }

    if (!auth()->user()->email_verified_at) {
        return back()->with('error', 'Email not verified');
    }

    // ... logout logic
}
```

### Email Validation

```php
// In controller
$validated = $request->validate([
    'email' => [
        'required',
        'email',
        'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/', // Gmail only
    ],
]);

// Or any email
$validated = $request->validate([
    'email' => ['required', 'email:rfc,dns'],
]);
```

---

## 📈 Monitoring & Logging

### Log Email Sends

```php
// In GmailMailService
private function logEmail($to, $subject)
{
    \Log::info('Email sent', [
        'to' => $to,
        'subject' => $subject,
        'timestamp' => now(),
    ]);
}
```

### Monitor OTP Usage

```php
// Check OTP resend frequency
$recent = User::where('email', $email)
    ->whereNotNull('otp_code')
    ->where('updated_at', '>', now()->subMinutes(1))
    ->count();

if ($recent > 5) {
    return back()->with('error', 'Too many OTP requests. Try again later.');
}
```

### Track Password Resets

```php
// Check reset frequency per user
$recentResets = PasswordReset::where('user_id', $user->id)
    ->where('created_at', '>', now()->subHours(24))
    ->count();

if ($recentResets > 3) {
    return back()->with('warning', 'Multiple reset requests detected. Check your email.');
}
```

---

## 🚀 Performance Tips

### Use Queue for Email Sending

```php
// Create a job
php artisan make:job SendOtpEmail

// In job
public function handle()
{
    $mailService = new GmailMailService();
    $mailService->sendOTP($this->email, $this->otp, $this->name);
}

// Dispatch from controller
SendOtpEmail::dispatch($email, $otp, $name);
```

### Cache Verification Status

```php
// Cache for 1 hour
$verified = Cache::remember("user.{$user->id}.verified", 3600, function () use ($user) {
    return $user->email_verified_at !== null;
});
```

### Index Database Columns

```sql
-- Add indexes for better query performance
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_otp_code ON users(otp_code);
CREATE INDEX idx_users_email_verified_at ON users(email_verified_at);
CREATE INDEX idx_password_resets_user_id ON password_resets(user_id);
CREATE INDEX idx_password_resets_created_at ON password_resets(created_at);
```

---

## 🐛 Debugging

### Enable PHPMailer Debug

```php
// In GmailMailService
if (app()->isLocal()) {
    $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
}

// Output SMTP conversation to logs:
$this->mail->Debugoutput = function($str, $level) {
    \Log::debug("SMTP[$level]: $str");
};
```

### Log All Errors

```php
try {
    $mailService->sendOTP($email, $otp, $name);
} catch (Exception $e) {
    \Log::error('OTP send failed', [
        'email' => $email,
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString(),
    ]);
}
```

---

## 📚 Resources

- **PHPMailer**: https://github.com/PHPMailer/PHPMailer
- **Gmail Settings**: https://myaccount.google.com/security
- **Laravel Mail**: https://laravel.com/docs/13/mail
- **Laravel Auth**: https://laravel.com/docs/13/authentication
- **Laravel Testing**: https://laravel.com/docs/13/testing

---

**Last Updated**: April 20, 2026  
**Version**: 1.0
