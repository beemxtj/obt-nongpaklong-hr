<?php
// views/layouts/sidebar.php

// --- เรียกใช้ไฟล์ที่จำเป็น ---
require_once __DIR__ . '/../../helpers/Permission.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Notification.php';
require_once __DIR__ . '/../../models/Setting.php';

// --- ดึงข้อมูล Notification ---
$database = new Database();
$db = $database->getConnection();
$unread_notifications_stmt = Notification::getUnreadByUser($db, $_SESSION['user_id']);
$unread_count = $unread_notifications_stmt->rowCount();

// --- ดึงการตั้งค่าระบบ ---
$setting = new Setting($db);
$system_settings = $setting->getSimpleSettings();

$org_name = $system_settings['org_name'] ?? 'HRM System';
$org_logo = $system_settings['org_logo'] ?? '';
$sidebar_bg_color = $system_settings['sidebar_bg_color'] ?? '#1f2937';
$header_bg_color = $system_settings['header_bg_color'] ?? '#ffffff';

// ตรวจสอบว่าเป็นธีมเข้มหรือไม่
$is_dark_header = (strtolower($header_bg_color) === '#1f2937' || strtolower($header_bg_color) === '#111827');

// --- ตรวจสอบหน้าปัจจุบันสำหรับ Active Menu ---
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$segments = explode('/', trim($path, '/'));
$current_page = $segments[1] ?? 'dashboard';
$current_subpage = $segments[2] ?? '';

?>

