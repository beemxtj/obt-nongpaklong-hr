<?php
// controllers/AuthController.php

// เรียกใช้ไฟล์ตั้งค่าและ Model ที่จำเป็น
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/User.php';
<<<<<<< HEAD
require_once __DIR__ . '/../models/AuditLog.php';
require_once __DIR__ . '/../models/Setting.php'; // เพิ่มการเรียกใช้ Setting Model

class AuthController
{
    private $db;

    public function __construct()
    {
        $database = new Database();
        $this->db = $database->getConnection();
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * แสดงหน้าฟอร์ม Login
     * ดึงข้อมูลการตั้งค่าระบบมาเพื่อใช้ในหน้า Login
     */
    public function index()
    {
        // ถ้าล็อกอินอยู่แล้ว ให้ redirect ไปหน้า dashboard
=======

class AuthController {

    // แสดงหน้าฟอร์ม Login
    public function index() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // ถ้า login อยู่แล้ว ให้ redirect ไปหน้า dashboard
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        }
<<<<<<< HEAD

        // ดึงข้อมูลการตั้งค่าระบบสำหรับหน้า Login
        $setting = new Setting($this->db);
        $system_settings = $setting->getSimpleSettings();

        // เรียกใช้ View ของหน้า Login และส่งค่า settings ไปด้วย
        require_once 'views/auth/login.php';
    }

    /**
     * ประมวลผลการ Login ด้วย Email/Password
     */
    public function login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user = new User($this->db);
=======
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

>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
            $user->email = $_POST['email'];
            $password = $_POST['password'];

            $stmt = $user->findByEmail();

            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
<<<<<<< HEAD

                // ตรวจสอบสถานะผู้ใช้ (ถ้ามีคอลัมน์ status)
                if (isset($row['status']) && $row['status'] !== 'active') {
                    $_SESSION['error'] = "บัญชีผู้ใช้นี้ถูกระงับการใช้งาน";
                    header('Location: ' . BASE_URL . '/login');
                    exit();
                }
                
                if (password_verify($password, $row['password'])) {
                    $this->establishSession($row);
                    
                    $auditLog = new AuditLog($this->db);
                    $auditLog->log('login_success', 'User logged in successfully via email.', $row['id']);
                    
=======
                
                if (password_verify($password, $row['password'])) {
                    $_SESSION['user_id'] = $row['id'];
                    $_SESSION['user_name'] = $row['first_name_th'];
                    $_SESSION['role_id'] = $row['role_id'];

>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
                    header('Location: ' . BASE_URL . '/dashboard');
                    exit();
                }
            }
<<<<<<< HEAD

=======
            
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
            $_SESSION['error'] = "อีเมลหรือรหัสผ่านไม่ถูกต้อง";
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
    }

<<<<<<< HEAD
    /**
     * Redirect ผู้ใช้ไปหน้า LINE Login
     */
    public function redirectToLine()
    {
        // ควรดึงค่า Client ID และ Secret มาจาก DB หรือไฟล์ config
        $setting = new Setting($this->db);
        $line_client_id = $setting->getSettingValue('line_login_channel_id', 'YOUR_CHANNEL_ID');
        $callback_url = BASE_URL . '/auth/line_callback';

        $_SESSION['line_state'] = bin2hex(random_bytes(16));
        $line_login_url = "https://access.line.me/oauth2/v2.1/authorize?" . http_build_query([
            'response_type' => 'code',
            'client_id'     => $line_client_id,
            'redirect_uri'  => $callback_url,
            'state'         => $_SESSION['line_state'],
            'scope'         => 'profile openid email',
        ]);
        header('Location: ' . $line_login_url);
        exit();
    }

