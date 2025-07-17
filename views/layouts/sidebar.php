<?php
// views/layouts/sidebar.php

// --- ดึงข้อมูล Notification ---
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../models/Notification.php';
$database = new Database();
$db = $database->getConnection();
$unread_notifications_stmt = Notification::getUnreadByUser($db, $_SESSION['user_id']);
$unread_count = $unread_notifications_stmt->rowCount();
// ------------------------------------
?>

<div class="flex min-h-screen">
    <!-- ===== Sidebar ===== -->
    <aside class="bg-indigo-800 text-white w-64 flex-shrink-0 hidden lg:block">
        <div class="p-6">
            <a href="<?php echo BASE_URL; ?>/dashboard" class="text-2xl font-bold flex items-center gap-2">
                <i class="fas fa-sitemap"></i>
                <span>HRM อบต.</span>
            </a>
        </div>
        <nav class="mt-4">
            <ul>
                <?php
                $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
                $segments = explode('/', trim($path, '/'));
                $current_page = $segments[1] ?? 'dashboard';
                ?>
                <li class="px-6 py-3 <?php echo ($current_page == 'dashboard') ? 'bg-indigo-700' : 'hover:bg-indigo-700'; ?>">
                    <a href="<?php echo BASE_URL; ?>/dashboard" class="flex items-center gap-3"><i class="fas fa-tachometer-alt fa-fw"></i>Dashboard</a>
                </li>
                <li class="px-6 py-3 <?php echo ($current_page == 'employee') ? 'bg-indigo-700' : 'hover:bg-indigo-700'; ?>">
                    <a href="<?php echo BASE_URL; ?>/employee" class="flex items-center gap-3"><i class="fas fa-user-friends fa-fw"></i>ข้อมูลพนักงาน</a>
                </li>
                <li class="px-6 py-3 <?php echo ($current_page == 'attendance') ? 'bg-indigo-700' : 'hover:bg-indigo-700'; ?>">
                    <a href="<?php echo BASE_URL; ?>/attendance/history" class="flex items-center gap-3"><i class="fas fa-history fa-fw"></i>ประวัติลงเวลา</a>
                </li>
                <li class="px-6 py-3 <?php echo ($current_page == 'leave' && strpos($_SERVER['REQUEST_URI'], 'approval') === false) ? 'bg-indigo-700' : 'hover:bg-indigo-700'; ?>">
                    <a href="<?php echo BASE_URL; ?>/leave/history" class="flex items-center gap-3"><i class="fas fa-calendar-day fa-fw"></i>ประวัติการลา</a>
                </li>

                <?php if (in_array($_SESSION['role_id'], [1, 2, 3])): ?>
                    <li class="px-6 py-3 <?php echo (strpos($_SERVER['REQUEST_URI'], 'approval') !== false) ? 'bg-indigo-700' : 'hover:bg-indigo-700'; ?>">
                        <a href="<?php echo BASE_URL; ?>/leave/approval" class="flex items-center gap-3"><i class="fas fa-check-square fa-fw"></i>อนุมัติใบลา</a>
                    </li>
                <?php endif; ?>

                <li class="px-6 py-3 <?php echo ($current_page == 'report') ? 'bg-indigo-700' : 'hover:bg-indigo-700'; ?>">
                    <a href="<?php echo BASE_URL; ?>/report/attendance" class="flex items-center gap-3"><i class="fas fa-chart-bar fa-fw"></i>รายงาน</a>
                </li>

                <?php if ($_SESSION['role_id'] == 1): // Admin Only 
                ?>
                    <li class="px-6 py-3 <?php echo ($current_page == 'movement') ? 'bg-indigo-700' : 'hover:bg-indigo-700'; ?>">
                        <a href="<?php echo BASE_URL; ?>/movement/create" class="flex items-center gap-3"><i class="fas fa-exchange-alt fa-fw"></i>บันทึกความเคลื่อนไหว</a>
                    </li>
                    <li class="px-6 py-3 <?php echo ($current_page == 'role') ? 'bg-indigo-700' : 'hover:bg-indigo-700'; ?>">
                        <a href="<?php echo BASE_URL; ?>/role" class="flex items-center gap-3"><i class="fas fa-user-shield fa-fw"></i>จัดการสิทธิ์</a>
                    </li>
                    <li class="px-6 py-3 <?php echo ($current_page == 'settings') ? 'bg-indigo-700' : 'hover:bg-indigo-700'; ?>">
                        <a href="<?php echo BASE_URL; ?>/settings" class="flex items-center gap-3"><i class="fas fa-cog fa-fw"></i>ตั้งค่าระบบ</a>
                    </li>
                <?php endif; ?>

                <li class="px-6 py-3 mt-10 hover:bg-indigo-700">
                    <a href="<?php echo BASE_URL; ?>/auth/logout" class="flex items-center gap-3"><i class="fas fa-sign-out-alt fa-fw"></i>ออกจากระบบ</a>
                </li>
            </ul>
        </nav>
    </aside>

    <!-- ===== Main Content Wrapper ===== -->
    <div class="flex-1 flex flex-col">
        <!-- ===== Top Bar ===== -->
        <header class="bg-white shadow-sm p-4 flex justify-between items-center">
            <!-- Mobile Menu Button -->
            <button id="mobile-menu-button" class="lg:hidden text-gray-600">
                <i class="fas fa-bars text-2xl"></i>
            </button>
            <div class="lg:hidden"></div> <!-- Spacer for mobile -->

            <div class="flex items-center gap-6">
                <!-- Notification Bell -->
                <div class="relative" id="notification-dropdown">
                    <button id="notification-button" class="text-gray-500 hover:text-indigo-600 focus:outline-none">
                        <i class="fas fa-bell text-xl"></i>
                        <?php if ($unread_count > 0): ?>
                            <span class="absolute -top-1 -right-1 flex h-4 w-4">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-4 w-4 bg-red-500 text-white text-xs items-center justify-center"><?php echo $unread_count; ?></span>
                            </span>
                        <?php endif; ?>
                    </button>
                    <div id="notification-panel" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-xl overflow-hidden z-20 hidden">
                        <div class="p-4 font-bold border-b">การแจ้งเตือน</div>
                        <div class="divide-y max-h-96 overflow-y-auto">
                            <?php if ($unread_count > 0): ?>
                                <?php while ($notification = $unread_notifications_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <a href="<?php echo htmlspecialchars($notification['link']); ?>" class="block px-4 py-3 hover:bg-gray-100">
                                        <p class="text-sm text-gray-700"><?php echo htmlspecialchars($notification['message']); ?></p>
                                        <p class="text-xs text-gray-500 mt-1"><?php echo date('d/m/Y H:i', strtotime($notification['created_at'])); ?></p>
                                    </a>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-center text-gray-500 py-4">ไม่มีการแจ้งเตือนใหม่</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- User Info -->
                <div class="flex items-center gap-2">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($_SESSION['user_name']); ?>&background=e0e7ff&color=4f46e5" alt="User Avatar" class="w-8 h-8 rounded-full">
                    <span class="hidden sm:inline text-gray-700 font-semibold"><?php echo htmlspecialchars($_SESSION['user_name']); ?></span>
                </div>
            </div>
        </header>