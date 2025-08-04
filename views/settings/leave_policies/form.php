<?php
// views/settings/leave_policies/form.php

// Ensure BASE_URL is defined
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/obt-nongpaklong-hr');
}

// Initialize $leave_policy as empty object if not set (for create mode)
if (!isset($leave_policy)) {
    $leave_policy = (object) [
        'id' => '',
        'leave_type_id' => '',
        'days_allowed_per_year' => 30,
        'is_unlimited' => 0,
        'can_be_carried_over' => 0,
        'max_carry_over_days' => 0,
        'min_notice_days' => 1,
        'max_consecutive_days' => 30,
        'requires_approval' => 1,
        'description' => ''
    ];
}

// Determine if it's edit mode and set the form action URL
$is_edit_mode = isset($leave_policy) && !empty($leave_policy->id);
$form_action_url = BASE_URL . '/leavepolicies/' . ($is_edit_mode ? 'update/' . $leave_policy->id : 'store');

// Set the page title
$page_title = $is_edit_mode ? 'แก้ไขนโยบายการลา' : 'เพิ่มนโยบายการลาใหม่';

// Helper function to safely get leave policy data
function get_policy_value($leave_policy, $field, $default = '') {
    if (isset($_SESSION['form_data'])) {
        return htmlspecialchars($_SESSION['form_data'][$field] ?? $default);
    }
    return isset($leave_policy) && isset($leave_policy->$field) ? htmlspecialchars($leave_policy->$field) : htmlspecialchars($default);
}

// Helper function to check if checkbox should be checked
function is_policy_checked($leave_policy, $field, $form_data_key = null) {
    $key = $form_data_key ?? $field;
    if (isset($_SESSION['form_data'])) {
        return isset($_SESSION['form_data'][$key]);
    }
    return isset($leave_policy) && isset($leave_policy->$field) && $leave_policy->$field == 1;
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
        font-size: 16px;
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
        font-size: 14px;
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
        color: #374151;
        text-decoration: none;
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

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 1.5rem;
    }

    .form-grid-item {
        background: white;
        border-radius: 12px;
        padding: 1rem;
        border: 1px solid #e2e8f0;
    }

    .form-grid-item.disabled {
        opacity: 0.5;
        pointer-events: none;
    }

    .toggle-section {
        margin-top: 12px;
        padding: 16px;
        background: #f0f9ff;
        border-radius: 8px;
        border: 1px solid #bfdbfe;
        transition: all 0.3s ease;
    }

    .toggle-section.hidden {
        display: none;
    }

    .leave-type-preview {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 8px;
    }

    .preview-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 8px 0;
        border-bottom: 1px dashed #cbd5e1;
    }

    .preview-item:last-child {
        border-bottom: none;
    }

    .preview-label {
        color: #6b7280;
        font-size: 13px;
    }

    .preview-value {
        font-weight: 600;
        color: #374151;
        font-size: 13px;
    }
</style>

