<?php
// controllers/NotificationController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../config/database.php';

class NotificationController {

    private $db;

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if (!isset($_SESSION['user_id'])) {
            http_response_code(403);
            exit('Authentication required.');
        }
        
        $database = new Database();
        $this->db = $database->getConnection();
    }

    /**
     * API Endpoint สำหรับอัปเดตสถานะการแจ้งเตือนเป็น "อ่านแล้ว"
     */
    public function markAsRead() {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $success = Notification::markAsRead($this->db, $_SESSION['user_id']);
            if ($success) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update notifications.']);
            }
        } else {
            http_response_code(405); // Method Not Allowed
            echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
        }
        exit();
    }
}
