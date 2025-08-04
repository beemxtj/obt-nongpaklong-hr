<?php
// controllers/AuditLogController.php

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../models/AuditLog.php';
require_once __DIR__ . '/../config/database.php';

class AuditLogController {

    public function __construct() {
        if (session_status() == PHP_SESSION_NONE) { session_start(); }
        if ($_SESSION['role_id'] != 1) { /* Admin only */ exit('Access Denied'); }
    }

    public function index() {
        $page_title = "บันทึกกิจกรรมผู้ใช้งาน";
        $database = new Database();
        $db = $database->getConnection();
        $auditLog = new AuditLog($db);
        
        $stmt = $auditLog->read();
        $num = $stmt->rowCount();

        require_once 'views/audit_logs/index.php';
    }
}
?>
