<?php 
require_once __DIR__ . '/../layouts/header.php'; 
?>
<!-- Chart.js Library -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .stat-card {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
<?php
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-gray-50">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">ภาพรวมระบบ</h1>
            <p class="text-gray-500 mt-1">ภาพรวมของทีม ณ วันที่ <?php echo date('d/m/Y'); ?></p>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 sm:gap-6 mb-6">
        <div class="stat-card bg-blue-500 text-white p-5 rounded-xl shadow-md">
            <p class="text-sm opacity-80">พนักงานในทีม</p>
            <div class="flex justify-between items-end">
                <p class="text-4xl font-bold"><?php echo $stats['total_employees']; ?></p>
                <i class="fas fa-users fa-2x opacity-50"></i>
            </div>
        </div>
        <div class="stat-card bg-green-500 text-white p-5 rounded-xl shadow-md">
            <p class="text-sm opacity-80">ตรงเวลา</p>
            <div class="flex justify-between items-end">
                <p class="text-4xl font-bold"><?php echo $stats['present_today'] - $stats['late_today']; ?></p>
                <i class="fas fa-user-check fa-2x opacity-50"></i>
            </div>
        </div>
        <div class="stat-card bg-yellow-500 text-white p-5 rounded-xl shadow-md">
            <p class="text-sm opacity-80">มาสาย</p>
            <div class="flex justify-between items-end">
                <p class="text-4xl font-bold"><?php echo $stats['late_today']; ?></p>
                <i class="fas fa-clock fa-2x opacity-50"></i>
            </div>
        </div>
        <div class="stat-card bg-red-500 text-white p-5 rounded-xl shadow-md">
            <p class="text-sm opacity-80">ลา</p>
            <div class="flex justify-between items-end">
                <p class="text-4xl font-bold"><?php echo $stats['on_leave_today']; ?></p>
                <i class="fas fa-calendar-day fa-2x opacity-50"></i>
            </div>
        </div>
        <div class="stat-card bg-cyan-500 text-white p-5 rounded-xl shadow-md">
            <p class="text-sm opacity-80">นอกสถานที่</p>
            <div class="flex justify-between items-end">
                <p class="text-4xl font-bold">0</p> <!-- Placeholder -->
                <i class="fas fa-briefcase fa-2x opacity-50"></i>
            </div>
        </div>
        <div class="stat-card bg-gray-600 text-white p-5 rounded-xl shadow-md">
            <p class="text-sm opacity-80">ขาดงาน</p>
            <div class="flex justify-between items-end">
                <p class="text-4xl font-bold"><?php echo $stats['total_employees'] - $stats['present_today'] - $stats['on_leave_today']; ?></p>
                <i class="fas fa-user-times fa-2x opacity-50"></i>
            </div>
        </div>
    </div>

    <!-- Main Content Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- My Team - Real-time Attendance -->
        <div class="lg:col-span-1 bg-white rounded-2xl shadow-lg">
            <div class="p-4 border-b">
                <h3 class="text-lg font-bold text-gray-800">สถานะทีมวันนี้</h3>
            </div>
            <div class="overflow-y-auto max-h-[450px] p-2">
                <ul class="divide-y divide-gray-100">
                    <?php if ($today_attendance_list->rowCount() > 0): ?>
                        <?php while($row = $today_attendance_list->fetch(PDO::FETCH_ASSOC)): ?>
                            <li class="p-3 flex items-center justify-between hover:bg-gray-50 rounded-lg">
                                <div class="flex items-center gap-3">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($row['first_name_th']); ?>&background=random" class="w-10 h-10 rounded-full">
                                    <div>
                                        <p class="font-semibold text-gray-800 text-sm"><?php echo htmlspecialchars($row['first_name_th'] . ' ' . $row['last_name_th']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($row['employee_code']); ?></p>
                                    </div>
                                </div>
                                <?php if ($row['clock_out_time']): ?>
                                    <span class="bg-red-100 text-red-700 text-xs font-bold px-3 py-1 rounded-full">OUT</span>
                                <?php else: ?>
                                    <span class="bg-green-100 text-green-700 text-xs font-bold px-3 py-1 rounded-full">IN</span>
                                <?php endif; ?>
                            </li>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <li class="p-4 text-center text-gray-500">ยังไม่มีพนักงานลงเวลา</li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>

        <!-- Charts -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">จำนวนครั้งที่สาย (30 วันย้อนหลัง)</h3>
                <canvas id="lateChart"></canvas>
            </div>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">จำนวนครั้งที่ขาดงาน (30 วันย้อนหลัง)</h3>
                <canvas id="absentChart"></canvas>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Data from PHP Controller ---
    const lateChartData = <?php echo $late_chart_data_json ?? '[]'; ?>;
    const absentChartData = <?php echo $absent_chart_data_json ?? '[]'; ?>;

    // --- Chart: Late Count ---
    const ctxLate = document.getElementById('lateChart').getContext('2d');
    new Chart(ctxLate, {
        type: 'bar',
        data: {
            labels: lateChartData.map(d => d.date),
            datasets: [{
                label: 'จำนวนครั้งที่สาย',
                data: lateChartData.map(d => d.count),
                backgroundColor: 'rgba(245, 158, 11, 0.6)',
                borderColor: 'rgba(245, 158, 11, 1)',
                borderWidth: 1
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });

    // --- Chart: Absent Count ---
    const ctxAbsent = document.getElementById('absentChart').getContext('2d');
    new Chart(ctxAbsent, {
        type: 'bar',
        data: {
            labels: absentChartData.map(d => d.date),
            datasets: [{
                label: 'จำนวนครั้งที่ขาดงาน',
                data: absentChartData.map(d => d.count),
                backgroundColor: 'rgba(107, 114, 128, 0.6)',
                borderColor: 'rgba(107, 114, 128, 1)',
                borderWidth: 1
            }]
        },
        options: { scales: { y: { beginAtZero: true } } }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
