<?php
require 'app/vendor/autoload.php'; // Autoload PHPMailer
require 'app/config/app.php'; // Include your app configuration file
use Predis\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Create a Predis client to connect to the Redis server
$client = new Client([
    'scheme' => 'tcp',
    'host' => 'redis', // This should match the service name defined in docker-compose.yml
    'port' => 6379,
]);

// Create a PHPMailer instance
$mail = new PHPMailer(true);

while (true) {
    // Check for email jobs in the Redis queue
    $job = $client->rpop('email_jobs');

    if ($job) {
        // Process the email job
        $data = json_decode($job, true);

        try {
            // SMTP settings
            $mail->isSMTP();
            $mail->Host = SMTP_SERVER; // Your SMTP server (from app.php)
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME; // Your SMTP username (from app.php)
            $mail->Password = SMTP_PASSWORD; // Your SMTP password (from app.php)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port = SMTP_PORT; // Your SMTP port (from app.php)

            // Sender and recipient information
            $mail->setFrom($data['from'], 'Sender Name');
            $mail->addAddress($data['to'], 'Recipient Name');

            // Email content
            $mail->isHTML(false);
            $mail->Subject = $data['subject'];
            $mail->Body = $data['body'];

            // Send the email
            $mail->send();
            echo "Email sent: " . $data['subject'] . "\n";
        } catch (Exception $e) {
            // Handle email sending errors
            echo "Email sending failed. Error: " . $mail->ErrorInfo . "\n";
        }
    }

    // Sleep for a while before checking for new jobs
    sleep(1);
}
