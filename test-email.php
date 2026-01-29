<?php
// test-email.php
require_once __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/app/Helpers/functions.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$to = $argv[1] ?? 'test@example.com';

echo "Testing SMTP Configuration:\n";
echo "Host: " . env('SMTP_HOST', 'NOT SET') . "\n";
echo "Port: " . env('SMTP_PORT', 'NOT SET') . "\n";
echo "User: " . env('SMTP_USER', 'NOT SET') . "\n";
echo "From: " . env('SMTP_FROM', 'NOT SET') . "\n\n";

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host = env('SMTP_HOST', 'localhost');
    $mail->SMTPAuth = true;
    $mail->Username = env('SMTP_USER');
    $mail->Password = env('SMTP_PASS');
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = env('SMTP_PORT', 587);

    // DEBUG
    $mail->SMTPDebug = 2; // Level 2: client and server messages

    // Recipients
    $mail->setFrom(env('SMTP_FROM', 'support@driftnimbus.com'), 'Drift Nimbus Support');
    $mail->addAddress($to);

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from Nimbus Dashboard';
    $mail->Body = 'If you are seeing this, your SMTP configuration is working.';

    echo "Attempting to send email to {$to}...\n";
    $mail->send();
    echo "\nSUCCESS: Email has been sent.\n";
} catch (Exception $e) {
    echo "\nERROR: Message could not be sent. Mailer Error: {$mail->ErrorInfo}\n";
}