<?php require_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-clipboard-list mr-3" style="-webkit-text-fill-color: #667eea;"></i>
                <?php echo htmlspecialchars($page_title); ?>
            </h1>
            <p class="text-gray-500 mt-2">
                <?php echo $is_edit_mode ? 'แก้ไขนโยบายการลาในระบบ' : 'เพิ่มนโยบายการลาใหม่ลงในระบบ'; ?>
            </p>
        </div>
        <a href="<?php echo BASE_URL; ?>/leavepolicies" class="btn-secondary hidden sm:flex items-center">
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
                <input type="hidden" name="id" value="<?php echo get_policy_value($leave_policy, 'id'); ?>">
            <?php endif; ?>

            <div class="form-content">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i>ข้อมูลพื้นฐาน
                    </div>
                    
                    <div class="mb-6">
                        <label for="leave_type_id" class="form-label">
                            <i class="fas fa-calendar-times"></i>ประเภทการลา<span class="required">*</span>
                        </label>
                        <select name="leave_type_id" 
                                id="leave_type_id" 
                                class="form-input w-full" 
                                required
                                onchange="updateLeaveTypePreview()">
                            <option value="">-- เลือกประเภทการลา --</option>
                            <?php if (isset($leave_types) && is_array($leave_types)): ?>
                                <?php foreach ($leave_types as $type): ?>
                                    <option value="<?php echo $type['id']; ?>" 
                                            data-name="<?php echo htmlspecialchars($type['name']); ?>"
                                            data-paid="<?php echo $type['is_paid']; ?>"
                                            <?php echo (get_policy_value($leave_policy, 'leave_type_id') == $type['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($type['name']); ?>
                                        (<?php echo $type['is_paid'] ? 'ได้เงิน' : 'ไม่ได้เงิน'; ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <div class="help-text">
                            เลือกประเภทการลาที่ต้องการกำหนดนโยบาย
                        </div>
                        
                        <!-- Leave Type Preview -->
                        <div id="leave-type-preview" class="leave-type-preview" style="display: none;">
                            <div class="preview-item">
                                <span class="preview-label">ประเภทการลา:</span>
                                <span class="preview-value" id="preview-name">-</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">สถานะการจ่ายเงิน:</span>
                                <span class="preview-value" id="preview-paid">-</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Days Policy Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-calendar-check"></i>นโยบายจำนวนวันลา
                    </div>
                    
                    <div class="mb-6">
                        <div class="checkbox-container">
                            <input type="checkbox" 
                                   id="is_unlimited" 
                                   name="is_unlimited"
                                   class="checkbox-input"
                                   onchange="toggleUnlimitedDays()"
                                   <?php echo is_policy_checked($leave_policy, 'is_unlimited') ? 'checked' : ''; ?>>
                            <label for="is_unlimited" class="checkbox-label">
                                <i class="fas fa-infinity text-blue-500 mr-2"></i>ลาได้ไม่จำกัดวัน
                            </label>
                        </div>
                        
                        <div id="days-section" class="toggle-section">
                            <label for="days_allowed_per_year" class="form-label">
                                <i class="fas fa-calendar-alt"></i>จำนวนวันลาที่อนุญาตต่อปี
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       name="days_allowed_per_year" 
                                       id="days_allowed_per_year" 
                                       class="form-input w-full input-with-addon" 
                                       value="<?php echo get_policy_value($leave_policy, 'days_allowed_per_year', '30'); ?>" 
                                       min="0" 
                                       max="365">
                                <span class="input-addon">วัน</span>
                            </div>
                            <div class="help-text">
                                กำหนดจำนวนวันลาสูงสุดที่พนักงานสามารถลาได้ต่อปี
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carry Over Policy Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-arrow-right"></i>นโยบายการโอนวันลา
                    </div>
                    
                    <div class="mb-6">
                        <div class="checkbox-container">
                            <input type="checkbox" 
                                   id="can_be_carried_over" 
                                   name="can_be_carried_over"
                                   class="checkbox-input"
                                   onchange="toggleCarryOver()"
                                   <?php echo is_policy_checked($leave_policy, 'can_be_carried_over') ? 'checked' : ''; ?>>
                            <label for="can_be_carried_over" class="checkbox-label">
                                <i class="fas fa-arrow-right text-green-500 mr-2"></i>อนุญาตให้โอนวันลาไปปีถัดไป
                            </label>
                        </div>
                        
                        <div id="carry-over-section" class="toggle-section">
                            <label for="max_carry_over_days" class="form-label">
                                <i class="fas fa-calendar-plus"></i>จำนวนวันลาสูงสุดที่โอนได้
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       name="max_carry_over_days" 
                                       id="max_carry_over_days" 
                                       class="form-input w-full input-with-addon" 
                                       value="<?php echo get_policy_value($leave_policy, 'max_carry_over_days', '0'); ?>" 
                                       min="0" 
                                       max="365">
                                <span class="input-addon">วัน</span>
                            </div>
                            <div class="help-text">
                                กำหนดจำนวนวันลาสูงสุดที่สามารถโอนไปยังปีถัดไป
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leave Rules Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-rules"></i>กฎและข้อกำหนดการลา
                    </div>
                    
                    <div class="form-grid">
                        <div class="form-grid-item">
                            <label for="min_notice_days" class="form-label">
                                <i class="fas fa-bell"></i>แจ้งล่วงหน้าขั้นต่ำ
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       name="min_notice_days" 
                                       id="min_notice_days" 
                                       class="form-input w-full input-with-addon" 
                                       value="<?php echo get_policy_value($leave_policy, 'min_notice_days', '1'); ?>" 
                                       min="0" 
                                       max="365">
                                <span class="input-addon">วัน</span>
                            </div>
                            <div class="help-text">
                                จำนวนวันขั้นต่ำที่ต้องแจ้งล่วงหน้าก่อนลา
                            </div>
                        </div>
                        
                        <div class="form-grid-item">
                            <label for="max_consecutive_days" class="form-label">
                                <i class="fas fa-calendar-week"></i>ลาติดต่อกันสูงสุด
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       name="max_consecutive_days" 
                                       id="max_consecutive_days" 
                                       class="form-input w-full input-with-addon" 
                                       value="<?php echo get_policy_value($leave_policy, 'max_consecutive_days', '30'); ?>" 
                                       min="0" 
                                       max="365">
                                <span class="input-addon">วัน</span>
                            </div>
                            <div class="help-text">
                                จำนวนวันสูงสุดที่สามารถลาติดต่อกันได้
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approval Policy Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-check-circle"></i>นโยบายการอนุมัติ
                    </div>
                    
                    <div class="mb-6">
                        <div class="checkbox-container">
                            <input type="checkbox" 
                                   id="requires_approval" 
                                   name="requires_approval"
                                   class="checkbox-input"
                                   <?php echo is_policy_checked($leave_policy, 'requires_approval') ? 'checked' : ''; ?>>
                            <label for="requires_approval" class="checkbox-label">
                                <i class="fas fa-check-circle text-red-500 mr-2"></i>ต้องได้รับการอนุมัติ
                            </label>
                        </div>
                        <div class="help-text">
                            หากเลือก การลาประเภทนี้จะต้องผ่านการอนุมัติจากผู้บังคับบัญชา
                        </div>
                    </div>
                </div>

                <!-- Additional Information Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-sticky-note"></i>รายละเอียดเพิ่มเติม
                    </div>
                    
                    <div class="mb-6">
                        <label for="description" class="form-label">
                            <i class="fas fa-align-left"></i>หมายเหตุและข้อกำหนดพิเศษ
                        </label>
                        <textarea name="description" 
                                  id="description" 
                                  rows="5" 
                                  class="form-input w-full"
                                  maxlength="1000"><?php echo get_policy_value($leave_policy, 'description'); ?></textarea>
                        <div class="help-text">
                            ระบุรายละเอียด หมายเหตุ หรือข้อกำหนดพิเศษของนโยบายการลา (สูงสุด 1000 ตัวอักษร)
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/leavepolicies" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $is_edit_mode ? 'บันทึกการเปลี่ยนแปลง' : 'เพิ่มนโยบายการลา'; ?>
                </button>
            </div>
        </form>
    </div>
</main>

<script>
// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    toggleUnlimitedDays();
    toggleCarryOver();
    updateLeaveTypePreview();
});

function toggleUnlimitedDays() {
    const unlimitedCheckbox = document.getElementById('is_unlimited');
    const daysSection = document.getElementById('days-section');
    const daysInput = document.getElementById('days_allowed_per_year');
    
    if (unlimitedCheckbox.checked) {
        daysSection.classList.add('hidden');
        daysInput.value = '0';
    } else {
        daysSection.classList.remove('hidden');
        if (daysInput.value === '0') {
            daysInput.value = '30'; // Default value
        }
    }
}

function toggleCarryOver() {
    const carryOverCheckbox = document.getElementById('can_be_carried_over');
    const carryOverSection = document.getElementById('carry-over-section');
    const maxCarryOverInput = document.getElementById('max_carry_over_days');
    
    if (carryOverCheckbox.checked) {
        carryOverSection.classList.remove('hidden');
        if (maxCarryOverInput.value === '0') {
            maxCarryOverInput.value = '5'; // Default value
        }
    } else {
        carryOverSection.classList.add('hidden');
        maxCarryOverInput.value = '0';
    }
}

function updateLeaveTypePreview() {
    const leaveTypeSelect = document.getElementById('leave_type_id');
    const preview = document.getElementById('leave-type-preview');
    const previewName = document.getElementById('preview-name');
    const previewPaid = document.getElementById('preview-paid');
    
    if (leaveTypeSelect.value) {
        const selectedOption = leaveTypeSelect.options[leaveTypeSelect.selectedIndex];
        const name = selectedOption.dataset.name;
        const isPaid = selectedOption.dataset.paid === '1';
        
        previewName.textContent = name;
        previewPaid.textContent = isPaid ? 'ลาได้เงิน' : 'ลาไม่ได้เงิน';
        preview.style.display = 'block';
    } else {
        preview.style.display = 'none';
    }
}

function validateForm() {
    const leaveTypeId = document.getElementById('leave_type_id').value;
    const isUnlimited = document.getElementById('is_unlimited').checked;
    const daysAllowed = document.getElementById('days_allowed_per_year').value;
    const canCarryOver = document.getElementById('can_be_carried_over').checked;
    const maxCarryOver = document.getElementById('max_carry_over_days').value;
    const minNotice = document.getElementById('min_notice_days').value;
    const maxConsecutive = document.getElementById('max_consecutive_days').value;
    
    // Validate leave type selection
    if (!leaveTypeId) {
        alert('กรุณาเลือกประเภทการลา');
        document.getElementById('leave_type_id').focus();
        return false;
    }
    
    // Validate days allowed if not unlimited
    if (!isUnlimited) {
        if (!daysAllowed || isNaN(daysAllowed) || parseInt(daysAllowed) < 0) {
            alert('กรุณากรอกจำนวนวันลาที่อนุญาตให้ถูกต้อง');
            document.getElementById('days_allowed_per_year').focus();
            return false;
        }
        
        if (parseInt(daysAllowed) > 365) {
            alert('จำนวนวันลาต่อปีต้องไม่เกิน 365 วัน');
            document.getElementById('days_allowed_per_year').focus();
            return false;
        }
    }
    
    // Validate carry over days
    if (canCarryOver) {
        if (!maxCarryOver || isNaN(maxCarryOver) || parseInt(maxCarryOver) < 0) {
            alert('กรุณากรอกจำนวนวันลาสูงสุดที่โอนได้ให้ถูกต้อง');
            document.getElementById('max_carry_over_days').focus();
            return false;
        }
        
        if (parseInt(maxCarryOver) > 365) {
            alert('จำนวนวันลาสูงสุดที่โอนได้ต้องไม่เกิน 365 วัน');
            document.getElementById('max_carry_over_days').focus();
            return false;
        }
        
        // Check if carry over days exceed allowed days (if not unlimited)
        if (!isUnlimited && parseInt(maxCarryOver) > parseInt(daysAllowed)) {
            alert('จำนวนวันลาที่โอนได้ต้องไม่เกินจำนวนวันลาที่อนุญาตต่อปี');
            document.getElementById('max_carry_over_days').focus();
            return false;
        }
    }
    
    // Validate notice days
    if (!minNotice || isNaN(minNotice) || parseInt(minNotice) < 0) {
        alert('กรุณากรอกจำนวนวันแจ้งล่วงหน้าให้ถูกต้อง');
        document.getElementById('min_notice_days').focus();
        return false;
    }
    
    if (parseInt(minNotice) > 365) {
        alert('จำนวนวันแจ้งล่วงหน้าต้องไม่เกิน 365 วัน');
        document.getElementById('min_notice_days').focus();
        return false;
    }
    
    // Validate consecutive days
    if (!maxConsecutive || isNaN(maxConsecutive) || parseInt(maxConsecutive) < 0) {
        alert('กรุณากรอกจำนวนวันลาติดต่อกันสูงสุดให้ถูกต้อง');
        document.getElementById('max_consecutive_days').focus();
        return false;
    }
    
    if (parseInt(maxConsecutive) > 365) {
        alert('จำนวนวันลาติดต่อกันสูงสุดต้องไม่เกิน 365 วัน');
        document.getElementById('max_consecutive_days').focus();
        return false;
    }
    
    // Check if consecutive days exceed allowed days (if not unlimited)
    if (!isUnlimited && parseInt(maxConsecutive) > parseInt(daysAllowed)) {
        alert('จำนวนวันลาติดต่อกันสูงสุดต้องไม่เกินจำนวนวันลาที่อนุญาตต่อปี');
        document.getElementById('max_consecutive_days').focus();
        return false;
    }
    
    return true;
}

// Character counter for description field
document.getElementById('description').addEventListener('input', function() {
    const maxLength = 1000;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    // You can add a character counter display here if needed
    if (remaining < 50) {
        this.style.borderColor = remaining < 0 ? '#ef4444' : '#f59e0b';
    } else {
        this.style.borderColor = '#e2e8f0';
    }
});

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-success, .alert-error');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
});
</script>

<?php 
// Clear form data from session after displaying
if (isset($_SESSION['form_data'])) {
    unset($_SESSION['form_data']);
}

require_once __DIR__ . '/../../layouts/footer.php'; 
?>