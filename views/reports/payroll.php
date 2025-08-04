<?php
// views/reports/payroll.php
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <div class="max-w-xl mx-auto">
        <div class="flex flex-col items-center text-center">
            <div class="bg-blue-100 text-blue-600 p-4 rounded-full mb-4">
                <i class="fas fa-file-invoice-dollar fa-2x"></i>
            </div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title); ?></h1>
            <p class="text-gray-500 mt-2">
                ระบบจะสร้างไฟล์ CSV ที่มีรายการพนักงานที่ "ลาโดยไม่ได้รับค่าจ้าง" ในเดือนที่เลือก
                เพื่อนำไปใช้ในการคำนวณเงินเดือนต่อไป
            </p>
        </div>

        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8 mt-8">
            <form action="<?php echo BASE_URL; ?>/report/exportForPayroll" method="POST" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="month" class="block text-sm font-medium text-gray-700 mb-1">เลือกเดือน</label>
                        <select name="month" id="month" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                            <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?php echo $m; ?>" <?php echo ($m == date('m')) ? 'selected' : ''; ?>>
                                    <?php echo str_pad($m, 2, '0', STR_PAD_LEFT); ?>
                                </option>
                            <?php endfor; ?>
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">เลือกปี (ค.ศ.)</label>
                        <select name="year" id="year" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                             <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                                <option value="<?php echo $y; ?>"><?php echo $y; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>

                <div class="border-t pt-6">
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center">
                        <i class="fas fa-download mr-2"></i>
                        Export ข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>