<?php

function pcforge_mail_settings(): array
{
    return [
        // Change these application settings in this file.
        // XAMPP's SMTP host, username, and app password remain in C:\\xampp\\sendmail\\sendmail.ini.
        'from_email' => getenv('PCFORGE_MAIL_FROM') ?: 'no-reply@localhost',
        'from_name' => 'PCForge',
        'subject' => 'Your PCForge verification code',
        'otp_ttl_seconds' => 600,
        'otp_max_attempts' => 5,
    ];
}

function send_registration_otp(string $email, string $otp, bool $emailChange = false): bool
{
    if (!function_exists('mail')) {
        return false;
    }

    $settings = pcforge_mail_settings();
    $from = $settings['from_email'];
    $subject = $settings['subject'];
    $minutes = max(1, (int) ceil((int) $settings['otp_ttl_seconds'] / 60));
    $message = "Your PCForge verification code is: {$otp}\n\nThis code expires in {$minutes} minutes. If you did not create a PCForge account, you can ignore this email.";
    if ($emailChange) {
        $message = "Your PCForge email change verification code is: {$otp}\n\nThis code expires in {$minutes} minutes. If you did not request this change, ignore this email.";
    }
    $headers = [
        'From: ' . $settings['from_name'] . ' <' . $from . '>',
        'Reply-To: ' . $from,
        'Content-Type: text/plain; charset=UTF-8',
        'X-Mailer: PCForge',
    ];

    return mail($email, $subject, $message, implode("\r\n", $headers));
}
