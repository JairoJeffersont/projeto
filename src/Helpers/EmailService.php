<?php

namespace App\Helpers;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Class EmailService
 *
 * A helper service to send emails using PHPMailer with SMTP configuration loaded from environment variables.
 *
 * This class initializes PHPMailer with settings loaded from environment variables (.env file),
 * and provides a method to send HTML emails.
 *
 * @package App\Helpers
 */
class EmailService {
    /**
     * The PHPMailer instance used to send emails.
     *
     * @var PHPMailer
     */
    protected $mailer;

    /**
     * EmailService constructor.
     *
     * Loads environment variables from the .env file (if not already loaded),
     * and configures the PHPMailer instance using SMTP settings.
     *
     * @throws \PHPMailer\PHPMailer\Exception if PHPMailer initialization fails
     */
    public function __construct() {
        

        $this->mailer = new PHPMailer(true);

        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['MAIL_HOST'];
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['MAIL_USERNAME'];
        $this->mailer->Password   = $_ENV['MAIL_PASSWORD'];
        $this->mailer->SMTPSecure = $_ENV['MAIL_ENCRYPTION'];
        $this->mailer->Port       = $_ENV['MAIL_PORT'];

        $this->mailer->setFrom($_ENV['MAIL_FROM_ADDRESS'], $_ENV['MAIL_FROM_NAME']);
        $this->mailer->isHTML(true);
        $this->mailer->CharSet = 'UTF-8';
    }

    /**
     * Sends an email to the specified recipient with the given subject and HTML body.
     *
     * @param string $to      The recipient email address.
     * @param string $subject The subject of the email.
     * @param string $body    The HTML body of the email.
     *
     * @return array An associative array containing the result status and a message.
     *
     * @throws \RuntimeException if the email fails to send.
     */
    public function sendMail(string $to, string $subject, string $body): array {
        try {
            $this->mailer->clearAllRecipients();
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->Body    = $body;

            $this->mailer->send();

            return ['status' => 'success'];
        } catch (Exception $e) {
            throw new \RuntimeException("Failed to send email: {$e->getMessage()}", 0, $e);
        }
    }
}
