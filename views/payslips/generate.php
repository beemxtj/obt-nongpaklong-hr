<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <h1 class="text-2xl md:text-3xl font-bold text-indigo-900 mb-4">สร้างสลิปเงินเดือน</h1>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?php echo $_SESSION['success_message']; ?></p>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-lg p-6 max-w-lg mx-auto">
        <p class="text-gray-600 mb-6 text-center">เลือกรอบเดือนและปีที่ต้องการประมวลผลเพื่อสร้างสลิปเงินเดือนให้พนักงานทุกคน</p>
        
        <form action="<?php echo BASE_URL; ?>/payslip/generate" method="POST" class="space-y-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700 mb-1">เดือน</label>
                    <select name="month" id="month" class="w-full border-gray-300 rounded-lg shadow-sm" required>
                        <?php foreach($thai_months as $num => $name): ?>
                            <option value="<?php echo $num; ?>" <?php echo ($num == date('m')) ? 'selected' : ''; ?>><?php echo $name; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700 mb-1">ปี (ค.ศ.)</label>
                    <input type="number" name="year" id="year" class="w-full border-gray-300 rounded-lg shadow-sm" value="<?php echo date('Y'); ?>" required>
                </div>
            </div>

            <div class="border-t pt-6">
                <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg flex items-center justify-center">
                    <i class="fas fa-cogs mr-2"></i>
                    เริ่มประมวลผลและสร้างสลิป
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>