<?php
// controllers/AuthController.php

// เรียกใช้ไฟล์ตั้งค่าและ Model ที่จำเป็น
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {

    // แสดงหน้าฟอร์ม Login
    public function index() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // ถ้า login อยู่แล้ว ให้ redirect ไปหน้า dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
        require_once 'views/auth/login.php';
    }

    // ประมวลผลการ Login
    public function login() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $database = new Database();
            $db = $database->getConnection();

            $user = new User($db);

            $user->email = $_POST['email'];
            $password = $_POST['password'];

            $stmt = $user->findByEmail();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_name'] = $row['first_name_th'];
                    $_SESSION['role_id'] = $row['role_id'];

                    header('Location: ' . BASE_URL . '/dashboard');
                    exit();
                }
            }
            
            $_SESSION['error'] = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }

    // ===== ฟังก์ชันสำหรับออกจากระบบ =====
    public function logout() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // ทำลายข้อมูล session ทั้งหมด
        session_unset();
        session_destroy();

        // ส่งผู้ใช้กลับไปที่หน้า Login
        header('Location: ' . BASE_URL . '/login');
        exit();
    }
}
?>
