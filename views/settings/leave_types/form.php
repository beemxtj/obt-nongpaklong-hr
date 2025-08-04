<?php
// views/settings/leave_types/form.php

// Ensure BASE_URL is defined
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/obt-nongpaklong-hr');
}

// Initialize $leave_type as empty object if not set (for create mode)
if (!isset($leave_type)) {
    $leave_type = (object) [
        'id' => '',
        'name' => '',
        'max_days_per_year' => 0,
        'is_paid' => 1
    ];
}

// Determine if it's edit mode and set the form action URL
$is_edit_mode = isset($leave_type) && !empty($leave_type->id);
$form_action_url = BASE_URL . '/leave_types/' . ($is_edit_mode ? 'update/' . $leave_type->id : 'store');

// Set the page title
$page_title = $is_edit_mode ? 'แก้ไขประเภทการลา' : 'เพิ่มประเภทการลาใหม่';

// Helper function to safely get leave type data
function get_leave_type_value($leave_type, $field, $default = '') {
    if (isset($_SESSION['form_data'])) {
        return htmlspecialchars($_SESSION['form_data'][$field] ?? $default);
    }
    return isset($leave_type) && isset($leave_type->$field) ? htmlspecialchars($leave_type->$field) : htmlspecialchars($default);
}

// Helper function to check if checkbox should be checked
function is_checked($leave_type, $field, $form_data_key = null) {
    $key = $form_data_key ?? $field;
    if (isset($_SESSION['form_data'])) {
        return isset($_SESSION['form_data'][$key]);
    }
    return isset($leave_type) && isset($leave_type->$field) && $leave_type->$field == 1;
}

require_once __DIR__ . '/../../layouts/header.php';
?>

<style>
    .form-input {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 12px 16px;
        background-color: #f8fafc;
    }

    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
        transform: translateY(-1px);
        background-color: #fff;
    }

    .form-input:hover:not(:focus) {
        border-color: #cbd5e1;
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

    .form-label .required {
        color: #ef4444;
        margin-left: 4px;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        border: none;
        cursor: pointer;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background-color: #f1f5f9;
        color: #374151;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
        text-decoration: none;
    }

    .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
        background-color: #fff;
    }

    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
    }

    .form-container {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.07);
        overflow: hidden;
    }

    .form-content {
        padding: 2rem 2.5rem;
    }

    .form-footer {
        background: #f8fafc;
        padding: 1.5rem 2.5rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 1rem;
    }

    .floating-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .alert-success, .alert-error {
        border-left-width: 4px;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border-color: #10b981;
        color: #065f46;
    }

    .alert-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-color: #ef4444;
        color: #991b1b;
    }

    .checkbox-container {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
    }

    .checkbox-input {
        width: 20px;
        height: 20px;
        border-radius: 6px;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .checkbox-input:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .checkbox-input:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .checkbox-label {
        font-weight: 500;
        color: #374151;
        cursor: pointer;
        user-select: none;
    }

    .form-section {
        background: #f8fafc;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #e2e8f0;
    }

    .form-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .form-section-title i {
        margin-right: 8px;
        color: #667eea;
    }

    .help-text {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 4px;
        line-height: 1.4;
    }

    .input-group {
        position: relative;
    }

    .input-addon {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #6b7280;
        font-weight: 500;
        pointer-events: none;
    }

    .input-with-addon {
        padding-right: 60px;
    }

    .unlimited-checkbox {
        margin-top: 12px;
    }

    .days-input-container {
        transition: all 0.3s ease;
    }

    .days-input-container.disabled {
        opacity: 0.5;
        pointer-events: none;
    }
</style>

<?php require_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-calendar-times mr-3" style="-webkit-text-fill-color: #667eea;"></i>
                <?php echo htmlspecialchars($page_title); ?>
            </h1>
            <p class="text-gray-500 mt-2">
                <?php echo $is_edit_mode ? 'แก้ไขข้อมูลประเภทการลาในระบบ' : 'เพิ่มประเภทการลาใหม่ลงในระบบ'; ?>
            </p>
        </div>
        <a href="<?php echo BASE_URL; ?>/leave_types" class="btn-secondary hidden sm:flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            กลับ
        </a>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert-success" role="alert">
            <p class="flex items-center"><i class="fas fa-check-circle mr-3"></i><?php echo $_SESSION['success_message']; ?></p>
            <button type="button" class="bg-transparent border-none text-lg font-bold cursor-pointer" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert-error" role="alert">
            <p class="flex items-center"><i class="fas fa-exclamation-circle mr-3"></i><?php echo $_SESSION['error_message']; ?></p>
            <button type="button" class="bg-transparent border-none text-lg font-bold cursor-pointer" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="form-container">
        <form action="<?php echo $form_action_url; ?>" method="POST" onsubmit="return validateForm()">
            <?php if ($is_edit_mode): ?>
                <input type="hidden" name="id" value="<?php echo get_leave_type_value($leave_type, 'id'); ?>">
            <?php endif; ?>

            <div class="form-content">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i>ข้อมูลพื้นฐาน
                    </div>
                    
                    <div class="mb-6">
                        <label for="name" class="form-label">
                            <i class="fas fa-tag"></i>ชื่อประเภทการลา<span class="required">*</span>
                        </label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-input w-full" 
                               value="<?php echo get_leave_type_value($leave_type, 'name'); ?>" 
                               maxlength="100"
                               required>
                        <div class="help-text">
                            ระบุชื่อประเภทการลา เช่น ลาป่วย, ลากิจ, ลาพักร้อน เป็นต้น (สูงสุด 100 ตัวอักษร)
                        </div>
                    </div>
                </div>

                <!-- Leave Policy Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-calendar-check"></i>นโยบายการลา
                    </div>
                    
                    <div class="mb-6">
                        <label for="max_days_per_year" class="form-label">
                            <i class="fas fa-calendar-alt"></i>จำนวนวันลาสูงสุดต่อปี
                        </label>
                        <div class="days-input-container" id="days-input-container">
                            <div class="input-group">
                                <input type="number" 
                                       name="max_days_per_year" 
                                       id="max_days_per_year" 
                                       class="form-input w-full input-with-addon" 
                                       value="<?php echo get_leave_type_value($leave_type, 'max_days_per_year', '0'); ?>" 
                                       min="0" 
                                       max="365">
                                <span class="input-addon">วัน</span>
                            </div>
                        </div>
                        
                        <div class="unlimited-checkbox">
                            <div class="checkbox-container">
                                <input type="checkbox" 
                                       id="unlimited_days" 
                                       class="checkbox-input"
                                       onchange="toggleUnlimitedDays()"
                                       <?php echo (get_leave_type_value($leave_type, 'max_days_per_year', '0') == '0') ? 'checked' : ''; ?>>
                                <label for="unlimited_days" class="checkbox-label">ไม่จำกัดจำนวนวันลา</label>
                            </div>
                        </div>
                        
                        <div class="help-text">
                            กำหนดจำนวนวันลาสูงสุดที่พนักงานสามารถลาได้ต่อปี หรือเลือกไม่จำกัดจำนวนวัน
                        </div>
                    </div>
                </div>

                <!-- Payment Policy Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-money-bill-wave"></i>นโยบายการจ่ายเงิน
                    </div>
                    
                    <div class="mb-6">
                        <label class="form-label">
                            <i class="fas fa-dollar-sign"></i>สถานะการจ่ายเงิน
                        </label>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                            <div class="checkbox-container">
                                <input type="radio" 
                                       name="is_paid_radio" 
                                       id="paid_yes" 
                                       value="1"
                                       class="checkbox-input"
                                       <?php echo is_checked($leave_type, 'is_paid') ? 'checked' : ''; ?>>
                                <label for="paid_yes" class="checkbox-label">
                                    <i class="fas fa-check-circle text-green-500 mr-2"></i>ลาได้เงิน
                                </label>
                            </div>
                            
                            <div class="checkbox-container">
                                <input type="radio" 
                                       name="is_paid_radio" 
                                       id="paid_no" 
                                       value="0"
                                       class="checkbox-input"
                                       <?php echo !is_checked($leave_type, 'is_paid') ? 'checked' : ''; ?>>
                                <label for="paid_no" class="checkbox-label">
                                    <i class="fas fa-times-circle text-red-500 mr-2"></i>ลาไม่ได้เงิน
                                </label>
                            </div>
                        </div>
                        
                        <!-- Hidden input for form submission -->
                        <input type="hidden" name="is_paid" id="is_paid" value="<?php echo is_checked($leave_type, 'is_paid') ? '1' : '0'; ?>">
                        
                        <div class="help-text">
                            เลือกว่าการลาประเภทนี้พนักงานจะได้รับเงินเดือนหรือไม่
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/leave_types" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $is_edit_mode ? 'บันทึกการเปลี่ยนแปลง' : 'เพิ่มประเภทการลา'; ?>
                </button>
            </div>
        </form>
    </div>
</main>

<script>
// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    // Set up radio button listeners
    document.querySelectorAll('input[name="is_paid_radio"]').forEach(radio => {
        radio.addEventListener('change', function() {
            document.getElementById('is_paid').value = this.value;
        });
    });
    
    // Initialize unlimited days state
    toggleUnlimitedDays();
});

