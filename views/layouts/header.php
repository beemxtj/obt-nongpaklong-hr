<?php
// views/layouts/header.php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// --- จุดที่เพิ่ม: ตรรกะการตรวจสอบ Session Timeout ---
$timeout_duration = 1800; // กำหนดเวลา Timeout (1800 วินาที = 30 นาที)

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $timeout_duration) {
    // หากไม่มีการใช้งานนานเกินกำหนด
    session_unset();     // ล้างข้อมูล session
    session_destroy();   // ทำลาย session
    
    // ส่งกลับไปหน้า Login พร้อมข้อความแจ้งเตือน
    // ตรวจสอบว่า BASE_URL ถูกกำหนดค่าแล้วหรือยัง
    $base_url = defined('BASE_URL') ? BASE_URL : '';
    header('Location: ' . $base_url . '/login?status=timeout');
    exit();
}
$_SESSION['last_activity'] = time(); // อัปเดตเวลาใช้งานล่าสุด
// --- สิ้นสุดตรรกะ Session Timeout ---

if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
if (!isset($_SESSION['user_id'])) { 
    // การตรวจสอบนี้เผื่อไว้ในกรณีที่ session หายไปด้วยเหตุผลอื่น
    header('Location: ' . BASE_URL . '/login'); 
    exit(); 
}

// โหลดการตั้งค่าระบบ
require_once __DIR__ . '/../../models/Setting.php';
require_once __DIR__ . '/../../config/database.php';

$database = new Database();
$db = $database->getConnection();
$setting = new Setting($db);
$system_settings = $setting->getSimpleSettings();

// ตั้งค่าเริ่มต้นหากไม่มีในฐานข้อมูล
$org_name = $system_settings['org_name'] ?? 'HRM อบต.หนองปากโลง';
$org_logo = $system_settings['org_logo'] ?? '';
$favicon = $system_settings['favicon'] ?? '';
$primary_color = $system_settings['primary_color'] ?? '#4f46e5';
$secondary_color = $system_settings['secondary_color'] ?? '#7c3aed';
$accent_color = $system_settings['accent_color'] ?? '#06b6d4';
$sidebar_bg_color = $system_settings['sidebar_bg_color'] ?? '#1f2937';
$header_bg_color = $system_settings['header_bg_color'] ?? '#ffffff';

// ตรวจสอบว่าเป็นหน้าที่มีธีมเข้ม
$is_dark_header = (strtolower($header_bg_color) === '#1f2937' || strtolower($header_bg_color) === '#111827');
$text_color = $is_dark_header ? '#ffffff' : '#1f2937';
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title ?? $org_name); ?></title>
    
    <!-- Favicon -->
    <?php if (!empty($favicon) && file_exists($favicon)): ?>
    <link rel="icon" type="image/x-icon" href="<?php echo BASE_URL . '/' . $favicon; ?>">
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo BASE_URL . '/' . $favicon; ?>">
    <?php endif; ?>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Color Picker -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/spectrum/1.8.1/spectrum.min.js"></script>

    <style>
        body { 
            font-family: 'Sarabun', sans-serif; 
        }
        
        :root {
            --primary-color: <?php echo $primary_color; ?>;
            --secondary-color: <?php echo $secondary_color; ?>;
            --accent-color: <?php echo $accent_color; ?>;
            --sidebar-bg-color: <?php echo $sidebar_bg_color; ?>;
            --header-bg-color: <?php echo $header_bg_color; ?>;
            --text-color: <?php echo $text_color; ?>;
        }
        
        .btn-gradient { 
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color)); 
        }
        
        .btn-primary {
            background-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
        }
        
        .text-primary {
            color: var(--primary-color);
        }
        
        .text-accent {
            color: var(--accent-color);
        }
        
        .border-primary {
            border-color: var(--primary-color);
        }
        
        .bg-primary {
            background-color: var(--primary-color);
        }
        
        .bg-secondary {
            background-color: var(--secondary-color);
        }
        
        .bg-accent {
            background-color: var(--accent-color);
        }
        
        .sidebar {
            background-color: var(--sidebar-bg-color);
        }
        
        .header {
            background-color: var(--header-bg-color);
            color: var(--text-color);
        }
        
        /* Custom scrollbar */
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 10px;
        }
        
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }
        
        /* Animation classes */
        .fade-in {
            animation: fadeIn 0.3s ease-in;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .slide-in {
            animation: slideIn 0.3s ease-out;
        }
        
        @keyframes slideIn {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        
        /* Color picker custom styles */
        .sp-container {
            z-index: 9999;
        }
        
        /* Loading spinner */
        .loading-spinner {
            border: 3px solid #f3f3f3;
            border-top: 3px solid var(--primary-color);
            border-radius: 50%;
            width: 20px;
            height: 20px;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Toast notification styles */
        .toast {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 10000;
            max-width: 400px;
            padding: 16px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease-in-out;
        }
        
        .toast.show {
            transform: translateX(0);
        }
        
        .toast-success {
            background-color: #10b981;
            color: white;
        }
        
        .toast-error {
            background-color: #ef4444;
            color: white;
        }
        
        .toast-warning {
            background-color: #f59e0b;
            color: white;
        }
        
        .toast-info {
            background-color: var(--accent-color);
            color: white;
        }
    </style>
</head>
<body class="bg-gray-100">

<!-- Toast Container -->
<div id="toast-container"></div>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white p-6 rounded-lg shadow-lg flex items-center space-x-3">
        <div class="loading-spinner"></div>
        <span class="text-gray-700">กำลังโหลด...</span>
    </div>
</div>

<script>
// Global JavaScript functions
window.systemSettings = {
    showLoading: function() {
        document.getElementById('loading-overlay').classList.remove('hidden');
    },
    
    hideLoading: function() {
        document.getElementById('loading-overlay').classList.add('hidden');
    },
    
    showToast: function(message, type = 'info', duration = 5000) {
        const toastContainer = document.getElementById('toast-container');
        const toast = document.createElement('div');
        toast.className = `toast toast-${type}`;
        toast.innerHTML = `
            <div class="flex items-center justify-between">
                <span>${message}</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        toastContainer.appendChild(toast);
        
        // Show toast
        setTimeout(() => toast.classList.add('show'), 100);
        
        // Auto remove
        setTimeout(() => {
            toast.classList.remove('show');
            setTimeout(() => toast.remove(), 300);
        }, duration);
    },
    
    confirmAction: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    }
};

// Show PHP session messages as toasts
<?php if (isset($_SESSION['success_message'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        systemSettings.showToast('<?php echo addslashes($_SESSION['success_message']); ?>', 'success');
    });
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['error_message'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        systemSettings.showToast('<?php echo addslashes($_SESSION['error_message']); ?>', 'error');
    });
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['warning_message'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        systemSettings.showToast('<?php echo addslashes($_SESSION['warning_message']); ?>', 'warning');
    });
    <?php unset($_SESSION['warning_message']); ?>
<?php endif; ?>
</script>


