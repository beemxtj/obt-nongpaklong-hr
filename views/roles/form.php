<?php 
if (!defined('BASE_URL')) { 
    require_once __DIR__ . '/../../config/app.php'; 
}

// สำหรับหน้าแก้ไข
$current_permissions = isset($role->permissions) ? json_decode($role->permissions, true) : [];
if (!is_array($current_permissions)) {
    $current_permissions = [];
}

// Determine if it's edit mode
$is_edit_mode = isset($role) && !empty($role->id);
$page_title = $is_edit_mode ? 'แก้ไขบทบาท' : 'เพิ่มบทบาทใหม่';

require_once __DIR__ . '/../layouts/header.php'; 
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

    .permissions-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
        margin-top: 1rem;
    }

    .permission-card {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
    }

    .permission-card:hover {
        border-color: #cbd5e1;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .permission-card.checked {
        border-color: #667eea;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.15);
    }

    .permission-checkbox {
        position: absolute;
        top: 12px;
        right: 12px;
        width: 20px;
        height: 20px;
        border-radius: 6px;
        border: 2px solid #e2e8f0;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .permission-checkbox:checked {
        background-color: #667eea;
        border-color: #667eea;
    }

    .permission-checkbox:focus {
        outline: none;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
    }

    .permission-title {
        font-weight: 600;
        color: #374151;
        margin-bottom: 4px;
        margin-right: 30px;
    }

    .permission-description {
        font-size: 0.875rem;
        color: #6b7280;
        line-height: 1.4;
    }

    .select-all-section {
        background: #f8fafc;
        border: 2px dashed #d1d5db;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        text-align: center;
    }

    .select-all-btn {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 0 4px;
    }

    .select-all-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }

    .deselect-all-btn {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border: none;
        border-radius: 8px;
        padding: 8px 16px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 0 4px;
    }

    .deselect-all-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }

    .permission-counter {
        background: #667eea;
        color: white;
        border-radius: 20px;
        padding: 4px 12px;
        font-size: 12px;
        font-weight: 600;
        margin-left: 8px;
    }
</style>

<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-user-shield mr-3" style="-webkit-text-fill-color: #667eea;"></i>
                <?php echo htmlspecialchars($page_title); ?>
            </h1>
            <p class="text-gray-500 mt-2">
                <?php echo $is_edit_mode ? 'แก้ไขข้อมูลบทบาทและกำหนดสิทธิ์' : 'กรอกข้อมูลบทบาทใหม่และกำหนดสิทธิ์'; ?>
            </p>
        </div>
        <a href="<?php echo BASE_URL; ?>/role" class="btn-secondary hidden sm:flex items-center">
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
        <form action="<?php echo isset($role->id) ? BASE_URL . '/role/update' : BASE_URL . '/role/store'; ?>" method="POST" onsubmit="return validateForm()">
            <?php if (isset($role->id)): ?>
                <input type="hidden" name="id" value="<?php echo $role->id; ?>">
            <?php endif; ?>

            <div class="form-content">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i>ข้อมูลพื้นฐาน
                    </div>
                    
                    <div class="mb-6">
                        <label for="role_name" class="form-label">
                            <i class="fas fa-tag"></i>ชื่อบทบาท<span class="required">*</span>
                        </label>
                        <input type="text" 
                               name="role_name" 
                               id="role_name" 
                               class="form-input w-full md:w-2/3" 
                               value="<?php echo htmlspecialchars($role->role_name ?? ''); ?>" 
                               maxlength="100"
                               required>
                        <div class="help-text">
                            ระบุชื่อบทบาท เช่น ผู้จัดการ, เจ้าหน้าที่ HR, พนักงานทั่วไป เป็นต้น (สูงสุด 100 ตัวอักษร)
                        </div>
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-key"></i>กำหนดสิทธิ์การใช้งาน
                        <span class="permission-counter" id="permission-counter">0 รายการ</span>
                    </div>
                    
                    <div class="help-text mb-4">
                        เลือกสิทธิ์ที่ต้องการให้กับบทบาทนี้ สามารถเลือกได้หลายรายการ
                    </div>

                    <!-- Select All/Deselect All -->
                    <div class="select-all-section">
                        <p class="text-gray-600 mb-3">จัดการการเลือกสิทธิ์:</p>
                        <button type="button" class="select-all-btn" onclick="selectAllPermissions()">
                            <i class="fas fa-check-double mr-1"></i> เลือกทั้งหมด
                        </button>
                        <button type="button" class="deselect-all-btn" onclick="deselectAllPermissions()">
                            <i class="fas fa-times mr-1"></i> ยกเลิกทั้งหมด
                        </button>
                    </div>

                    <!-- Permissions Grid -->
                    <div class="permissions-grid">
                        <?php if (isset($permissions_list) && is_array($permissions_list)): ?>
                            <?php foreach ($permissions_list as $key => $label): ?>
                                <div class="permission-card" onclick="togglePermission('<?php echo $key; ?>')">
                                    <input type="checkbox" 
                                           id="perm_<?php echo $key; ?>" 
                                           name="permissions[]" 
                                           value="<?php echo $key; ?>" 
                                           class="permission-checkbox"
                                           <?php echo in_array($key, $current_permissions) ? 'checked' : ''; ?>
                                           onchange="updatePermissionCard(this)">
                                    
                                    <div class="permission-title">
                                        <?php echo htmlspecialchars($label); ?>
                                    </div>
                                    <div class="permission-description">
                                        <?php
                                        // Add descriptions for permissions
                                        $descriptions = [
                                            'view_dashboard' => 'เข้าถึงหน้าแดชบอร์ดและดูสถิติพื้นฐาน',
                                            'manage_employees' => 'จัดการข้อมูลพนักงาน เพิ่ม แก้ไข ลบข้อมูล',
                                            'view_attendance' => 'ดูข้อมูลการลงเวลาของพนักงาน',
                                            'manage_attendance' => 'จัดการและแก้ไขข้อมูลการลงเวลา',
                                            'view_leave' => 'ดูข้อมูลการลาของพนักงาน',
                                            'manage_leave' => 'อนุมัติและจัดการคำขอลาของพนักงาน',
                                            'view_payroll' => 'ดูข้อมูลเงินเดือนและผลตอบแทน',
                                            'manage_payroll' => 'จัดการข้อมูลเงินเดือนและคำนวณผลตอบแทน',
                                            'view_reports' => 'ดูรายงานต่างๆ ในระบบ',
                                            'manage_reports' => 'สร้างและจัดการรายงานขั้นสูง',
                                            'manage_settings' => 'จัดการการตั้งค่าระบบและข้อมูลหลัก',
                                            'super_admin' => 'สิทธิ์ผู้ดูแลระบบสูงสุด เข้าถึงได้ทุกฟังก์ชัน'
                                        ];
                                        echo htmlspecialchars($descriptions[$key] ?? 'สิทธิ์ในการใช้งานฟีเจอร์นี้');
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="col-span-full text-center text-gray-500 py-8">
                                <i class="fas fa-exclamation-triangle text-4xl mb-3"></i>
                                <p>ไม่พบรายการสิทธิ์ในระบบ</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/role" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $is_edit_mode ? 'บันทึกการเปลี่ยนแปลง' : 'เพิ่มบทบาท'; ?>
                </button>
            </div>
        </form>
    </div>
