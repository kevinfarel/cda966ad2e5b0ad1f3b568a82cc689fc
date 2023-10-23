<?php
require 'app/vendor/autoload.php'; // Autoload PHPMailer
require 'app/config/app.php'; // Include your app configuration file
require 'app/models/EmailLogModel.php'; 

use PHPMailer\PHPMailer\Exception;
use Predis\Client;

class EmailController {
    public function sendEmail() {
        $response = [
            'error' => true,
            'message' => 'Email job queued.'
        ];

        // Create a Predis client to connect to the Redis server
        $client = new Client([
            'scheme' => 'tcp',
            'host' => 'host.docker.internal', // Use this to connect to Redis on localhost
            'port' => 6379,
        ]);

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
