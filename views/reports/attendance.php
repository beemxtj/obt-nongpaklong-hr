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
            <p class="text-gray-500 mt-1">เลือกเดือนและปีเพื่อสร้างรายงาน</p>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-8">
        <form action="<?php echo BASE_URL; ?>/report/attendance" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
            <div>
                <label for="month" class="block text-sm font-medium text-gray-700">เดือน</label>
                <select name="month" id="month" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?php echo $m; ?>" <?php echo ($m == $month) ? 'selected' : ''; ?>>
                            <?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <label for="year" class="block text-sm font-medium text-gray-700">ปี (พ.ศ.)</label>
                <select name="year" id="year" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?php echo $y; ?>" <?php echo ($y == $year) ? 'selected' : ''; ?>>
                            <?php echo $y + 543; ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
            <div>
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                    สร้างรายงาน
                </button>
            </div>
        </form>
    </div>

    <!-- Report Table -->
    <?php if (!empty($report_data)): ?>
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">รหัสพนักงาน</th>
                        <th scope="col" class="px-6 py-3">ชื่อ - นามสกุล</th>
                        <th scope="col" class="px-6 py-3 text-center">วันทำงาน</th>
                        <th scope="col" class="px-6 py-3 text-center">วันมาสาย</th>
                        <th scope="col" class="px-6 py-3 text-center">วันลา</th>
                        <th scope="col" class="px-6 py-3 text-center">รวมมาทำงาน</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($report_data as $row): ?>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($row['employee_code']); ?></td>
                            <td class="px-6 py-4"><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td class="px-6 py-4 text-center"><?php echo $row['work_days']; ?></td>
                            <td class="px-6 py-4 text-center"><?php echo $row['late_days']; ?></td>
                            <td class="px-6 py-4 text-center"><?php echo $row['leave_days']; ?></td>
                            <td class="px-6 py-4 text-center font-bold"><?php echo $row['total_present']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
