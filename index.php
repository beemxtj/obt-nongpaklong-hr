<?php
// index.php
session_start(); // เริ่มต้น session สำหรับการเก็บข้อมูล login

// --- Routing แบบง่าย ---
// แยกส่วนของ URL ออกมา
$url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'login';
$url = filter_var($url, FILTER_SANITIZE_URL);
$urlParts = explode('/', $url);

// กำหนด controller และ action เริ่มต้น
$controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) . 'Controller' : 'AuthController';
$actionName = isset($urlParts[1]) ? $urlParts[1] : 'index';
$params = array_slice($urlParts, 2);

// ตรวจสอบว่าไฟล์ controller มีอยู่จริงหรือไม่
$controllerFile = 'controllers/' . $controllerName . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;
    // ตรวจสอบว่า class และ method มีอยู่จริงหรือไม่
    if (class_exists($controllerName) && method_exists($controllerName, $actionName)) {
        $controller = new $controllerName();
        // เรียกใช้งาน action พร้อมส่ง parameter (ถ้ามี)
        call_user_func_array([$controller, $actionName], $params);
    } else {
        // หาไม่เจอ ให้ไปหน้า 404
        echo "404 - Method or Class Not Found";
    }
} else {
    // ถ้าเข้ามาหน้าแรกสุด หรือไม่มี controller ให้ไปหน้า login
    if ($controllerName === 'LoginController' || $controllerName === 'AuthController') {
         require_once 'controllers/AuthController.php';
         $controller = new AuthController();
         $controller->index();
    } else {
        // หาไม่เจอ ให้ไปหน้า 404
        echo "404 - Controller Not Found";
    }
}
?>
