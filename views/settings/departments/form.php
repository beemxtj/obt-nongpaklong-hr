<?php
// views/settings/departments/form.php

// Ensure BASE_URL is defined
if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/obt-nongpaklong-hr');
}

// Determine if it's edit mode and set the form action URL
$is_edit_mode = isset($department) && !empty($department->id);
$form_action_url = BASE_URL . '/departments/' . ($is_edit_mode ? 'update' : 'store');

// Set the page title
$page_title = $is_edit_mode ? 'แก้ไขข้อมูลแผนก' : 'เพิ่มแผนกใหม่';

// Helper function to safely get department data
function get_dept_value($department, $field) {
    return isset($department) ? htmlspecialchars($department->$field ?? '') : '';
}

require_once __DIR__ . '/../../layouts/header.php'; // Adjust path as needed
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
</style>

<?php require_once __DIR__ . '/../../layouts/sidebar.php'; // Adjust path as needed ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <?php echo htmlspecialchars($page_title); ?>
            </h1>
            <p class="text-gray-500 mt-2">จัดการรายละเอียดข้อมูลแผนกในระบบ</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/departments" class="btn-secondary hidden sm:flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            กลับ
        </a>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert-success" role="alert">
            <p class="flex items-center"><i class="fas fa-check-circle mr-3"></i><?php echo $_SESSION['success_message']; ?></p>
            <button type="button" class="bg-transparent border-none text-lg font-bold" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert-error" role="alert">
            <p class="flex items-center"><i class="fas fa-exclamation-circle mr-3"></i><?php echo $_SESSION['error_message']; ?></p>
            <button type="button" class="bg-transparent border-none text-lg font-bold" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="form-container">
        <form action="<?php echo $form_action_url; ?>" method="POST">
            <?php if ($is_edit_mode): ?>
                <input type="hidden" name="id" value="<?php echo get_dept_value($department, 'id'); ?>">
            <?php endif; ?>

            <div class="form-content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                    <div>
                        <label for="name_th" class="form-label">
                            <i class="fas fa-tag"></i>ชื่อแผนก (ไทย) <span class="text-red-500 ml-1">*</span>
                        </label>
                        <input type="text" name="name_th" id="name_th" class="form-input w-full" value="<?php echo get_dept_value($department, 'name_th'); ?>" required>
                    </div>
                    <div>
                        <label for="name_en" class="form-label">
                            <i class="fas fa-tag"></i>ชื่อแผนก (อังกฤษ)
                        </label>
                        <input type="text" name="name_en" id="name_en" class="form-input w-full" value="<?php echo get_dept_value($department, 'name_en'); ?>">
                    </div>
                </div>

                <div>
                    <label for="description" class="form-label">
                        <i class="fas fa-align-left"></i>รายละเอียด
                    </label>
                    <textarea name="description" id="description" rows="5" class="form-input w-full"><?php echo get_dept_value($department, 'description'); ?></textarea>
                </div>
            </div>

            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/departments" class="btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $is_edit_mode ? 'บันทึกการเปลี่ยนแปลง' : 'เพิ่มแผนก'; ?>
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; // Adjust path as needed ?>