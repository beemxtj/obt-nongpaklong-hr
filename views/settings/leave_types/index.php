<?php
// views/settings/leave_types/index.php

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

    .status-badge {
        padding: 4px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .status-paid {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .status-unpaid {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }

    .days-badge {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e40af;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
        font-weight: 600;
    }

    .bulk-actions {
        background: #f8fafc;
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        display: none;
    }

    .bulk-actions.active {
        display: block;
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
</style>

<?php require_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-calendar-times mr-3" style="-webkit-text-fill-color: #667eea;"></i>
                <?php echo htmlspecialchars($page_title ?? 'จัดการประเภทการลา'); ?>
            </h1>
            <p class="text-gray-500 mt-2">จัดการและกำหนดประเภทการลาต่างๆ ในระบบ</p>
        </div>
        <div class="header-actions">
            <a href="<?php echo BASE_URL; ?>/leave_types/export" class="btn-export" title="ส่งออกข้อมูล">
                <i class="fas fa-download mr-2"></i>
                ส่งออก
            </a>
            <a href="<?php echo BASE_URL; ?>/leave_types/create" class="btn-primary">
                <i class="fas fa-plus mr-2"></i>
                เพิ่มประเภทการลาใหม่
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
                <i class="fas fa-list text-blue-600"></i>
            </div>
            <div class="stat-card-value" id="total-leave-types">
                <?php echo $num ?? 0; ?>
            </div>
            <div class="stat-card-label">ประเภทการลาทั้งหมด</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);">
                <i class="fas fa-money-bill-wave text-green-600"></i>
            </div>
            <div class="stat-card-value" id="paid-leave-types">-</div>
            <div class="stat-card-label">ลาได้เงิน</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                <i class="fas fa-ban text-yellow-600"></i>
            </div>
            <div class="stat-card-value" id="unpaid-leave-types">-</div>
            <div class="stat-card-label">ลาไม่ได้เงิน</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-icon" style="background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);">
                <i class="fas fa-star text-indigo-600"></i>
            </div>
            <div class="stat-card-value" id="most-used" style="font-size: 1rem;">-</div>
            <div class="stat-card-label">ประเภทที่ใช้มากที่สุด</div>
        </div>
    </div>

    <div class="table-container">
        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulk-actions">
            <form action="<?php echo BASE_URL; ?>/leave_types/bulk" method="POST" onsubmit="return confirmBulkAction()">
                <div class="flex items-center gap-4">
                    <span class="font-medium text-gray-700">การดำเนินการกับรายการที่เลือก:</span>
                    <select name="bulk_action" class="border border-gray-300 rounded-lg px-3 py-2">
                        <option value="">เลือกการดำเนินการ</option>
                        <option value="make_paid">เปลี่ยนเป็นลาได้เงิน</option>
                        <option value="make_unpaid">เปลี่ยนเป็นลาไม่ได้เงิน</option>
                        <option value="delete" style="color: #dc2626;">ลบ</option>
                    </select>
                    <button type="submit" class="btn-secondary">ดำเนินการ</button>
                    <button type="button" onclick="cancelBulkSelection()" class="text-gray-500 hover:text-gray-700">ยกเลิก</button>
                </div>
                <input type="hidden" name="selected_ids" id="selected_ids">
            </form>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left">
                            <input type="checkbox" id="select-all" onchange="toggleSelectAll()" class="rounded border-gray-300">
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ชื่อประเภทการลา
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            วันลาสูงสุด/ปี
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            สถานะการจ่ายเงิน
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            วันที่สร้าง
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
                                    <input type="checkbox" class="row-checkbox rounded border-gray-300" 
                                           value="<?php echo htmlspecialchars($row['id']); ?>" 
                                           onchange="updateBulkActions()">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600 flex items-center justify-center">
                                                <i class="fas fa-calendar-times text-white text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">
                                                <?php echo htmlspecialchars($row['name']); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="days-badge">
                                        <?php echo $row['max_days_per_year'] > 0 ? $row['max_days_per_year'] . ' วัน' : 'ไม่จำกัด'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="status-badge <?php echo $row['is_paid'] ? 'status-paid' : 'status-unpaid'; ?>">
                                        <?php echo $row['is_paid'] ? 'ได้เงิน' : 'ไม่ได้เงิน'; ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                    <?php echo date('d/m/Y', strtotime($row['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-4">
                                    <a href="<?php echo BASE_URL; ?>/leave_types/edit/<?php echo htmlspecialchars($row['id']); ?>" 
                                       class="table-action-link" title="แก้ไข">
                                        <i class="fas fa-pencil-alt mr-1"></i>แก้ไข
                                    </a>
                                    <form action="<?php echo BASE_URL; ?>/leave_types/destroy/<?php echo htmlspecialchars($row['id']); ?>" 
                                          method="POST" class="inline-block" 
                                          onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบประเภทการลา &quot;<?php echo htmlspecialchars(addslashes($row['name'])); ?>&quot;?\n\nการลบจะไม่สามารถกู้คืนได้');">
                                        <button type="submit" class="table-action-delete bg-transparent border-none p-0 cursor-pointer" title="ลบ">
                                            <i class="fas fa-trash-alt mr-1"></i>ลบ
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-12">
                                <div class="flex flex-col items-center">
                                    <i class="fas fa-calendar-times text-4xl text-gray-300 mb-3"></i>
                                    <p class="text-gray-500 text-lg mb-2">ยังไม่มีประเภทการลาในระบบ</p>
                                    <p class="text-gray-400 text-sm mb-4">เริ่มต้นด้วยการเพิ่มประเภทการลาแรกของคุณ</p>
                                    <a href="<?php echo BASE_URL; ?>/leave_types/create" class="btn-primary">
                                        <i class="fas fa-plus mr-2"></i>เพิ่มประเภทการลาใหม่
                                    </a>
                                </div>
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
    fetch('<?php echo BASE_URL; ?>/leave_types/stats')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('paid-leave-types').textContent = data.data.paid || 0;
                document.getElementById('unpaid-leave-types').textContent = data.data.unpaid || 0;
                document.getElementById('most-used').textContent = data.data.most_used || 'ไม่มีข้อมูล';
            }
        })
        .catch(error => {
            console.error('Error loading statistics:', error);
        });
}

