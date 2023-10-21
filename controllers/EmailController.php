<?php
require 'vendor/autoload.php'; // Autoload PHPMailer
require 'config/app.php'; // Include your app configuration file

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailController {
    public function sendEmail() {
        $response = [
            'error' => true,
            'message' => 'Email sending failed.'
        ];

        try {
            // Check if the request is a POST request and contains JSON data
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                http_response_code(400); // Bad Request
                $response['message'] = 'Invalid request.';
                $this->sendJsonResponse($response);
                return;
            }

            // Get the JSON data from the request body
            $requestData = json_decode(file_get_contents('php://input'), true);

            if ($requestData === null) {
                http_response_code(400); // Bad Request
                $response['message'] = 'Invalid JSON data in the request body.';
                $this->sendJsonResponse($response);
                return;
            }

            // Extract data from the JSON request
            $from = $requestData['from'] ?? 'sender@example.com';
            $to = $requestData['to'] ?? 'recipient@example.com';
            $subject = $requestData['subject'] ?? 'Test Email';
            $body = $requestData['body'] ?? 'This is a test email sent using PHPMailer and TLS encryption';

            // Create a PHPMailer instance
            $mail = new PHPMailer(true);

            // SMTP settings
            $mail->isSMTP();
            $mail->Host = SMTP_SERVER; // Your SMTP server (from app.php)
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME; // Your SMTP username (from app.php)
            $mail->Password = SMTP_PASSWORD; // Your SMTP password (from app.php)
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Enable TLS encryption
            $mail->Port = SMTP_PORT; // Your SMTP port (from app.php)

            // Sender and recipient information
            $mail->setFrom($from, 'Sender Name');
            $mail->addAddress($to, 'Recipient Name');

            // Email content
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $body;

            // Send the email
            $mail->send();
            $response['error'] = false;
            $response['message'] = 'Email sent successfully';
        } catch (Exception $e) {
            $response['error'] = true;
            $response['message'] = 'Email sending failed. Error: ' . $mail->ErrorInfo;
        }

        $this->sendJsonResponse($response);
    }

    private function sendJsonResponse($response) {
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
?>
