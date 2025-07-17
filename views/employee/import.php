<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900">นำเข้าข้อมูลพนักงานจาก Excel</h1>
            <p class="text-gray-500 mt-1">อัปโหลดไฟล์ .xlsx หรือ .xls เพื่อเพิ่มข้อมูลพนักงานจำนวนมาก</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/employee" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">กลับ</a>
    </div>

    <!-- Upload Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
        <div class="max-w-xl mx-auto">
            <!-- แสดงข้อความแจ้งเตือน (ถ้ามี) -->
            <?php if (isset($_SESSION['import_status'])): ?>
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative mb-6" role="alert">
                    <?php echo nl2br(htmlspecialchars($_SESSION['import_status'])); ?>
                </div>
                <?php unset($_SESSION['import_status']); ?>
            <?php endif; ?>

            <form action="<?php echo BASE_URL; ?>/employee/upload" method="POST" enctype="multipart/form-data">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center">
                    <i class="fas fa-file-excel text-5xl text-green-500 mb-4"></i>
                    <label for="excel_file" class="cursor-pointer bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                        เลือกไฟล์ Excel
                    </label>
                    <input type="file" name="excel_file" id="excel_file" class="hidden" accept=".xlsx, .xls" required>
                    <p id="file-name" class="mt-4 text-sm text-gray-500">ยังไม่ได้เลือกไฟล์</p>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-lg">
                        <i class="fas fa-upload mr-2"></i>
                        เริ่มนำเข้าข้อมูล
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
    // Script เล็กน้อยสำหรับแสดงชื่อไฟล์ที่เลือก
    document.getElementById('excel_file').addEventListener('change', function() {
        var fileName = this.files[0] ? this.files[0].name : 'ยังไม่ได้เลือกไฟล์';
        document.getElementById('file-name').textContent = fileName;
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
