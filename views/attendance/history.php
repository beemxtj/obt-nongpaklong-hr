<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
?>
<!-- Chart.js และ FullCalendar CSS -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

<style>
    /* Enhanced Custom styles inspired by form.php */
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .stats-card {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .stats-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(102, 126, 234, 0.15);
    }

    .tab-active { 
        border-color: #667eea; 
        color: #667eea; 
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        position: relative;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
        border-radius: 12px 12px 0 0;
    }
    
    .tab-active::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: -2px;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px 2px 0 0;
    }

    .tab-button {
        transition: all 0.3s ease;
        border-radius: 12px 12px 0 0;
        padding: 16px 24px;
    }

    .tab-button:hover:not(.tab-active) {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        transform: translateY(-1px);
        color: #4f46e5;
    }

    .tab-content { 
        display: none; 
        animation: fadeIn 0.5s ease-in;
    }
    
    .tab-content.active { 
        display: block; 
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .attendance-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 20px;
        padding: 24px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .attendance-card:hover {
        transform: translateY(-4px) scale(1.02);
        box-shadow: 0 16px 32px rgba(102, 126, 234, 0.15);
    }

    .form-input {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.9);
    }

    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        color: white;
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #374151;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
    }

    .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    .btn-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 8px 16px;
        font-weight: 600;
        border: none;
        font-size: 0.875rem;
    }

    .btn-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .btn-edit {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 8px 16px;
        font-weight: 600;
        border: none;
        font-size: 0.875rem;
        margin-right: 8px;
    }

    .btn-edit:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .floating-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 20px;
        margin-bottom: 32px;
        padding: 24px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 12px;
        color: #667eea;
        -webkit-text-fill-color: #667eea;
    }

    .filter-section {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
    }

    .image-modal-backdrop {
        transition: opacity 0.3s ease;
        backdrop-filter: blur(10px);
    }

    .animate-fade-in {
        animation: fadeIn 0.6s ease-in;
    }

    .animate-slide-up {
        animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        border: 2px solid;
        backdrop-filter: blur(10px);
    }

    .status-normal {
        background: linear-gradient(135deg, #dcfdf4 0%, #a7f3d0 100%);
        color: #065f46;
        border-color: #34d399;
    }

    .status-late {
        background: linear-gradient(135deg, #fef3c7 0%, #fcd34d 100%);
        color: #92400e;
        border-color: #f59e0b;
    }

    .status-absent {
        background: linear-gradient(135deg, #fee2e2 0%, #fca5a5 100%);
        color: #991b1b;
        border-color: #ef4444;
    }

    .chart-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border-left: 4px solid #10b981;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        animation: slideIn 0.5s ease-out;
    }

    .alert-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-left: 4px solid #ef4444;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .table-container {
        overflow-x: auto;
        border-radius: 20px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .table-container table {
        border-radius: 20px;
        overflow: hidden;
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>

<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <!-- Enhanced Header Section -->
    <div class="floating-header animate-fade-in">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    <?php echo htmlspecialchars($page_title); ?>
                </h1>
                <p class="text-gray-600 mt-2 text-lg flex items-center">
                    <i class="fas fa-clock mr-2 text-indigo-600"></i>
                    <?php echo $is_admin ? 'ภาพรวมการลงเวลาทั้งองค์กร' : 'ตรวจสอบบันทึกเวลาทำงานของคุณ'; ?>
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/dashboard" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>กลับ
            </a>
        </div>
    </div>

    <!-- Enhanced Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert-success" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3 text-xl"></i>
                <p class="font-medium text-green-800"><?php echo $_SESSION['success_message']; ?></p>
                <button type="button" class="ml-auto text-green-600 hover:text-green-800 text-xl" onclick="this.parentElement.parentElement.style.display='none';">&times;</button>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert-error" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 mr-3 text-xl"></i>
                <p class="font-medium text-red-800"><?php echo $_SESSION['error_message']; ?></p>
                <button type="button" class="ml-auto text-red-600 hover:text-red-800 text-xl" onclick="this.parentElement.parentElement.style.display='none';">&times;</button>
            </div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <?php if ($is_admin): ?>
    <!-- Admin Statistics Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8 animate-slide-up">
        <?php if ($stats): ?>
            <div class="stats-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-users text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">พนักงานเข้างาน</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['unique_employees']); ?></div>
                    </div>
                </div>
            </div>

            <div class="stats-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-check-circle text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">ตรงเวลา</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['on_time']); ?></div>
                    </div>
                </div>
            </div>

            <div class="stats-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-exclamation-triangle text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">มาสาย</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['late']); ?></div>
                    </div>
                </div>
            </div>

            <div class="stats-card">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                            <i class="fas fa-clock text-white"></i>
                        </div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">ชั่วโมงเฉลี่ย</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['avg_work_hours'], 1); ?></div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Admin Filters Section -->
    <div class="filter-section animate-slide-up">
        <h3 class="section-header text-xl mb-4">
            <i class="fas fa-filter"></i>ตัวกรองข้อมูล
        </h3>
        <form method="GET" action="<?php echo BASE_URL; ?>/attendance/history" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">วันที่เริ่มต้น</label>
                <input type="date" name="date_from" value="<?php echo htmlspecialchars($date_from); ?>" class="form-input w-full">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">วันที่สิ้นสุด</label>
                <input type="date" name="date_to" value="<?php echo htmlspecialchars($date_to); ?>" class="form-input w-full">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">แผนก</label>
                <select name="department_id" id="department_filter" class="form-input w-full">
                    <option value="">ทุกแผนก</option>
                    <?php if ($departments_stmt): ?>
                        <?php while ($dept = $departments_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $dept['id']; ?>" <?php echo $selected_department_id == $dept['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['name_th']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">พนักงาน</label>
                <select name="employee_id" id="employee_filter" class="form-input w-full">
                    <option value="">ทุกคน</option>
                    <?php if ($employees_stmt): ?>
                        <?php while ($emp = $employees_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $emp['id']; ?>" <?php echo $selected_employee_id == $emp['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($emp['full_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">สถานะ</label>
                <select name="status" class="form-input w-full">
                    <option value="">ทุกสถานะ</option>
                    <option value="ปกติ" <?php echo $selected_status == 'ปกติ' ? 'selected' : ''; ?>>ปกติ</option>
                    <option value="สาย" <?php echo $selected_status == 'สาย' ? 'selected' : ''; ?>>สาย</option>
                    <option value="ขาดงาน" <?php echo $selected_status == 'ขาดงาน' ? 'selected' : ''; ?>>ขาดงาน</option>
                </select>
            </div>
            <div class="flex items-end space-x-2">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search mr-2"></i>ค้นหา
                </button>
                <a href="<?php echo BASE_URL; ?>/attendance/export?<?php echo $_SERVER['QUERY_STRING']; ?>" class="btn-secondary">
                    <i class="fas fa-download mr-2"></i>Export
                </a>
            </div>
        </form>
    </div>

    <!-- Chart Section for Admin -->
    <?php if (!empty($chart_data)): ?>
    <div class="chart-container animate-slide-up">
        <h3 class="section-header text-xl mb-4">
            <i class="fas fa-chart-line"></i>แนวโน้มการเข้างาน
        </h3>
        <canvas id="attendanceChart" width="400" height="100"></canvas>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- Tab Navigation -->
    <div class="mb-6 animate-slide-up">
        <div class="glass-card p-0 overflow-hidden">
            <nav class="flex space-x-4 p-6 pb-0" aria-label="Tabs">
                <button id="tab-card" class="tab-button tab-active">
                    <i class="fas fa-th-large mr-2"></i>มุมมองการ์ด
                </button>
                <button id="tab-calendar" class="tab-button text-gray-500 hover:text-gray-700">
                    <i class="fas fa-calendar-alt mr-2"></i>มุมมองปฏิทิน
                </button>
                <?php if ($is_admin): ?>
                <button id="tab-table" class="tab-button text-gray-500 hover:text-gray-700">
                    <i class="fas fa-table mr-2"></i>มุมมองตาราง
                </button>
                <?php endif; ?>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div class="animate-slide-up">
        <!-- Card View -->
        <div id="view-card" class="tab-content active">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($num > 0): ?>
                    <?php foreach ($attendance_logs as $row): ?>
                        <?php
                            $status_class = 'status-normal';
                            if ($row['status'] == 'สาย') {
                                $status_class = 'status-late';
                            } elseif ($row['status'] == 'ขาดงาน') {
                                $status_class = 'status-absent';
                            }
                        ?>
                        <div class="attendance-card">
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h3 class="text-lg font-bold text-indigo-800">
                                        <?php echo date('d M Y', strtotime($row['clock_in_time'])); ?>
                                    </h3>
                                    <?php if ($is_admin): ?>
                                        <p class="text-sm text-gray-600 mt-1">
                                            <span class="font-semibold"><?php echo htmlspecialchars($row['employee_code']); ?></span>
                                            <?php echo htmlspecialchars($row['first_name_th'] . ' ' . $row['last_name_th']); ?>
                                        </p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($row['department_name'] ?? 'ไม่ระบุแผนก'); ?></p>
                                    <?php endif; ?>
                                </div>
                                <div class="flex flex-col items-end space-y-2">
                                    <span class="status-badge <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                    <?php if ($is_admin): ?>
                                    <div class="flex space-x-1">
                                        <a href="<?php echo BASE_URL; ?>/attendance/edit/<?php echo $row['id']; ?>" class="btn-edit">
                                            <i class="fas fa-edit text-sm"></i>
                                        </a>
                                        <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn-danger">
                                            <i class="fas fa-trash text-sm"></i>
                                        </button>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 flex items-center">
                                        <i class="fas fa-sign-in-alt text-green-500 w-5 mr-2"></i>เวลาเข้า
                                    </span>
                                    <span class="font-semibold"><?php echo date('H:i:s', strtotime($row['clock_in_time'])); ?> น.</span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 flex items-center">
                                        <i class="fas fa-sign-out-alt text-red-500 w-5 mr-2"></i>เวลาออก
                                    </span>
                                    <span class="font-semibold"><?php echo $row['clock_out_time'] ? date('H:i:s', strtotime($row['clock_out_time'])) . ' น.' : '-'; ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 flex items-center">
                                        <i class="fas fa-hourglass-half text-blue-500 w-5 mr-2"></i>ชั่วโมงทำงาน
                                    </span>
                                    <span class="font-semibold"><?php echo !empty($row['work_hours']) ? number_format($row['work_hours'], 2) . ' ชม.' : '-'; ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 flex items-center">
                                        <i class="fas fa-plus-circle text-purple-500 w-5 mr-2"></i>ชั่วโมง OT
                                    </span>
                                    <span class="font-semibold"><?php echo !empty($row['ot_hours']) ? number_format($row['ot_hours'], 2) . ' ชม.' : '-'; ?></span>
                                </div>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-200 text-sm text-gray-500">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-2">
                                        <span class="font-semibold">รูปถ่าย:</span>
                                        <?php if (!empty($row['clock_in_image_path'])): ?>
                                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($row['clock_in_image_path']); ?>" 
                                                 alt="ภาพเข้างาน" 
                                                 class="view-image-trigger w-8 h-8 rounded-full object-cover border-2 border-green-400 cursor-pointer transition-transform hover:scale-110">
                                        <?php endif; ?>
                                        <?php if (!empty($row['clock_out_image_path'])): ?>
                                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($row['clock_out_image_path']); ?>" 
                                                 alt="ภาพออกงาน" 
                                                 class="view-image-trigger w-8 h-8 rounded-full object-cover border-2 border-red-400 cursor-pointer transition-transform hover:scale-110">
                                        <?php endif; ?>
                                        <?php if (empty($row['clock_in_image_path']) && empty($row['clock_out_image_path'])): ?>
                                            <span class="text-xs text-gray-400">ไม่มีรูป</span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <?php if (!empty($row['clock_in_latitude'])): ?>
                                            <a href="https://www.google.com/maps?q=<?php echo $row['clock_in_latitude']; ?>,<?php echo $row['clock_in_longitude']; ?>" 
                                               target="_blank" 
                                               class="text-green-600 hover:underline flex items-center transition-colors hover:text-green-800" 
                                               title="แผนที่เข้างาน">
                                                <i class="fas fa-map-marker-alt mr-1"></i>เข้า
                                            </a>
                                        <?php endif; ?>
                                        <?php if (!empty($row['clock_out_latitude'])): ?>
                                            <a href="https://www.google.com/maps?q=<?php echo $row['clock_out_latitude']; ?>,<?php echo $row['clock_out_longitude']; ?>" 
                                               target="_blank" 
                                               class="text-red-600 hover:underline flex items-center transition-colors hover:text-red-800" 
                                               title="แผนที่ออกงาน">
                                                <i class="fas fa-map-marker-alt mr-1"></i>ออก
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-span-full text-center py-12">
                        <div class="glass-card p-8">
                            <i class="fas fa-calendar-times text-6xl text-gray-400 mb-4"></i>
                            <h3 class="text-xl font-semibold text-gray-600 mb-2">ไม่พบประวัติการลงเวลา</h3>
                            <p class="text-gray-500">ไม่มีข้อมูลการลงเวลาในช่วงที่เลือก</p>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Calendar View -->
        <div id="view-calendar" class="tab-content hidden">
            <div class="glass-card p-6">
                <div id='calendar'></div>
            </div>
        </div>

        <?php if ($is_admin): ?>
        <!-- Table View (Admin Only) -->
        <div id="view-table" class="tab-content hidden">
            <div class="glass-card overflow-hidden">
                <div class="table-container">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gradient-to-r from-indigo-50 to-purple-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">วันที่</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">พนักงาน</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">แผนก</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เวลาเข้า</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เวลาออก</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ชั่วโมง</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รูปภาพ</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">การจัดการ</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php if ($num > 0): ?>
                                <?php foreach ($attendance_logs as $row): ?>
                                    <tr class="hover:bg-gray-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                            <?php echo date('d/m/Y', strtotime($row['clock_in_time'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-10 w-10">
                                                    <img class="h-10 w-10 rounded-full object-cover" 
                                                         src="<?php echo BASE_URL . '/' . (!empty($row['profile_image_path']) ? $row['profile_image_path'] : 'assets/images/default-profile.png'); ?>" 
                                                         alt="Profile">
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($row['first_name_th'] . ' ' . $row['last_name_th']); ?>
                                                    </div>
                                                    <div class="text-sm text-gray-500">
                                                        <?php echo htmlspecialchars($row['employee_code']); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo htmlspecialchars($row['department_name'] ?? '-'); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo date('H:i:s', strtotime($row['clock_in_time'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo $row['clock_out_time'] ? date('H:i:s', strtotime($row['clock_out_time'])) : '-'; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                            <?php echo !empty($row['work_hours']) ? number_format($row['work_hours'], 2) . ' ชม.' : '-'; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="status-badge <?php echo $row['status'] == 'ปกติ' ? 'status-normal' : ($row['status'] == 'สาย' ? 'status-late' : 'status-absent'); ?>">
                                                <?php echo htmlspecialchars($row['status']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex space-x-2">
                                                <?php if (!empty($row['clock_in_image_path'])): ?>
                                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($row['clock_in_image_path']); ?>" 
                                                         alt="เข้างาน" 
                                                         class="view-image-trigger w-8 h-8 rounded-full object-cover border-2 border-green-400 cursor-pointer">
                                                <?php endif; ?>
                                                <?php if (!empty($row['clock_out_image_path'])): ?>
                                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($row['clock_out_image_path']); ?>" 
                                                         alt="ออกงาน" 
                                                         class="view-image-trigger w-8 h-8 rounded-full object-cover border-2 border-red-400 cursor-pointer">
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="<?php echo BASE_URL; ?>/attendance/edit/<?php echo $row['id']; ?>" class="btn-edit">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button onclick="confirmDelete(<?php echo $row['id']; ?>)" class="btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9" class="px-6 py-12 text-center text-gray-500">
                                        ไม่พบข้อมูลการลงเวลา
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Image Modal -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden p-4 image-modal-backdrop">
    <div class="relative bg-white p-2 rounded-lg max-w-3xl max-h-full shadow-xl">
        <button id="closeImageModal" class="absolute -top-3 -right-3 text-white bg-gray-800 rounded-full w-8 h-8 flex items-center justify-center text-xl font-bold hover:bg-gray-700 transition-colors">&times;</button>
        <img id="modalImage" src="" alt="ขยายรูปภาพ" class="max-w-full max-h-[90vh] object-contain rounded">
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">ยืนยันการลบ</h3>
        <p class="text-gray-600 mb-6">คุณแน่ใจหรือไม่ที่จะลบข้อมูลการลงเวลานี้? การกระทำนี้ไม่สามารถยกเลิกได้</p>
        <div class="flex space-x-4 justify-end">
            <button onclick="closeDeleteModal()" class="btn-secondary">ยกเลิก</button>
            <button id="confirmDeleteBtn" class="btn-danger">ลบข้อมูล</button>
        </div>
    </div>
</div>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/th.js'></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching logic
    const tabCard = document.getElementById('tab-card');
    const tabCalendar = document.getElementById('tab-calendar');
    const tabTable = document.getElementById('tab-table');
    const viewCard = document.getElementById('view-card');
    const viewCalendar = document.getElementById('view-calendar');
    const viewTable = document.getElementById('view-table');
    const calendarEl = document.getElementById('calendar');
    
    // Image modal logic
    const imageModal = document.getElementById('imageModal');
    const modalImage = document.getElementById('modalImage');
    const closeImageModal = document.getElementById('closeImageModal');
    const imageTriggers = document.querySelectorAll('.view-image-trigger');

    // Delete modal elements
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    let calendar;
    let deleteId = null;

    // Enhanced tab switching
    function switchTab(activeTab) {
        // Remove active class from all tabs
        [tabCard, tabCalendar, tabTable].forEach(tab => {
            if (tab) tab.classList.remove('tab-active');
        });
        
        // Hide all views
        [viewCard, viewCalendar, viewTable].forEach(view => {
            if (view) view.classList.add('hidden');
        });

        // Show selected tab and view
        if (activeTab === 'calendar') {
            tabCalendar.classList.add('tab-active');
            viewCalendar.classList.remove('hidden');
            if (!calendar) {
                initializeCalendar();
            }
            calendar.render();
        } else if (activeTab === 'table' && tabTable) {
            tabTable.classList.add('tab-active');
            viewTable.classList.remove('hidden');
        } else {
            tabCard.classList.add('tab-active');
            viewCard.classList.remove('hidden');
        }
    }

    // Tab event listeners
    tabCard.addEventListener('click', () => switchTab('card'));
    tabCalendar.addEventListener('click', () => switchTab('calendar'));
    if (tabTable) {
        tabTable.addEventListener('click', () => switchTab('table'));
    }

    // Image modal functionality
    imageTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const imgSrc = this.getAttribute('src');
            if (imgSrc) {
                modalImage.setAttribute('src', imgSrc);
                imageModal.classList.remove('hidden');
            }
        });
    });

    function closeModal() {
        imageModal.classList.add('hidden');
        modalImage.setAttribute('src', '');
    }

    closeImageModal.addEventListener('click', closeModal);
    imageModal.addEventListener('click', function(event) {
        if (event.target === imageModal) {
            closeModal();
        }
    });

    // Delete functionality
    window.confirmDelete = function(id) {
        deleteId = id;
        deleteModal.classList.remove('hidden');
    };

    window.closeDeleteModal = function() {
        deleteModal.classList.add('hidden');
        deleteId = null;
    };

    confirmDeleteBtn.addEventListener('click', function() {
        if (deleteId) {
            // Show loading state
            this.innerHTML = '<span class="loading-spinner"></span> กำลังลบ...';
            this.disabled = true;
            
            // Redirect to delete endpoint
            window.location.href = `<?php echo BASE_URL; ?>/attendance/delete/${deleteId}`;
        }
    });

    // Department filter change
    const departmentFilter = document.getElementById('department_filter');
    const employeeFilter = document.getElementById('employee_filter');
    
    if (departmentFilter && employeeFilter) {
        departmentFilter.addEventListener('change', function() {
            const departmentId = this.value;
            
            // Clear employee filter
            employeeFilter.innerHTML = '<option value="">ทุกคน</option>';
            
            if (departmentId) {
                // Show loading state
                employeeFilter.innerHTML = '<option value="">กำลังโหลด...</option>';
                
                // Fetch employees for selected department
                fetch(`<?php echo BASE_URL; ?>/attendance/getEmployeesByDepartment?department_id=${departmentId}`)
                    .then(response => response.json())
                    .then(employees => {
                        employeeFilter.innerHTML = '<option value="">ทุกคน</option>';
                        employees.forEach(employee => {
                            const option = document.createElement('option');
                            option.value = employee.id;
                            option.textContent = employee.full_name;
                            employeeFilter.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        employeeFilter.innerHTML = '<option value="">เกิดข้อผิดพลาด</option>';
                    });
            }
        });
    }

    // Calendar data preparation
    const attendanceEvents = <?php
        if (isset($attendance_logs)) {
            $events = [];
            foreach ($attendance_logs as $row) {
                $color = '#3b82f6'; // ปกติ (สีน้ำเงิน)
                $title = '✓ ' . date('H:i', strtotime($row['clock_in_time']));
                if ($row['status'] == 'สาย') {
                    $color = '#f59e0b'; // สีเหลือง
                    $title = '⚠ ' . date('H:i', strtotime($row['clock_in_time']));
                } elseif ($row['status'] == 'ขาดงาน') {
                    $color = '#ef4444'; // สีแดง
                    $title = '✗ ขาดงาน';
                }
                
                $event_title = $title;
                if ($is_admin && isset($row['first_name_th'])) {
                    $event_title = $row['first_name_th'] . ' ' . $row['last_name_th'] . ' - ' . $title;
                }
                
                $events[] = [
                    'title' => $event_title,
                    'start' => date('Y-m-d', strtotime($row['clock_in_time'])),
                    'backgroundColor' => $color,
                    'borderColor' => $color
                ];
            }
            echo json_encode($events);
        } else {
            echo '[]';
        }
    ?>;

    // FullCalendar initialization
    function initializeCalendar() {
        calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'th',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,listWeek'
            },
            events: attendanceEvents,
            eventDisplay: 'block',
            height: 'auto',
            eventClick: function(info) {
                // Optional: Add click event for calendar events
                info.jsEvent.preventDefault();
            }
        });
    }

    <?php if ($is_admin && !empty($chart_data)): ?>
    // Chart.js initialization for admin
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        const chartData = <?php echo json_encode(array_reverse($chart_data)); ?>;
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: chartData.map(item => {
                    const date = new Date(item.attendance_date);
                    return date.toLocaleDateString('th-TH', { day: '2-digit', month: 'short' });
                }),
                datasets: [{
                    label: 'ตรงเวลา',
                    data: chartData.map(item => item.on_time_count),
                    borderColor: 'rgb(34, 197, 94)',
                    backgroundColor: 'rgba(34, 197, 94, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'มาสาย',
                    data: chartData.map(item => item.late_count),
                    borderColor: 'rgb(245, 158, 11)',
                    backgroundColor: 'rgba(245, 158, 11, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'ขาดงาน',
                    data: chartData.map(item => item.absent_count),
                    borderColor: 'rgb(239, 68, 68)',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'แนวโน้มการเข้างานรายวัน'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
    <?php endif; ?>

    // Enhanced animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe all cards for animation
    document.querySelectorAll('.attendance-card, .stats-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });

    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert-success, .alert-error');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });

    // Close modals with Escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            if (!imageModal.classList.contains('hidden')) {
                closeModal();
            }
            if (!deleteModal.classList.contains('hidden')) {
                closeDeleteModal();
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>