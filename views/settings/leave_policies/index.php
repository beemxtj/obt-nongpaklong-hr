<?php
// views/settings/leave_policies/index.php

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

    .policy-badge {
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 2px;
        display: inline-block;
    }

    .badge-unlimited {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }

    .badge-limited {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
    }

    .badge-carry-over {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .badge-no-carry-over {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #374151;
    }

    .badge-approval {
        background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);
        color: #991b1b;
    }

    .badge-no-approval {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
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

    .leave-type-info {
        display: flex;
        align-items: center;
    }

    .leave-type-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 12px;
        flex-shrink: 0;
    }

    .leave-type-details {
        flex: 1;
    }

    .leave-type-name {
        font-weight: 600;
        color: #374151;
        margin-bottom: 2px;
    }

    .leave-type-status {
        font-size: 12px;
        color: #6b7280;
    }

    .policy-details {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .policy-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 13px;
    }

    .policy-label {
        color: #6b7280;
        min-width: 80px;
    }

    .policy-value {
        font-weight: 600;
        color: #374151;
    }
</style>

<?php require_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-clipboard-list mr-3" style="-webkit-text-fill-color: #667eea;"></i>
                <?php echo htmlspecialchars($page_title ?? 'จัดการนโยบายการลา'); ?>
            </h1>
            <p class="text-gray-500 mt-2">กำหนดและจัดการนโยบายการลาสำหรับประเภทการลาต่างๆ</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo BASE_URL; ?>/leavepolicies/export" class="btn-export" title="ส่งออกข้อมูล">
                <i class="fas fa-download mr-2"></i>
                ส่งออก
            </a>
            <a href="<?php echo BASE_URL; ?>/leavepolicies/create" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                เพิ่มนโยบายใหม่
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
                <i class="fas fa-clipboard-list text-blue-600"></i>
            </div>
            <div class="stat-card-value" id="total-policies">
                <?php echo $num ?? 0; ?>
            </div>
            <div class="stat-card-label">นโยบายทั้งหมด</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                <i class="fas fa-infinity text-yellow-600"></i>
            </div>
            <div class="stat-card-value" id="unlimited-policies">-</div>
            <div class="stat-card-label">ลาไม่จำกัด</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                <i class="fas fa-arrow-right text-green-600"></i>
            </div>
            <div class="stat-card-value" id="carry-over-policies">-</div>
            <div class="stat-card-label">โอนวันลาได้</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #fecaca 0%, #fca5a5 100%);">
                <i class="fas fa-check-circle text-red-600"></i>
            </div>
            <div class="stat-card-value" id="approval-policies">-</div>
            <div class="stat-card-label">ต้องอนุมัติ</div>
        </div>
    </div>

    <div class="table-container">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ประเภทการลา
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            จำนวนวันลา/ปี
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            รายละเอียดนโยบาย
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
                    <?php if (isset($num) && $num > 0 && isset($stmt) && $stmt !== null): ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="leave-type-info">
                                        <div class="leave-type-icon" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                            <i class="fas fa-calendar-times text-white text-sm"></i>
                                        </div>
                                        <div class="leave-type-details">
                                            <div class="leave-type-name">
                                                <?php echo htmlspecialchars($row['leave_type_name']); ?>
                                            </div>
                                            <div class="leave-type-status">
                                                <?php echo $row['is_paid'] ? 'ลาได้เงิน' : 'ลาไม่ได้เงิน'; ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <?php if ($row['is_unlimited']): ?>
                                        <span class="policy-badge badge-unlimited">
                                            <i class="fas fa-infinity mr-1"></i>ไม่จำกัด
                                        </span>
                                    <?php else: ?>
                                        <span class="policy-badge badge-limited">
                                            <?php echo $row['days_allowed_per_year']; ?> วัน
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="policy-details">
                                        <div class="policy-row">
                                            <span class="policy-label">โอนวันลา:</span>
                                            <span class="policy-value">
                                                <?php if ($row['can_be_carried_over']): ?>
                                                    ได้ (สูงสุด <?php echo $row['max_carry_over_days']; ?> วัน)
                                                <?php else: ?>
                                                    ไม่ได้
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <div class="policy-row">
                                            <span class="policy-label">แจ้งล่วงหน้า:</span>
                                            <span class="policy-value"><?php echo $row['min_notice_days']; ?> วัน</span>
                                        </div>
                                        <div class="policy-row">
                                            <span class="policy-label">ลาติดต่อกัน:</span>
                                            <span class="policy-value">สูงสุด <?php echo $row['max_consecutive_days']; ?> วัน</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex flex-col gap-2">
                                        <?php if ($row['can_be_carried_over']): ?>
                                            <span class="policy-badge badge-carry-over">
                                                <i class="fas fa-arrow-right mr-1"></i>โอนได้
                                            </span>
                                        <?php else: ?>
                                            <span class="policy-badge badge-no-carry-over">
                                                <i class="fas fa-ban mr-1"></i>โอนไม่ได้
                                            </span>
                                        <?php endif; ?>
                                        
                                        <?php if ($row['requires_approval']): ?>
                                            <span class="policy-badge badge-approval">
                                                <i class="fas fa-check-circle mr-1"></i>ต้องอนุมัติ
                                            </span>
                                        <?php else: ?>
                                            <span class="policy-badge badge-no-approval">
                                                <i class="fas fa-times-circle mr-1"></i>ไม่ต้องอนุมัติ
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="flex justify-center items-center space-x-2">
                                        <a href="<?php echo BASE_URL; ?>/leavepolicies/edit/<?php echo htmlspecialchars($row['id']); ?>" 
                                           class="table-action-link" 
                                           title="แก้ไขนโยบาย">
                                            <i class="fas fa-edit mr-1"></i>
                                            แก้ไข
                                        </a>
                                        <form action="<?php echo BASE_URL; ?>/leavepolicies/destroy/<?php echo htmlspecialchars($row['id']); ?>" 
                                              method="POST" 
                                              class="inline-block" 
                                              onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบนโยบายการลา &quot;<?php echo htmlspecialchars(addslashes($row['leave_type_name'])); ?>&quot;?\n\nการลบจะไม่สามารถกู้คืนได้');">
                                            <button type="submit" 
                                                    class="table-action-delete bg-transparent border-none cursor-pointer" 
                                                    title="ลบนโยบาย">
                                                <i class="fas fa-trash-alt mr-1"></i>
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="empty-state">
                                <i class="fas fa-clipboard-list"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">ยังไม่มีนโยบายการลาในระบบ</h3>
                                <p class="text-gray-500 mb-4">เริ่มต้นด้วยการเพิ่มนโยบายการลาแรกของคุณ</p>
                                <a href="<?php echo BASE_URL; ?>/leavepolicies/create" class="btn-primary">
                                    <i class="fas fa-plus mr-2"></i>เพิ่มนโยบายใหม่
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
// Load statistics
document.addEventListener('DOMContentLoaded', function() {
    loadStatistics();
});

function loadStatistics() {
    fetch('<?php echo BASE_URL; ?>/leavepolicies/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('unlimited-policies').textContent = data.data.unlimited || 0;
                document.getElementById('carry-over-policies').textContent = data.data.carry_over || 0;
                document.getElementById('approval-policies').textContent = data.data.requires_approval || 0;
            }
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
}

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

// Add hover effects for policy badges
document.addEventListener('DOMContentLoaded', function() {
    const badges = document.querySelectorAll('.policy-badge');
    badges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>