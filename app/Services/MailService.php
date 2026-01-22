<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class MailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configure();
    }

    private function configure()
    {
        // Server settings
        $this->mailer->isSMTP();
        $this->mailer->Host = env('SMTP_HOST');
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = env('SMTP_USER');
        $this->mailer->Password = env('SMTP_PASS');
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Or ENCRYPTION_SMTPS depending on port
        $this->mailer->Port = env('SMTP_PORT', 587);

        // Default Sender
        $this->mailer->setFrom(
            env('SMTP_FROM_EMAIL', 'no-reply@driftnimbus.com'),
            env('SMTP_FROM_NAME', 'Drift Nimbus')
        );
    }

    public function sendOnboardingEmail($userData, $apiToken)
    {
        // Template Path (assuming onboardingEmail.md is in root or views)
        // Adjust path as needed. The user mentioned it's in the root based on previous context or provided path.
        $templatePath = __DIR__ . '/../../onboardingEmail.md';

        if (!file_exists($templatePath)) {
            error_log("Email template not found at: $templatePath");
            return false;
        }

        $template = file_get_contents($templatePath);

        // Replace placeholders
        $body = str_replace(
            ['{{username}}', '{{password}}', '{{domainURI}}'],
            [$userData['cpanel_username'], $apiToken, $userData['domain']],
            $template
        );

        try {
            // Recipients
            $this->mailer->addAddress($userData['email'], $userData['full_name'] ?? $userData['cpanel_username']);

            // Content
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Welcome to Drift Nimbus';
            $this->mailer->Body = $body;
            $this->mailer->AltBody = "Welcome to Drift Nimbus.\nUsername: {$userData['cpanel_username']}\nAPI Token: {$apiToken}\nDomain: {$userData['domain']}";

            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$this->mailer->ErrorInfo}");
            return false;
        }
    }
}
