<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title ?? 'ยื่นใบลา'); ?></h1>
            <p class="text-gray-500 mt-1">กรุณากรอกข้อมูลการลาให้ครบถ้วน</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">กลับ</a>
    </div>

    <!-- Leave Request Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
        <form action="<?php echo BASE_URL; ?>/leave/store" method="POST" enctype="multipart/form-data">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- ประเภทการลา -->
                <div class="md:col-span-2">
                    <label for="leave_type_id" class="block text-sm font-medium text-gray-700">ประเภทการลา <span class="text-red-500">*</span></label>
                    <select id="leave_type_id" name="leave_type_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" required>
                        <option value="">-- กรุณาเลือกประเภทการลา --</option>
                        <?php while ($row = $leave_types->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- วันที่เริ่มลา -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">วันที่เริ่มลา <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="start_date" id="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <!-- วันที่สิ้นสุดการลา -->
                <div>
                    <label for="end_date" class="block text-sm font-medium text-gray-700">วันที่สิ้นสุดการลา <span class="text-red-500">*</span></label>
                    <input type="datetime-local" name="end_date" id="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <!-- เหตุผลการลา -->
                <div class="md:col-span-2">
                    <label for="reason" class="block text-sm font-medium text-gray-700">เหตุผลการลา <span class="text-red-500">*</span></label>
                    <textarea id="reason" name="reason" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required></textarea>
                </div>

                <!-- ไฟล์แนบ -->
                <div class="md:col-span-2">
                    <label for="attachment" class="block text-sm font-medium text-gray-700">ไฟล์แนบ (ถ้ามี)</label>
                    <input type="file" name="attachment" id="attachment" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                    ส่งคำขอ
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
