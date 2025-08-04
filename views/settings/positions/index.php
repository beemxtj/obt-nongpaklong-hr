<?php
// views/settings/positions/index.php

if (!defined('BASE_URL')) {
    define('BASE_URL', 'http://localhost/obt-nongpaklong-hr');
}

require_once __DIR__ . '/../../layouts/header.php';
?>

<style>
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        border: none;
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
        padding: 10px 20px;
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

    .floating-header {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    @media (min-width: 1024px) {
        .floating-header {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
    }
    
    .search-input {
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 10px 16px 10px 40px;
        transition: all 0.3s ease;
    }
    .search-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
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
        background-color: transparent;
        border: none;
        cursor: pointer;
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
                <i class="fas fa-briefcase mr-3" style="-webkit-text-fill-color: #667eea;"></i>
                <?php echo htmlspecialchars($page_title ?? 'จัดการข้อมูลตำแหน่ง'); ?>
            </h1>
            <p class="text-gray-500 mt-2">รายการตำแหน่งทั้งหมดในระบบ</p>
        </div>
        <div class="flex flex-col sm:flex-row gap-3 w-full lg:w-auto">
            <form action="<?php echo BASE_URL; ?>/positions/search" method="GET" class="flex-1 lg:flex-initial">
                <div class="relative">
                    <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" name="search" value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>" placeholder="ค้นหาตำแหน่ง..." class="search-input w-full">
                </div>
            </form>
            <a href="<?php echo BASE_URL; ?>/positions/create" class="btn-primary flex items-center justify-center whitespace-nowrap">
                <i class="fas fa-plus mr-2"></i>
                เพิ่มตำแหน่งใหม่
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert-success" role="alert">
            <p class="flex items-center"><i class="fas fa-check-circle mr-3"></i><?php echo $_SESSION['success_message']; ?></p>
            <button type="button" class="bg-transparent border-none text-lg" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert-error" role="alert">
            <p class="flex items-center"><i class="fas fa-exclamation-circle mr-3"></i><?php echo $_SESSION['error_message']; ?></p>
            <button type="button" class="bg-transparent border-none text-lg" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="table-container">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">ชื่อตำแหน่ง</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden md:table-cell">รายละเอียด</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider hidden lg:table-cell">วันที่สร้าง</th>
                        <th class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($num) && $num > 0 && isset($stmt)): ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="font-medium text-gray-900"><?php echo htmlspecialchars($row['name_th']); ?></div>
                                    <div class="text-sm text-gray-500"><?php echo htmlspecialchars($row['name_en'] ?? ''); ?></div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500 hidden md:table-cell">
                                    <div class="max-w-xs truncate" title="<?php echo htmlspecialchars($row['description'] ?? ''); ?>">
                                        <?php echo htmlspecialchars($row['description'] ?? '-'); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden lg:table-cell">
                                    <?php echo $row['created_at'] ? date('d/m/Y', strtotime($row['created_at'])) : '-'; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
                                    <a href="<?php echo BASE_URL; ?>/positions/edit/<?php echo $row['id']; ?>" class="table-action-link"><i class="fas fa-pencil-alt mr-1"></i>แก้ไข</a>
                                    <form action="<?php echo BASE_URL; ?>/positions/destroy/<?php echo $row['id']; ?>" method="POST" class="inline-block" onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบตำแหน่งนี้?');">
                                        <button type="submit" class="table-action-delete"><i class="fas fa-trash-alt mr-1"></i>ลบ</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-16">
                                <i class="fas fa-folder-open text-5xl text-gray-300 mb-4"></i>
                                <?php if (isset($_GET['search']) && !empty($_GET['search'])): ?>
                                    <h3 class="text-lg font-medium text-gray-800">ไม่พบผลลัพธ์</h3>
                                    <p class="text-gray-500 mt-1">ไม่พบตำแหน่งที่ตรงกับ "<?php echo htmlspecialchars($_GET['search']); ?>"</p>
                                    <a href="<?php echo BASE_URL; ?>/positions" class="text-indigo-600 hover:underline mt-4 inline-block">ดูตำแหน่งทั้งหมด</a>
                                <?php else: ?>
                                    <h3 class="text-lg font-medium text-gray-800">ยังไม่มีข้อมูลตำแหน่ง</h3>
                                    <p class="text-gray-500 mt-1">เริ่มต้นด้วยการเพิ่มตำแหน่งใหม่</p>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>