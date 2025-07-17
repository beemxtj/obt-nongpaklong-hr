<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title); ?></h1>
            <p class="text-gray-500 mt-1">บันทึกประวัติการปรับตำแหน่ง, เงินเดือน, หรือสถานะ</p>
        </div>
    </div>

    <!-- Display Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?php echo $_SESSION['success_message']; ?></p>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <!-- Movement Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
        <form action="<?php echo BASE_URL; ?>/movement/store" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- เลือกพนักงาน -->
                <div class="md:col-span-2">
                    <label for="employee_id" class="block text-sm font-medium text-gray-700">เลือกพนักงาน <span class="text-red-500">*</span></label>
                    <select id="employee_id" name="employee_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">-- กรุณาเลือกพนักงาน --</option>
                        <?php while ($row = $employees->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['employee_code'] . ' - ' . $row['full_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <!-- ประเภทความเคลื่อนไหว -->
                <div>
                    <label for="movement_type" class="block text-sm font-medium text-gray-700">ประเภทความเคลื่อนไหว <span class="text-red-500">*</span></label>
                    <select id="movement_type" name="movement_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="ผ่านทดลองงาน">ผ่านทดลองงาน</option>
                        <option value="ปรับตำแหน่ง">ปรับตำแหน่ง</option>
                        <option value="ปรับเงินเดือน">ปรับเงินเดือน</option>
                        <option value="ย้ายหน่วยงาน">ย้ายหน่วยงาน</option>
                        <option value="ลาออก">ลาออก</option>
                    </select>
                </div>

                <!-- วันที่มีผล -->
                <div>
                    <label for="effective_date" class="block text-sm font-medium text-gray-700">วันที่มีผล <span class="text-red-500">*</span></label>
                    <input type="date" name="effective_date" id="effective_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                </div>

                <!-- รายละเอียด -->
                <div class="md:col-span-2">
                    <label for="details" class="block text-sm font-medium text-gray-700">รายละเอียดเพิ่มเติม</label>
                    <textarea id="details" name="details" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="เช่น: ปรับตำแหน่งจาก 'นักทรัพยากรบุคคล' เป็น 'หัวหน้าฝ่ายบุคคล'"></textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
