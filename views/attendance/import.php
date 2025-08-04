<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
?>

<style>
    /* Enhanced Custom styles inspired by form.php */
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
    }

    .upload-area {
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
        padding: 48px 24px;
        text-align: center;
        transition: all 0.3s ease;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        position: relative;
        overflow: hidden;
    }

    .upload-area:hover {
        border-color: #667eea;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(102, 126, 234, 0.15);
    }

    .upload-area.dragover {
        border-color: #667eea;
        background: linear-gradient(135deg, #eef2ff 0%, #e0e7ff 100%);
        transform: scale(1.02);
    }

    .form-input {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.9);
    }

    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
        transform: translateY(-1px);
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        color: white;
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #374151;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
    }

    .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    .btn-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        border: none;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(16, 185, 129, 0.4);
    }

    .floating-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 20px;
        margin-bottom: 32px;
        padding: 24px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 12px;
        color: #667eea;
        -webkit-text-fill-color: #667eea;
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border-left: 4px solid #10b981;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        animation: slideIn 0.5s ease-out;
    }

    .alert-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-left: 4px solid #ef4444;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        animation: slideIn 0.5s ease-out;
    }

    .alert-info {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        border-left: 4px solid #3b82f6;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from { opacity: 0; transform: translateX(-20px); }
        to { opacity: 1; transform: translateX(0); }
    }

    .animate-fade-in {
        animation: fadeIn 0.6s ease-in;
    }

    .animate-slide-up {
        animation: slideUp 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .progress-bar {
        background: linear-gradient(135deg, #e5e7eb 0%, #d1d5db 100%);
        border-radius: 10px;
        overflow: hidden;
        height: 8px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 10px;
        transition: width 0.3s ease;
        width: 0%;
    }

    .file-info {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 16px;
        padding: 16px;
        margin-top: 16px;
        border: 1px solid #e2e8f0;
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 32px;
    }

    .step {
        flex: 1;
        text-align: center;
        padding: 16px;
        border-radius: 12px;
        margin: 0 8px;
        transition: all 0.3s ease;
    }

    .step.active {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transform: translateY(-4px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
    }

    .step.completed {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }

    .step.inactive {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #6b7280;
    }
</style>

<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <!-- Enhanced Header Section -->
    <div class="floating-header animate-fade-in">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    <?php echo htmlspecialchars($page_title); ?>
                </h1>
                <p class="text-gray-600 mt-2 text-lg flex items-center">
                    <i class="fas fa-file-import mr-2 text-indigo-600"></i>
                    นำเข้าข้อมูลการลงเวลาจากไฟล์ Excel
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/attendance/history" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>กลับ
            </a>
        </div>
    </div>

    <!-- Enhanced Messages -->
    <?php if (isset($_SESSION['import_status'])): ?>
        <div class="alert-info" role="alert">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-600 mr-3 text-xl mt-1"></i>
                <div class="flex-1">
                    <h4 class="font-medium text-blue-800 mb-2">ผลการนำเข้าข้อมูล</h4>
                    <div class="text-blue-700"><?php echo $_SESSION['import_status']; ?></div>
                </div>
                <button type="button" class="ml-auto text-blue-600 hover:text-blue-800 text-xl" onclick="this.parentElement.parentElement.style.display='none';">&times;</button>
            </div>
        </div>
        <?php unset($_SESSION['import_status']); ?>
    <?php endif; ?>

    <!-- Step Indicator -->
    <div class="step-indicator animate-slide-up">
        <div class="step active" id="step1">
            <i class="fas fa-upload text-2xl mb-2"></i>
            <div class="font-semibold">เลือกไฟล์</div>
            <div class="text-sm">เลือกไฟล์ Excel ที่ต้องการนำเข้า</div>
        </div>
        <div class="step inactive" id="step2">
            <i class="fas fa-check-circle text-2xl mb-2"></i>
            <div class="font-semibold">ตรวจสอบ</div>
            <div class="text-sm">ตรวจสอบรูปแบบและข้อมูล</div>
        </div>
        <div class="step inactive" id="step3">
            <i class="fas fa-database text-2xl mb-2"></i>
            <div class="font-semibold">นำเข้า</div>
            <div class="text-sm">ประมวลผลและบันทึกข้อมูล</div>
        </div>
        <div class="step inactive" id="step4">
            <i class="fas fa-chart-line text-2xl mb-2"></i>
            <div class="font-semibold">เสร็จสิ้น</div>
            <div class="text-sm">แสดงผลการนำเข้าข้อมูล</div>
        </div>
    </div>

    <div class="animate-slide-up">
        <!-- Instructions Card -->
        <div class="glass-card mb-8">
            <div class="p-6">
                <h3 class="section-header text-xl mb-4">
                    <i class="fas fa-info-circle"></i>คำแนะนำการใช้งาน
                </h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-file-excel text-green-600 mr-2"></i>
                            รูปแบบไฟล์ Excel
                        </h4>
                        <ul class="space-y-2 text-sm text-gray-600">
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                ไฟล์ต้องเป็น .xlsx หรือ .xls เท่านั้น
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                แถวแรกต้องเป็นหัวตาราง (Header)
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                ข้อมูลเริ่มจากแถวที่ 2 เป็นต้นไป
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check text-green-500 mr-2 mt-1"></i>
                                ไม่มีแถวว่างระหว่างข้อมูล
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                            <i class="fas fa-table text-blue-600 mr-2"></i>
                            คอลัมน์ที่จำเป็น
                        </h4>
                        <div class="space-y-2 text-sm text-gray-600">
                            <div class="flex justify-between">
                                <span>รหัสพนักงาน</span>
                                <span class="text-red-500 font-medium">จำเป็น</span>
                            </div>
                            <div class="flex justify-between">
                                <span>วันที่ลงเวลา</span>
                                <span class="text-red-500 font-medium">จำเป็น</span>
                            </div>
                            <div class="flex justify-between">
                                <span>เวลาเข้างาน</span>
                                <span class="text-red-500 font-medium">จำเป็น</span>
                            </div>
                            <div class="flex justify-between">
                                <span>เวลาออกงาน</span>
                                <span class="text-blue-500 font-medium">ไม่บังคับ</span>
                            </div>
                            <div class="flex justify-between">
                                <span>สถานะ</span>
                                <span class="text-blue-500 font-medium">ไม่บังคับ</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 p-4 bg-amber-50 border border-amber-200 rounded-xl">
                    <div class="flex items-start">
                        <i class="fas fa-exclamation-triangle text-amber-600 mr-3 mt-1"></i>
                        <div>
                            <h5 class="font-semibold text-amber-800 mb-2">หมายเหตุสำคัญ</h5>
                            <ul class="text-sm text-amber-700 space-y-1">
                                <li>• หากรหัสพนักงานซ้ำกับข้อมูลเดิม ระบบจะอัปเดตข้อมูลใหม่</li>
                                <li>• วันที่และเวลาต้องอยู่ในรูปแบบที่ Excel รองรับ</li>
                                <li>• ระบบจะตรวจสอบความถูกต้องของข้อมูลก่อนนำเข้า</li>
                                <li>• การนำเข้าข้อมูลจำนวนมากอาจใช้เวลาสักครู่</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Upload Form -->
        <div class="glass-card">
            <div class="p-6">
                <h3 class="section-header text-xl mb-6">
                    <i class="fas fa-cloud-upload-alt"></i>อัปโหลดไฟล์
                </h3>

                <form action="<?php echo BASE_URL; ?>/attendance/import" method="POST" enctype="multipart/form-data" id="importForm">
                    <div class="upload-area" id="uploadArea">
                        <input type="file" name="excel_file" id="excelFile" accept=".xlsx,.xls" class="hidden" required>
                        
                        <div id="uploadContent">
                            <div class="mb-4">
                                <i class="fas fa-cloud-upload-alt text-6xl text-gray-400"></i>
                            </div>
                            <h4 class="text-xl font-semibold text-gray-700 mb-2">เลือกไฟล์ Excel</h4>
                            <p class="text-gray-500 mb-4">ลากและวางไฟล์ที่นี่ หรือคลิกเพื่อเลือกไฟล์</p>
                            <button type="button" class="btn-primary" onclick="document.getElementById('excelFile').click()">
                                <i class="fas fa-folder-open mr-2"></i>เลือกไฟล์
                            </button>
                            <p class="text-xs text-gray-400 mt-2">รองรับไฟล์ .xlsx และ .xls เท่านั้น (ไม่เกิน 10MB)</p>
                        </div>

                        <div id="fileInfo" class="file-info hidden">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <i class="fas fa-file-excel text-green-600 text-2xl mr-3"></i>
                                    <div>
                                        <div class="font-semibold text-gray-800" id="fileName"></div>
                                        <div class="text-sm text-gray-500" id="fileSize"></div>
                                    </div>
                                </div>
                                <button type="button" class="text-red-500 hover:text-red-700" onclick="clearFile()">
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            <div class="mt-4">
                                <div class="progress-bar">
                                    <div class="progress-fill" id="progressFill"></div>
                                </div>
                                <div class="text-xs text-gray-500 mt-1" id="progressText">พร้อมสำหรับอัปโหลด</div>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="mt-6 grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-cog mr-2"></i>ตัวเลือกการนำเข้า
                            </label>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" name="skip_duplicates" value="1" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">ข้ามรายการที่มีอยู่แล้ว</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="validate_employee" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">ตรวจสอบรหัสพนักงาน</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" name="auto_calculate_hours" value="1" checked class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                    <span class="ml-2 text-sm text-gray-600">คำนวณชั่วโมงทำงานอัตโนมัติ</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-calendar mr-2"></i>ช่วงวันที่ (ไม่บังคับ)
                            </label>
                            <div class="space-y-3">
                                <div>
                                    <label class="text-xs text-gray-500">วันที่เริ่มต้น</label>
                                    <input type="date" name="date_from" class="form-input w-full text-sm">
                                </div>
                                <div>
                                    <label class="text-xs text-gray-500">วันที่สิ้นสุด</label>
                                    <input type="date" name="date_to" class="form-input w-full text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                        <a href="<?php echo BASE_URL; ?>/attendance/history" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>ยกเลิก
                        </a>
                        <button type="submit" class="btn-success" id="submitBtn" disabled>
                            <i class="fas fa-upload mr-2"></i>เริ่มนำเข้าข้อมูล
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sample Template Download -->
        <div class="glass-card mt-6">
            <div class="p-6">
                <h3 class="section-header text-xl mb-4">
                    <i class="fas fa-download"></i>ดาวน์โหลดแม่แบบ
                </h3>
                <p class="text-gray-600 mb-4">
                    ดาวน์โหลดไฟล์แม่แบบ Excel เพื่อใช้เป็นตัวอย่างในการจัดรูปแบบข้อมูลก่อนนำเข้า
                </p>
                <div class="flex space-x-4">
                    <a href="<?php echo BASE_URL; ?>/assets/templates/attendance_template.xlsx" class="btn-secondary" download>
                        <i class="fas fa-file-excel mr-2"></i>ดาวน์โหลดแม่แบบ Excel
                    </a>
                    <a href="<?php echo BASE_URL; ?>/assets/templates/attendance_sample.xlsx" class="btn-secondary" download>
                        <i class="fas fa-file-download mr-2"></i>ดาวน์โหลดข้อมูลตัวอย่าง
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const uploadArea = document.getElementById('uploadArea');
    const fileInput = document.getElementById('excelFile');
    const uploadContent = document.getElementById('uploadContent');
    const fileInfo = document.getElementById('fileInfo');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const submitBtn = document.getElementById('submitBtn');
    const progressFill = document.getElementById('progressFill');
    const progressText = document.getElementById('progressText');
    const form = document.getElementById('importForm');

    // Drag and drop functionality
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, preventDefaults, false);
    });

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    ['dragenter', 'dragover'].forEach(eventName => {
        uploadArea.addEventListener(eventName, highlight, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        uploadArea.addEventListener(eventName, unhighlight, false);
    });

    function highlight() {
        uploadArea.classList.add('dragover');
    }

    function unhighlight() {
        uploadArea.classList.remove('dragover');
    }

    uploadArea.addEventListener('drop', handleDrop, false);

    function handleDrop(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            handleFile(files[0]);
        }
    }

    // Click to upload
    uploadArea.addEventListener('click', function() {
        if (!fileInput.files[0]) {
            fileInput.click();
        }
    });

    fileInput.addEventListener('change', function() {
        if (this.files[0]) {
            handleFile(this.files[0]);
        }
    });

    function handleFile(file) {
        // Validate file type
        const allowedTypes = [
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'application/vnd.ms-excel'
        ];
        
        if (!allowedTypes.includes(file.type)) {
            alert('กรุณาเลือกไฟล์ Excel (.xlsx หรือ .xls) เท่านั้น');
            return;
        }

        // Validate file size (10MB)
        if (file.size > 10 * 1024 * 1024) {
            alert('ไฟล์มีขนาดใหญ่เกินไป กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 10MB');
            return;
        }

        // Update UI
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        
        uploadContent.classList.add('hidden');
        fileInfo.classList.remove('hidden');
        
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');

        // Update step indicator
        updateStepIndicator(2);

        // Simulate file validation
        simulateProgress();
    }

    function clearFile() {
        fileInput.value = '';
        uploadContent.classList.remove('hidden');
        fileInfo.classList.add('hidden');
        submitBtn.disabled = true;
        submitBtn.classList.add('opacity-50', 'cursor-not-allowed');
        progressFill.style.width = '0%';
        progressText.textContent = 'พร้อมสำหรับอัปโหลด';
        
        // Reset step indicator
        updateStepIndicator(1);
    }

    function simulateProgress() {
        let progress = 0;
        const interval = setInterval(() => {
            progress += Math.random() * 30;
            if (progress >= 100) {
                progress = 100;
                clearInterval(interval);
                progressText.textContent = 'ไฟล์พร้อมสำหรับนำเข้า';
            } else {
                progressText.textContent = `กำลังตรวจสอบไฟล์... ${Math.round(progress)}%`;
            }
            progressFill.style.width = progress + '%';
        }, 200);
    }

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function updateStepIndicator(activeStep) {
        for (let i = 1; i <= 4; i++) {
            const step = document.getElementById(`step${i}`);
            step.classList.remove('active', 'completed', 'inactive');
            
            if (i < activeStep) {
                step.classList.add('completed');
            } else if (i === activeStep) {
                step.classList.add('active');
            } else {
                step.classList.add('inactive');
            }
        }
    }

    // Form submission
    form.addEventListener('submit', function(e) {
        if (!fileInput.files[0]) {
            e.preventDefault();
            alert('กรุณาเลือกไฟล์ Excel ก่อนทำการนำเข้า');
            return;
        }

        // Update step indicator
        updateStepIndicator(3);

        // Show loading state
        submitBtn.innerHTML = '<span class="loading-spinner"></span> กำลังนำเข้าข้อมูล...';
        submitBtn.disabled = true;

        // Show progress
        progressText.textContent = 'กำลังประมวลผลข้อมูล...';
        progressFill.style.width = '0%';
        
        // Simulate upload progress
        let uploadProgress = 0;
        const uploadInterval = setInterval(() => {
            uploadProgress += Math.random() * 20;
            if (uploadProgress >= 90) {
                uploadProgress = 90;
                clearInterval(uploadInterval);
                progressText.textContent = 'กำลังบันทึกข้อมูลลงฐานข้อมูล...';
            }
            progressFill.style.width = uploadProgress + '%';
        }, 300);
    });

    // Initialize step indicator
    updateStepIndicator(1);

    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert-success, .alert-error, .alert-info');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 10000); // 10 seconds for import status
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>