<div class="lg:flex min-h-screen">
    <div id="sidebar-backdrop" class="fixed inset-0 bg-black bg-opacity-50 z-20 hidden lg:hidden"></div>

    <aside id="sidebar" class="sidebar text-white w-64 flex-shrink-0 fixed lg:relative inset-y-0 left-0 h-full z-30 -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out slide-in custom-scrollbar overflow-y-auto"
        style="background-color: <?php echo $sidebar_bg_color; ?>">
        <div class="p-6">
            <a href="<?php echo BASE_URL; ?>/dashboard" class="text-2xl font-bold flex items-center gap-3 hover:opacity-80 transition-opacity">
                <?php if (!empty($org_logo) && file_exists($org_logo)): ?>
                    <img src="<?php echo BASE_URL . '/' . $org_logo; ?>" alt="Logo" class="w-10 h-10 object-contain rounded">
                <?php else: ?>
                    <i class="fas fa-sitemap text-2xl"></i>
                <?php endif; ?>
                <span class="truncate"><?php echo htmlspecialchars($org_name); ?></span>
            </a>
        </div>

        <nav class="mt-4 pb-6">
            <ul class="space-y-1">
                <!-- ================== DASHBOARD ================== -->
                <li class="px-4">
                    <a href="<?php echo BASE_URL; ?>/dashboard"
                        class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 <?php echo ($current_page == 'dashboard' && $current_subpage == '') ? 'bg-white bg-opacity-20 text-white font-semibold' : 'hover:bg-white hover:bg-opacity-10 text-gray-200 hover:text-white'; ?>">
                        <i class="fas fa-tachometer-alt fa-fw w-5"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <?php if (in_array($_SESSION['role_id'], [1, 2, 3])): // Admin, HR, Supervisor ?>
                <li class="px-4">
                    <a href="<?php echo BASE_URL; ?>/dashboard/analytics"
                       class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 <?php echo ($current_subpage == 'analytics') ? 'bg-white bg-opacity-20 text-white font-semibold' : 'hover:bg-white hover:bg-opacity-10 text-gray-200 hover:text-white'; ?>">
                        <i class="fas fa-chart-pie fa-fw w-5"></i>
                        <span>ภาพรวมเชิงวิเคราะห์</span>
                    </a>
                </li>
                <?php endif; ?>

                <!-- ================== HR MANAGEMENT ================== -->
                <li class="px-4 py-2 mt-2">
                    <span class="px-3 text-xs font-bold uppercase text-gray-400">การจัดการ HR</span>
                </li>
                <li class="px-4">
                    <a href="<?php echo BASE_URL; ?>/employee"
                        class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 <?php echo ($current_page == 'employee') ? 'bg-white bg-opacity-20 text-white font-semibold' : 'hover:bg-white hover:bg-opacity-10 text-gray-200 hover:text-white'; ?>">
                        <i class="fas fa-user-friends fa-fw w-5"></i>
                        <span>ข้อมูลพนักงาน</span>
                    </a>
                </li>
                <li class="px-4">
                    <a href="<?php echo BASE_URL; ?>/department"
                        class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 <?php echo ($current_page == 'department') ? 'bg-white bg-opacity-20 text-white font-semibold' : 'hover:bg-white hover:bg-opacity-10 text-gray-200 hover:text-white'; ?>">
                        <i class="fas fa-building fa-fw w-5"></i>
                        <span>จัดการแผนก</span>
                    </a>
                </li>
                <li class="px-4">
                    <a href="<?php echo BASE_URL; ?>/position"
                        class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 <?php echo ($current_page == 'position') ? 'bg-white bg-opacity-20 text-white font-semibold' : 'hover:bg-white hover:bg-opacity-10 text-gray-200 hover:text-white'; ?>">
                        <i class="fas fa-user-tag fa-fw w-5"></i>
                        <span>จัดการตำแหน่งงาน</span>
                    </a>
                </li>

                <!-- ================== ATTENDANCE & LEAVE ================== -->
                <li class="px-4 py-2 mt-2">
                    <span class="px-3 text-xs font-bold uppercase text-gray-400">เวลาและการลา</span>
                </li>
                <li class="px-4">
                    <a href="<?php echo BASE_URL; ?>/attendance/history"
                        class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 <?php echo ($current_page == 'attendance') ? 'bg-white bg-opacity-20 text-white font-semibold' : 'hover:bg-white hover:bg-opacity-10 text-gray-200 hover:text-white'; ?>">
                        <i class="fas fa-history fa-fw w-5"></i>
                        <span>ประวัติลงเวลา</span>
                    </a>
                </li>
                <li class="px-4">
                    <a href="<?php echo BASE_URL; ?>/leave/history"
                        class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 <?php echo ($current_page == 'leave') ? 'bg-white bg-opacity-20 text-white font-semibold' : 'hover:bg-white hover:bg-opacity-10 text-gray-200 hover:text-white'; ?>">
                        <i class="fas fa-calendar-day fa-fw w-5"></i>
                        <span>ประวัติการลา</span>
                    </a>
                </li>
                <?php if (Permission::has('approve_leave')): ?>
                    <li class="px-4">
                        <a href="<?php echo BASE_URL; ?>/leave/approval"
                            class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 <?php echo ($current_subpage == 'approval') ? 'bg-white bg-opacity-20 text-white font-semibold' : 'hover:bg-white hover:bg-opacity-10 text-gray-200 hover:text-white'; ?>">
                            <i class="fas fa-check-square fa-fw w-5"></i>
                            <span>อนุมัติใบลา</span>
                        </a>
                    </li>
                <?php endif; ?>

                <!-- ================== PAYROLL & REPORTS ================== -->
                <li class="px-4 py-2 mt-2">
                    <span class="px-3 text-xs font-bold uppercase text-gray-400">เงินเดือนและรายงาน</span>
                </li>
                <li class="px-4">
                    <a href="<?php echo BASE_URL; ?>/payslip/history"
                        class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 <?php echo ($current_page == 'payslip') ? 'bg-white bg-opacity-20 text-white font-semibold' : 'hover:bg-white hover:bg-opacity-10 text-gray-200 hover:text-white'; ?>">
                        <i class="fas fa-file-invoice-dollar fa-fw w-5"></i>
                        <span>สลิปเงินเดือน</span>
                    </a>
                </li>
                <li class="px-4">
                    <a href="<?php echo BASE_URL; ?>/payroll"
                        class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 <?php echo ($current_page == 'payroll') ? 'bg-white bg-opacity-20 text-white font-semibold' : 'hover:bg-white hover:bg-opacity-10 text-gray-200 hover:text-white'; ?>">
                        <i class="fas fa-calculator fa-fw w-5"></i>
                        <span>คำนวณเงินเดือน</span>
                    </a>
                </li>
                <?php if (Permission::has('view_reports')):
                    $isReportPage = ($current_page == 'report');
                ?>
                    <li class="px-4">
                        <div class="<?php echo $isReportPage ? 'bg-white bg-opacity-20 rounded-lg' : ''; ?>">
                            <a href="#" data-toggle="dropdown"
                                class="flex items-center justify-between w-full px-3 py-3 rounded-lg ...">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-chart-bar fa-fw w-5"></i>
                                    <span>รายงาน</span>
                                </span>
                                <i class="fas <?php echo $isReportPage ? 'fa-chevron-down' : 'fa-chevron-right'; ?> ..."></i>
                            </a>
                            <ul class="pl-8 mt-2 space-y-1 text-sm <?php echo $isReportPage ? '' : 'hidden'; ?>">
                                <li><a href="<?php echo BASE_URL; ?>/report/attendance" class="block ...">สรุปการลงเวลา</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/report/timesheet" class="block ...">ใบลงเวลาทำงาน</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/report/leaveSummary" class="block ...">รายงานสรุปการลา</a></li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                <!-- ================== ZK-BIOTIMEAPP ================== -->
                <?php if (in_array($_SESSION['role_id'], [1, 2])): // Admin, HR
                    $isZkPage = (strpos($current_page, 'zk-') === 0);
                ?>
                    <li class="px-4 py-2 mt-2">
                        <span class="px-3 text-xs font-bold uppercase text-gray-400">ZK-BIOTIMEAPP</span>
                    </li>
                    <li class="px-4">
                         <div class="<?php echo $isZkPage ? 'bg-white bg-opacity-20 rounded-lg' : ''; ?>">
                            <ul class="pl-8 mt-2 space-y-1 text-sm <?php echo $isZkPage ? '' : ''; ?>">
                                <li><a href="<?php echo BASE_URL; ?>/zk-device" class="block ...">จัดการอุปกรณ์ ZKTech</a></li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                <!-- ================== SETTINGS ================== -->
                <?php if (Permission::has('manage_settings')):
                    $isSettingsPage = in_array($current_page, ['settings', 'role', 'leave_type', 'leavepolicy', 'workflow', 'holiday', 'announcement', 'audit_log', 'backup']);
                ?>
                    <li class="px-4 py-2 mt-2"><hr class="border-gray-600 opacity-30"></li>
                    <li class="px-4">
                         <div class="<?php echo $isSettingsPage ? 'bg-white bg-opacity-20 rounded-lg' : ''; ?>">
                            <a href="#" data-toggle="dropdown" class="flex items-center justify-between w-full px-3 py-3 ...">
                                <span class="flex items-center gap-3">
                                    <i class="fas fa-cogs fa-fw w-5"></i>
                                    <span>ตั้งค่าระบบ</span>
                                </span>
                                <i class="fas <?php echo $isSettingsPage ? 'fa-chevron-down' : 'fa-chevron-right'; ?> ..."></i>
                            </a>
                            <ul class="pl-8 mt-2 space-y-1 text-sm <?php echo $isSettingsPage ? '' : 'hidden'; ?>">
                                <li><a href="<?php echo BASE_URL; ?>/settings" class="block ...">ตั้งค่าทั่วไป</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/role" class="block ...">จัดการสิทธิ์</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/leave_type" class="block ...">ประเภทการลา</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/leavepolicy" class="block ...">นโยบายการลา</a></li>
                                <!-- ===== แก้ไขลิงก์ที่นี่ ===== -->
                                <li><a href="<?php echo BASE_URL; ?>/workflow" class="block px-3 py-2 rounded text-gray-300 hover:text-white hover:bg-white hover:bg-opacity-10">สายการอนุมัติ</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/holiday" class="block ...">จัดการวันหยุด</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/announcement" class="block ...">จัดการประกาศ</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/audit_log" class="block ...">บันทึกกิจกรรม</a></li>
                                <li><a href="<?php echo BASE_URL; ?>/backup" class="block ...">สำรองข้อมูล</a></li>
                            </ul>
                        </div>
                    </li>
                <?php endif; ?>

                <!-- ================== LOGOUT ================== -->
                <li class="px-4 pt-4 mt-auto">
                    <hr class="border-gray-600 opacity-30">
                </li>
                <li class="px-4">
                    <a href="<?php echo BASE_URL; ?>/auth/logout"
                        class="flex items-center gap-3 px-3 py-3 rounded-lg transition-all duration-200 hover:bg-red-600 hover:bg-opacity-20 text-gray-200 hover:text-red-300">
                        <i class="fas fa-sign-out-alt fa-fw w-5"></i>
                        <span>ออกจากระบบ</span>
                    </a>
                </li>
            </ul>
        </nav>
    </aside>
    <div class="flex-1 flex flex-col">
        <header class="header shadow-sm p-4 flex justify-between items-center sticky top-0 z-10 transition-colors duration-200"
            style="background-color: <?php echo $header_bg_color; ?>; color: <?php echo $is_dark_header ? '#ffffff' : '#1f2937'; ?>">
            <button id="mobile-menu-button" class="lg:hidden <?php echo $is_dark_header ? 'text-gray-300 hover:text-white' : 'text-gray-600 hover:text-gray-800'; ?> transition-colors">
                <i class="fas fa-bars text-2xl"></i>
            </button>

            <div class="hidden lg:block font-semibold">
                 <?php echo htmlspecialchars($page_title ?? 'Dashboard'); ?>
            </div>

            <div class="flex items-center gap-6">
                <div class="relative" id="notification-dropdown">
                    <button id="notification-button" class="<?php echo $is_dark_header ? 'text-gray-300 hover:text-white' : 'text-gray-500 hover:text-indigo-600'; ?> focus:outline-none transition-colors relative">
                        <i class="fas fa-bell text-xl"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="absolute -top-1 -right-1 flex h-5 w-5">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-5 w-5 bg-red-500 text-white text-xs items-center justify-center font-semibold">
                                    <?php echo $unread_count > 9 ? '9+' : $unread_count; ?>
                                </span>
                            </span>
                        <?php endif; ?>
                    </button>
                    <div id="notification-panel" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-20 hidden fade-in">
                        </div>
                </div>

                <div class="relative" id="user-dropdown">
                    <button id="user-button" class="flex items-center gap-2 hover:opacity-80 transition-opacity">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=e0e7ff&color=4f46e5"
                            alt="User Avatar" class="w-8 h-8 rounded-full border-2 border-white shadow-sm">
                        <span class="hidden sm:inline <?php echo $is_dark_header ? 'text-white' : 'text-gray-700'; ?> font-semibold">
                            <?php echo htmlspecialchars($_SESSION['user_name']); ?>
                        </span>
                        <i class="fas fa-chevron-down text-xs <?php echo $is_dark_header ? 'text-gray-300' : 'text-gray-500'; ?>"></i>
                    </button>

                    <div id="user-panel" class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-xl z-20 hidden fade-in overflow-hidden">
                        <div class="p-3 bg-gray-50 border-b">
                            <p class="text-sm font-semibold text-gray-800"><?php echo htmlspecialchars($_SESSION['user_name']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo htmlspecialchars($_SESSION['role_name'] ?? 'ผู้ใช้'); ?></p>
                        </div>
                        <a href="<?php echo BASE_URL; ?>/profile" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-indigo-50 hover:text-indigo-600"><i class="fas fa-user-circle w-4"></i><span>โปรไฟล์ของฉัน</span></a>
                        <a href="<?php echo BASE_URL; ?>/auth/logout" class="flex items-center gap-3 px-4 py-3 text-sm text-gray-700 hover:bg-red-50 hover:text-red-600"><i class="fas fa-sign-out-alt w-4"></i><span>ออกจากระบบ</span></a>
                    </div>
                </div>
            </div>
        </header>