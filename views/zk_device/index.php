<?php
// views/zk_device/index.php

/*
 * This page provides a simple interface for administrators to view the
 * endpoint URL used by ZKTeco devices to push real‑time attendance data into
 * the HRM system.  The URL is generated in the controller and passed in
 * through the `$api_endpoint_url` variable.  To maintain consistency with
 * other pages in the application, we include the shared header and sidebar
 * layouts.  Access to this page is restricted in the controller based on
 * user role.
 */

if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/app.php';
}

// Include standard page layout components
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Title and Breadcrumb -->
    <div class="mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-indigo-900">จัดการอุปกรณ์ ZKTeco</h1>
        <p class="text-gray-600 mt-1">ตั้งค่าการเชื่อมต่อและตรวจสอบสถานะของอุปกรณ์ลงเวลา</p>
    </div>

    <!-- Display success or error messages from session -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p><?php echo $_SESSION['success_message']; ?></p>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p><?php echo $_SESSION['error_message']; ?></p>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Card with endpoint information -->
    <div class="bg-white rounded-2xl shadow-lg p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">URL สำหรับรับข้อมูลจากเครื่องลงเวลา</h2>
        <p class="text-gray-700 mb-2">
            กรุณานำ URL นี้ไปตั้งค่าในอุปกรณ์ ZKTeco เพื่อให้เครื่องส่งข้อมูลการลงเวลามายังระบบ HRM ของเรา:
        </p>
        <div class="bg-gray-100 border border-gray-200 rounded-lg p-4 mb-4 break-all">
            <code class="text-indigo-600 font-mono"><?php echo htmlspecialchars($api_endpoint_url ?? ''); ?></code>
        </div>
        <p class="text-sm text-gray-600">
            หมายเหตุ: URL นี้จะต้องสามารถเข้าถึงได้จากเครือข่ายเดียวกับอุปกรณ์ลงเวลา หากคุณกำลังใช้งานในสภาพแวดล้อมทดสอบ
            โปรดตรวจสอบให้แน่ใจว่าเครือข่ายของอุปกรณ์สามารถส่งข้อมูลมาที่เซิร์ฟเวอร์นี้ได้
        </p>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>