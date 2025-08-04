<<<<<<< HEAD
<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/app.php';
}
require_once __DIR__ . '/../layouts/header.php';
?>
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    .btn-primary { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
        color: white; 
        transition: all 0.3s ease; 
        border-radius: 12px; 
        padding: 12px 24px; 
        font-weight: 600; 
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); 
        border: none; 
    }
    .btn-primary:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); 
    }
    
    .floating-header { 
        display: flex; 
        flex-direction: column; 
        gap: 1.5rem; 
        margin-bottom: 2rem;
    }
    @media (min-width: 768px) { 
        .floating-header { 
            flex-direction: row; 
            justify-content: space-between; 
            align-items: center; 
        } 
    }
    
    .section-header { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
        -webkit-background-clip: text; 
        -webkit-text-fill-color: transparent; 
        background-clip: text; 
        font-weight: 700; 
    }
    
    .filter-container {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    
    .search-input {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.3s ease;
        width: 100%;
    }
    .search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .filter-select {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.3s ease;
        background: white;
    }
    .filter-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .tab-button { 
        transition: all 0.3s ease-in-out; 
        border-bottom: 3px solid transparent; 
        padding: 12px 20px;
        font-weight: 500;
        border-radius: 8px 8px 0 0;
    }
    .tab-active { 
        color: #667eea; 
        border-color: #667eea; 
        font-weight: 600; 
        background: rgba(102, 126, 234, 0.05);
    }
    
    .status-badge { 
        padding: 6px 14px; 
        border-radius: 9999px; 
        font-weight: 600; 
        font-size: 0.75rem; 
        text-align: center; 
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    .status-approved { 
        background-color: #d1fae5; 
        color: #065f46; 
    }
    .status-rejected { 
        background-color: #fee2e2; 
        color: #991b1b; 
    }
    .status-pending { 
        background-color: #fef3c7; 
        color: #92400e; 
    }
    .status-canceled { 
        background-color: #e5e7eb; 
        color: #4b5563; 
    }
    
    .leave-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #f3f4f6;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .leave-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border-color: #667eea;
    }
    
    .leave-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #e5e7eb;
    }
    .leave-card.approved::before { background: #22c55e; }
    .leave-card.rejected::before { background: #ef4444; }
    .leave-card.pending::before { background: #f59e0b; }
    .leave-card.canceled::before { background: #9ca3af; }
    
    .table-container { 
        background: white; 
        border-radius: 16px; 
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); 
        overflow: hidden; 
        border: 1px solid #f3f4f6;
    }
    
    .table-header {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
        border-bottom: 2px solid #e2e8f0;
    }
    
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border: 1px solid #f3f4f6;
        text-align: center;
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .action-button {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .action-cancel {
        background: #fee2e2;
        color: #dc2626;
    }
    .action-cancel:hover {
        background: #fecaca;
    }
    
    #calendar { 
        --fc-button-bg-color: #667eea; 
        --fc-button-border-color: #667eea; 
        --fc-button-hover-bg-color: #764ba2; 
        --fc-button-hover-border-color: #764ba2; 
        --fc-today-bg-color: rgba(102, 126, 234, 0.1); 
    }
    
    .date-range {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .leave-type {
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.5rem;
    }
    
    .leave-reason {
        color: #6b7280;
        font-size: 0.875rem;
        line-height: 1.4;
        margin-bottom: 1rem;
    }
    
    @media (max-width: 768px) {
        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .filter-container {
            padding: 1rem;
        }
        
        .floating-header {
            gap: 1rem;
        }
    }
</style>

<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-history mr-3" style="-webkit-text-fill-color: #667eea;"></i>ประวัติการลา
            </h1>
            <p class="text-gray-500 mt-2">
                <?php echo ($permissions['is_admin'] || $permissions['is_hr']) ? 'ภาพรวมการลาทั้งหมดในระบบ' : 'ตรวจสอบสถานะและประวัติการลาของคุณ'; ?>
            </p>
        </div>
        <a href="<?php echo BASE_URL; ?>/leave/create" class="btn-primary flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i> ยื่นใบลา
        </a>
    </div>

    <?php if ($permissions['is_admin'] || $permissions['is_hr']) : ?>
        <!-- สำหรับ Admin/HR -->
        <div class="filter-container">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหาพนักงาน</label>
                    <input type="text" id="search-employee" class="search-input" placeholder="ชื่อพนักงาน...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ประเภทการลา</label>
                    <select id="filter-leave-type" class="filter-select">
                        <option value="">ทั้งหมด</option>
                        <option value="ลาป่วย">ลาป่วย</option>
                        <option value="ลากิจ">ลากิจ</option>
                        <option value="ลาพักผ่อน">ลาพักผ่อน</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">สถานะ</label>
                    <select id="filter-status" class="filter-select">
                        <option value="">ทั้งหมด</option>
                        <option value="รออนุมัติ">รออนุมัติ</option>
                        <option value="อนุมัติ">อนุมัติ</option>
                        <option value="ไม่อนุมัติ">ไม่อนุมัติ</option>
                        <option value="ยกเลิก">ยกเลิก</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ปี</label>
                    <select id="filter-year" class="filter-select">
                        <option value="">ทั้งหมด</option>
                        <?php 
                        $current_year = date('Y');
                        for ($i = $current_year; $i >= $current_year - 3; $i--) {
                            echo "<option value='$i'>$i</option>";
                        }
                        ?>
                    </select>
                </div>
            </div>
        </div>

        <?php if (isset($num) && $num > 0) : ?>
            <!-- สถิติโดยรวม -->
            <div class="stats-container">
                <?php
                $leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $total = count($leave_requests);
                $approved = count(array_filter($leave_requests, fn($r) => $r['status'] == 'อนุมัติ'));
                $pending = count(array_filter($leave_requests, fn($r) => $r['status'] == 'รออนุมัติ'));
                $rejected = count(array_filter($leave_requests, fn($r) => $r['status'] == 'ไม่อนุมัติ'));
                ?>
                <div class="stat-card">
                    <div class="stat-number text-blue-600"><?php echo $total; ?></div>
                    <div class="stat-label">ทั้งหมด</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number text-green-600"><?php echo $approved; ?></div>
                    <div class="stat-label">อนุมัติแล้ว</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number text-yellow-600"><?php echo $pending; ?></div>
                    <div class="stat-label">รออนุมัติ</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number text-red-600"><?php echo $rejected; ?></div>
                    <div class="stat-label">ไม่อนุมัติ</div>
                </div>
            </div>

            <div class="table-container">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left text-gray-500" id="leave-table">
                        <thead class="text-xs text-gray-700 uppercase table-header">
                            <tr>
                                <th scope="col" class="px-6 py-4 font-semibold">พนักงาน</th>
                                <th scope="col" class="px-6 py-4 font-semibold">ประเภทการลา</th>
                                <th scope="col" class="px-6 py-4 font-semibold">วันที่ลา</th>
                                <th scope="col" class="px-6 py-4 font-semibold">จำนวนวัน</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">สถานะ</th>
                                <th scope="col" class="px-6 py-4 font-semibold text-center">วันที่ยื่น</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($leave_requests as $row) : 
                                $status_class = 'status-pending';
                                $status_icon = 'fas fa-clock';
                                
                                if ($row['status'] == 'อนุมัติ') {
                                    $status_class = 'status-approved';
                                    $status_icon = 'fas fa-check';
                                } elseif ($row['status'] == 'ไม่อนุมัติ') {
                                    $status_class = 'status-rejected';
                                    $status_icon = 'fas fa-times';
                                } elseif ($row['status'] == 'ยกเลิก') {
                                    $status_class = 'status-canceled';
                                    $status_icon = 'fas fa-ban';
                                }
                                
                                $start_date = new DateTime($row['start_date']);
                                $end_date = new DateTime($row['end_date']);
                                $days_count = $start_date->diff($end_date)->days + 1;
                            ?>
                                <tr class="bg-white border-b hover:bg-slate-50 transition-colors duration-200" 
                                    data-employee="<?php echo strtolower($row['employee_name']); ?>"
                                    data-leave-type="<?php echo $row['leave_type_name']; ?>"
                                    data-status="<?php echo $row['status']; ?>"
                                    data-year="<?php echo date('Y', strtotime($row['start_date'])); ?>">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900"><?php echo htmlspecialchars($row['employee_name']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-medium text-gray-700"><?php echo htmlspecialchars($row['leave_type_name']); ?></span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-gray-900"><?php echo $start_date->format('d/m/Y'); ?></div>
                                        <div class="text-gray-500 text-xs">ถึง <?php echo $end_date->format('d/m/Y'); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-gray-900"><?php echo $days_count; ?> วัน</span>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <span class="status-badge <?php echo $status_class; ?>">
                                            <i class="<?php echo $status_icon; ?>"></i>
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-center text-gray-500">
                                        <?php echo date('d/m/Y', strtotime($row['created_at'] ?? $row['start_date'])); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else : ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-calendar-times"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">ไม่พบข้อมูลการลา</h3>
                <p class="text-gray-500">ยังไม่มีข้อมูลการลาในระบบ</p>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <!-- สำหรับพนักงานทั่วไป -->
        <div class="mb-6 border-b border-gray-200">
            <nav class="flex space-x-6" aria-label="Tabs">
                <button id="tab-card" class="tab-button tab-active">
                    <i class="fas fa-th-large mr-2"></i>มุมมองการ์ด
                </button>
                <button id="tab-calendar" class="tab-button text-gray-500 hover:text-gray-700">
=======
<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
?>
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    /* Custom styles for FullCalendar */
    .fc-event { border: none !important; }
    .fc-daygrid-event { padding: 2px 5px; font-size: 0.75rem; }
    .fc-toolbar-title { font-size: 1.25rem !important; }
    .fc-button { text-transform: capitalize !important; }
    .tab-active { 
        border-color: #4f46e5; 
        color: #4f46e5;
        background-color: #eef2ff;
    }
</style>
<?php
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900">ประวัติการลา</h1>
            <p class="text-gray-500 mt-1">ตรวจสอบสถานะและประวัติการลาของคุณ</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">กลับ</a>
        <a href="<?php echo BASE_URL; ?>/leave/create" class="mt-4 sm:mt-0 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i>
            ยื่นใบลา
        </a>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-4" aria-label="Tabs">
                <button id="tab-card" class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm tab-active">
                    <i class="fas fa-list mr-2"></i>มุมมองการ์ด
                </button>
                <button id="tab-calendar" class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700">
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
                    <i class="fas fa-calendar-alt mr-2"></i>มุมมองปฏิทิน
                </button>
            </nav>
        </div>
<<<<<<< HEAD

        <div>
            <div id="view-card">
                <?php if (isset($num) && $num > 0) : ?>
                    <!-- สถิติส่วนตัว -->
                    <?php
                    $leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    $total = count($leave_requests);
                    $approved = count(array_filter($leave_requests, fn($r) => $r['status'] == 'อนุมัติ'));
                    $pending = count(array_filter($leave_requests, fn($r) => $r['status'] == 'รออนุมัติ'));
                    ?>
                    <div class="stats-container">
                        <div class="stat-card">
                            <div class="stat-number text-blue-600"><?php echo $total; ?></div>
                            <div class="stat-label">ทั้งหมด</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number text-green-600"><?php echo $approved; ?></div>
                            <div class="stat-label">อนุมัติแล้ว</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number text-yellow-600"><?php echo $pending; ?></div>
                            <div class="stat-label">รออนุมัติ</div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php foreach ($leave_requests as $row) : 
                            $status_class = 'pending';
                            $status_badge_class = 'status-pending';
                            $status_icon = 'fas fa-clock';
                            
                            if ($row['status'] == 'อนุมัติ') {
                                $status_class = 'approved';
                                $status_badge_class = 'status-approved';
                                $status_icon = 'fas fa-check';
                            } elseif ($row['status'] == 'ไม่อนุมัติ') {
                                $status_class = 'rejected';
                                $status_badge_class = 'status-rejected';
                                $status_icon = 'fas fa-times';
                            } elseif ($row['status'] == 'ยกเลิก') {
                                $status_class = 'canceled';
                                $status_badge_class = 'status-canceled';
                                $status_icon = 'fas fa-ban';
                            }
                            
                            $start_date = new DateTime($row['start_date']);
                            $end_date = new DateTime($row['end_date']);
                            $days_count = $start_date->diff($end_date)->days + 1;
                        ?>
                            <div class="leave-card <?php echo $status_class; ?>">
                                <div class="mb-4">
                                    <div class="flex justify-between items-start mb-3">
                                        <h3 class="leave-type"><?php echo htmlspecialchars($row['leave_type_name']); ?></h3>
                                        <span class="status-badge <?php echo $status_badge_class; ?>">
                                            <i class="<?php echo $status_icon; ?>"></i>
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="leave-reason">
                                        <?php echo htmlspecialchars($row['reason']); ?>
                                    </div>
                                </div>
                                
                                <div class="border-t border-gray-100 pt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="date-range">
                                            <i class="fas fa-calendar-alt text-gray-400 mr-2"></i>
                                            <?php echo $start_date->format('d/m/Y'); ?> - <?php echo $end_date->format('d/m/Y'); ?>
                                        </span>
                                        <span class="text-sm font-semibold text-gray-700">
                                            <?php echo $days_count; ?> วัน
                                        </span>
                                    </div>
                                    
                                    <div class="flex justify-between items-center">
                                        <span class="text-xs text-gray-500">
                                            ยื่นเมื่อ <?php echo date('d/m/Y', strtotime($row['created_at'] ?? $row['start_date'])); ?>
                                        </span>
                                        <?php if ($row['status'] == 'รออนุมัติ') : ?>
                                            <form action="<?php echo BASE_URL; ?>/leave/cancel/<?php echo $row['id']; ?>" method="POST" 
                                                  onsubmit="return confirm('คุณต้องการยกเลิกใบลาใช่หรือไม่?');" class="inline">
                                                <button type="submit" class="action-button action-cancel">
                                                    <i class="fas fa-times mr-1"></i>ยกเลิก
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else : ?>
                    <div class="empty-state">
                        <div class="empty-state-icon">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">ยังไม่มีประวัติการลา</h3>
                        <p class="text-gray-500 mb-4">เริ่มต้นยื่นใบลาเพื่อติดตามประวัติการลาของคุณ</p>
                        <a href="<?php echo BASE_URL; ?>/leave/create" class="btn-primary inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i> ยื่นใบลาแรก
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <div id="view-calendar" class="hidden bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <div id='calendar'></div>
            </div>
        </div>

        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
        <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/th.js'></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Tab switching
                const tabs = { 
                    card: document.getElementById('tab-card'), 
                    calendar: document.getElementById('tab-calendar') 
                };
                const views = { 
                    card: document.getElementById('view-card'), 
                    calendar: document.getElementById('view-calendar') 
                };
                const calendarEl = document.getElementById('calendar');

                const switchTab = (activeKey) => {
                    Object.keys(tabs).forEach(key => {
                        if (key === activeKey) {
                            tabs[key].classList.add('tab-active');
                            tabs[key].classList.remove('text-gray-500', 'hover:text-gray-700');
                            views[key].classList.remove('hidden');
                        } else {
                            tabs[key].classList.remove('tab-active');
                            tabs[key].classList.add('text-gray-500', 'hover:text-gray-700');
                            views[key].classList.add('hidden');
                        }
                    });
                    
                    if (activeKey === 'calendar' && window.calendar) {
                        setTimeout(() => window.calendar.render(), 100);
                    }
                };

                tabs.card.addEventListener('click', () => switchTab('card'));
                tabs.calendar.addEventListener('click', () => switchTab('calendar'));

                // Calendar setup
                const leaveEvents = <?php
                    if (isset($leave_requests)) {
                        $events = [];
                        foreach ($leave_requests as $row) {
                            $color = '#f59e0b'; // Pending
                            if ($row['status'] == 'อนุมัติ') $color = '#22c55e';
                            elseif ($row['status'] == 'ไม่อนุมัติ') $color = '#ef4444';
                            elseif ($row['status'] == 'ยกเลิก') $color = '#9ca3af';
                            
                            if ($row['status'] != 'ไม่อนุมัติ') {
                                $events[] = [
                                    'title' => $row['leave_type_name'], 
                                    'start' => $row['start_date'], 
                                    'end' => date('Y-m-d', strtotime($row['end_date'] . ' +1 day')), 
                                    'backgroundColor' => $color, 
                                    'borderColor' => $color,
                                    'extendedProps' => [
                                        'status' => $row['status'],
                                        'reason' => $row['reason']
                                    ]
                                ];
                            }
                        } 
                        echo json_encode($events);
                    } else { 
                        echo '[]'; 
                    }
                ?>;

                if (calendarEl) {
                    window.calendar = new FullCalendar.Calendar(calendarEl, {
                        initialView: 'dayGridMonth',
                        locale: 'th',
                        events: leaveEvents,
                        headerToolbar: {
                            left: 'prev,next today',
                            center: 'title',
                            right: 'dayGridMonth,listMonth'
                        },
                        height: 600,
                        eventClick: function(info) {
                            alert('ประเภท: ' + info.event.title + '\nสถานะ: ' + info.event.extendedProps.status + '\nเหตุผล: ' + info.event.extendedProps.reason);
                        }
                    });
                }
            });
        </script>
    <?php endif; ?>

    <?php if ($permissions['is_admin'] || $permissions['is_hr']) : ?>
        <!-- Filtering Script สำหรับ Admin/HR -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const searchInput = document.getElementById('search-employee');
                const leaveTypeFilter = document.getElementById('filter-leave-type');
                const statusFilter = document.getElementById('filter-status');
                const yearFilter = document.getElementById('filter-year');
                const tableRows = document.querySelectorAll('#leave-table tbody tr');

                function filterTable() {
                    const searchTerm = searchInput.value.toLowerCase();
                    const selectedLeaveType = leaveTypeFilter.value;
                    const selectedStatus = statusFilter.value;
                    const selectedYear = yearFilter.value;

                    tableRows.forEach(row => {
                        const employee = row.dataset.employee;
                        const leaveType = row.dataset.leaveType;
                        const status = row.dataset.status;
                        const year = row.dataset.year;

                        const matchesSearch = !searchTerm || employee.includes(searchTerm);
                        const matchesLeaveType = !selectedLeaveType || leaveType === selectedLeaveType;
                        const matchesStatus = !selectedStatus || status === selectedStatus;
                        const matchesYear = !selectedYear || year === selectedYear;

                        if (matchesSearch && matchesLeaveType && matchesStatus && matchesYear) {
                            row.style.display = '';
                        } else {
                            row.style.display = 'none';
                        }
                    });
                }

                searchInput.addEventListener('input', filterTable);
                leaveTypeFilter.addEventListener('change', filterTable);
                statusFilter.addEventListener('change', filterTable);
                yearFilter.addEventListener('change', filterTable);
            });
        </script>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
