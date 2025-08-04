<?php
// controllers/ZkDeviceController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

class ZkDeviceController {

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        // จำกัดสิทธิ์ให้เฉพาะ Admin หรือ HR
        if (!in_array($_SESSION['role_id'], [1, 2])) {
            $_SESSION['error_message'] = "คุณไม่มีสิทธิ์เข้าถึงหน้านี้";
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
    }

    /**
     * แสดงหน้าจัดการอุปกรณ์ ZKTeco
     */
    public function index() {
        $page_title = "จัดการอุปกรณ์ ZKTeco";
        
        // สร้าง URL ของ API Endpoint เพื่อนำไปแสดงในหน้าเว็บ
        $api_endpoint_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]" . BASE_URL . "/api/zkteco_listener.php";

        require_once 'views/zk_device/index.php'; // **ต้องสร้าง View นี้**
    }
}
