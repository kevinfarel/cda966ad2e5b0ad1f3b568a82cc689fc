<?php

// Define your static client credentials
$clientId = 'your_client_id';
$clientSecret = 'your_client_secret';

// Check if the request contains client credentials
if (!isset($_SERVER['HTTP_AUTHORIZATION'])) {
    header('HTTP/1.1 401 Unauthorized');
    header('WWW-Authenticate: Basic realm="OAuth2 Authentication"');
    echo 'Unauthorized';
    exit;
}

// Parse the HTTP Authorization header
list($type, $credentials) = explode(' ', $_SERVER['HTTP_AUTHORIZATION'], 2);
list($client_id, $client_secret) = explode(':', base64_decode($credentials));

// Check if client credentials match
if ($client_id !== $clientId || $client_secret !== $clientSecret) {
    header('HTTP/1.1 401 Unauthorized');
    echo 'Unauthorized';
    exit;
}

// Client credentials are valid, generate an access token (simplified)
$accessToken = bin2hex(random_bytes(16));

// Respond with the access token
echo json_encode(['access_token' => $accessToken]);

// In a real-world scenario, you would also implement token expiration, user authentication, and more.
