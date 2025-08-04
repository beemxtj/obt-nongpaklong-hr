<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
?>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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

    .chart-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        padding: 24px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
    }

    .animate-fade-in {
        animation: fadeIn 0.6s ease-in;
    }

    .animate-slide-up {
        animation: slideUp 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .summary-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    .percentage-bar {
        background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
        border-radius: 10px;
        overflow: hidden;
        height: 8px;
    }

    .percentage-fill {
        height: 100%;
        border-radius: 10px;
        transition: width 0.8s ease;
    }

    .fill-green {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    .fill-yellow {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    }

    .fill-red {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .fill-blue {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
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
                    <i class="fas fa-chart-pie mr-2 text-indigo-600"></i>
                    รายงานสรุปการเข้างานประจำเดือน
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/attendance/history" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>กลับ
            </a>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-section animate-slide-up">
        <h3 class="section-header text-xl mb-4">
            <i class="fas fa-filter"></i>เลือกช่วงเวลา
        </h3>
        <form method="GET" action="<?php echo BASE_URL; ?>/attendance/summary" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">เดือน</label>
                <select name="month" class="form-input w-full">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo sprintf('%02d', $m); ?>" <?php echo (isset($month) && $month == sprintf('%02d', $m)) ? 'selected' : ''; ?>>
                            <?php 
                                $thai_months = [
                                    '01' => 'มกราคม', '02' => 'กุมภาพันธ์', '03' => 'มีนาคม', '04' => 'เมษายน',
                                    '05' => 'พฤษภาคม', '06' => 'มิถุนายน', '07' => 'กรกฎาคม', '08' => 'สิงหาคม',
                                    '09' => 'กันยายน', '10' => 'ตุลาคม', '11' => 'พฤศจิกายน', '12' => 'ธันวาคม'
                                ];
                                echo $thai_months[sprintf('%02d', $m)];
                            ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">ปี</label>
                <select name="year" class="form-input w-full">
                    <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?php echo $y; ?>" <?php echo (isset($year) && $year == $y) ? 'selected' : ''; ?>>
                            <?php echo $y + 543; ?> (<?php echo $y; ?>)
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">แผนก</label>
                <select name="department_id" class="form-input w-full">
                    <option value="">ทุกแผนก</option>
                    <?php if ($departments_stmt): ?>
                        <?php while ($dept = $departments_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $dept['id']; ?>" <?php echo (isset($department_id) && $department_id == $dept['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['name_th']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="btn-primary w-full">
                    <i class="fas fa-search mr-2"></i>สร้างรายงาน
                </button>
            </div>
        </form>
    </div>

    <?php if (isset($stats) && $stats): ?>
    <!-- Summary Statistics -->
    <div class="summary-grid animate-slide-up">
        <div class="stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-blue-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-check text-white"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">ยรวมวันทำงาน</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_attendance']); ?></div>
                    </div>
                </div>
            </div>
            <div class="text-sm text-gray-600">
                จากพนักงาน <?php echo number_format($stats['unique_employees']); ?> คน
            </div>
        </div>

        <div class="stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-green-500 to-green-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle text-white"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">มาตรงเวลา</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['on_time']); ?></div>
                    </div>
                </div>
            </div>
            <div class="percentage-bar mb-2">
                <div class="percentage-fill fill-green" style="width: <?php echo $stats['total_attendance'] > 0 ? round(($stats['on_time'] / $stats['total_attendance']) * 100, 1) : 0; ?>%"></div>
            </div>
            <div class="text-sm text-gray-600">
                <?php echo $stats['total_attendance'] > 0 ? round(($stats['on_time'] / $stats['total_attendance']) * 100, 1) : 0; ?>% ของทั้งหมด
            </div>
        </div>

        <div class="stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-yellow-500 to-yellow-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">มาสาย</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['late']); ?></div>
                    </div>
                </div>
            </div>
            <div class="percentage-bar mb-2">
                <div class="percentage-fill fill-yellow" style="width: <?php echo $stats['total_attendance'] > 0 ? round(($stats['late'] / $stats['total_attendance']) * 100, 1) : 0; ?>%"></div>
            </div>
            <div class="text-sm text-gray-600">
                <?php echo $stats['total_attendance'] > 0 ? round(($stats['late'] / $stats['total_attendance']) * 100, 1) : 0; ?>% ของทั้งหมด
            </div>
        </div>

        <div class="stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-purple-500 to-purple-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">ชั่วโมงเฉลี่ย</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['avg_work_hours'], 1); ?></div>
                    </div>
                </div>
            </div>
            <div class="text-sm text-gray-600">
                ชั่วโมงต่อวัน
            </div>
        </div>

        <div class="stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-plus-circle text-white"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">ชั่วโมง OT รวม</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_ot_hours'], 1); ?></div>
                    </div>
                </div>
            </div>
            <div class="text-sm text-gray-600">
                ชั่วโมงทั้งหมด
            </div>
        </div>

        <div class="stats-card">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-gradient-to-r from-red-500 to-red-600 rounded-full flex items-center justify-center">
                        <i class="fas fa-times-circle text-white"></i>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">ขาดงาน</div>
                        <div class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['absent']); ?></div>
                    </div>
                </div>
            </div>
            <div class="percentage-bar mb-2">
                <div class="percentage-fill fill-red" style="width: <?php echo $stats['total_attendance'] > 0 ? round(($stats['absent'] / $stats['total_attendance']) * 100, 1) : 0; ?>%"></div>
            </div>
            <div class="text-sm text-gray-600">
                <?php echo $stats['total_attendance'] > 0 ? round(($stats['absent'] / $stats['total_attendance']) * 100, 1) : 0; ?>% ของทั้งหมด
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <?php if (!empty($chart_data)): ?>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-8 animate-slide-up">
        <!-- Daily Attendance Chart -->
        <div class="chart-container">
            <h3 class="section-header text-xl mb-4">
                <i class="fas fa-chart-line"></i>แนวโน้มรายวัน
            </h3>
            <canvas id="dailyChart" width="400" height="200"></canvas>
        </div>

        <!-- Status Distribution Chart -->
        <div class="chart-container">
            <h3 class="section-header text-xl mb-4">
                <i class="fas fa-chart-pie"></i>สัดส่วนสถานะ
            </h3>
            <canvas id="statusChart" width="400" height="200"></canvas>
        </div>
    </div>

    <!-- Weekly Summary Chart -->
    <div class="chart-container mt-6 animate-slide-up">
        <h3 class="section-header text-xl mb-4">
            <i class="fas fa-chart-bar"></i>สรุปรายสัปดาห์
        </h3>
        <canvas id="weeklyChart" width="400" height="100"></canvas>
    </div>
    <?php endif; ?>

    <!-- Summary Table -->
    <div class="glass-card mt-6 animate-slide-up">
        <div class="p-6">
            <h3 class="section-header text-xl mb-4">
                <i class="fas fa-table"></i>สรุปรายละเอียด
            </h3>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-indigo-50 to-purple-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">รายการ</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">จำนวน</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">เปอร์เซ็นต์</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">กราฟ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-green-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-900">มาตรงเวลา</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo number_format($stats['on_time']); ?> วัน
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $stats['total_attendance'] > 0 ? round(($stats['on_time'] / $stats['total_attendance']) * 100, 1) : 0; ?>%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: <?php echo $stats['total_attendance'] > 0 ? round(($stats['on_time'] / $stats['total_attendance']) * 100, 1) : 0; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-yellow-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-900">มาสาย</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo number_format($stats['late']); ?> วัน
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $stats['total_attendance'] > 0 ? round(($stats['late'] / $stats['total_attendance']) * 100, 1) : 0; ?>%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: <?php echo $stats['total_attendance'] > 0 ? round(($stats['late'] / $stats['total_attendance']) * 100, 1) : 0; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="w-4 h-4 bg-red-500 rounded-full mr-3"></div>
                                    <span class="text-sm font-medium text-gray-900">ขาดงาน</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo number_format($stats['absent']); ?> วัน
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                <?php echo $stats['total_attendance'] > 0 ? round(($stats['absent'] / $stats['total_attendance']) * 100, 1) : 0; ?>%
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="w-24 bg-gray-200 rounded-full h-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: <?php echo $stats['total_attendance'] > 0 ? round(($stats['absent'] / $stats['total_attendance']) * 100, 1) : 0; ?>%"></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- No Data Message -->
    <div class="glass-card mt-6 animate-slide-up">
        <div class="p-12 text-center">
            <i class="fas fa-chart-pie text-6xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-600 mb-2">ไม่พบข้อมูล</h3>
            <p class="text-gray-500">ไม่มีข้อมูลการลงเวลาในช่วงเวลาที่เลือก กรุณาเลือกช่วงเวลาอื่น</p>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php if (isset($stats) && $stats && !empty($chart_data)): ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartData = <?php echo json_encode($chart_data); ?>;
    
    // Daily Attendance Chart
    const dailyCtx = document.getElementById('dailyChart');
    if (dailyCtx) {
        new Chart(dailyCtx, {
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
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
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

    // Status Distribution Chart
    const statusCtx = document.getElementById('statusChart');
    if (statusCtx) {
        const stats = <?php echo json_encode($stats); ?>;
        new Chart(statusCtx, {
            type: 'doughnut',
            data: {
                labels: ['ตรงเวลา', 'มาสาย', 'ขาดงาน'],
                datasets: [{
                    data: [stats.on_time, stats.late, stats.absent],
                    backgroundColor: [
                        'rgb(34, 197, 94)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    }

    // Weekly Summary Chart
    const weeklyCtx = document.getElementById('weeklyChart');
    if (weeklyCtx) {
        // Group data by week
        const weeklyData = {};
        chartData.forEach(item => {
            const date = new Date(item.attendance_date);
            const week = getWeekNumber(date);
            const weekKey = `สัปดาห์ที่ ${week}`;
            
            if (!weeklyData[weekKey]) {
                weeklyData[weekKey] = {
                    on_time: 0,
                    late: 0,
                    absent: 0,
                    total: 0
                };
            }
            
            weeklyData[weekKey].on_time += parseInt(item.on_time_count);
            weeklyData[weekKey].late += parseInt(item.late_count);
            weeklyData[weekKey].absent += parseInt(item.absent_count);
            weeklyData[weekKey].total += parseInt(item.total_count);
        });

        new Chart(weeklyCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(weeklyData),
                datasets: [{
                    label: 'ตรงเวลา',
                    data: Object.values(weeklyData).map(week => week.on_time),
                    backgroundColor: 'rgba(34, 197, 94, 0.8)',
                }, {
                    label: 'มาสาย',
                    data: Object.values(weeklyData).map(week => week.late),
                    backgroundColor: 'rgba(245, 158, 11, 0.8)',
                }, {
                    label: 'ขาดงาน',
                    data: Object.values(weeklyData).map(week => week.absent),
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    x: {
                        stacked: true,
                    },
                    y: {
                        stacked: true,
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }

    function getWeekNumber(date) {
        const firstDayOfMonth = new Date(date.getFullYear(), date.getMonth(), 1);
        const dayOfMonth = date.getDate();
        const dayOfWeek = firstDayOfMonth.getDay();
        return Math.ceil((dayOfMonth + dayOfWeek) / 7);
    }

    // Animate percentage bars
    const percentageFills = document.querySelectorAll('.percentage-fill');
    percentageFills.forEach(fill => {
        const targetWidth = fill.style.width;
        fill.style.width = '0%';
        setTimeout(() => {
            fill.style.width = targetWidth;
        }, 500);
    });
});
</script>
<?php endif; ?>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>