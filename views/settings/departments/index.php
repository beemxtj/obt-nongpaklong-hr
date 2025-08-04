<?php
// views/settings/departments/index.php

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/obt-nongpaklong-hr');
}

require_once __DIR__ . '/../../layouts/header.php';
// NOTE: Ensure the new UI styles are loaded here, either via a shared CSS file or a <style> block.
?>

<style>
    /* Add styles needed for this page if not globally available */
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        border: none;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }
    
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
    }

    .floating-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border-left: 4px solid #10b981;
        color: #065f46;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .alert-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-left: 4px solid #ef4444;
        color: #991b1b;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .table-container {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.07);
        overflow: hidden;
    }
    
    .table-action-link {
        color: #4f46e5;
        font-weight: 600;
        transition: color 0.3s;
    }
    .table-action-link:hover {
        color: #3730a3;
    }
    
    .table-action-delete {
        color: #dc2626;
        font-weight: 600;
        transition: color 0.3s;
    }
    .table-action-delete:hover {
        color: #991b1b;
    }
</style>

<?php require_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-building mr-3" style="-webkit-text-fill-color: #667eea;"></i>
                <?php echo htmlspecialchars($page_title ?? 'จัดการข้อมูลแผนก'); ?>
            </h1>
            <p class="text-gray-500 mt-2">รายการแผนกทั้งหมดในระบบ</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/departments/create" class="btn-primary">
            <i class="fas fa-plus mr-2"></i>
            เพิ่มแผนกใหม่
        </a>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert-success" role="alert">
            <p class="flex items-center"><i class="fas fa-check-circle mr-3"></i><?php echo $_SESSION['success_message']; ?></p>
            <button type="button" class="text-green-800 hover:text-green-900" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert-error" role="alert">
            <p class="flex items-center"><i class="fas fa-exclamation-circle mr-3"></i><?php echo $_SESSION['error_message']; ?></p>
            <button type="button" class="text-red-800 hover:text-red-900" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="table-container">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ชื่อแผนก (ไทย)
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ชื่อแผนก (อังกฤษ)
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            รายละเอียด
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($num) && $num > 0 && isset($stmt) && $stmt !== null): ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($row['name_th']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php echo htmlspecialchars($row['name_en']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 max-w-xs truncate">
                                    <?php echo htmlspecialchars($row['description']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
                                    <a href="<?php echo BASE_URL; ?>/departments/edit/<?php echo htmlspecialchars($row['id']); ?>" class="table-action-link">
                                        <i class="fas fa-pencil-alt mr-1"></i>แก้ไข
                                    </a>
                                    <form action="<?php echo BASE_URL; ?>/departments/destroy/<?php echo htmlspecialchars($row['id']); ?>" method="POST" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบแผนก <?php echo htmlspecialchars(addslashes($row['name_th'])); ?>?');">
                                        <button type="submit" class="table-action-delete bg-transparent border-none p-0 cursor-pointer">
                                            <i class="fas fa-trash-alt mr-1"></i>ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-12">
                                <i class="fas fa-folder-open text-4xl text-gray-300 mb-3"></i>
                                <p class="text-center text-gray-500">ยังไม่มีข้อมูลแผนกในขณะนี้</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>