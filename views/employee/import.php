<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title); ?></h1>
            <p class="text-gray-500 mt-1">อัปโหลดไฟล์ .xlsx หรือ .xls เพื่อเพิ่มข้อมูลพนักงานจำนวนมาก</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/employee" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">กลับ</a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
        <div class="max-w-2xl mx-auto">
            
            <!-- Instructions -->
            <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-lg mb-6">
                <h3 class="font-bold mb-2">คำแนะนำก่อนนำเข้าข้อมูล:</h3>
                <ul class="list-disc list-inside text-sm space-y-1">
                    <li>ไฟล์ต้องเป็นนามสกุล <code>.xlsx</code> หรือ <code>.xls</code> เท่านั้น</li>
                    <li>ข้อมูลแถวแรก (Header Row) ต้องมีชื่อคอลัมน์ที่ถูกต้องตามที่ระบุด้านล่าง (ตัวพิมพ์เล็ก-ใหญ่ ไม่สำคัญ แต่การสะกดต้องถูกต้อง)</li>
                    <li>หากต้องการอัปเดตข้อมูลพนักงานที่มีอยู่แล้วในระบบ ให้ใส่ <code>รหัสพนักงาน</code> ที่มีอยู่</li>
                    <li>หาก <code>รหัสพนักงาน</code> ไม่มีอยู่ในระบบ ระบบจะทำการเพิ่มพนักงานใหม่</li>
                    <li>คอลัมน์ที่จำเป็นต้องมี: <code>รหัสพนักงาน</code>, <code>คำนำหน้า</code>, <code>ชื่อ (ไทย)</code>, <code>นามสกุล (ไทย)</code>, <code>อีเมล</code>, <code>วันที่เริ่มงาน</code>, <code>สถานะ</code>, <code>ID ตำแหน่ง</code>, <code>ID แผนก</code>, และ <code>ID บทบาท</code></li>
                </ul>
            </div>

            <!-- Column Examples -->
            <div class="bg-gray-50 border border-gray-200 p-4 rounded-lg mb-6">
                <h4 class="font-bold mb-2 text-gray-800">ตัวอย่างคอลัมน์ที่สามารถมีได้ในไฟล์ Excel:</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm text-gray-700">
                    <div>
                        <p><strong>ข้อมูลพื้นฐาน:</strong></p>
                        <ul class="list-disc list-inside ml-2 space-y-1">
                            <li><code>รหัสพนักงาน</code> <span class="text-red-500">*</span></li>
                            <li><code>รหัสผ่าน</code></li>
                            <li><code>คำนำหน้า</code> <span class="text-red-500">*</span></li>
                            <li><code>ชื่อ (ไทย)</code> <span class="text-red-500">*</span></li>
                            <li><code>นามสกุล (ไทย)</code> <span class="text-red-500">*</span></li>
                            <li><code>ชื่อ (อังกฤษ)</code></li>
                            <li><code>นามสกุล (อังกฤษ)</code></li>
                            <li><code>เพศ</code></li>
                            <li><code>วันเกิด</code></li>
                            <li><code>สัญชาติ</code></li>
                            <li><code>อีเมล</code> <span class="text-red-500">*</span></li>
                        </ul>
                    </div>
                    <div>
                        <p><strong>ข้อมูลการติดต่อและที่อยู่:</strong></p>
                        <ul class="list-disc list-inside ml-2 space-y-1">
                            <li><code>เบอร์โทรศัพท์</code></li>
                            <li><code>เบอร์โทรศัพท์ที่ทำงาน</code></li>
                            <li><code>เลขบัตรประชาชน</code></li>
                            <li><code>ที่อยู่</code></li>
                            <li><code>อำเภอ/เขต</code></li>
                            <li><code>จังหวัด</code></li>
                            <li><code>รหัสไปรษณีย์</code></li>
                        </ul>
                    </div>
                    <div>
                        <p><strong>ข้อมูลการทำงาน:</strong></p>
                        <ul class="list-disc list-inside ml-2 space-y-1">
                            <li><code>วันที่เริ่มงาน</code> <span class="text-red-500">*</span></li>
                            <li><code>วันทดลองงาน</code></li>
                            <li><code>สถานะ</code> <span class="text-red-500">*</span></li>
                            <li><code>ID ตำแหน่ง</code> <span class="text-red-500">*</span></li>
                            <li><code>ID แผนก</code> <span class="text-red-500">*</span></li>
                            <li><code>ID หัวหน้างาน</code></li>
                            <li><code>ID บทบาท</code> <span class="text-red-500">*</span></li>
                        </ul>
                    </div>
                    <div>
                        <p><strong>ข้อมูลการเงิน:</strong></p>
                        <ul class="list-disc list-inside ml-2 space-y-1">
                            <li><code>เงินเดือน</code></li>
                            <li><code>ธนาคาร</code></li>
                            <li><code>เลขบัญชีธนาคาร</code></li>
                            <li><code>เลขประจำตัวผู้เสียภาษี</code></li>
                            <li><code>อัตรากองทุนสำรองเลี้ยงชีพ (พนักงาน)</code></li>
                            <li><code>อัตรากองทุนสำรองเลี้ยงชีพ (บริษัท)</code></li>
                        </ul>
                    </div>
                </div>
                <p class="text-xs text-red-500 mt-2"><span class="text-red-500">*</span> = ข้อมูลที่จำเป็นต้องมี</p>
            </div>

            <!-- Import Status Messages -->
            <?php if (isset($_SESSION['import_status'])): ?>
                <div class="mb-6">
                    <?php 
                    $is_error = strpos($_SESSION['import_status'], 'ข้อผิดพลาด') !== false || strpos($_SESSION['import_status'], 'เกิดข้อผิดพลาด') !== false;
                    $status_class = $is_error ? 'bg-red-100 border-red-500 text-red-700' : 'bg-green-100 border-green-500 text-green-700';
                    ?>
                    <div class="<?php echo $status_class; ?> border-l-4 p-4 rounded-lg" role="alert">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <i class="fas <?php echo $is_error ? 'fa-exclamation-triangle' : 'fa-check-circle'; ?> text-lg"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm leading-5">
                                    <?php echo $_SESSION['import_status']; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php unset($_SESSION['import_status']); ?>
            <?php endif; ?>

            <!-- File Upload Form -->
            <form action="<?php echo BASE_URL; ?>/employee/upload" method="POST" enctype="multipart/form-data" class="space-y-6">
                <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-indigo-400 transition-colors duration-200">
                    <div class="space-y-4">
                        <div>
                            <i class="fas fa-file-excel text-5xl text-green-500 mb-4"></i>
                        </div>
                        <div>
                            <label for="excel_file" class="cursor-pointer inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                                <i class="fas fa-upload mr-2"></i>
                                เลือกไฟล์ Excel
                            </label>
                            <input type="file" name="excel_file" id="excel_file" class="hidden" accept=".xlsx,.xls" required>
                        </div>
                        <div>
                            <p id="file-name" class="text-sm text-gray-500">ยังไม่ได้เลือกไฟล์</p>
                            <p class="text-xs text-gray-400 mt-1">รองรับไฟล์ .xlsx และ .xls เท่านั้น</p>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-end space-x-3">
                    <a href="<?php echo BASE_URL; ?>/employee" class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        ยกเลิก
                    </a>
                    <button type="submit" class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed" id="submit-btn" disabled>
                        <i class="fas fa-upload mr-2"></i>
                        เริ่มนำเข้าข้อมูล
                    </button>
                </div>
            </form>

            <!-- Additional Tips -->
            <div class="mt-8 bg-yellow-50 border border-yellow-200 text-yellow-800 p-4 rounded-lg">
                <h4 class="font-bold mb-2">เคล็ดลับ:</h4>
                <ul class="list-disc list-inside text-sm space-y-1">
                    <li>ตรวจสอบให้แน่ใจว่าข้อมูลในไฟล์ Excel ถูกต้องก่อนอัปโหลด</li>
                    <li>วันที่ควรอยู่ในรูปแบบที่ Excel รู้จัก หรือ YYYY-MM-DD</li>
                    <li>ID ต่างๆ ต้องเป็นตัวเลขที่มีอยู่จริงในระบบ</li>
                    <li>หากมีข้อผิดพลาด ระบบจะแสดงรายละเอียดให้ทราบ</li>
                </ul>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('excel_file');
    const fileName = document.getElementById('file-name');
    const submitBtn = document.getElementById('submit-btn');

    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            const allowedTypes = ['.xlsx', '.xls'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
            
            if (allowedTypes.includes(fileExtension)) {
                fileName.textContent = file.name;
                fileName.className = 'text-sm text-green-600 font-medium';
                submitBtn.disabled = false;
            } else {
                fileName.textContent = 'ไฟล์ไม่ถูกต้อง กรุณาเลือกไฟล์ .xlsx หรือ .xls';
                fileName.className = 'text-sm text-red-500';
                submitBtn.disabled = true;
                this.value = '';
            }
        } else {
            fileName.textContent = 'ยังไม่ได้เลือกไฟล์';
            fileName.className = 'text-sm text-gray-500';
            submitBtn.disabled = true;
        }
    });

    // Form submission loading state
    document.querySelector('form').addEventListener('submit', function() {
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังประมวลผล...';
        submitBtn.disabled = true;
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>