</main>

<script>
// Initialize form
document.addEventListener('DOMContentLoaded', function() {
    updatePermissionCounter();
    initializePermissionCards();
});

function togglePermission(key) {
    const checkbox = document.getElementById('perm_' + key);
    checkbox.checked = !checkbox.checked;
    updatePermissionCard(checkbox);
    updatePermissionCounter();
}

function updatePermissionCard(checkbox) {
    const card = checkbox.closest('.permission-card');
    if (checkbox.checked) {
        card.classList.add('checked');
    } else {
        card.classList.remove('checked');
    }
}

function initializePermissionCards() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        updatePermissionCard(checkbox);
        checkbox.addEventListener('change', () => {
            updatePermissionCounter();
        });
    });
}

function updatePermissionCounter() {
    const checkedBoxes = document.querySelectorAll('.permission-checkbox:checked');
    const counter = document.getElementById('permission-counter');
    counter.textContent = checkedBoxes.length + ' รายการ';
}

function selectAllPermissions() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = true;
        updatePermissionCard(checkbox);
    });
    updatePermissionCounter();
}

function deselectAllPermissions() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = false;
        updatePermissionCard(checkbox);
    });
    updatePermissionCounter();
}

function validateForm() {
    const roleName = document.getElementById('role_name').value.trim();
    
    // Validate role name
    if (!roleName) {
        alert('กรุณากรอกชื่อบทบาท');
        document.getElementById('role_name').focus();
        return false;
    }
    
    if (roleName.length > 100) {
        alert('ชื่อบทบาทต้องไม่เกิน 100 ตัวอักษร');
        document.getElementById('role_name').focus();
        return false;
    }
    
    // Check if at least one permission is selected (optional validation)
    const checkedPermissions = document.querySelectorAll('.permission-checkbox:checked');
    if (checkedPermissions.length === 0) {
        const confirmResult = confirm('คุณยังไม่ได้เลือกสิทธิ์ใดๆ บทบาทนี้จะไม่สามารถเข้าถึงฟีเจอร์ใดได้\n\nต้องการดำเนินการต่อหรือไม่?');
        if (!confirmResult) {
            return false;
        }
    }
    
    return true;
}

// Character counter for role name field
document.getElementById('role_name').addEventListener('input', function() {
    const maxLength = 100;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    if (remaining < 10) {
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

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>