function toggleUnlimitedDays() {
    const unlimitedCheckbox = document.getElementById('unlimited_days');
    const daysInput = document.getElementById('max_days_per_year');
    const daysContainer = document.getElementById('days-input-container');
    
    if (unlimitedCheckbox.checked) {
        daysInput.value = '0';
        daysInput.disabled = true;
        daysContainer.classList.add('disabled');
    } else {
        daysInput.disabled = false;
        daysContainer.classList.remove('disabled');
        if (daysInput.value === '0') {
            daysInput.value = '30'; // Default value
        }
    }
}

function validateForm() {
    const name = document.getElementById('name').value.trim();
    const maxDays = document.getElementById('max_days_per_year').value;
    const unlimitedDays = document.getElementById('unlimited_days').checked;
    
    // Validate name
    if (!name) {
        alert('กรุณากรอกชื่อประเภทการลา');
        document.getElementById('name').focus();
        return false;
    }
    
    if (name.length > 100) {
        alert('ชื่อประเภทการลาต้องไม่เกิน 100 ตัวอักษร');
        document.getElementById('name').focus();
        return false;
    }
    
    // Validate max days
    if (!unlimitedDays) {
        if (!maxDays || isNaN(maxDays) || parseInt(maxDays) < 0) {
            alert('กรุณากรอกจำนวนวันลาสูงสุดให้ถูกต้อง');
            document.getElementById('max_days_per_year').focus();
            return false;
        }
        
        if (parseInt(maxDays) > 365) {
            alert('จำนวนวันลาสูงสุดต่อปีต้องไม่เกิน 365 วัน');
            document.getElementById('max_days_per_year').focus();
            return false;
        }
    }
    
    return true;
}

// Character counter for name field
document.getElementById('name').addEventListener('input', function() {
    const maxLength = 100;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    // You can add a character counter display here if needed
    if (remaining < 10) {
        this.style.borderColor = remaining < 0 ? '#ef4444' : '#f59e0b';
    } else {
        this.style.borderColor = '#e2e8f0';
    }
});
</script>

<?php 
// Clear form data from session after displaying
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}

require_once __DIR__ . '/../../layouts/footer.php'; 
?>