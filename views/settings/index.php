<?php

// views/settings/index.php - Enhanced with Role-Based Access Control & Work Shifts
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

// Permissions are now dynamically checked via RoleHelper, this is for fallback/display
$permissions = RoleHelper::getUserPermissions();

?>

<main class="flex-1 p-6 bg-gray-50 min-h-screen">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">ตั้งค่าระบบ</h1>
            <p class="text-gray-600">จัดการการตั้งค่าทั่วไปของระบบ HRM ตามสิทธิ์ของคุณ</p>
            <div class="flex items-center gap-2 mt-2">
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    <i class="fas fa-user-tag mr-1"></i>
                    <?php echo htmlspecialchars(RoleHelper::getCurrentRole() ?? 'N/A'); ?>
                </span>
                <?php if ($permissions['can_manage_settings']) : ?>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <i class="fas fa-crown mr-1"></i>
                        สิทธิ์ผู้ดูแลระบบ
                    </span>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex gap-3 mt-4 sm:mt-0">
            <?php if ($permissions['can_export_data']) : ?>
                <button onclick="location.href='<?php echo BASE_URL; ?>/settings/export'" class="btn-primary px-4 py-2 rounded-lg text-white hover:opacity-90 transition-opacity">
                    <i class="fas fa-download mr-2"></i>ส่งออกการตั้งค่า
                </button>
            <?php endif; ?>

            <?php if ($permissions['can_manage_settings']) : ?>
                <button onclick="document.getElementById('import-modal').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 px-4 py-2 rounded-lg text-white transition-colors">
                    <i class="fas fa-upload mr-2"></i>นำเข้าการตั้งค่า
                </button>
                <button onclick="confirmReset()" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg text-white transition-colors">
                    <i class="fas fa-undo mr-2"></i>รีเซ็ต
                </button>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!$permissions['can_manage_settings']) : ?>
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-info-circle text-yellow-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-yellow-800">ข้อมูลสิทธิ์การใช้งาน</h3>
                    <div class="mt-2 text-sm text-yellow-700">
                        <p>คุณสามารถดูและแก้ไขการตั้งค่าได้เฉพาะในส่วนที่ได้รับอนุญาต การตั้งค่าระบบและความปลอดภัยต้องการสิทธิ์ผู้ดูแลระบบ</p>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <div class="bg-white rounded-t-xl shadow-sm border border-gray-200 border-b-0">
        <div class="flex overflow-x-auto">
            <?php
            $first_tab = true;
            foreach ($settings as $category_key => $category) :
            ?>
                <button type="button" data-tab-id="<?php echo $category_key; ?>" id="tab-<?php echo $category_key; ?>" class="tab-button flex items-center gap-3 px-6 py-4 text-sm font-medium border-b-2 whitespace-nowrap transition-all duration-200 <?php echo $first_tab ? 'active border-indigo-500 text-indigo-600 bg-indigo-50' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'; ?>">
                    <i class="<?php echo $category['icon'] ?? 'fas fa-cog'; ?>"></i>
                    <span><?php echo htmlspecialchars($category['title']); ?></span>
                    <?php if (!empty($category['settings']) || isset($category['has_custom_content'])) : ?>
                        <span class="inline-flex items-center justify-center w-5 h-5 text-xs bg-gray-200 text-gray-600 rounded-full">
                            <?php echo isset($category['has_custom_content']) ? '•' : count($category['settings']); ?>
                        </span>
                    <?php endif; ?>
                </button>
            <?php
                $first_tab = false;
            endforeach;
            ?>
        </div>
    </div>

    <form action="<?php echo BASE_URL; ?>/settings/update" method="POST" enctype="multipart/form-data" class="bg-white rounded-b-xl shadow-sm border border-gray-200">

        <?php
        $first_content = true;
        foreach ($settings as $category_key => $category) :
        ?>
            <div id="content-<?php echo $category_key; ?>" class="tab-content p-8 <?php echo !$first_content ? 'hidden' : ''; ?>">
                <div class="mb-6">
                    <h2 class="text-2xl font-semibold text-gray-900 flex items-center gap-3 mb-2">
                        <i class="<?php echo $category['icon'] ?? 'fas fa-cog'; ?> text-indigo-600"></i>
                        <?php echo htmlspecialchars($category['title']); ?>
                    </h2>
                    <p class="text-gray-600">
                        <?php echo htmlspecialchars($category['description'] ?? ''); ?>
                    </p>
                </div>

                <?php if ($category_key === 'work_shifts') : ?>
                    <?php // The content for 'work_shifts' tab remains the same ?>
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
                            <?php
                            // Assuming $workShift is available from the controller
                            $shift_stats = isset($workShift) ? $workShift->getShiftStatistics() : ['total_shifts' => 0, 'active_shifts' => 0, 'employees_with_shifts' => 0, 'total_assignments' => 0];
                            ?>
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-calendar-alt text-blue-600 text-2xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-blue-900">กะทั้งหมด</p>
                                        <p class="text-2xl font-bold text-blue-600"><?php echo $shift_stats['total_shifts']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-green-900">กะที่ใช้งาน</p>
                                        <p class="text-2xl font-bold text-green-600"><?php echo $shift_stats['active_shifts']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-users text-purple-600 text-2xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-purple-900">พนักงานที่มีกะ</p>
                                        <p class="text-2xl font-bold text-purple-600"><?php echo $shift_stats['employees_with_shifts']; ?></p>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-orange-50 rounded-lg p-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-clipboard-list text-orange-600 text-2xl"></i>
                                    </div>
                                    <div class="ml-4">
                                        <p class="text-sm font-medium text-orange-900">การมอบหมายทั้งหมด</p>
                                        <p class="text-2xl font-bold text-orange-600"><?php echo $shift_stats['total_assignments']; ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-between items-center">
                            <h3 class="text-lg font-semibold text-gray-900">รายการกะการทำงาน</h3>
                            <button type="button" onclick="showCreateShiftModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg transition-colors">
                                <i class="fas fa-plus mr-2"></i>เพิ่มกะใหม่
                            </button>
                        </div>

                        <div class="grid gap-4">
                            <?php if (!empty($work_shifts)) : ?>
                                <?php foreach ($work_shifts as $shift) : ?>
                                    <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                                        <div class="flex items-center justify-between">
                                            <div class="flex-1">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-4 h-4 rounded-full" style="background-color: <?php echo WorkShift::getShiftColor($shift['id']); ?>"></div>
                                                    <h4 class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($shift['shift_name']); ?></h4>
                                                    <?php if ($shift['is_active']) : ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            <i class="fas fa-check-circle mr-1"></i>ใช้งาน
                                                        </span>
                                                    <?php else : ?>
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                            <i class="fas fa-pause-circle mr-1"></i>ไม่ใช้งาน
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mt-2 text-sm text-gray-600">
                                                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                                        <div>
                                                            <span class="font-medium">เวลาทำงาน:</span>
                                                            <?php echo date('H:i', strtotime($shift['start_time'])); ?> - <?php echo date('H:i', strtotime($shift['end_time'])); ?>
                                                        </div>
                                                        <div>
                                                            <span class="font-medium">เวลาพัก:</span>
                                                            <?php if ($shift['break_start'] && $shift['break_end']) : ?>
                                                                <?php echo date('H:i', strtotime($shift['break_start'])); ?> - <?php echo date('H:i', strtotime($shift['break_end'])); ?>
                                                            <?php else : ?>
                                                                ไม่กำหนด
                                                            <?php endif; ?>
                                                        </div>
                                                        <div>
                                                            <span class="font-medium">วันทำงาน:</span>
                                                            <?php echo WorkShift::formatWorkDays($shift['work_days']); ?>
                                                        </div>
                                                    </div>
                                                    <div class="mt-2">
                                                        <span class="font-medium">พนักงานในกะ:</span>
                                                        <span class="inline-flex items-center px-2 py-1 text-xs bg-blue-100 text-blue-800 rounded-full">
                                                            <i class="fas fa-users mr-1"></i><?php echo $shift['assigned_employees']; ?> คน
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2 ml-4">
                                                <button type="button" onclick="viewShiftEmployees(<?php echo $shift['id']; ?>)" class="text-blue-600 hover:text-blue-800 p-2" title="ดูพนักงานในกะ">
                                                    <i class="fas fa-users"></i>
                                                </button>
                                                <button type="button" onclick="showAssignEmployeeModal(<?php echo $shift['id']; ?>)" class="text-green-600 hover:text-green-800 p-2" title="มอบหมายพนักงาน">
                                                    <i class="fas fa-user-plus"></i>
                                                </button>
                                                <button type="button" onclick="editShift(<?php echo $shift['id']; ?>)" class="text-indigo-600 hover:text-indigo-800 p-2" title="แก้ไข">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <?php if ($permissions['can_manage_employees']) : ?>
                                                    <button type="button" onclick="deleteShift(<?php echo $shift['id']; ?>)" class="text-red-600 hover:text-red-800 p-2" title="ลบ">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                                    <i class="fas fa-clock text-gray-400 text-4xl mb-4"></i>
                                    <h3 class="text-lg font-medium text-gray-900 mb-2">ยังไม่มีกะการทำงาน</h3>
                                    <p class="text-gray-500 mb-4">เริ่มต้นด้วยการสร้างกะการทำงานแรกของคุณ</p>
                                    <button type="button" onclick="showCreateShiftModal()" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2 rounded-lg">
                                        <i class="fas fa-plus mr-2"></i>สร้างกะแรก
                                    </button>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php else : ?>
                    <?php if (!empty($category['settings'])) : ?>
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <?php foreach ($category['settings'] as $setting_key => $setting_data) : ?>
                                <div class="space-y-3">
                                    <label for="<?php echo $setting_key; ?>" class="block text-sm font-semibold text-gray-700">
                                        <?php echo htmlspecialchars($setting_data['description'] ?? $setting_key); ?>
                                        <?php if (in_array($setting_key, ['session_timeout', 'system_timezone', 'debug_mode'])) : ?>
                                            <span class="ml-1 text-xs text-red-500" title="การเปลี่ยนแปลงต้องการรีสตาร์ทระบบ">*</span>
                                        <?php endif; ?>
                                    </label>

                                    <?php
                                    $setting_type = $setting_data['type'] ?? 'text';
                                    $setting_value = $setting_data['value'] ?? '';

                                    // Render different input types
                                    switch ($setting_type):
                                        case 'textarea':
                                    ?>
                                            <textarea name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors" placeholder="กรอก<?php echo htmlspecialchars($setting_data['description'] ?? $setting_key); ?>"><?php echo htmlspecialchars($setting_value); ?></textarea>
                                        <?php
                                            break;
                                        case 'file':
                                        ?>
                                            <div class="space-y-4">
                                                <?php if (!empty($setting_value) && file_exists($setting_value)) : ?>
                                                    <div class="flex items-center gap-4 p-4 bg-gray-50 rounded-lg border">
                                                        <?php if (in_array($setting_key, ['org_logo', 'favicon', 'login_bg_image'])) : ?>
                                                            <img src="<?php echo BASE_URL . '/' . $setting_value; ?>" alt="Current <?php echo $setting_key; ?>" class="w-16 h-16 object-contain rounded-lg border bg-white">
                                                        <?php endif; ?>
                                                        <div class="flex-1">
                                                            <p class="text-sm font-medium text-gray-700">ไฟล์ปัจจุบัน</p>
                                                            <p class="text-xs text-gray-500"><?php echo basename($setting_value); ?></p>
                                                            <p class="text-xs text-green-600 mt-1">
                                                                <i class="fas fa-check-circle mr-1"></i>อัปโหลดแล้ว
                                                            </p>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>

                                                <div class="relative">
                                                    <input type="file" name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" accept="<?php echo $setting_key === 'favicon' ? '.ico,.png,.jpg,.jpeg' : '.jpg,.jpeg,.png,.gif'; ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                                </div>

                                                <div class="text-xs text-gray-500 bg-blue-50 p-3 rounded-lg">
                                                    <i class="fas fa-info-circle text-blue-500 mr-1"></i>
                                                    <?php
                                                    $file_info = [
                                                        'org_logo' => 'รองรับไฟล์: JPG, PNG, GIF | ขนาดสูงสุด: 2MB | ขนาดแนะนำ: 200x200 พิกเซล',
                                                        'favicon' => 'รองรับไฟล์: ICO, PNG, JPG | ขนาดสูงสุด: 1MB | ขนาดแนะนำ: 32x32 พิกเซล',
                                                        'login_bg_image' => 'รองรับไฟล์: JPG, PNG | ขนาดสูงสุด: 5MB | ขนาดแนะนำ: 1920x1080 พิกเซล'
                                                    ];
                                                    echo $file_info[$setting_key] ?? 'รองรับไฟล์: JPG, PNG, GIF | ขนาดสูงสุด: 2MB';
                                                    ?>
                                                </div>
                                            </div>
                                        <?php
                                            break;
                                        case 'color':
                                        ?>
                                            <div class="flex gap-3 items-center">
                                                <div class="relative">
                                                    <input type="color" name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" value="<?php echo htmlspecialchars($setting_value); ?>" class="w-16 h-12 border border-gray-300 rounded-lg cursor-pointer">
                                                    <div class="absolute inset-0 rounded-lg border-2 border-white pointer-events-none"></div>
                                                </div>
                                                <input type="text" value="<?php echo htmlspecialchars($setting_value); ?>" class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent font-mono text-sm" readonly>
                                                <button type="button" onclick="resetColor('<?php echo $setting_key; ?>')" class="px-3 py-3 text-gray-500 hover:text-gray-700 transition-colors" title="รีเซ็ตสี">
                                                    <i class="fas fa-undo"></i>
                                                </button>
                                            </div>
                                        <?php
                                            break;
                                        case 'boolean':
                                        ?>
                                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg border">
                                                <div class="flex items-center">
                                                    <input type="checkbox" name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" value="1" <?php echo ($setting_value == '1') ? 'checked' : ''; ?> class="w-5 h-5 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 focus:ring-2">
                                                    <label for="<?php echo $setting_key; ?>" class="ml-3 text-sm font-medium text-gray-700">เปิดใช้งาน</label>
                                                </div>
                                                <div class="text-xs text-gray-500">
                                                    <i class="fas fa-toggle-<?php echo ($setting_value == '1') ? 'on text-green-500' : 'off text-gray-400'; ?>"></i>
                                                </div>
                                            </div>
                                        <?php
                                            break;
                                        case 'select':
                                        ?>
                                            <select name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                                <?php
                                                $options = [];
                                                switch ($setting_key) {
                                                    case 'system_timezone':
                                                        $options = [
                                                            'Asia/Bangkok' => 'เอเชีย/กรุงเทพฯ (UTC+7)',
                                                            'Asia/Jakarta' => 'เอเชีย/จาการ์ตา (UTC+7)',
                                                            'Asia/Singapore' => 'เอเชีย/สิงคโปร์ (UTC+8)',
                                                            'Asia/Tokyo' => 'เอเชีย/โตเกียว (UTC+9)'
                                                        ];
                                                        break;
                                                    case 'date_format':
                                                        $options = [
                                                            'd/m/Y' => 'วว/ดด/ปปปป (31/12/2024)',
                                                            'Y-m-d' => 'ปปปป-ดด-วว (2024-12-31)',
                                                            'd-m-Y' => 'วว-ดด-ปปปป (31-12-2024)',
                                                            'M d, Y' => 'เดือน วว, ปปปป (Dec 31, 2024)'
                                                        ];
                                                        break;
                                                    case 'time_format':
                                                        $options = [
                                                            'H:i' => '24 ชั่วโมง (14:30)',
                                                            'h:i A' => '12 ชั่วโมง (2:30 PM)'
                                                        ];
                                                        break;
                                                    case 'language':
                                                        $options = [
                                                            'th' => 'ไทย (Thai)',
                                                            'en' => 'อังกฤษ (English)'
                                                        ];
                                                        break;
                                                    case 'currency':
                                                        $options = [
                                                            'THB' => 'บาทไทย (THB) ฿',
                                                            'USD' => 'ดอลลาร์สหรัฐ (USD) $',
                                                            'EUR' => 'ยูโร (EUR) €'
                                                        ];
                                                        break;
                                                    case 'backup_frequency':
                                                        $options = [
                                                            'daily' => 'ทุกวัน',
                                                            'weekly' => 'รายสัปดาห์',
                                                            'monthly' => 'รายเดือน'
                                                        ];
                                                        break;
                                                    case 'logo_position':
                                                        $options = [
                                                            'left' => 'ซ้าย',
                                                            'center' => 'กลาง',
                                                            'right' => 'ขวา'
                                                        ];
                                                        break;
                                                    case 'theme_mode':
                                                        $options = [
                                                            'light' => 'สว่าง',
                                                            'dark' => 'มืด',
                                                            'auto' => 'อัตโนมัติ'
                                                        ];
                                                        break;
                                                }

                                                foreach ($options as $value => $label) :
                                                ?>
                                                    <option value="<?php echo $value; ?>" <?php echo ($setting_value == $value) ? 'selected' : ''; ?>>
                                                        <?php echo $label; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        <?php
                                            break;
                                        default: // Handles text, time, number, email, url, tel, password
                                            $type_map = [
                                                'time' => 'time', 'number' => 'number', 'email' => 'email', 
                                                'url' => 'url', 'tel' => 'tel', 'password' => 'password'
                                            ];
                                            $input_type = $type_map[$setting_type] ?? 'text';
                                        ?>
                                            <input type="<?php echo $input_type; ?>" name="<?php echo $setting_key; ?>" id="<?php echo $setting_key; ?>" value="<?php echo htmlspecialchars($setting_value); ?>" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="กรอก<?php echo htmlspecialchars($setting_data['description'] ?? $setting_key); ?>">
                                    <?php
                                            break;
                                    endswitch;
                                    ?>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <?php if ($category_key === 'theme') : ?>
                            <div class="mt-8 pt-6 border-t border-gray-200">
                                <div class="flex gap-4">
                                    <button type="button" onclick="previewTheme()" class="bg-indigo-100 hover:bg-indigo-200 text-indigo-700 px-6 py-3 rounded-lg transition-colors font-medium">
                                        <i class="fas fa-eye mr-2"></i>ดูตัวอย่างธีม
                                    </button>
                                    <button type="button" onclick="resetThemeColors()" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-lg transition-colors font-medium">
                                        <i class="fas fa-undo mr-2"></i>รีเซ็ตสีเริ่มต้น
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                    <?php else : ?>
                        <div class="text-center py-12 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
                            <i class="fas fa-lock text-gray-400 text-4xl mb-4"></i>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">ไม่มีสิทธิ์เข้าถึง</h3>
                            <p class="text-gray-500">คุณไม่มีสิทธิ์ในการดูหรือแก้ไขการตั้งค่าในหมวดนี้</p>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php
            $first_content = false;
        endforeach;
        ?>

        <div class="flex justify-between items-center px-8 py-6 bg-gray-50 border-t border-gray-200">
            <div class="text-sm text-gray-500">
                <i class="fas fa-info-circle mr-1"></i>
                การเปลี่ยนแปลงจะมีผลทันทีหลังจากบันทึก
                <?php if (!$permissions['can_manage_settings']) : ?>
                    <br><span class="text-xs text-amber-600">* คุณสามารถแก้ไขได้เฉพาะการตั้งค่าที่ได้รับอนุญาต</span>
                <?php endif; ?>
            </div>
            <div class="flex gap-4">
                <button type="button" onclick="window.location.reload()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </button>
                <button type="submit" class="btn-gradient px-8 py-3 rounded-lg text-white hover:opacity-90 transition-opacity font-medium">
                    <i class="fas fa-save mr-2"></i>บันทึกการตั้งค่า
                </button>
            </div>
        </div>
    </form>