    /**
     * จัดการ Callback จาก LINE
     */
    public function lineCallback()
    {
        // 1. ตรวจสอบ state token เพื่อป้องกัน CSRF attack
        if (!isset($_GET['state']) || empty($_GET['state']) || !isset($_SESSION['line_state']) || $_GET['state'] !== $_SESSION['line_state']) {
            $_SESSION['error'] = "Invalid state parameter. CSRF attack detected.";
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        unset($_SESSION['line_state']); // ใช้แล้วลบทิ้ง

        // 2. นำ code ที่ได้ไปแลกเป็น access token
        $setting = new Setting($this->db);
        $line_client_id = $setting->getSettingValue('line_login_channel_id', 'YOUR_CHANNEL_ID');
        $line_client_secret = $setting->getSettingValue('line_login_channel_secret', 'YOUR_CHANNEL_SECRET');
        $callback_url = BASE_URL . '/auth/line_callback';

        $token_url = 'https://api.line.me/oauth2/v2.1/token';
        $params = [
            'grant_type'    => 'authorization_code',
            'code'          => $_GET['code'],
            'redirect_uri'  => $callback_url,
            'client_id'     => $line_client_id,
            'client_secret' => $line_client_secret,
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $token_url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        $token_data = json_decode($response, true);
        if ($http_code !== 200 || !isset($token_data['access_token'])) {
            $_SESSION['error'] = 'Error: Failed to get access token from LINE.';
            header('Location: ' . BASE_URL . '/login');
            exit();
        }

        // 3. ใช้ access token ดึงข้อมูลโปรไฟล์ผู้ใช้
        $profile_url = 'https://api.line.me/v2/profile';
        $headers = ['Authorization: Bearer ' . $token_data['access_token']];
        
        $ch_profile = curl_init();
        curl_setopt($ch_profile, CURLOPT_URL, $profile_url);
        curl_setopt($ch_profile, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch_profile, CURLOPT_RETURNTRANSFER, true);
        $profile_response = curl_exec($ch_profile);
        curl_close($ch_profile);
        
        $profile_data = json_decode($profile_response, true);
        if (!isset($profile_data['userId'])) {
            $_SESSION['error'] = 'Error: Failed to get user profile from LINE.';
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        $line_user_id = $profile_data['userId'];

        // 4. ตรวจสอบผู้ใช้ในฐานข้อมูลและดำเนินการต่อ
        $user = new User($this->db);
        $stmt = $user->findByLineUserId($line_user_id); // **ต้องสร้างฟังก์ชันนี้ใน User Model**

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $this->establishSession($row);
            $auditLog = new AuditLog($this->db);
            $auditLog->log('login_success', 'User logged in successfully via LINE.', $row['id']);
            header('Location: ' . BASE_URL . '/dashboard');
            exit();
        } else {
            // ถ้าไม่พบ ให้พาไปหน้าเชื่อมบัญชี
            $_SESSION['line_user_id_to_link'] = $line_user_id;
            $_SESSION['line_display_name'] = $profile_data['displayName'];
            $_SESSION['line_picture_url'] = $profile_data['pictureUrl'] ?? '';
            header('Location: ' . BASE_URL . '/auth/link_account');
            exit();
        }
    }

    /**
     * แสดงหน้าฟอร์มเชื่อมบัญชี
     */
    public function linkAccount()
    {
        if (!isset($_SESSION['line_user_id_to_link'])) {
            header('Location: ' . BASE_URL . '/login');
            exit();
        }
        // ต้องสร้างไฟล์ View: views/auth/link_account.php
        require_once 'views/auth/link_account.php'; 
    }

    /**
     * ประมวลผลการเชื่อมบัญชี
     */
    public function processLink()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['line_user_id_to_link'])) {
            $user = new User($this->db);
            // ให้ login ด้วย email และ password เพื่อยืนยันตัวตนก่อนเชื่อม
            $user->email = $_POST['email'];
            $password = $_POST['password'];
            $line_user_id = $_SESSION['line_user_id_to_link'];

            $stmt = $user->findByEmail();
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                // ตรวจสอบรหัสผ่านถูกต้อง
                if (password_verify($password, $row['password'])) {
                    // ทำการอัปเดต line_user_id ให้กับพนักงานคนนี้
                    if ($user->updateLineUserId($row['id'], $line_user_id)) { // **ต้องสร้างฟังก์ชันนี้ใน User Model**
                        // เชื่อมบัญชีสำเร็จ ทำการล็อกอิน
                        $this->establishSession($row);
                        unset($_SESSION['line_user_id_to_link'], $_SESSION['line_display_name'], $_SESSION['line_picture_url']);
                        
                        $auditLog = new AuditLog($this->db);
                        $auditLog->log('link_account_success', 'User linked LINE account successfully.', $row['id']);

                        header('Location: ' . BASE_URL . '/dashboard');
                        exit();
                    }
                }
            }
            $_SESSION['link_error'] = "อีเมลหรือรหัสผ่านไม่ถูกต้อง ไม่สามารถเชื่อมต่อบัญชีได้";
            header('Location: ' . BASE_URL . '/auth/link_account');
            exit();
        }
    }

    /**
     * ฟังก์ชันสำหรับออกจากระบบ
     */
    public function logout()
    {
        if (isset($_SESSION['user_id'])) {
            $auditLog = new AuditLog($this->db);
            $auditLog->log('logout', 'User logged out successfully.', $_SESSION['user_id']);
        }
        session_unset();
        session_destroy();
        header('Location: ' . BASE_URL . '/login');
        exit();
    }
    
    /**
     * ฟังก์ชันช่วยในการตั้งค่า Session
     * @param array $userData ข้อมูลผู้ใช้จากฐานข้อมูล
     */
        private function establishSession($userData) {
        $role_query = "SELECT role_name FROM roles WHERE id = ?";
        $role_stmt = $this->db->prepare($role_query);
        $role_stmt->execute([$userData['role_id']]);
        $role_result = $role_stmt->fetch(PDO::FETCH_ASSOC);

        $_SESSION['user_id'] = $userData['id'];
        $_SESSION['user_name'] = $userData['first_name_th'];
        $_SESSION['role_id'] = $userData['role_id'];
        $_SESSION['role_name'] = $role_result['role_name'] ?? 'ผู้ใช้';
        $_SESSION['permissions'] = $userData['permissions'] ?? '[]';
    }
}
=======
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
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
