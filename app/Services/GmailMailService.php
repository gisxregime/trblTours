<?php

namespace App\Services;

use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class GmailMailService
{
    private $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->setupGmailSMTP();
    }

    private function setupGmailSMTP(): void
    {
        try {
            $this->mail->isSMTP();
            $this->mail->Host = 'smtp.gmail.com';
            $this->mail->SMTPAuth = true;
            $this->mail->Username = env('GMAIL_USERNAME');
            $this->mail->Password = env('GMAIL_APP_PASSWORD');
            $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mail->Port = 587;
            $this->mail->setFrom(env('GMAIL_USERNAME'), env('APP_NAME'));
            // Do not echo SMTP traffic into HTTP responses.
            $this->mail->SMTPDebug = SMTP::DEBUG_OFF;

            if (app()->isLocal() && filter_var(env('GMAIL_SMTP_DEBUG', false), FILTER_VALIDATE_BOOL)) {
                $this->mail->SMTPDebug = SMTP::DEBUG_SERVER;
                $this->mail->Debugoutput = 'error_log';
            }
        } catch (Exception $e) {
            throw new Exception("Gmail SMTP setup failed: {$e->getMessage()}");
        }
    }

    public function sendOTP(string $toEmail, string $otpCode, string $userName): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $userName);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Your Email Verification Code - '.env('APP_NAME');
            $this->mail->Body = $this->getOTPEmailTemplate($otpCode, $userName);
            $this->mail->AltBody = "Your verification code is: {$otpCode}. This code expires in 15 minutes.";

            return $this->mail->send();
        } catch (Exception $e) {
            \Log::error("Failed to send OTP email to {$toEmail}", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    public function sendResetLink(string $toEmail, string $resetToken, string $userName, string $resetUrl): bool
    {
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($toEmail, $userName);
            $this->mail->isHTML(true);
            $this->mail->Subject = 'Password Reset Request - '.env('APP_NAME');
            $this->mail->Body = $this->getResetEmailTemplate($resetUrl, $userName);
            $this->mail->AltBody = "Click this link to reset your password: {$resetUrl}";

            return $this->mail->send();
        } catch (Exception $e) {
            \Log::error("Failed to send reset email to {$toEmail}", [
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    private function getOTPEmailTemplate(string $otpCode, string $userName): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f4efe6; padding: 20px; text-align: center; border-radius: 5px; }
                .content { padding: 20px; }
                .otp-box { background-color: #fffbf4; border: 2px solid #d4a563; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0; }
                .otp-code { font-size: 32px; font-weight: bold; color: #8b4e1c; letter-spacing: 5px; }
                .footer { color: #999; font-size: 12px; text-align: center; margin-top: 20px; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2 style="color: #8b4e1c; margin: 0;">Email Verification</h2>
                </div>
                <div class="content">
                    <p>Hello {$userName},</p>
                    <p>Thank you for signing up! To verify your email address, please use the verification code below:</p>
                    <div class="otp-box">
                        <div class="otp-code">{$otpCode}</div>
                    </div>
                    <p>This code will expire in <strong>15 minutes</strong>.</p>
                    <p>If you didn't request this verification code, please ignore this email.</p>
                </div>
                <div class="footer">
                    <p>&copy; {$this->getCurrentYear()} {$this->getAppName()}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    private function getResetEmailTemplate(string $resetUrl, string $userName): string
    {
        return <<<HTML
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">
            <style>
                body { font-family: Arial, sans-serif; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background-color: #f4efe6; padding: 20px; text-align: center; border-radius: 5px; }
                .content { padding: 20px; }
                .button { display: inline-block; background-color: #8b4e1c; color: #fff; padding: 12px 30px; text-decoration: none; border-radius: 5px; margin: 20px 0; }
                .footer { color: #999; font-size: 12px; text-align: center; margin-top: 20px; }
                .warning { color: #c41e3a; font-weight: bold; }
            </style>
        </head>
        <body>
            <div class="container">
                <div class="header">
                    <h2 style="color: #8b4e1c; margin: 0;">Password Reset Request</h2>
                </div>
                <div class="content">
                    <p>Hello {$userName},</p>
                    <p>We received a request to reset your password. Click the button below to create a new password:</p>
                    <p style="text-align: center;">
                        <a href="{$resetUrl}" class="button">Reset Password</a>
                    </p>
                    <p>Or copy and paste this link in your browser:</p>
                    <p style="word-break: break-all; background-color: #f5f5f5; padding: 10px; border-radius: 3px;">
                        {$resetUrl}
                    </p>
                    <p><span class="warning">⚠️ This link will expire in 1 hour.</span></p>
                    <p>If you didn't request this password reset, please ignore this email and your password will remain unchanged.</p>
                </div>
                <div class="footer">
                    <p>&copy; {$this->getCurrentYear()} {$this->getAppName()}. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>
        HTML;
    }

    private function getCurrentYear(): int
    {
        return (int) date('Y');
    }

    private function getAppName(): string
    {
        return (string) config('app.name', 'Laravel');
    }
}