</main>

<?php // All modals (import, shift, assign, view) remain the same ?>
<?php if ($permissions['can_manage_settings']) : ?>
    <div id="import-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-xl p-6 max-w-md w-full mx-4 shadow-2xl">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-upload text-green-600"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900">นำเข้าการตั้งค่า</h3>
            </div>
            <form action="<?php echo BASE_URL; ?>/settings/import" method="POST" enctype="multipart/form-data">
                <div class="mb-6">
                    <label for="settings_file" class="block text-sm font-medium text-gray-700 mb-2">เลือกไฟล์ JSON</label>
                    <input type="file" name="settings_file" id="settings_file" accept=".json" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                    <p class="text-xs text-gray-500 mt-2">
                        <i class="fas fa-info-circle mr-1"></i>
                        รองรับเฉพาะไฟล์ JSON ที่ส่งออกจากระบบนี้เท่านั้น
                    </p>
                </div>
                <div class="flex justify-end gap-3">
                    <button type="button" onclick="document.getElementById('import-modal').classList.add('hidden')" class="px-4 py-2 text-gray-600 hover:text-gray-800 font-medium">ยกเลิก</button>
                    <button type="submit" class="btn-primary px-6 py-2 rounded-lg text-white font-medium">
                        <i class="fas fa-upload mr-2"></i>นำเข้า
                    </button>
                </div>
            </form>
        </div>
    </div>
