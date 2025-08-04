<?php
// views/dashboard/analytics.php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <h1 class="text-2xl md:text-3xl font-bold text-indigo-900 mb-6"><?php echo htmlspecialchars($page_title); ?> (ปี <?php echo date('Y') + 543; ?>)</h1>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-2xl shadow-lg">
            <h3 class="text-lg font-bold text-gray-800 mb-4">แนวโน้มการลาในแต่ละเดือน</h3>
            <canvas id="leaveTrendsChart"></canvas>
        </div>

        <div class="bg-white p-6 rounded-2xl shadow-lg">
             <h3 class="text-lg font-bold text-gray-800 mb-4">สรุปจำนวนวันลาแยกตามแผนก</h3>
            <canvas id="departmentSummaryChart"></canvas>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Data from PHP Controller ---
    const leaveTrendsData = <?php echo $leave_trends_json; ?>;
    const departmentData = <?php echo $department_summary_json; ?>;

    // --- Chart 1: Leave Trends by Month (Line Chart) ---
    const ctxTrends = document.getElementById('leaveTrendsChart').getContext('2d');
    
    // Prepare data for line chart
    const months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    const leaveTypes = [...new Set(leaveTrendsData.map(item => item.leave_type))];
    const datasets = leaveTypes.map((type, index) => {
        const colors = ['#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#6366F1'];
        const data = months.map((month, monthIndex) => {
            const entry = leaveTrendsData.find(d => (d.month == monthIndex + 1) && d.leave_type === type);
            return entry ? entry.total_requests : 0;
        });
        return {
            label: type,
            data: data,
            borderColor: colors[index % colors.length],
            tension: 0.1
        };
    });

    new Chart(ctxTrends, {
        type: 'line',
        data: {
            labels: months,
            datasets: datasets
        }
    });

    // --- Chart 2: Department Summary (Bar Chart) ---
    const ctxDept = document.getElementById('departmentSummaryChart').getContext('2d');
    
    // Prepare data for bar chart
    const departmentLabels = departmentData.map(item => item.department_name);
    const departmentDays = departmentData.map(item => item.total_days);

    new Chart(ctxDept, {
        type: 'bar',
        data: {
            labels: departmentLabels,
            datasets: [{
                label: 'จำนวนวันลาทั้งหมด',
                data: departmentDays,
                backgroundColor: '#7C3AED',
            }]
        },
        options: {
            indexAxis: 'y', // Show bars horizontally
            responsive: true
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>