<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/app.php';
}
require_once __DIR__ . '/../layouts/header.php';
?>

<style>
    .form-input {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 12px 16px;
        background-color: #f8fafc;
        width: 100%;
    }
    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
        background-color: #fff;
    }
    .form-label {
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }
    .form-label i { margin-right: 8px; color: #667eea; }
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
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4); }
    .btn-secondary {
        background-color: #f1f5f9;
        color: #374151;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
    }
    .btn-secondary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05); }
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        font-weight: 700;
    }
    .form-container { background: white; border-radius: 24px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.07); overflow: hidden; }
    .form-content { padding: 2rem 2.5rem; }
    .form-footer { background: #f8fafc; padding: 1.5rem 2.5rem; border-top: 1px solid #e2e8f0; display: flex; justify-content: flex-end; }
    .floating-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
</style>

<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-paper-plane mr-3" style="-webkit-text-fill-color: #667eea;"></i><?php echo htmlspecialchars($page_title ?? 'ยื่นใบลา'); ?>
            </h1>
            <p class="text-gray-500 mt-2">กรุณากรอกข้อมูลการลาให้ครบถ้วนและถูกต้อง</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/leave/history" class="btn-secondary hidden sm:flex items-center"><i class="fas fa-arrow-left mr-2"></i>กลับ</a>
    </div>

    <div class="form-container">
        <form action="<?php echo BASE_URL; ?>/leave/store" method="POST" enctype="multipart/form-data">
            <div class="form-content">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="md:col-span-2">
                        <label for="leave_type_id" class="form-label"><i class="fas fa-list-alt"></i>ประเภทการลา <span class="text-red-500 ml-1">*</span></label>
                        <select id="leave_type_id" name="leave_type_id" class="form-input" required>
                            <option value="">-- กรุณาเลือกประเภทการลา --</option>
                            <?php while ($row = $leave_types->fetch(PDO::FETCH_ASSOC)) : ?>
                                <option value="<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label for="start_date" class="form-label"><i class="fas fa-calendar-alt"></i>วันที่เริ่มลา <span class="text-red-500 ml-1">*</span></label>
                        <input type="date" name="start_date" id="start_date" class="form-input" required>
                    </div>

                    <div>
                        <label for="end_date" class="form-label"><i class="fas fa-calendar-check"></i>วันที่สิ้นสุดการลา <span class="text-red-500 ml-1">*</span></label>
                        <input type="date" name="end_date" id="end_date" class="form-input" required>
                    </div>

                    <div class="md:col-span-2">
                        <label for="reason" class="form-label"><i class="fas fa-comment-alt"></i>เหตุผลการลา <span class="text-red-500 ml-1">*</span></label>
                        <textarea id="reason" name="reason" rows="4" class="form-input" required></textarea>
                    </div>

                    <div class="md:col-span-2">
                        <label for="attachment" class="form-label"><i class="fas fa-paperclip"></i>ไฟล์แนบ (ถ้ามี)</label>
                        <input type="file" name="attachment" id="attachment" class="form-input py-3">
                    </div>
                </div>
            </div>
            
            <div class="form-footer">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-paper-plane mr-2"></i>ส่งคำขอ
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>