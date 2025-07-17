<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
?>
<style>
    .tab-active { 
        border-color: #4f46e5; 
        color: #4f46e5;
        background-color: #f1f2f7;
    }
</style>
<?php
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title ?? 'ตั้งค่าระบบ'); ?></h1>
            <p class="text-gray-500 mt-1">จัดการการตั้งค่าต่างๆ ของระบบ</p>
        </div>
    </div>

    <!-- Display Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?php echo $_SESSION['success_message']; ?></p>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p><?php echo $_SESSION['error_message']; ?></p>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Settings Form -->
    <form action="<?php echo BASE_URL; ?>/settings/update" method="POST" enctype="multipart/form-data">
        <div class="bg-white rounded-2xl shadow-lg">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-1 sm:space-x-4 px-4">
                    <button type="button" data-tab="org" class="tab-button whitespace-nowrap py-4 px-1 sm:px-4 border-b-2 font-medium text-sm tab-active">
                        ข้อมูลองค์กร
                    </button>
                    <button type="button" data-tab="worktime" class="tab-button whitespace-nowrap py-4 px-1 sm:px-4 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700">
                        เวลาทำงาน
                    </button>
                    <button type="button" data-tab="ot" class="tab-button whitespace-nowrap py-4 px-1 sm:px-4 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700">
                        ล่วงเวลา (OT)
                    </button>
                </nav>
            </div>

            <div class="p-6 md:p-8">
                <!-- Organization Info Tab -->
                <div id="tab-content-org" class="tab-content">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">ข้อมูลองค์กร</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label for="org_name" class="block text-sm font-medium text-gray-700">ชื่อองค์กร</label>
                            <input type="text" name="org_name" id="org_name" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($settings['org_name'] ?? ''); ?>">
                        </div>
                        <div class="md:col-span-2">
                            <label for="org_address" class="block text-sm font-medium text-gray-700">ที่อยู่</label>
                            <textarea name="org_address" id="org_address" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm"><?php echo htmlspecialchars($settings['org_address'] ?? ''); ?></textarea>
                        </div>
                        <div>
                            <label for="org_logo" class="block text-sm font-medium text-gray-700">โลโก้</label>
                            <input type="file" name="org_logo" id="org_logo" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        </div>
                        <?php if (!empty($settings['org_logo'])): ?>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500 mb-2">โลโก้ปัจจุบัน:</p>
                            <img src="<?php echo BASE_URL . '/' . htmlspecialchars($settings['org_logo']); ?>" alt="Organization Logo" class="h-16 w-auto rounded-md">
                        </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Working Hours Tab -->
                <div id="tab-content-worktime" class="tab-content hidden">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">ตั้งค่าเวลาทำงานและมาสาย</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="work_start_time" class="block text-sm font-medium text-gray-700">เวลาเข้างานปกติ</label>
                            <input type="time" name="work_start_time" id="work_start_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($settings['work_start_time'] ?? '08:30'); ?>">
                        </div>
                        <div>
                            <label for="work_end_time" class="block text-sm font-medium text-gray-700">เวลาเลิกงานปกติ</label>
                            <input type="time" name="work_end_time" id="work_end_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($settings['work_end_time'] ?? '17:30'); ?>">
                        </div>
                        <div>
                            <label for="grace_period_minutes" class="block text-sm font-medium text-gray-700">นาทีที่อนุโลมให้สายได้</label>
                            <input type="number" name="grace_period_minutes" id="grace_period_minutes" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($settings['grace_period_minutes'] ?? '15'); ?>" min="0">
                        </div>
                    </div>
                </div>

                <!-- OT Settings Tab -->
                <div id="tab-content-ot" class="tab-content hidden">
                    <h2 class="text-xl font-bold text-gray-800 mb-6">ตั้งค่าการทำงานล่วงเวลา</h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div>
                            <label for="ot_start_time" class="block text-sm font-medium text-gray-700">เวลาที่เริ่มนับ OT</label>
                            <input type="time" name="ot_start_time" id="ot_start_time" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($settings['ot_start_time'] ?? '18:00'); ?>">
                            <p class="mt-1 text-xs text-gray-500">เช่น หากเลิกงาน 17:30 และเริ่มนับ OT ตอน 18:00</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="px-6 md:px-8 pb-6 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                    บันทึกการตั้งค่า
                </button>
            </div>
        </div>
    </form>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const tabName = this.dataset.tab;

            // Update button styles
            tabButtons.forEach(btn => {
                btn.classList.remove('tab-active');
                btn.classList.add('text-gray-500', 'border-transparent', 'hover:text-gray-700', 'hover:border-gray-300');
            });
            this.classList.add('tab-active');
            this.classList.remove('text-gray-500', 'border-transparent', 'hover:text-gray-700', 'hover:border-gray-300');

            // Show/hide content
            tabContents.forEach(content => {
                if (content.id === 'tab-content-' + tabName) {
                    content.classList.remove('hidden');
                } else {
                    content.classList.add('hidden');
                }
            });
        });
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
