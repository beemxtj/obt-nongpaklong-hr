<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title); ?></h1>
            <p class="text-gray-500 mt-1">เลือกพนักงาน, เดือน, และปีเพื่อสร้างรายงาน</p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-8">
        <form action="<?php echo BASE_URL; ?>/report/timesheet" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
            <div class="lg:col-span-2">
                <label for="employee_id" class="block text-sm font-medium text-gray-700">พนักงาน</label>
                <select name="employee_id" id="employee_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                    <option value="">-- กรุณาเลือกพนักงาน --</option>
                    <?php while ($row = $employees->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo (isset($selected_employee) && $selected_employee->id == $row['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['employee_code'] . ' - ' . $row['full_name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700">เดือน</label>
                <select name="month" id="month" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php echo ($m == $month) ? 'selected' : ''; ?>><?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700">ปี (พ.ศ.)</label>
                <select name="year" id="year" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo ($y == $year) ? 'selected' : ''; ?>><?php echo $y + 543; ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-4">
                <button type="submit" class="w-full lg:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">สร้างรายงาน</button>
            </div>
        </form>
    </div>

    <!-- Report Table -->
    <?php if (!empty($report_data)): ?>
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="font-bold">ใบลงเวลาทำงาน: <?php echo htmlspecialchars($selected_employee->full_name); ?></h3>
            <p class="text-sm text-gray-600">ประจำเดือน <?php echo $month; ?> ปี <?php echo $year + 543; ?></p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">วันที่</th>
                        <th class="px-4 py-3">สถานะ</th>
                        <th class="px-4 py-3 text-center">เวลาเข้า</th>
                        <th class="px-4 py-3 text-center">เวลาออก</th>
                        <th class="px-4 py-3 text-center">ชั่วโมงทำงาน</th>
                        <th class="px-4 py-3 text-center">ชั่วโมง OT</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report_data as $row): ?>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium text-gray-900"><?php echo date('d/m/Y', strtotime($row['date'])); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['status']); ?></td>
                            <td class="px-4 py-2 text-center"><?php echo $row['clock_in']; ?></td>
                            <td class="px-4 py-2 text-center"><?php echo $row['clock_out']; ?></td>
                            <td class="px-4 py-2 text-center"><?php echo number_format($row['work_hours'], 2); ?></td>
                            <td class="px-4 py-2 text-center"><?php echo number_format($row['ot_hours'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