=======
    </div>

    <!-- Tab Content -->
    <div>
        <!-- Card View -->
        <div id="view-card">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($num > 0): ?>
                    <?php 
                        // เราต้อง reset pointer ของ $stmt เพราะจะใช้ loop 2 ครั้ง
                        $leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php foreach ($leave_requests as $row): ?>
                        <?php
                            $status_color = 'bg-yellow-100 text-yellow-800 border-yellow-400';
                            if ($row['status'] == 'อนุมัติ') {
                                $status_color = 'bg-green-100 text-green-800 border-green-400';
                            } elseif ($row['status'] == 'ไม่อนุมัติ') {
                                $status_color = 'bg-red-100 text-red-800 border-red-400';
                            }
                        ?>
                        <div class="bg-white rounded-xl shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300">
                            <div class="p-5">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-bold text-indigo-800"><?php echo htmlspecialchars($row['leave_type_name']); ?></h3>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full border <?php echo $status_color; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                    <?php echo htmlspecialchars($row['reason']); ?>
                                </p>
                                <div class="mt-4 border-t pt-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-check text-green-500 mr-2"></i>
                                        <span><?php echo date('d/m/Y', strtotime($row['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($row['end_date'])); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-gray-500 md:col-span-3">ไม่พบประวัติการลา</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Calendar View -->
        <div id="view-calendar" class="hidden bg-white p-4 rounded-2xl shadow-lg">
            <div id='calendar'></div>
        </div>
    </div>
</main>

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/th.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching logic
        const tabCard = document.getElementById('tab-card');
        const tabCalendar = document.getElementById('tab-calendar');
        const viewCard = document.getElementById('view-card');
        const viewCalendar = document.getElementById('view-calendar');
        const calendarEl = document.getElementById('calendar');

        function switchTab(activeTab) {
            if (activeTab === 'calendar') {
                tabCard.classList.remove('tab-active');
                tabCalendar.classList.add('tab-active');
                viewCard.classList.add('hidden');
                viewCalendar.classList.remove('hidden');
                calendar.render(); // Re-render the calendar when tab is shown
            } else {
                tabCalendar.classList.remove('tab-active');
                tabCard.classList.add('tab-active');
                viewCalendar.classList.add('hidden');
                viewCard.classList.remove('hidden');
            }
        }

        tabCard.addEventListener('click', () => switchTab('card'));
        tabCalendar.addEventListener('click', () => switchTab('calendar'));

        // Prepare data for FullCalendar
        const leaveEvents = <?php
            if (isset($leave_requests)) {
                $events = [];
                foreach ($leave_requests as $row) {
                    $color = '#f59e0b'; // Default: รออนุมัติ (สีเหลือง)
                    if ($row['status'] == 'อนุมัติ') {
                        $color = '#22c55e'; // สีเขียว
                    } elseif ($row['status'] == 'ไม่อนุมัติ') {
                        $color = '#ef4444'; // สีแดง
                    }
                    $events[] = [
                        'title' => $row['leave_type_name'],
                        'start' => $row['start_date'],
                        'end' => date('Y-m-d', strtotime($row['end_date'] . ' +1 day')), // Add 1 day for proper end date rendering
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
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'th',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: leaveEvents,
            eventDisplay: 'block'
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