// Bulk selection functions
function toggleSelectAll() {
    const selectAll = document.getElementById('select-all');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
    
    updateBulkActions();
}

function updateBulkActions() {
    const checkboxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkActions = document.getElementById('bulk-actions');
    const selectAll = document.getElementById('select-all');
    
    if (checkboxes.length > 0) {
        bulkActions.classList.add('active');
        
        // Update selected IDs
        const selectedIds = Array.from(checkboxes).map(cb => cb.value);
        document.getElementById('selected_ids').value = selectedIds.join(',');
    } else {
        bulkActions.classList.remove('active');
    }
    
    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.row-checkbox');
    selectAll.checked = allCheckboxes.length > 0 && checkboxes.length === allCheckboxes.length;
    selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
}

function cancelBulkSelection() {
    document.getElementById('select-all').checked = false;
    document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
    updateBulkActions();
}

function confirmBulkAction() {
    const action = document.querySelector('select[name="bulk_action"]').value;
    const selectedCount = document.querySelectorAll('.row-checkbox:checked').length;
    
    if (!action) {
        alert('กรุณาเลือกการดำเนินการ');
        return false;
    }
    
    let confirmMessage = `คุณแน่ใจหรือไม่ที่จะดำเนินการกับ ${selectedCount} รายการที่เลือก?`;
    
    if (action === 'delete') {
        confirmMessage = `คุณแน่ใจหรือไม่ที่จะลบ ${selectedCount} รายการที่เลือก?\n\nการลบจะไม่สามารถกู้คืนได้`;
    }
    
    return confirm(confirmMessage);
}
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>