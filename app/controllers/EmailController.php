<?php
require 'app/vendor/autoload.php';
require 'app/config/app.php';
require 'app/models/EmailLogModel.php'; 

use PHPMailer\PHPMailer\Exception;
use Predis\Client;

class EmailController {
    public function sendEmail() {
        $response = [
            'error' => true,
            'message' => 'Email job queued.'
        ];

        $client = new Client([
            'scheme' => 'tcp',
            'host' => 'host.docker.internal', 
            'port' => 6379,
        ]);

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_SERVER['CONTENT_TYPE']) || $_SERVER['CONTENT_TYPE'] !== 'application/json') {
                http_response_code(400); 
                $response['message'] = 'Invalid request.';
                $this->sendJsonResponse($response);
                return;
            }

            $requestData = json_decode(file_get_contents('php://input'), true);

            if ($requestData === null) {
                http_response_code(400); 
                $response['message'] = 'Invalid JSON data in the request body.';
                $this->sendJsonResponse($response);
                return;
            }

            // Extract data from the JSON request
            $from = $requestData['from'] ?? 'sender@example.com';
            $to = $requestData['to'] ?? 'recipient@example.com';
            $subject = $requestData['subject'] ?? 'Test Email';
            $body = $requestData['body'] ?? 'This is a test email sent using PHPMailer and TLS encryption';

            // Push the email job to the Redis queue
            $emailData = [
                'from' => $from,
                'to' => $to,
                'subject' => $subject,
                'body' => $body,
            ];

            $client->lpush('email_jobs', json_encode($emailData));

            $response['error'] = false;
            $response['message'] = 'Email job queued.';

            // Log the email in the database
            $emailLogModel = new EmailLogModel();
            $emailLogModel->logEmail($from, $to, $subject, $body);
        } catch (Exception $e) {
            // Handle errors
        }

        $this->sendJsonResponse($response);
    }

    private function sendJsonResponse($response) {
        header('Content-Type: application/json');
        echo json_encode($response);
    }
}
?>
