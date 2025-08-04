<?php
// views/settings/positions/form.php

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/obt-nongpaklong-hr');
}

$is_edit = isset($position->id) && !empty($position->id);
$page_title = $is_edit ? 'แก้ไขตำแหน่ง' : 'เพิ่มตำแหน่งใหม่';
$form_action = BASE_URL . '/positions/' . ($is_edit ? 'update' : 'store');

function get_pos_value($position, $field) {
    return isset($position) ? htmlspecialchars($position->$field ?? '') : '';
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
    
    .alert-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-left: 4px solid #ef4444;
        color: #991b1b;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>

<?php require_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <?php echo htmlspecialchars($page_title); ?>
            </h1>
            <p class="text-gray-500 mt-2">กรอกข้อมูลตำแหน่งเพื่อเพิ่มหรือแก้ไขในระบบ</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/positions" class="btn-secondary hidden sm:flex items-center">
            <i class="fas fa-arrow-left mr-2"></i>
            กลับ
        </a>
    </div>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert-error" role="alert">
            <p class="flex items-center"><i class="fas fa-exclamation-circle mr-3"></i><?php echo $_SESSION['error_message']; ?></p>
            <button type="button" class="bg-transparent border-none text-lg" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="form-container">
        <form action="<?php echo $form_action; ?>" method="POST" novalidate>
            <?php if ($is_edit): ?>
                <input type="hidden" name="id" value="<?php echo get_pos_value($position, 'id'); ?>">
            <?php endif; ?>

            <div class="form-content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-6">
                    <div>
                        <label for="name_th" class="form-label"><i class="fas fa-tag"></i>ชื่อตำแหน่ง (ไทย) <span class="text-red-500 ml-1">*</span></label>
                        <input type="text" id="name_th" name="name_th" value="<?php echo get_pos_value($position, 'name_th'); ?>" class="form-input w-full" required>
                    </div>
                    <div>
                        <label for="name_en" class="form-label"><i class="fas fa-language"></i>ชื่อตำแหน่ง (อังกฤษ)</label>
                        <input type="text" id="name_en" name="name_en" value="<?php echo get_pos_value($position, 'name_en'); ?>" class="form-input w-full">
                    </div>
                </div>
                <div>
                    <label for="description" class="form-label"><i class="fas fa-align-left"></i>รายละเอียด</label>
                    <textarea id="description" name="description" rows="4" class="form-input w-full"><?php echo get_pos_value($position, 'description'); ?></textarea>
                </div>
            </div>
            
            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/positions" class="btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $is_edit ? 'บันทึกการแก้ไข' : 'เพิ่มตำแหน่ง'; ?>
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>