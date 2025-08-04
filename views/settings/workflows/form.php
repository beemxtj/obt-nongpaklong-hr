<?php 
require_once __DIR__ . '/../../layouts/header.php'; 

$is_edit_mode = isset($workflow) && $workflow['id'];
$page_title = $is_edit_mode ? 'แก้ไขสายการอนุมัติ' : 'สร้างสายการอนุมัติใหม่';
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

    .btn-add {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        transition: all 0.3s ease;
        border-radius: 10px;
        padding: 10px 20px;
        font-weight: 600;
        border: none;
        cursor: pointer;
        font-size: 14px;
    }

    .btn-add:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
    }

    .btn-remove {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        transition: all 0.3s ease;
        border-radius: 8px;
        padding: 8px 12px;
        font-weight: 600;
        border: none;
        cursor: pointer;
    }

    .btn-remove:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
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

    .step-entry {
        background: white;
        border: 2px solid #e2e8f0;
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        position: relative;
    }

    .step-entry:hover {
        border-color: #cbd5e1;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .step-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .step-number {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .step-label {
        flex: 1;
        font-weight: 600;
        color: #374151;
    }

    .step-controls {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .step-select {
        flex: 1;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 12px 16px;
        background-color: #f8fafc;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .step-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
        background-color: #fff;
    }

    .steps-container {
        max-height: 400px;
        overflow-y: auto;
        padding-right: 8px;
    }

    .steps-container::-webkit-scrollbar {
        width: 6px;
    }

    .steps-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 6px;
    }

    .steps-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 6px;
    }

    .add-step-area {
        text-align: center;
        padding: 2rem;
        border: 2px dashed #d1d5db;
        border-radius: 16px;
        background: #f9fafb;
        transition: all 0.3s ease;
    }

    .add-step-area:hover {
        border-color: #667eea;
        background: #f0f9ff;
    }

    .step-arrow {
        text-align: center;
        margin: 0.5rem 0;
        color: #9ca3af;
    }

    .step-arrow i {
        font-size: 1.5rem;
    }

    .workflow-preview {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        border: 1px solid #bfdbfe;
        border-radius: 16px;
        padding: 1.5rem;
        margin-top: 1rem;
    }

    .preview-title {
        font-weight: 600;
        color: #1e40af;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
    }

    .preview-flow {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .preview-step {
        background: white;
        border: 1px solid #bfdbfe;
        border-radius: 8px;
        padding: 8px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #1e40af;
    }

    .preview-arrow {
        color: #6b7280;
        margin: 0 4px;
    }

    .empty-steps-message {
        text-align: center;
        color: #6b7280;
        font-style: italic;
        padding: 2rem;
    }
</style>

<?php require_once __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-sitemap mr-3" style="-webkit-text-fill-color: #667eea;"></i>
                <?php echo htmlspecialchars($page_title); ?>
            </h1>
            <p class="text-gray-500 mt-2">
                <?php echo $is_edit_mode ? 'แก้ไขชื่อและลำดับขั้นการอนุมัติ' : 'กำหนดชื่อและเพิ่มลำดับขั้นการอนุมัติ'; ?>
            </p>
        </div>
        <a href="<?php echo BASE_URL; ?>/workflow" class="btn-secondary hidden sm:flex items-center">
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
        <form action="<?php echo $is_edit_mode ? BASE_URL . '/workflow/update/' . $workflow['id'] : BASE_URL . '/workflow/store'; ?>" method="POST" onsubmit="return validateForm()">
            
            <div class="form-content">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i>ข้อมูลพื้นฐาน
                    </div>
                    
                    <div class="mb-6">
                        <label for="workflow_name" class="form-label">
                            <i class="fas fa-tag"></i>ชื่อสายการอนุมัติ<span class="required">*</span>
                        </label>
                        <input type="text" 
                               name="workflow_name" 
                               id="workflow_name" 
                               class="form-input w-full md:w-2/3" 
                               value="<?php echo htmlspecialchars($workflow['name'] ?? ''); ?>" 
                               maxlength="100"
                               required>
                        <div class="help-text">
                            ระบุชื่อสายการอนุมัติ เช่น อนุมัติลาป่วย, อนุมัติลากิจ, อนุมัติเอกสารการเงิน เป็นต้น (สูงสุด 100 ตัวอักษร)
                        </div>
                    </div>
                </div>

                <!-- Approval Steps Section -->
                <div class="form-section">
                    <div class="form-section-title">
                        <i class="fas fa-list-ol"></i>ลำดับขั้นการอนุมัติ
                        <span class="ml-auto text-sm font-normal text-gray-500" id="step-counter">0 ขั้นตอน</span>
                    </div>
                    
                    <div class="help-text mb-4">
                        กำหนดลำดับการอนุมัติตั้งแต่ขั้นตอนแรกจนถึงขั้นตอนสุดท้าย ระบบจะดำเนินการตามลำดับที่กำหนด
                    </div>

                    <div id="steps-container" class="steps-container">
                        <?php if (!empty($steps)): ?>
                            <?php foreach ($steps as $step): ?>
                                <div class="step-entry">
                                    <div class="step-header">
                                        <div class="step-number"><?php echo $step['step_number']; ?></div>
                                        <div class="step-label">ขั้นตอนที่ <?php echo $step['step_number']; ?></div>
                                    </div>
                                    <div class="step-controls">
                                        <select name="steps[<?php echo $step['step_number']; ?>]" class="step-select">
                                            <option value="">-- เลือกตำแหน่งผู้อนุมัติ --</option>
                                            <?php foreach ($available_roles as $role): ?>
                                                <option value="<?php echo htmlspecialchars($role); ?>" 
                                                        <?php echo ($step['approver_role'] == $role) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($role); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button type="button" class="btn-remove remove-step-btn" title="ลบขั้นตอนนี้">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php if ($step['step_number'] < count($steps)): ?>
                                    <div class="step-arrow">
                                        <i class="fas fa-arrow-down"></i>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <div class="add-step-area">
                        <button type="button" id="add-step-btn" class="btn-add">
                            <i class="fas fa-plus mr-2"></i>เพิ่มขั้นตอนการอนุมัติ
                        </button>
                        <p class="text-sm text-gray-500 mt-2">คลิกเพื่อเพิ่มขั้นตอนการอนุมัติใหม่</p>
                    </div>

                    <!-- Workflow Preview -->
                    <div class="workflow-preview" id="workflow-preview" style="display: none;">
                        <div class="preview-title">
                            <i class="fas fa-eye mr-2"></i>ตัวอย่างลำดับการอนุมัติ
                        </div>
                        <div class="preview-flow" id="preview-flow">
                            <!-- Dynamic content will be inserted here -->
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/workflow" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $is_edit_mode ? 'บันทึกการเปลี่ยนแปลง' : 'สร้างสายการอนุมัติ'; ?>
                </button>
            </div>
        </form>
    </div>
</main>

<!-- Template for new step (hidden) -->
<div id="step-template" class="hidden">
    <div class="step-entry">
        <div class="step-header">
            <div class="step-number"></div>
            <div class="step-label"></div>
        </div>
        <div class="step-controls">
            <select class="step-select">
                <option value="">-- เลือกตำแหน่งผู้อนุมัติ --</option>
                <?php foreach ($available_roles as $role): ?>
                    <option value="<?php echo htmlspecialchars($role); ?>">
                        <?php echo htmlspecialchars($role); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <button type="button" class="btn-remove remove-step-btn" title="ลบขั้นตอนนี้">
                <i class="fas fa-trash-alt"></i>
            </button>
        </div>
    </div>
</div>

<div id="arrow-template" class="hidden">
    <div class="step-arrow">
        <i class="fas fa-arrow-down"></i>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const stepsContainer = document.getElementById('steps-container');
    const addStepBtn = document.getElementById('add-step-btn');
    const stepTemplate = document.getElementById('step-template');
    const arrowTemplate = document.getElementById('arrow-template');
    const stepCounter = document.getElementById('step-counter');
    const workflowPreview = document.getElementById('workflow-preview');
    const previewFlow = document.getElementById('preview-flow');

    function updateStepCounter() {
        const stepCount = stepsContainer.querySelectorAll('.step-entry').length;
        stepCounter.textContent = stepCount + ' ขั้นตอน';
    }

    function updateWorkflowPreview() {
        const steps = stepsContainer.querySelectorAll('.step-entry');
        previewFlow.innerHTML = '';
        
        if (steps.length > 0) {
            workflowPreview.style.display = 'block';
            
            steps.forEach((step, index) => {
                const select = step.querySelector('select');
                const roleName = select.value || 'ยังไม่ได้เลือก';
                
                const previewStep = document.createElement('div');
                previewStep.className = 'preview-step';
                previewStep.textContent = `${index + 1}. ${roleName}`;
                previewFlow.appendChild(previewStep);
                
                if (index < steps.length - 1) {
                    const arrow = document.createElement('span');
                    arrow.className = 'preview-arrow';
                    arrow.innerHTML = '→';
                    previewFlow.appendChild(arrow);
                }
            });
        } else {
            workflowPreview.style.display = 'none';
        }
    }

    function renumberSteps() {
        const allSteps = stepsContainer.querySelectorAll('.step-entry');
        
        // Remove all arrows first
        stepsContainer.querySelectorAll('.step-arrow').forEach(arrow => arrow.remove());
        
        allSteps.forEach((step, index) => {
            const stepNumber = index + 1;
            const numberElement = step.querySelector('.step-number');
            const labelElement = step.querySelector('.step-label');
            const selectElement = step.querySelector('select');
            
            numberElement.textContent = stepNumber;
            labelElement.textContent = `ขั้นตอนที่ ${stepNumber}`;
            selectElement.name = `steps[${stepNumber}]`;
            
            // Add arrow after each step except the last one
            if (index < allSteps.length - 1) {
                const arrow = arrowTemplate.firstElementChild.cloneNode(true);
                step.parentNode.insertBefore(arrow, step.nextSibling);
            }
        });
        
        updateStepCounter();
        updateWorkflowPreview();
    }

    function addStep() {
        const newStep = stepTemplate.firstElementChild.cloneNode(true);
        
        // Add event listener for the select change
        const select = newStep.querySelector('select');
        select.addEventListener('change', updateWorkflowPreview);
        
        stepsContainer.appendChild(newStep);
        renumberSteps();
        
        // Scroll to the new step
        newStep.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Focus on the select element
        setTimeout(() => {
            select.focus();
        }, 100);
    }

    function removeStep(stepElement) {
        stepElement.remove();
        renumberSteps();
    }

    addStepBtn.addEventListener('click', addStep);

    stepsContainer.addEventListener('click', function(e) {
        if (e.target.closest('.remove-step-btn')) {
            const stepEntry = e.target.closest('.step-entry');
            removeStep(stepEntry);
        }
    });

    // Add event listeners for existing selects
    stepsContainer.addEventListener('change', function(e) {
        if (e.target.matches('select')) {
            updateWorkflowPreview();
        }
    });

    // Initial setup
    renumberSteps();
    updateWorkflowPreview();

    // Form validation
    window.validateForm = function() {
        const workflowName = document.getElementById('workflow_name').value.trim();
        const steps = stepsContainer.querySelectorAll('.step-entry');
        
        if (!workflowName) {
            alert('กรุณากรอกชื่อสายการอนุมัติ');
            document.getElementById('workflow_name').focus();
            return false;
        }
        
        if (workflowName.length > 100) {
            alert('ชื่อสายการอนุมัติต้องไม่เกิน 100 ตัวอักษร');
            document.getElementById('workflow_name').focus();
            return false;
        }
        
        if (steps.length === 0) {
            alert('กรุณาเพิ่มขั้นตอนการอนุมัติอย่างน้อย 1 ขั้นตอน');
            addStepBtn.focus();
            return false;
        }
        
        // Check if all steps have selected roles
        let hasEmptyStep = false;
        steps.forEach((step, index) => {
            const select = step.querySelector('select');
            if (!select.value) {
                alert(`กรุณาเลือกตำแหน่งผู้อนุมัติสำหรับขั้นตอนที่ ${index + 1}`);
                select.focus();
                hasEmptyStep = true;
                return false;
            }
        });
        
        if (hasEmptyStep) {
            return false;
        }
        
        return true;
    };

    // Auto-hide alerts
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

// Character counter for workflow name
document.getElementById('workflow_name').addEventListener('input', function() {
    const maxLength = 100;
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    if (remaining < 10) {
        this.style.borderColor = remaining < 0 ? '#ef4444' : '#f59e0b';
    } else {
        this.style.borderColor = '#e2e8f0';
    }
});
</script>

<?php require_once __DIR__ . '/../../layouts/footer.php'; ?>