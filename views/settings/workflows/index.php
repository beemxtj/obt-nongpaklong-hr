<?php 
require_once __DIR__ . '/../../layouts/header.php'; 
?>

<style>
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background-color: #f1f5f9;
        color: #374151;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 8px 16px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
        background-color: #fff;
        color: #374151;
        text-decoration: none;
    }

    .btn-export {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 10px 20px;
        font-weight: 600;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-export:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
        color: white;
        text-decoration: none;
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
        flex-wrap: wrap;
        gap: 1rem;
    }

    .header-actions {
        display: flex;
        gap: 12px;
        flex-wrap: wrap;
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
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        margin: 0 2px;
    }
    .table-action-link:hover {
        color: #3730a3;
        background: #f0f9ff;
        text-decoration: none;
    }
    
    .table-action-delete {
        color: #dc2626;
        font-weight: 600;
        transition: color 0.3s;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        margin: 0 2px;
    }
    .table-action-delete:hover {
        color: #991b1b;
        background: #fef2f2;
        text-decoration: none;
    }

    .workflow-card {
        transition: all 0.3s ease;
    }

    .workflow-card:hover {
        transform: translateY(-2px);
    }

    .workflow-name {
        font-weight: 700;
        color: #1f2937;
        font-size: 1rem;
        margin-bottom: 4px;
        display: flex;
        align-items: center;
    }

    .workflow-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .workflow-details {
        flex: 1;
    }

    .workflow-info {
        display: flex;
        align-items: center;
    }

    .workflow-description {
        font-size: 12px;
        color: #6b7280;
        margin-top: 2px;
    }

    .steps-display {
        background: #f8fafc;
        border-radius: 12px;
        padding: 12px;
        border: 1px solid #e2e8f0;
    }

    .step-item {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        margin: 2px;
        display: inline-block;
    }

    .step-arrow {
        color: #9ca3af;
        margin: 0 4px;
        font-size: 12px;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .stats-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
    }

    .stat-card-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .stat-card-value {
        font-size: 2rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .stat-card-label {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
    }

    .workflow-status {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 8px;
    }

    .status-badge {
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-active {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .status-inactive {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #374151;
    }

    .steps-count {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
        padding: 2px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }
</style>

<?php require_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-sitemap mr-3" style="-webkit-text-fill-color: #667eea;"></i>
                <?php echo htmlspecialchars($page_title ?? 'จัดการสายการอนุมัติ'); ?>
            </h1>
            <p class="text-gray-500 mt-2">สร้างและจัดการลำดับขั้นการอนุมัติใบลาและเอกสารต่างๆ</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo BASE_URL; ?>/workflow/export" class="btn-export" title="ส่งออกข้อมูล">
                <i class="fas fa-download mr-2"></i>
                ส่งออก
            </a>
            <a href="<?php echo BASE_URL; ?>/workflow/create" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                สร้างสายการอนุมัติใหม่
            </a>
        </div>
    </div>

    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert-success" role="alert">
            <p class="flex items-center"><i class="fas fa-check-circle mr-3"></i><?php echo $_SESSION['success_message']; ?></p>
            <button type="button" class="text-green-800 hover:text-green-900 text-xl font-bold bg-transparent border-none cursor-pointer" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert-error" role="alert">
            <p class="flex items-center"><i class="fas fa-exclamation-circle mr-3"></i><?php echo $_SESSION['error_message']; ?></p>
            <button type="button" class="text-red-800 hover:text-red-900 text-xl font-bold bg-transparent border-none cursor-pointer" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
                <i class="fas fa-sitemap text-blue-600"></i>
            </div>
            <div class="stat-card-value">
                <?php echo count($workflows ?? []); ?>
            </div>
            <div class="stat-card-label">สายการอนุมัติทั้งหมด</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                <i class="fas fa-check-circle text-green-600"></i>
            </div>
            <div class="stat-card-value">
                <?php 
                $active_count = 0;
                if (!empty($workflows)) {
                    foreach ($workflows as $wf) {
                        if (!empty($wf['steps_summary']) && $wf['steps_summary'] !== 'ยังไม่มีขั้นตอน') {
                            $active_count++;
                        }
                    }
                }
                echo $active_count;
                ?>
            </div>
            <div class="stat-card-label">พร้อมใช้งาน</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                <i class="fas fa-clock text-yellow-600"></i>
            </div>
            <div class="stat-card-value">
                <?php 
                $total_steps = 0;
                if (!empty($workflows)) {
                    foreach ($workflows as $wf) {
                        $steps = explode(' → ', $wf['steps_summary'] ?? '');
                        if ($wf['steps_summary'] !== 'ยังไม่มีขั้นตอน') {
                            $total_steps += count($steps);
                        }
                    }
                }
                echo $total_steps;
                ?>
            </div>
            <div class="stat-card-label">ขั้นตอนทั้งหมด</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);">
                <i class="fas fa-users text-indigo-600"></i>
            </div>
            <div class="stat-card-value">
                <?php 
                $avg_steps = 0;
                if (!empty($workflows) && $active_count > 0) {
                    $avg_steps = round($total_steps / $active_count, 1);
                }
                echo $avg_steps;
                ?>
            </div>
            <div class="stat-card-label">ขั้นตอนเฉลี่ย</div>
        </div>
    </div>

    <!-- Workflows Table -->
    <div class="table-container">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ชื่อสายการอนุมัติ
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ลำดับขั้นการอนุมัติ
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            สถานะ
                        </th>
                        <th scope="col" class="relative px-6 py-3">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (!empty($workflows)): ?>
                        <?php foreach ($workflows as $workflow): ?>
                            <tr class="workflow-card hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="workflow-info">
                                        <div class="workflow-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="fas fa-sitemap text-white text-sm"></i>
                                        </div>
                                        <div class="workflow-details">
                                            <div class="workflow-name">
                                                <?php echo htmlspecialchars($workflow['name']); ?>
                                            </div>
                                            <div class="workflow-description">
                                                สายการอนุมัติสำหรับเอกสารและใบลา
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="steps-display">
                                        <?php if (!empty($workflow['steps_summary']) && $workflow['steps_summary'] !== 'ยังไม่มีขั้นตอน'): ?>
                                            <?php 
                                            $steps = explode(' → ', $workflow['steps_summary']);
                                            foreach ($steps as $index => $step): 
                                            ?>
                                                <span class="step-item">
                                                    <?php echo ($index + 1) . '. ' . htmlspecialchars(trim($step)); ?>
                                                </span>
                                                <?php if ($index < count($steps) - 1): ?>
                                                    <span class="step-arrow">→</span>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <span class="text-gray-400 italic">ยังไม่มีขั้นตอนการอนุมัติ</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="workflow-status">
                                        <?php if (!empty($workflow['steps_summary']) && $workflow['steps_summary'] !== 'ยังไม่มีขั้นตอน'): ?>
                                            <span class="status-badge status-active">
                                                <i class="fas fa-check-circle mr-1"></i>พร้อมใช้งาน
                                            </span>
                                            <span class="steps-count">
                                                <?php echo count(explode(' → ', $workflow['steps_summary'])); ?> ขั้นตอน
                                            </span>
                                        <?php else: ?>
                                            <span class="status-badge status-inactive">
                                                <i class="fas fa-exclamation-circle mr-1"></i>ไม่พร้อม
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-center items-center space-x-2">
                                        <a href="<?php echo BASE_URL; ?>/workflow/edit/<?php echo $workflow['id']; ?>" 
                                           class="table-action-link" 
                                           title="แก้ไขสายการอนุมัติ">
                                            <i class="fas fa-edit mr-1"></i>
                                            แก้ไข
                                        </a>
                                        <form action="<?php echo BASE_URL; ?>/workflow/destroy/<?php echo $workflow['id']; ?>" 
                                              method="POST" 
                                              class="inline-block" 
                                              onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบสายการอนุมัติ &quot;<?php echo htmlspecialchars(addslashes($workflow['name'])); ?>&quot;?\n\nการลบจะไม่สามารถกู้คืนได้');">
                                            <button type="submit" 
                                                    class="table-action-delete bg-transparent border-none cursor-pointer" 
                                                    title="ลบสายการอนุมัติ">
                                                <i class="fas fa-trash-alt mr-1"></i>
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="fas fa-sitemap"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">ยังไม่มีสายการอนุมัติในระบบ</h3>
                                <p class="text-gray-500 mb-4">เริ่มต้นด้วยการสร้างสายการอนุมัติแรกของคุณ</p>
                                <a href="<?php echo BASE_URL; ?>/workflow/create" class="btn-primary">
                                    <i class="fas fa-plus mr-2"></i>สร้างสายการอนุมัติใหม่
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
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

// Add hover effects for step items
document.addEventListener('DOMContentLoaded', function() {
    const stepItems = document.querySelectorAll('.step-item');
    stepItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>