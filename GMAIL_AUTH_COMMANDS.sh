#!/bin/bash
# Gmail Auth System - Terminal Command Reference
# Copy & paste these commands to set up the system

echo "=== Gmail Auth System Setup ==="
echo ""

# ========================================
# 1. VERIFY INSTALLATION
# ========================================
echo "✓ Step 1: Verify Installation"
composer require phpmailer/phpmailer
echo ""

# ========================================
# 2. CHECK MIGRATIONS
# ========================================
echo "✓ Step 2: Check Migration Status"
php artisan migrate:status
echo ""

# ========================================
# 3. RUN MIGRATIONS
# ========================================
echo "✓ Step 3: Run Migrations (creates OTP and password_resets tables)"
php artisan migrate
echo ""
# If you get foreign key errors:
# php artisan migrate:fresh  # ⚠️ Warning: This deletes all data!

# ========================================
# 4. VERIFY DATABASE TABLES
# ========================================
echo "✓ Step 4: Verify Database Tables"
php artisan tinker << 'EOF'
echo "Users table structure:\n";
DB::statement('DESCRIBE users');
echo "\nPassword resets table structure:\n";
DB::statement('DESCRIBE password_resets');
exit;
EOF
echo ""

# ========================================
# 5. VERIFY ENVIRONMENT VARIABLES
# ========================================
echo "✓ Step 5: Check Gmail Configuration"
php artisan config:show mail
echo ""

php artisan tinker << 'EOF'
echo "Gmail Username: " . env('GMAIL_USERNAME') . "\n";
echo "Gmail App Password loaded: " . (env('GMAIL_APP_PASSWORD') ? 'YES' : 'NO') . "\n";
exit;
EOF
echo ""

# ========================================
# 6. TEST GMAIL CONNECTION
# ========================================
echo "✓ Step 6: Test Gmail SMTP Connection"
php artisan tinker << 'EOF'
use App\Services\GmailMailService;
try {
    $mail = new GmailMailService();
    echo "✓ Gmail SMTP connection successful!\n";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
exit;
EOF
echo ""

# ========================================
# 7. CHECK ROUTES
# ========================================
echo "✓ Step 7: Verify Auth Routes"
php artisan route:list | grep gmail
echo ""

# ========================================
# 8. FORMAT CODE
# ========================================
echo "✓ Step 8: Format Code with Pint"
vendor/bin/pint --dirty
echo ""

# ========================================
# 9. START DEVELOPMENT SERVER
# ========================================
echo "✓ Step 9: Start Development Server"
echo "Run in new terminal:"
echo "  php artisan serve"
echo ""
echo "Then visit:"
echo "  http://localhost:8000/gmail/register"
echo ""

# ========================================
# OPTIONAL: USEFUL COMMANDS
# ========================================
echo ""
echo "=== OPTIONAL TESTING COMMANDS ==="
echo ""

echo "Monitor email logs in real-time:"
echo "  tail -f storage/logs/laravel.log | grep -i 'email\\|smtp'"
echo ""

echo "Interactive testing with Tinker:"
echo "  php artisan tinker"
echo "  >>> use App\Services\GmailMailService;"
echo "  >>> \$mail = new GmailMailService();"
echo "  >>> \$mail->sendOTP('test@gmail.com', '123456', 'Test User');"
echo "  >>> exit"
echo ""

echo "List all auth routes:"
echo "  php artisan route:list --name=gmail"
echo ""

echo "Completely reset database (⚠️ deletes all data):"
echo "  php artisan migrate:fresh"
echo ""

echo "Clear all caches:"
echo "  php artisan cache:clear"
echo "  php artisan config:clear"
echo ""

echo "Create test user manually:"
echo "  php artisan tinker"
echo "  >>> use App\Models\User;"
echo "  >>> User::create(['name' => 'Test', 'email' => 'test@gmail.com', 'password' => bcrypt('test123'), 'email_verified_at' => now()]);"
echo "  >>> exit"
echo ""

echo "=== SETUP COMPLETE ==="
