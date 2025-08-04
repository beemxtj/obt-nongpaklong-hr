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

    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .form-label i {
        margin-right: 8px;
        color: #667eea;
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

    .employee-info-card {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        border-radius: 20px;
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid #e2e8f0;
    }

    .required-asterisk {
        color: #ef4444;
        font-weight: bold;
        margin-left: 4px;
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
                    <i class="fas fa-edit mr-2 text-indigo-600"></i>
                    แก้ไขข้อมูลการลงเวลาของพนักงาน
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/attendance/history" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>กลับ
            </a>
        </div>
    </div>

    <!-- Enhanced Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert-success" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3 text-xl"></i>
                <p class="font-medium text-green-800"><?php echo $_SESSION['success_message']; ?></p>
                <button type="button" class="ml-auto text-green-600 hover:text-green-800 text-xl" onclick="this.parentElement.parentElement.style.display='none';">&times;</button>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert-error" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 mr-3 text-xl"></i>
                <p class="font-medium text-red-800"><?php echo $_SESSION['error_message']; ?></p>
                <button type="button" class="ml-auto text-red-600 hover:text-red-800 text-xl" onclick="this.parentElement.parentElement.style.display='none';">&times;</button>
            </div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="animate-slide-up">
        <!-- Employee Information Card -->
        <div class="employee-info-card">
            <h3 class="section-header text-xl mb-4">
                <i class="fas fa-user"></i>ข้อมูลพนักงาน
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div>
                    <label class="text-sm font-medium text-gray-500">รหัสพนักงาน</label>
                    <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($attendance_record['employee_code']); ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">ชื่อ-นามสกุล</label>
                    <p class="text-lg font-semibold text-gray-900"><?php echo htmlspecialchars($attendance_record['first_name_th'] . ' ' . $attendance_record['last_name_th']); ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">วันที่ลงเวลา</label>
                    <p class="text-lg font-semibold text-gray-900"><?php echo date('d/m/Y', strtotime($attendance_record['clock_in_time'])); ?></p>
                </div>
                <div>
                    <label class="text-sm font-medium text-gray-500">สถานะเดิม</label>
                    <p class="text-lg font-semibold <?php echo $attendance_record['status'] == 'ปกติ' ? 'text-green-600' : ($attendance_record['status'] == 'สาย' ? 'text-yellow-600' : 'text-red-600'); ?>">
                        <?php echo htmlspecialchars($attendance_record['status']); ?>
                    </p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="glass-card">
            <div class="p-8">
                <h3 class="section-header text-xl mb-6">
                    <i class="fas fa-clock"></i>แก้ไขข้อมูลการลงเวลา
                </h3>

                <form action="<?php echo BASE_URL; ?>/attendance/edit/<?php echo $attendance_record['id']; ?>" method="POST" id="editAttendanceForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Clock In Time -->
                        <div class="form-group">
                            <label for="clock_in_time" class="form-label">
                                <i class="fas fa-sign-in-alt"></i>เวลาเข้างาน <span class="required-asterisk">*</span>
                            </label>
                            <input type="datetime-local" 
                                   name="clock_in_time" 
                                   id="clock_in_time" 
                                   class="form-input w-full" 
                                   value="<?php echo date('Y-m-d\TH:i', strtotime($attendance_record['clock_in_time'])); ?>" 
                                   required>
                        </div>

                        <!-- Clock Out Time -->
                        <div class="form-group">
                            <label for="clock_out_time" class="form-label">
                                <i class="fas fa-sign-out-alt"></i>เวลาออกงาน
                            </label>
                            <input type="datetime-local" 
                                   name="clock_out_time" 
                                   id="clock_out_time" 
                                   class="form-input w-full" 
                                   value="<?php echo $attendance_record['clock_out_time'] ? date('Y-m-d\TH:i', strtotime($attendance_record['clock_out_time'])) : ''; ?>">
                        </div>

                        <!-- Status -->
                        <div class="form-group">
                            <label for="status" class="form-label">
                                <i class="fas fa-info-circle"></i>สถานะ <span class="required-asterisk">*</span>
                            </label>
                            <select name="status" id="status" class="form-input w-full" required>
                                <option value="ปกติ" <?php echo $attendance_record['status'] == 'ปกติ' ? 'selected' : ''; ?>>ปกติ</option>
                                <option value="สาย" <?php echo $attendance_record['status'] == 'สาย' ? 'selected' : ''; ?>>สาย</option>
                                <option value="ขาดงาน" <?php echo $attendance_record['status'] == 'ขาดงาน' ? 'selected' : ''; ?>>ขาดงาน</option>
                            </select>
                        </div>

                        <!-- Work Hours (Read-only, calculated automatically) -->
                        <div class="form-group">
                            <label for="work_hours_display" class="form-label">
                                <i class="fas fa-hourglass-half"></i>ชั่วโมงทำงาน
                            </label>
                            <input type="text" 
                                   id="work_hours_display" 
                                   class="form-input w-full bg-gray-100" 
                                   value="<?php echo $attendance_record['work_hours'] ? number_format($attendance_record['work_hours'], 2) . ' ชั่วโมง' : '-'; ?>" 
                                   readonly>
                            <p class="text-sm text-gray-500 mt-1">จะคำนวณอัตโนมัติจากเวลาเข้า-ออก</p>
                        </div>
                    </div>

                    <!-- Current Data Display -->
                    <div class="mt-8 p-6 bg-gray-50 rounded-xl">
                        <h4 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-history mr-2 text-gray-600"></i>ข้อมูลปัจจุบัน
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-gray-600">เวลาเข้าเดิม:</span>
                                <p class="text-gray-900"><?php echo date('d/m/Y H:i:s', strtotime($attendance_record['clock_in_time'])); ?></p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">เวลาออกเดิม:</span>
                                <p class="text-gray-900"><?php echo $attendance_record['clock_out_time'] ? date('d/m/Y H:i:s', strtotime($attendance_record['clock_out_time'])) : '-'; ?></p>
                            </div>
                            <div>
                                <span class="font-medium text-gray-600">ชั่วโมงทำงานเดิม:</span>
                                <p class="text-gray-900"><?php echo $attendance_record['work_hours'] ? number_format($attendance_record['work_hours'], 2) . ' ชั่วโมง' : '-'; ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="flex justify-end space-x-4 mt-8 pt-6 border-t border-gray-200">
                        <a href="<?php echo BASE_URL; ?>/attendance/history" class="btn-secondary">
                            <i class="fas fa-times mr-2"></i>ยกเลิก
                        </a>
                        <button type="submit" class="btn-primary" id="submitBtn">
                            <i class="fas fa-save mr-2"></i>บันทึกการแก้ไข
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Warning Card -->
        <div class="mt-6 p-6 bg-amber-50 border border-amber-200 rounded-xl">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-amber-600 text-xl"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-lg font-medium text-amber-800">คำเตือน</h3>
                    <div class="mt-2 text-sm text-amber-700">
                        <ul class="list-disc list-inside space-y-1">
                            <li>การแก้ไขข้อมูลการลงเวลาจะส่งผลต่อการคำนวณเงินเดือนและค่าตอบแทน</li>
                            <li>ชั่วโมงทำงานจะถูกคำนวณใหม่อัตโนมัติตามเวลาที่แก้ไข</li>
                            <li>การเปลี่ยนแปลงนี้จะบันทึกในระบบและสามารถตรวจสอบได้</li>
                            <li>หากเวลาออกงานว่างเปล่า ระบบจะถือว่าพนักงานยังไม่ได้ออกงาน</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const clockInInput = document.getElementById('clock_in_time');
    const clockOutInput = document.getElementById('clock_out_time');
    const workHoursDisplay = document.getElementById('work_hours_display');
    const form = document.getElementById('editAttendanceForm');
    const submitBtn = document.getElementById('submitBtn');

    // Function to calculate work hours
    function calculateWorkHours() {
        const clockIn = clockInInput.value;
        const clockOut = clockOutInput.value;

        if (clockIn && clockOut) {
            const inTime = new Date(clockIn);
            const outTime = new Date(clockOut);
            
            if (outTime > inTime) {
                const diffMs = outTime - inTime;
                const diffHours = diffMs / (1000 * 60 * 60);
                workHoursDisplay.value = diffHours.toFixed(2) + ' ชั่วโมง';
            } else {
                workHoursDisplay.value = '0.00 ชั่วโมง';
            }
        } else if (clockIn && !clockOut) {
            workHoursDisplay.value = 'รอการออกงาน';
        } else {
            workHoursDisplay.value = '-';
        }
    }

    // Event listeners for time inputs
    clockInInput.addEventListener('change', calculateWorkHours);
    clockOutInput.addEventListener('change', calculateWorkHours);

    // Form validation
    form.addEventListener('submit', function(e) {
        const clockIn = clockInInput.value;
        const clockOut = clockOutInput.value;

        if (!clockIn) {
            e.preventDefault();
            alert('กรุณาระบุเวลาเข้างาน');
            clockInInput.focus();
            return;
        }

        if (clockOut && new Date(clockOut) <= new Date(clockIn)) {
            e.preventDefault();
            alert('เวลาออกงานต้องมากกว่าเวลาเข้างาน');
            clockOutInput.focus();
            return;
        }

        // Confirm before submit
        if (!confirm('คุณแน่ใจหรือไม่ที่จะแก้ไขข้อมูลการลงเวลานี้?')) {
            e.preventDefault();
            return;
        }

        // Show loading state
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังบันทึก...';
        submitBtn.disabled = true;
    });

    // Input validation styling
    const inputs = form.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.style.borderColor = '#ef4444';
                this.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.1)';
            } else {
                this.style.borderColor = '#10b981';
                this.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.1)';
            }
        });

        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.style.borderColor = '#10b981';
                this.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.1)';
            }
        });
    });

    // Calculate initial work hours
    calculateWorkHours();

    // Auto-dismiss alerts
    const alerts = document.querySelectorAll('.alert-success, .alert-error');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateX(-20px)';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });

    // Enhanced animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe elements for animation
    document.querySelectorAll('.glass-card, .employee-info-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>