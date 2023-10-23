<?php
require_once 'config/app.php';

class EmailLogModel {
    private $db;
    public function __construct() {
        $dbHost = constant('DB_HOST');
        $dbName = constant('DB_NAME');
        $dbUser = constant('DB_USER');
        $dbPassword = constant('DB_PASSWORD');
        
        $this->db = new PDO("pgsql:host=$dbHost;dbname=$dbName", $dbUser, $dbPassword);
        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    public function logEmail($from, $to, $subject, $message) {
        $query = $this->db->prepare("INSERT INTO email_log (from_email, to_email, subject, message, sent_at) VALUES (:from, :to, :subject, :message, NOW())");
        $query->execute([
            'from' => $from,
            'to' => $to,
            'subject' => $subject,
            'message' => $message,
        ]);
    }
}
?>
