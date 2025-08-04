<?php
// views/reports/leave_summary.php
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title); ?></h1>
            <p class="text-gray-500 mt-1">กรองข้อมูลและสร้างรายงานสรุปการลา</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mb-8">
        <form action="<?php echo BASE_URL; ?>/report/leaveSummary" method="POST" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 xl:grid-cols-5 gap-4 items-end">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700">วันที่เริ่ม</label>
                <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($filters['start_date'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700">วันที่สิ้นสุด</label>
                <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($filters['end_date'] ?? ''); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
            </div>
            <div>
                <label for="department_id" class="block text-sm font-medium text-gray-700">แผนก</label>
                <select name="department_id" id="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">-- ทุกแผนก --</option>
                    <?php while ($row = $departments->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo (($filters['department_id'] ?? '') == $row['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['name_th']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div>
                <label for="leave_type_id" class="block text-sm font-medium text-gray-700">ประเภทการลา</label>
                <select name="leave_type_id" id="leave_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">-- ทุกประเภท --</option>
                    <?php while ($row = $leave_types->fetch(PDO::FETCH_ASSOC)): ?>
                        <option value="<?php echo $row['id']; ?>" <?php echo (($filters['leave_type_id'] ?? '') == $row['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($row['name']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="xl:col-span-1">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">สร้างรายงาน</button>
            </div>
        </form>
    </div>

    <?php if (isset($_POST['start_date'])): // แสดงตารางเมื่อมีการกดสร้างรายงานแล้วเท่านั้น ?>
    <div class="bg-white rounded-2xl shadow-lg">
        <div class="p-4 flex justify-between items-center border-b">
            <h3 class="font-bold">ผลลัพธ์รายงาน</h3>
            <a href="<?php echo BASE_URL . '/report/exportLeaveSummary?' . http_build_query($filters); ?>"
               class="bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded-lg text-sm">
               <i class="fas fa-file-excel mr-2"></i>Export to CSV
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-4 py-3">พนักงาน</th>
                        <th class="px-4 py-3">แผนก</th>
                        <th class="px-4 py-3">ประเภทลา</th>
                        <th class="px-4 py-3">ช่วงวันที่ลา</th>
                        <th class="px-4 py-3 text-center">จำนวนวัน</th>
                        <th class="px-4 py-3 text-center">สถานะ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $report_data->fetch(PDO::FETCH_ASSOC)): ?>
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-4 py-2 font-medium"><?php echo htmlspecialchars($row['full_name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['department_name']); ?></td>
                            <td class="px-4 py-2"><?php echo htmlspecialchars($row['leave_type_name']); ?></td>
                            <td class="px-4 py-2"><?php echo date('d/m/y', strtotime($row['start_date'])) . ' - ' . date('d/m/y', strtotime($row['end_date'])); ?></td>
                            <td class="px-4 py-2 text-center"><?php echo $row['total_days']; ?></td>
                            <td class="px-4 py-2 text-center"><?php echo htmlspecialchars($row['status']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>