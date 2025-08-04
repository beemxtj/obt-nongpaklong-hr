<<<<<<< HEAD
<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/app.php';
}
require_once __DIR__ . '/../layouts/header.php';

// ดึงข้อมูลทั้งหมดจาก statement ก่อน
$requests_data = [];
if (isset($stmt) && $stmt) {
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $requests_data[] = $row;
    }
}
?>

<style>
    .btn-primary { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
        color: white; 
        transition: all 0.3s ease; 
        border-radius: 12px; 
        padding: 12px 24px; 
        font-weight: 600; 
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3); 
        border: none; 
    }
    .btn-primary:hover { 
        transform: translateY(-2px); 
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4); 
    }
    
    .floating-header { 
        display: flex; 
        flex-direction: column; 
        gap: 1.5rem; 
        margin-bottom: 2rem;
    }
    @media (min-width: 768px) { 
        .floating-header { 
            flex-direction: row; 
            justify-content: space-between; 
            align-items: center; 
        } 
    }
    
    .section-header { 
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
        -webkit-background-clip: text; 
        -webkit-text-fill-color: transparent; 
        background-clip: text; 
        font-weight: 700; 
    }
    
    .filter-container {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        margin-bottom: 1.5rem;
    }
    
    .search-input, .filter-select {
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        padding: 12px 16px;
        transition: all 0.3s ease;
        width: 100%;
        background: white;
    }
    .search-input:focus, .filter-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        outline: none;
    }
    
    .stats-container {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }
    
    .stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        border: 1px solid #f3f4f6;
        text-align: center;
        transition: all 0.3s ease;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        font-weight: 500;
    }
    
    .approval-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        border: 1px solid #f3f4f6;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }
    .approval-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        border-color: #667eea;
    }
    
    .approval-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }
    .approval-card.risk-low::before { background: #22c55e; }
    .approval-card.risk-medium::before { background: #f59e0b; }
    .approval-card.risk-high::before { background: #ef4444; }
    
    .card-header {
        display: flex;
        justify-content: between;
        align-items: start;
        margin-bottom: 1rem;
        flex-wrap: wrap;
        gap: 1rem;
    }
    
    .employee-info {
        display: flex;
        align-items: center;
        flex: 1;
        min-width: 0;
    }
    
    .employee-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #667eea, #764ba2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        margin-right: 1rem;
        flex-shrink: 0;
    }
    
    .employee-details h3 {
        font-size: 1.125rem;
        font-weight: 600;
        color: #1f2937;
        margin: 0 0 0.25rem 0;
    }
    
    .employee-details p {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }
    
    .status-badges {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
        align-items: flex-end;
    }
    
    .status-badge, .risk-badge {
        padding: 6px 14px;
        border-radius: 9999px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }
    
    .status-pending { background-color: #fef3c7; color: #92400e; }
    .status-approved { background-color: #d1fae5; color: #065f46; }
    .status-rejected { background-color: #fee2e2; color: #991b1b; }
    
    .risk-low { background-color: #d1fae5; color: #065f46; }
    .risk-medium { background-color: #fef3c7; color: #92400e; }
    .risk-high { background-color: #fee2e2; color: #991b1b; }
    
    .leave-summary {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1rem;
    }
    
    .summary-item {
        background: #f8fafc;
        padding: 1rem;
        border-radius: 8px;
        border-left: 3px solid #667eea;
    }
    
    .summary-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }
    
    .summary-value {
        font-weight: 600;
        color: #1e293b;
        font-size: 0.875rem;
    }
    
    .leave-reason {
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .reason-label {
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }
    
    .reason-text {
        color: #374151;
        line-height: 1.5;
        font-size: 0.875rem;
    }
    
    .policy-status {
        margin-bottom: 1rem;
    }
    
    .policy-success {
        background: #dcfce7;
        border: 1px solid #22c55e;
        border-radius: 8px;
        padding: 1rem;
        color: #166534;
    }
    
    .policy-warning {
        background: #fef3c7;
        border: 1px solid #f59e0b;
        border-radius: 8px;
        padding: 1rem;
        color: #92400e;
    }
    
    .policy-error {
        background: #fee2e2;
        border: 1px solid #ef4444;
        border-radius: 8px;
        padding: 1rem;
        color: #991b1b;
    }
    
    .workflow-info {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
    }
    
    .workflow-steps {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .workflow-step {
        padding: 4px 12px;
        border-radius: 16px;
        font-size: 0.75rem;
        font-weight: 600;
        background: #e2e8f0;
        color: #64748b;
    }
    .workflow-step.completed { background: #22c55e; color: white; }
    .workflow-step.current { background: #3b82f6; color: white; }
    
    .workflow-arrow {
        color: #94a3b8;
        font-size: 0.875rem;
    }
    
    .action-buttons {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        padding-top: 1rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .btn-action {
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 600;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }
    
    .btn-approve {
        background-color: #22c55e;
        color: white;
    }
    .btn-approve:hover {
        background-color: #16a34a;
        transform: translateY(-1px);
    }
    
    .btn-reject {
        background-color: #ef4444;
        color: white;
    }
    .btn-reject:hover {
        background-color: #dc2626;
        transform: translateY(-1px);
    }
    
    .btn-detail {
        background-color: #6366f1;
        color: white;
    }
    .btn-detail:hover {
        background-color: #4f46e5;
        transform: translateY(-1px);
    }
    
    .btn-secondary {
        background-color: #f3f4f6;
        color: #374151;
        border: 1px solid #d1d5db;
    }
    .btn-secondary:hover {
        background-color: #e5e7eb;
    }
    
    .attachment-link {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        color: #3b82f6;
        text-decoration: none;
        padding: 0.5rem 1rem;
        background: #eff6ff;
        border-radius: 6px;
        font-size: 0.875rem;
        margin-bottom: 1rem;
    }
    .attachment-link:hover {
        background: #dbeafe;
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #6b7280;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }
    
    .modal {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, 0.5);
        z-index: 50;
        display: none;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    
    .modal.show {
        display: flex;
    }
    
    .modal-content {
        background: white;
        border-radius: 16px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        max-width: 4xl;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
    }
    
    .modal-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .modal-body {
        padding: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .floating-header {
            gap: 1rem;
        }
        
        .stats-container {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .filter-container {
            padding: 1rem;
        }
        
        .card-header {
            flex-direction: column;
            align-items: stretch;
        }
        
        .status-badges {
            align-items: flex-start;
            flex-direction: row;
            flex-wrap: wrap;
        }
        
        .leave-summary {
            grid-template-columns: 1fr;
        }
        
        .action-buttons {
            flex-direction: column;
        }
        
        .btn-action {
            justify-content: center;
        }
    }
</style>

<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-user-check mr-3" style="-webkit-text-fill-color: #667eea;"></i>อนุมัติใบลา
            </h1>
            <p class="text-gray-500 mt-2">รายการคำขอที่รอการอนุมัติจากคุณ พร้อมข้อมูลนโยบายและการวิเคราะห์ความเสี่ยง</p>
        </div>
        <div class="flex gap-2">
            <button onclick="refreshPage()" class="btn-secondary">
                <i class="fas fa-sync-alt mr-2"></i>รีเฟรช
            </button>
            <button onclick="showPolicyGuide()" class="btn-primary">
                <i class="fas fa-info-circle mr-2"></i>คู่มือ
            </button>
        </div>
    </div>

    <?php if (!empty($requests_data)) : ?>
        <!-- ระบบกรองและค้นหา -->
        <div class="filter-container">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ค้นหาพนักงาน</label>
                    <input type="text" id="search-employee" class="search-input" placeholder="ชื่อพนักงาน...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ประเภทการลา</label>
                    <select id="filter-leave-type" class="filter-select">
                        <option value="">ทั้งหมด</option>
                        <option value="ลาป่วย">ลาป่วย</option>
                        <option value="ลากิจ">ลากิจ</option>
                        <option value="ลาพักผ่อน">ลาพักผ่อน</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">ความเสี่ยง</label>
                    <select id="filter-risk" class="filter-select">
                        <option value="">ทั้งหมด</option>
                        <option value="low">ความเสี่ยงต่ำ</option>
                        <option value="medium">ความเสี่ยงปานกลาง</option>
                        <option value="high">ความเสี่ยงสูง</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">สถานะ</label>
                    <select id="filter-status" class="filter-select">
                        <option value="">ทั้งหมด</option>
                        <option value="รออนุมัติ">รออนุมัติ</option>
                        <option value="อนุมัติ">อนุมัติแล้ว</option>
                        <option value="ไม่อนุมัติ">ไม่อนุมัติ</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- สถิติโดยรวม -->
        <?php
        $total = count($requests_data);
        $pending = count(array_filter($requests_data, fn($r) => $r['status'] == 'รออนุมัติ'));
        $approved = count(array_filter($requests_data, fn($r) => $r['status'] == 'อนุมัติ'));
        $urgent = 0; // คำนวณจำนวนด่วน
        
        foreach ($requests_data as $req) {
            $start_date = new DateTime($req['start_date']);
            $notice_days = (new DateTime())->diff($start_date)->days;
            if ($notice_days <= 1) $urgent++;
        }
        ?>
        
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-number text-blue-600"><?php echo $total; ?></div>
                <div class="stat-label">ทั้งหมด</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-yellow-600"><?php echo $pending; ?></div>
                <div class="stat-label">รออนุมัติ</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-green-600"><?php echo $approved; ?></div>
                <div class="stat-label">อนุมัติแล้ว</div>
            </div>
            <div class="stat-card">
                <div class="stat-number text-red-600"><?php echo $urgent; ?></div>
                <div class="stat-label">ด่วน</div>
            </div>
        </div>

        <!-- รายการคำขอ -->
        <div id="approval-list">
            <?php foreach ($requests_data as $row) : 
                // คำนวณข้อมูลสำคัญ
                $start_date = new DateTime($row['start_date']);
                $end_date = new DateTime($row['end_date']);
                $leave_days = $start_date->diff($end_date)->days + 1;
                $notice_days = (new DateTime())->diff($start_date)->days;
                
                // วิเคราะห์ความเสี่ยง
                $policy_violations = [];
                $risk_level = 'low';
                $risk_score = 0;
                
                if (isset($row['policy_id']) && $row['policy_id']) {
                    // ตรวจสอบวันแจ้งล่วงหน้า
                    if ($notice_days < $row['min_notice_days']) {
                        $policy_violations[] = "แจ้งล่วงหน้าไม่เพียงพอ";
                        $risk_score += 2;
                    }
                    
                    // ตรวจสอบวันลาติดต่อกัน
                    if ($row['max_consecutive_days'] > 0 && $leave_days > $row['max_consecutive_days']) {
                        $policy_violations[] = "ลาติดต่อกันเกินกำหนด";
                        $risk_score += 3;
                    }
                    
                    // จำลองการตรวจสอบวันลาคงเหลือ
                    if (!$row['is_unlimited']) {
                        $used_days = rand(3, 8); // จำลองข้อมูล
                        $remaining = $row['days_allowed_per_year'] - $used_days;
                        if ($leave_days > $remaining) {
                            $policy_violations[] = "วันลาไม่เพียงพอ";
                            $risk_score += 4;
                        }
                    }
                }
                
                // กำหนดระดับความเสี่ยง
                if ($risk_score >= 5) $risk_level = 'high';
                elseif ($risk_score >= 2) $risk_level = 'medium';
                
                // ด่วนหรือไม่
                $is_urgent = $notice_days <= 1;
                if ($is_urgent) $risk_score += 1;
            ?>
            
            <div class="approval-card risk-<?php echo $risk_level; ?>" 
                 data-employee="<?php echo strtolower($row['employee_name']); ?>"
                 data-leave-type="<?php echo $row['leave_type_name']; ?>"
                 data-risk="<?php echo $risk_level; ?>"
                 data-status="<?php echo $row['status']; ?>">
                
                <div class="card-header">
                    <div class="employee-info">
                        <div class="employee-avatar">
                            <?php echo strtoupper(mb_substr($row['employee_name'], 0, 2)); ?>
                        </div>
                        <div class="employee-details">
                            <h3><?php echo htmlspecialchars($row['employee_name']); ?></h3>
                            <p>รหัส: <?php echo htmlspecialchars($row['employee_code']); ?></p>
                        </div>
                    </div>
                    
                    <div class="status-badges">
                        <span class="risk-badge risk-<?php echo $risk_level; ?>">
                            <i class="fas fa-<?php echo $risk_level === 'high' ? 'exclamation-triangle' : ($risk_level === 'medium' ? 'exclamation-circle' : 'check-circle'); ?>"></i>
                            <?php echo $risk_level === 'high' ? 'ความเสี่ยงสูง' : ($risk_level === 'medium' ? 'ปานกลาง' : 'ความเสี่ยงต่ำ'); ?>
                        </span>
                        <span class="status-badge status-<?php echo $row['status'] == 'รออนุมัติ' ? 'pending' : ($row['status'] == 'อนุมัติ' ? 'approved' : 'rejected'); ?>">
                            <i class="fas fa-<?php echo $row['status'] == 'รออนุมัติ' ? 'clock' : ($row['status'] == 'อนุมัติ' ? 'check' : 'times'); ?>"></i>
                            <?php echo $row['status']; ?>
                        </span>
                        <?php if ($is_urgent) : ?>
                        <span class="status-badge" style="background: #fee2e2; color: #991b1b;">
                            <i class="fas fa-fire"></i>ด่วน
                        </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="leave-summary">
                    <div class="summary-item">
                        <div class="summary-label">ประเภทการลา</div>
                        <div class="summary-value">
                            <i class="fas fa-calendar-alt mr-2 text-blue-500"></i>
                            <?php echo htmlspecialchars($row['leave_type_name']); ?>
                        </div>
                    </div>
                    
                    <div class="summary-item">
                        <div class="summary-label">วันที่ลา</div>
                        <div class="summary-value">
                            <i class="fas fa-calendar-day mr-2 text-green-500"></i>
                            <?php echo $start_date->format('d/m/Y'); ?> - <?php echo $end_date->format('d/m/Y'); ?>
                        </div>
                    </div>
                    
                    <div class="summary-item">
                        <div class="summary-label">จำนวนวัน</div>
                        <div class="summary-value">
                            <i class="fas fa-clock mr-2 text-orange-500"></i>
                            <?php echo $leave_days; ?> วัน
                        </div>
                    </div>
                    
                    <div class="summary-item">
                        <div class="summary-label">แจ้งล่วงหน้า</div>
                        <div class="summary-value">
                            <i class="fas fa-bell mr-2 text-purple-500"></i>
                            <?php echo $notice_days; ?> วัน
                        </div>
                    </div>
                </div>

                <div class="leave-reason">
                    <div class="reason-label">เหตุผลการลา</div>
                    <div class="reason-text"><?php echo nl2br(htmlspecialchars($row['reason'])); ?></div>
                </div>

                <!-- ไฟล์แนบ -->
                <?php if (!empty($row['attachment_path'])) : ?>
                <a href="<?php echo BASE_URL; ?>/<?php echo $row['attachment_path']; ?>" target="_blank" class="attachment-link">
                    <i class="fas fa-paperclip"></i>
                    ดูไฟล์แนบ
                </a>
                <?php endif; ?>

                <!-- สถานะนโยบาย -->
                <div class="policy-status">
                    <?php if (empty($policy_violations)) : ?>
                    <div class="policy-success">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <strong>ผ่าน:</strong> คำขอนี้เป็นไปตามนโยบายการลาทุกประการ
                        </div>
                    </div>
                    <?php else : ?>
                    <div class="<?php echo $risk_level === 'high' ? 'policy-error' : 'policy-warning'; ?>">
                        <div class="flex items-start">
                            <i class="fas fa-exclamation-triangle mr-2 mt-1 flex-shrink-0"></i>
                            <div>
                                <strong>พบข้อผิดพลาด:</strong>
                                <ul class="list-disc list-inside mt-1 space-y-1">
                                    <?php foreach ($policy_violations as $violation) : ?>
                                        <li><?php echo $violation; ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- สายการอนุมัติ -->
                <div class="workflow-info">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-semibold text-blue-700">
                            <i class="fas fa-sitemap mr-2"></i>สายการอนุมัติ: การลาทั่วไป
                        </span>
                        <span class="text-xs text-gray-500">ขั้นตอนที่ 1 จาก 2</span>
                    </div>
                    <div class="workflow-steps">
                        <div class="workflow-step completed">
                            <i class="fas fa-user mr-1"></i>พนักงานยื่น
                        </div>
                        <div class="workflow-arrow">→</div>
                        <div class="workflow-step current">
                            <i class="fas fa-user-tie mr-1"></i>หัวหน้าอนุมัติ
                        </div>
                        <div class="workflow-arrow">→</div>
                        <div class="workflow-step">
                            <i class="fas fa-users mr-1"></i>HR รับทราบ
                        </div>
                    </div>
                </div>

                <!-- ปุ่มดำเนินการ -->
                <div class="action-buttons">
                    <?php if ($row['status'] == 'รออนุมัติ') : ?>
                        <a href="<?php echo BASE_URL; ?>/leave/approve/<?php echo $row['id']; ?>" 
                           class="btn-action btn-approve" 
                           onclick="return confirmApproval('<?php echo addslashes($row['employee_name']); ?>', '<?php echo addslashes($row['leave_type_name']); ?>', <?php echo count($policy_violations); ?>)">
                            <i class="fas fa-check"></i>อนุมัติ
                        </a>
                        
                        <a href="<?php echo BASE_URL; ?>/leave/reject/<?php echo $row['id']; ?>" 
                           class="btn-action btn-reject" 
                           onclick="return confirmRejection('<?php echo addslashes($row['employee_name']); ?>')">
                            <i class="fas fa-times"></i>ไม่อนุมัติ
                        </a>
                    <?php endif; ?>
                    
                    <button onclick="showDetailModal(<?php echo htmlspecialchars(json_encode($row)); ?>)" class="btn-action btn-detail">
                        <i class="fas fa-eye"></i>รายละเอียด
                    </button>
                    
                    <button onclick="showPolicyDetail('<?php echo $row['leave_type_name']; ?>')" class="btn-action btn-secondary">
                        <i class="fas fa-clipboard-list"></i>นโยบาย
                    </button>
                </div>
            </div>
            
            <?php endforeach; ?>
        </div>

    <?php else : ?>
        <div class="approval-card">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 mb-2">ไม่มีคำขอที่รอการอนุมัติ</h3>
                <p class="text-gray-500 mb-4">ยอดเยี่ยม! คุณได้จัดการคำขอทั้งหมดแล้ว</p>
                <a href="<?php echo BASE_URL; ?>/leave/history" class="btn-primary">
                    <i class="fas fa-history mr-2"></i>ดูประวัติการลา
                </a>
            </div>
        </div>
    <?php endif; ?>
</main>

<!-- Modal รายละเอียด -->
<div id="detailModal" class="modal" onclick="hideModal('detailModal')">
    <div class="modal-content" onclick="event.stopPropagation()" style="max-width: 48rem;">
        <div class="modal-header">
            <h3 class="text-lg font-semibold">รายละเอียดการลาและนโยบาย</h3>
            <button onclick="hideModal('detailModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="modal-body" id="modalContent">
            <!-- เนื้อหาจะถูกเพิ่มด้วย JavaScript -->
        </div>
    </div>
</div>

<!-- Modal คู่มือ -->
<div id="policyGuideModal" class="modal" onclick="hideModal('policyGuideModal')">
    <div class="modal-content" onclick="event.stopPropagation()" style="max-width: 32rem;">
        <div class="modal-header">
            <h3 class="text-lg font-semibold">คู่มือการอนุมัติการลา</h3>
            <button onclick="hideModal('policyGuideModal')" class="text-gray-400 hover:text-gray-600">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="modal-body">
            <div class="space-y-4">
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <h4 class="font-semibold text-green-800 mb-2">
                        <i class="fas fa-check-circle mr-2"></i>ความเสี่ยงต่ำ (สีเขียว)
                    </h4>
                    <p class="text-green-700 text-sm">คำขอเป็นไปตามนโยบายทุกประการ สามารถอนุมัติได้ทันที</p>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                    <h4 class="font-semibold text-yellow-800 mb-2">
                        <i class="fas fa-exclamation-circle mr-2"></i>ความเสี่ยงปานกลาง (สีเหลือง)
                    </h4>
                    <p class="text-yellow-700 text-sm">มีข้อผิดพลาดเล็กน้อย เช่น แจ้งล่วงหน้าไม่เพียงพอ ควรพิจารณาเป็นกรณีพิเศษ</p>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <h4 class="font-semibold text-red-800 mb-2">
                        <i class="fas fa-exclamation-triangle mr-2"></i>ความเสี่ยงสูง (สีแดง)
                    </h4>
                    <p class="text-red-700 text-sm">ผิดนโยบายหลายข้อ เช่น วันลาไม่เพียงพอ ลาติดต่อกันเกินกำหนด ต้องพิจารณาอย่างรอบคอบ</p>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                    <h4 class="font-semibold text-blue-800 mb-2">
                        <i class="fas fa-lightbulb mr-2"></i>คำแนะนำการอนุมัติ
                    </h4>
                    <ul class="text-blue-700 text-sm space-y-1">
                        <li>• ตรวจสอบเหตุผลการลาให้สมเหตุสมผล</li>
                        <li>• พิจารณาไฟล์แนบประกอบ (ถ้ามี)</li>
                        <li>• สอบถามข้อมูลเพิ่มเติมหากจำเป็น</li>
                        <li>• บันทึกหมายเหตุสำหรับการอนุมัติพิเศษ</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function refreshPage() {
    window.location.reload();
}

function showModal(modalId) {
    document.getElementById(modalId).classList.add('show');
}

function hideModal(modalId) {
    document.getElementById(modalId).classList.remove('show');
}

function showPolicyGuide() {
    showModal('policyGuideModal');
}

function showDetailModal(data) {
    const content = document.getElementById('modalContent');
    
    content.innerHTML = `
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold mb-3 text-gray-800">ข้อมูลพนักงาน</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium text-gray-600">ชื่อ:</span> ${data.employee_name}</div>
                        <div><span class="font-medium text-gray-600">รหัส:</span> ${data.employee_code}</div>
                        <div><span class="font-medium text-gray-600">ประเภทการลา:</span> ${data.leave_type_name}</div>
                    </div>
                </div>
                
                <div class="bg-gray-50 p-4 rounded-lg">
                    <h4 class="font-semibold mb-3 text-gray-800">ช่วงเวลาการลา</h4>
                    <div class="space-y-2 text-sm">
                        <div><span class="font-medium text-gray-600">วันที่เริ่ม:</span> ${new Date(data.start_date).toLocaleDateString('th-TH')}</div>
                        <div><span class="font-medium text-gray-600">วันที่สิ้นสุด:</span> ${new Date(data.end_date).toLocaleDateString('th-TH')}</div>
                        <div><span class="font-medium text-gray-600">สถานะ:</span> ${data.status}</div>
                    </div>
                </div>
            </div>
            
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-semibold mb-3 text-blue-800">เหตุผลการลา</h4>
                <p class="text-sm text-gray-700 whitespace-pre-line">${data.reason}</p>
            </div>
        </div>
    `;
    
    showModal('detailModal');
}

function showPolicyDetail(leaveType) {
    alert(`แสดงรายละเอียดนโยบายสำหรับ: ${leaveType}\n\n(ฟีเจอร์นี้จะเชื่อมต่อกับระบบจัดการนโยบายในอนาคต)`);
}

function confirmApproval(employeeName, leaveType, violationCount) {
    let message = `ยืนยันการอนุมัติใบลา?\n\nพนักงาน: ${employeeName}\nประเภท: ${leaveType}`;
    
    if (violationCount > 0) {
        message += `\n\n⚠️ คำเตือน: พบข้อผิดพลาดจากนโยบาย ${violationCount} ข้อ\nต้องการอนุมัติเป็นกรณีพิเศษหรือไม่?`;
    }
    
    return confirm(message);
}

function confirmRejection(employeeName) {
    return confirm(`ยืนยันการไม่อนุมัติใบลา?\n\nพนักงาน: ${employeeName}\n\nการกระทำนี้จะส่งการแจ้งเตือนไปยังพนักงาน`);
}

// ระบบกรอง
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('search-employee');
    const leaveTypeFilter = document.getElementById('filter-leave-type');
    const riskFilter = document.getElementById('filter-risk');
    const statusFilter = document.getElementById('filter-status');
    const approvalCards = document.querySelectorAll('.approval-card[data-employee]');

    function filterCards() {
        const searchTerm = searchInput?.value.toLowerCase() || '';
        const selectedLeaveType = leaveTypeFilter?.value || '';
        const selectedRisk = riskFilter?.value || '';
        const selectedStatus = statusFilter?.value || '';

        approvalCards.forEach(card => {
            const employee = card.dataset.employee || '';
            const leaveType = card.dataset.leaveType || '';
            const risk = card.dataset.risk || '';
            const status = card.dataset.status || '';

            const matchesSearch = !searchTerm || employee.includes(searchTerm);
            const matchesLeaveType = !selectedLeaveType || leaveType === selectedLeaveType;
            const matchesRisk = !selectedRisk || risk === selectedRisk;
            const matchesStatus = !selectedStatus || status === selectedStatus;

            if (matchesSearch && matchesLeaveType && matchesRisk && matchesStatus) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // เพิ่ม event listeners
    searchInput?.addEventListener('input', filterCards);
    leaveTypeFilter?.addEventListener('change', filterCards);
    riskFilter?.addEventListener('change', filterCards);
    statusFilter?.addEventListener('change', filterCards);

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            hideModal('detailModal');
            hideModal('policyGuideModal');
        }
        if (e.key === 'F5') {
            e.preventDefault();
            refreshPage();
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
=======
<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900">อนุมัติใบลา</h1>
            <p class="text-gray-500 mt-1">รายการคำขอการลาที่รอการอนุมัติจากคุณ</p>
        </div>
    </div>

    <!-- Pending Leave Requests Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">ชื่อพนักงาน</th>
                        <th scope="col" class="px-6 py-3">ประเภทการลา</th>
                        <th scope="col" class="px-6 py-3">วันที่ลา</th>
                        <th scope="col" class="px-6 py-3">เหตุผล</th>
                        <th scope="col" class="px-6 py-3 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($num > 0): ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <?php echo htmlspecialchars($row['employee_name']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo htmlspecialchars($row['leave_type_name']); ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo date('d/m/Y', strtotime($row['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($row['end_date'])); ?>
                                </td>
                                <td class="px-6 py-4 max-w-xs truncate" title="<?php echo htmlspecialchars($row['reason']); ?>">
                                    <?php echo htmlspecialchars($row['reason']); ?>
                                </td>
                                <td class="px-6 py-4 text-center whitespace-nowrap">
                                    <a href="<?php echo BASE_URL; ?>/leave/approve/<?php echo $row['id']; ?>" class="inline-flex items-center px-3 py-1 bg-green-500 hover:bg-green-600 text-white text-xs font-medium rounded-md" onclick="return confirm('คุณต้องการอนุมัติใบลาของ <?php echo htmlspecialchars($row['employee_name']); ?> หรือไม่?')">
                                        <i class="fas fa-check mr-1"></i> อนุมัติ
                                    </a>
                                    <a href="<?php echo BASE_URL; ?>/leave/reject/<?php echo $row['id']; ?>" class="inline-flex items-center px-3 py-1 bg-red-500 hover:bg-red-600 text-white text-xs font-medium rounded-md ml-2" onclick="return confirm('คุณต้องการไม่อนุมัติใบลาของ <?php echo htmlspecialchars($row['employee_name']); ?> หรือไม่?')">
                                        <i class="fas fa-times mr-1"></i> ไม่อนุมัติ
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="bg-white border-b">
                            <td colspan="5" class="px-6 py-4 text-center text-gray-500">ไม่มีคำขอการลาที่รอการอนุมัติ</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