<?php endif; ?>

<div id="shift-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-2xl w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-clock text-blue-600"></i>
            </div>
            <h3 id="shift-modal-title" class="text-lg font-semibold text-gray-900">เพิ่มกะการทำงานใหม่</h3>
        </div>

        <form id="shift-form" method="POST">
            <input type="hidden" id="shift-id" name="shift_id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label for="shift-name" class="block text-sm font-medium text-gray-700 mb-2">ชื่อกะ</label>
                    <input type="text" id="shift-name" name="shift_name" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" placeholder="เช่น กะเช้า, กะบ่าย, กะดึก">
                </div>

                <div>
                    <label for="shift-start-time" class="block text-sm font-medium text-gray-700 mb-2">เวลาเริ่มงาน</label>
                    <input type="time" id="shift-start-time" name="start_time" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="shift-end-time" class="block text-sm font-medium text-gray-700 mb-2">เวลาเลิกงาน</label>
                    <input type="time" id="shift-end-time" name="end_time" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="shift-break-start" class="block text-sm font-medium text-gray-700 mb-2">เวลาเริ่มพัก (ไม่บังคับ)</label>
                    <input type="time" id="shift-break-start" name="break_start" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div>
                    <label for="shift-break-end" class="block text-sm font-medium text-gray-700 mb-2">เวลาสิ้นสุดพัก (ไม่บังคับ)</label>
                    <input type="time" id="shift-break-end" name="break_end" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-3">วันทำงาน</label>
                    <div class="grid grid-cols-4 md:grid-cols-7 gap-2">
                        <?php
                        $work_days = WorkShift::getWorkDaysOptions();
                        foreach ($work_days as $day_value => $day_name) :
                        ?>
                            <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                <input type="checkbox" name="work_days[]" value="<?php echo $day_value; ?>" class="mr-2">
                                <span class="text-sm"><?php echo $day_name; ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="md:col-span-2">
                    <label class="flex items-center">
                        <input type="checkbox" id="shift-is-active" name="is_active" value="1" checked class="mr-3">
                        <span class="text-sm font-medium text-gray-700">เปิดใช้งานกะนี้</span>
                    </label>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8">
                <button type="button" onclick="closeShiftModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                    ยกเลิก
                </button>
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                    <i class="fas fa-save mr-2"></i>บันทึกกะ
                </button>
            </div>
        </form>
    </div>
</div>

<div id="assign-employee-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-4xl w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center gap-3 mb-6">
            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-user-plus text-green-600"></i>
            </div>
            <h3 class="text-lg font-semibold text-gray-900">มอบหมายพนักงานเข้ากะ</h3>
        </div>

        <form action="<?php echo BASE_URL; ?>/settings/assignShift" method="POST">
            <input type="hidden" id="assign-shift-id" name="shift_id">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="assign-start-date" class="block text-sm font-medium text-gray-700 mb-2">วันที่เริ่มต้น</label>
                    <input type="date" id="assign-start-date" name="start_date" required class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent" value="<?php echo date('Y-m-d'); ?>">
                </div>

                <div>
                    <label for="assign-end-date" class="block text-sm font-medium text-gray-700 mb-2">วันที่สิ้นสุด (ไม่บังคับ)</label>
                    <input type="date" id="assign-end-date" name="end_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-3">เลือกพนักงาน</label>
                <div class="border border-gray-300 rounded-lg max-h-80 overflow-y-auto">
                    <div class="p-3 border-b border-gray-200 bg-gray-50">
                        <label class="flex items-center">
                            <input type="checkbox" id="select-all-employees" class="mr-3">
                            <span class="font-medium text-gray-700">เลือกทั้งหมด</span>
                        </label>
                    </div>

                    <div class="p-3 space-y-3">
                        <?php if (!empty($employees)) : ?>
                            <?php foreach ($employees as $employee) : ?>
                                <label class="flex items-center p-3 hover:bg-gray-50 rounded-lg cursor-pointer">
                                    <input type="checkbox" name="employee_ids[]" value="<?php echo $employee['id']; ?>" class="mr-3 employee-checkbox">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">
                                            <?php echo htmlspecialchars($employee['first_name'] . ' ' . $employee['last_name']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            รหัส: <?php echo htmlspecialchars($employee['emp_code']); ?> |
                                            แผนก: <?php echo htmlspecialchars($employee['department_name'] ?? 'ไม่ระบุ'); ?> |
                                            ตำแหน่ง: <?php echo htmlspecialchars($employee['position_name'] ?? 'ไม่ระบุ'); ?>
                                        </div>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeAssignEmployeeModal()" class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors font-medium">
                    ยกเลิก
                </button>
                <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg transition-colors font-medium">
                    <i class="fas fa-user-plus mr-2"></i>มอบหมายพนักงาน
                </button>
            </div>
        </form>
    </div>
</div>

<div id="view-employees-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-xl p-6 max-w-4xl w-full mx-4 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center">
                    <i class="fas fa-users text-purple-600"></i>
                </div>
                <h3 id="view-employees-title" class="text-lg font-semibold text-gray-900">พนักงานในกะ</h3>
            </div>
            <button onclick="closeViewEmployeesModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>

        <div id="shift-employees-content" class="space-y-4">
            </div>
    </div>
</div>


<style>
    .tab-button {
        cursor: pointer;
        user-select: none;
    }
    
    .tab-button:hover {
        background-color: #f9fafb;
    }
    
    .tab-button.active {
        border-color: #4f46e5;
        color: #4f46e5;
        background-color: #eef2ff;
    }

    .tab-content {
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Custom scrollbar for tab navigation */
    .flex.overflow-x-auto::-webkit-scrollbar {
        height: 4px;
    }

    .flex.overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .flex.overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 2px;
    }

    .flex.overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>

<script>
    // This script block now contains all the logic needed for the tabs to work.
    document.addEventListener('DOMContentLoaded', function() {
        const tabContainer = document.querySelector('.flex.overflow-x-auto');
        if (!tabContainer) return;

        const tabs = Array.from(tabContainer.querySelectorAll('.tab-button'));
        const contents = document.querySelectorAll('.tab-content');

        function switchTab(targetTab) {
            if (!targetTab) return;
            const tabId = targetTab.dataset.tabId;

            // Update tab styles
            tabs.forEach(tab => {
                const isTarget = tab === targetTab;
                tab.classList.toggle('active', isTarget);
                tab.classList.toggle('border-indigo-500', isTarget);
                tab.classList.toggle('text-indigo-600', isTarget);
                tab.classList.toggle('bg-indigo-50', isTarget);
                tab.classList.toggle('border-transparent', !isTarget);
                tab.classList.toggle('text-gray-500', !isTarget);
                tab.classList.toggle('hover:text-gray-700', !isTarget);
                tab.classList.toggle('hover:border-gray-300', !isTarget);
            });

            // Update content visibility
            contents.forEach(content => {
                content.classList.toggle('hidden', content.id !== `content-${tabId}`);
            });
        }

        // Add click listener to tab container (event delegation)
        tabContainer.addEventListener('click', function(e) {
            const clickedTab = e.target.closest('.tab-button');
            if (clickedTab) {
                e.preventDefault();
                switchTab(clickedTab);
                
                // Update URL hash for deep linking
                const tabId = clickedTab.dataset.tabId;
                if (history.pushState) {
                    history.pushState(null, null, `#${tabId}`);
                } else {
                    window.location.hash = `#${tabId}`;
                }
            }
        });

        // Handle initial page load with a hash in the URL
        const currentHash = window.location.hash.substring(1);
        if (currentHash) {
            const targetTabOnLoad = document.getElementById(`tab-${currentHash}`);
            if (targetTabOnLoad) {
                switchTab(targetTabOnLoad);
            }
        }
    });

    // Dummy functions that might be called from other parts of the page, to avoid errors
    function confirmReset() {
        if (confirm('คุณแน่ใจหรือไม่ว่าต้องการรีเซ็ตการตั้งค่าทั้งหมดเป็นค่าเริ่มต้น? การกระทำนี้ไม่สามารถย้อนกลับได้')) {
            // Create a form and submit it to the reset action
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '<?php echo BASE_URL; ?>/settings/reset';
            document.body.appendChild(form);
            form.submit();
        }
    }
    // Other functions like showCreateShiftModal, previewTheme etc. are assumed to be in settings.js
</script>
<script src="<?php echo BASE_URL; ?>/public/js/settings.js"></script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
