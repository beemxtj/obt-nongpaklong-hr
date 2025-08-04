<<<<<<< HEAD
<?php
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/app.php';
}
require_once __DIR__ . '/../layouts/header.php';
?>
<style>
    /* Enhanced Custom styles for modern UI */
    .gradient-bg {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .tab-active {
        border-color: #667eea;
        color: #667eea;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        position: relative;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .tab-active::after {
        content: '';
        position: absolute;
        left: 0;
        right: 0;
        bottom: -2px;
        height: 4px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 2px 2px 0 0;
    }

    .tab-button {
        transition: all 0.3s ease;
        border-radius: 12px 12px 0 0;
    }

    .tab-button:hover:not(.tab-active) {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        transform: translateY(-1px);
        color: #4f46e5;
    }

    .tab-content {
        display: none;
        animation: fadeIn 0.5s ease-in;
    }

    .tab-content.active {
        display: block;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .form-input {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: 2px solid #e2e8f0;
        padding: 12px 16px;
    }

    .form-input:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        outline: none;
        transform: translateY(-1px);
    }

    .form-input:hover {
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
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        color: #374151;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
    }

    .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #cbd5e1;
    }

    .profile-upload-area {
        position: relative;
        transition: all 0.3s ease;
        border-radius: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 24px;
        border: 2px dashed #cbd5e1;
    }

    .profile-upload-area:hover {
        border-color: #667eea;
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.1);
    }

    .profile-image {
        transition: all 0.3s ease;
        border: 4px solid #e2e8f0;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .profile-image:hover {
        border-color: #667eea;
        transform: scale(1.05);
        box-shadow: 0 12px 24px rgba(102, 126, 234, 0.2);
    }

    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
        margin-bottom: 24px;
        display: flex;
        align-items: center;
    }

    .section-header i {
        margin-right: 12px;
        color: #667eea;
        -webkit-text-fill-color: #667eea;
    }

    .form-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 24px;
    }

    .form-group {
        position: relative;
    }

    .required-asterisk {
        color: #ef4444;
        font-weight: bold;
        margin-left: 4px;
    }

    .floating-header {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        border-radius: 20px;
        margin-bottom: 32px;
        padding: 24px;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border-left: 4px solid #10b981;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        animation: slideIn 0.5s ease-out;
    }

    .alert-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-left: 4px solid #ef4444;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        animation: slideIn 0.5s ease-out;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateX(-20px);
        }

        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .form-container {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .tab-navigation {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .form-content {
        padding: 32px;
    }

    .form-footer {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        padding: 24px 32px;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 16px;
    }

    .animate-fade-in {
        animation: fadeIn 0.6s ease-in;
    }

    .animate-slide-up {
        animation: slideUp 0.6s ease-out;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
<?php
require_once __DIR__ . '/../layouts/sidebar.php';

// Check if we are in edit mode
$is_edit_mode = isset($employee) && isset($employee->id) && !empty($employee->id);

// Set profile image URL
$profile_image_url = BASE_URL . '/' . (isset($employee->profile_image_path) && $employee->profile_image_path ? $employee->profile_image_path : 'assets/images/default-profile.png');

// Helper function to get default value for fields
function get_field_value($employee, $field_name, $default = '')
{
    return (isset($employee) && isset($employee->$field_name)) ? htmlspecialchars($employee->$field_name) : $default;
}

// Fetch dropdown data - Create new instances for dropdown data
$dropdown_employee = new Employee($db ?? $this->db);
$positions_stmt = $dropdown_employee->readPositions();
$departments_stmt = $dropdown_employee->readDepartments();
$roles_stmt = $dropdown_employee->readRoles();
$supervisor_list_stmt = $dropdown_employee->getSupervisorList($is_edit_mode && isset($employee->id) ? $employee->id : null);
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <!-- Enhanced Header Section -->
    <div class="floating-header animate-fade-in">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    <?php echo htmlspecialchars($page_title); ?>
                </h1>
                <p class="text-gray-600 mt-2 text-lg flex items-center">
                    <i class="fas fa-user-edit mr-2 text-indigo-600"></i>
                    จัดการข้อมูลพนักงานอย่างมีประสิทธิภาพ
                </p>
            </div>
            <a href="<?php echo BASE_URL; ?>/employee" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>กลับ
            </a>
        </div>
    </div>

    <!-- Enhanced Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert-success" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-3 text-xl"></i>
                <p class="font-medium text-green-800"><?php echo $_SESSION['success_message']; ?></p>
                <button type="button" class="ml-auto text-green-600 hover:text-green-800 text-xl" onclick="this.parentElement.parentElement.style.display='none';">&times;</button>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert-error" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 mr-3 text-xl"></i>
                <p class="font-medium text-red-800"><?php echo $_SESSION['error_message']; ?></p>
                <button type="button" class="ml-auto text-red-600 hover:text-red-800 text-xl" onclick="this.parentElement.parentElement.style.display='none';">&times;</button>
            </div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <form action="<?php echo $is_edit_mode && isset($employee->id) ? BASE_URL . '/employee/save/' . $employee->id : BASE_URL . '/employee/store'; ?>" method="POST" enctype="multipart/form-data" class="animate-slide-up">
        <div class="form-container">
            <!-- Enhanced Tab Navigation -->
            <div class="tab-navigation">
                <nav class="flex space-x-4" role="tablist">
                    <button type="button" class="tab-button tab-active px-6 py-3 text-sm font-semibold transition-all duration-300" data-tab="profile">
                        <i class="fas fa-user-circle mr-2"></i>ข้อมูลโปรไฟล์
                    </button>
                    <button type="button" class="tab-button px-6 py-3 text-sm font-semibold transition-all duration-300" data-tab="personal">
                        <i class="fas fa-id-card mr-2"></i>ข้อมูลส่วนตัว
                    </button>
                    <button type="button" class="tab-button px-6 py-3 text-sm font-semibold transition-all duration-300" data-tab="address">
                        <i class="fas fa-map-marker-alt mr-2"></i>ที่อยู่
                    </button>
                    <button type="button" class="tab-button px-6 py-3 text-sm font-semibold transition-all duration-300" data-tab="work">
                        <i class="fas fa-briefcase mr-2"></i>ข้อมูลการทำงาน
                    </button>
                </nav>
            </div>

            <div class="form-content">
                <!-- Profile Tab -->
                <div id="profile" class="tab-content active">
                    <h2 class="section-header text-2xl">
                        <i class="fas fa-user-circle"></i>ข้อมูลโปรไฟล์
                    </h2>
                    <div class="form-grid">
                        <div class="profile-upload-area text-center">
                            <label for="profile_image" class="form-label text-center justify-center">
                                <i class="fas fa-camera"></i>รูปโปรไฟล์
                            </label>
                            <div class="mt-4 flex justify-center">
                                <img id="image-preview" src="<?php echo $profile_image_url; ?>" alt="Profile Preview" class="w-32 h-32 rounded-full object-cover profile-image">
                            </div>
                            <input type="file" name="profile_image" id="profile_image" class="mt-4 block text-sm text-gray-600 file:mr-4 file:py-3 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer transition-all">
                            <input type="hidden" name="current_profile_image_path" value="<?php echo get_field_value($employee, 'profile_image_path'); ?>">
                            <p class="text-sm text-gray-500 mt-2">คลิกเพื่อเลือกรูปภาพ (JPG, PNG)</p>
                        </div>

                        <div class="space-y-6">
                            <div class="form-group">
                                <label for="employee_code" class="form-label">
                                    <i class="fas fa-id-badge"></i>รหัสพนักงาน
                                </label>
                                <input type="text" name="employee_code" id="employee_code" class="form-input w-full bg-gray-100 cursor-not-allowed" value="<?php echo get_field_value($employee, 'employee_code'); ?>" readonly>
                            </div>
                            <div class="form-group">
                                <label for="password" class="form-label">
                                    <i class="fas fa-lock"></i>รหัสผ่าน
                                    <?php if ($is_edit_mode): ?>
                                        <span class="text-sm text-gray-500">(เว้นว่างหากไม่ต้องการเปลี่ยน)</span>
                                    <?php else: ?>
                                        <span class="required-asterisk">*</span>
                                    <?php endif; ?>
                                </label>
                                <input type="password" name="password" id="password" class="form-input w-full" <?php echo $is_edit_mode ? '' : 'required'; ?>>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Personal Tab -->
                <div id="personal" class="tab-content">
                    <h2 class="section-header text-2xl">
                        <i class="fas fa-id-card"></i>ข้อมูลส่วนตัว
                    </h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="prefix" class="form-label">
                                <i class="fas fa-user-tag"></i>คำนำหน้า <span class="required-asterisk">*</span>
                            </label>
                            <select name="prefix" id="prefix" class="form-input w-full" required>
                                <option value="">เลือกคำนำหน้า</option>
                                <option value="นาย" <?php echo (isset($employee->prefix) && $employee->prefix == 'นาย') ? 'selected' : ''; ?>>นาย</option>
                                <option value="นาง" <?php echo (isset($employee->prefix) && $employee->prefix == 'นาง') ? 'selected' : ''; ?>>นาง</option>
                                <option value="นางสาว" <?php echo (isset($employee->prefix) && $employee->prefix == 'นางสาว') ? 'selected' : ''; ?>>นางสาว</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="first_name_th" class="form-label">
                                <i class="fas fa-signature"></i>ชื่อ (ภาษาไทย) <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" name="first_name_th" id="first_name_th" class="form-input w-full" value="<?php echo get_field_value($employee, 'first_name_th'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="last_name_th" class="form-label">
                                <i class="fas fa-signature"></i>นามสกุล (ภาษาไทย) <span class="required-asterisk">*</span>
                            </label>
                            <input type="text" name="last_name_th" id="last_name_th" class="form-input w-full" value="<?php echo get_field_value($employee, 'last_name_th'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="first_name_en" class="form-label">
                                <i class="fas fa-font"></i>ชื่อ (ภาษาอังกฤษ)
                            </label>
                            <input type="text" name="first_name_en" id="first_name_en" class="form-input w-full" value="<?php echo get_field_value($employee, 'first_name_en'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="last_name_en" class="form-label">
                                <i class="fas fa-font"></i>นามสกุล (ภาษาอังกฤษ)
                            </label>
                            <input type="text" name="last_name_en" id="last_name_en" class="form-input w-full" value="<?php echo get_field_value($employee, 'last_name_en'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="gender" class="form-label">
                                <i class="fas fa-venus-mars"></i>เพศ
                            </label>
                            <select name="gender" id="gender" class="form-input w-full">
                                <option value="">เลือกเพศ</option>
                                <option value="ชาย" <?php echo (isset($employee->gender) && $employee->gender == 'ชาย') ? 'selected' : ''; ?>>ชาย</option>
                                <option value="หญิง" <?php echo (isset($employee->gender) && $employee->gender == 'หญิง') ? 'selected' : ''; ?>>หญิง</option>
                                <option value="อื่นๆ" <?php echo (isset($employee->gender) && $employee->gender == 'อื่นๆ') ? 'selected' : ''; ?>>อื่นๆ</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="birth_date" class="form-label">
                                <i class="fas fa-birthday-cake"></i>วันเกิด
                            </label>
                            <input type="date" name="birth_date" id="birth_date" class="form-input w-full" value="<?php echo get_field_value($employee, 'birth_date'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="nationality" class="form-label">
                                <i class="fas fa-flag"></i>สัญชาติ
                            </label>
                            <input type="text" name="nationality" id="nationality" class="form-input w-full" value="<?php echo get_field_value($employee, 'nationality'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i>อีเมล <span class="required-asterisk">*</span>
                            </label>
                            <input type="email" name="email" id="email" class="form-input w-full" value="<?php echo get_field_value($employee, 'email'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="phone_number" class="form-label">
                                <i class="fas fa-phone"></i>เบอร์โทรศัพท์
                            </label>
                            <input type="text" name="phone_number" id="phone_number" class="form-input w-full" value="<?php echo get_field_value($employee, 'phone_number'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="work_phone" class="form-label">
                                <i class="fas fa-phone-office"></i>เบอร์โทรศัพท์ที่ทำงาน
                            </label>
                            <input type="text" name="work_phone" id="work_phone" class="form-input w-full" value="<?php echo get_field_value($employee, 'work_phone'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="national_id" class="form-label">
                                <i class="fas fa-id-card"></i>เลขบัตรประชาชน
                            </label>
                            <input type="text" name="national_id" id="national_id" class="form-input w-full" value="<?php echo get_field_value($employee, 'national_id'); ?>">
                        </div>
                    </div>
                </div>

                <!-- Address Tab -->
                <div id="address" class="tab-content">
                    <h2 class="section-header text-2xl">
                        <i class="fas fa-map-marker-alt"></i>ที่อยู่
                    </h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="address_line1" class="form-label">
                                <i class="fas fa-home"></i>ที่อยู่
                            </label>
                            <input type="text" name="address_line1" id="address_line1" class="form-input w-full" value="<?php echo get_field_value($employee, 'address_line1'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="district" class="form-label">
                                <i class="fas fa-map"></i>อำเภอ/เขต
                            </label>
                            <input type="text" name="district" id="district" class="form-input w-full" value="<?php echo get_field_value($employee, 'district'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="province" class="form-label">
                                <i class="fas fa-map-marked-alt"></i>จังหวัด
                            </label>
                            <input type="text" name="province" id="province" class="form-input w-full" value="<?php echo get_field_value($employee, 'province'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="postal_code" class="form-label">
                                <i class="fas fa-mail-bulk"></i>รหัสไปรษณีย์
                            </label>
                            <input type="text" name="postal_code" id="postal_code" class="form-input w-full" value="<?php echo get_field_value($employee, 'postal_code'); ?>">
                        </div>
                    </div>
                </div>

                <!-- Work Tab -->
                <div id="work" class="tab-content">
                    <h2 class="section-header text-2xl">
                        <i class="fas fa-briefcase"></i>ข้อมูลการทำงาน
                    </h2>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="start_date" class="form-label">
                                <i class="fas fa-calendar-plus"></i>วันที่เริ่มงาน <span class="required-asterisk">*</span>
                            </label>
                            <input type="date" name="start_date" id="start_date" class="form-input w-full" value="<?php echo get_field_value($employee, 'start_date'); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="probation_days" class="form-label">
                                <i class="fas fa-clock"></i>จำนวนวันทดลองงาน
                            </label>
                            <input type="number" name="probation_days" id="probation_days" class="form-input w-full" value="<?php echo get_field_value($employee, 'probation_days', '0'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="position_id" class="form-label">
                                <i class="fas fa-user-tie"></i>ตำแหน่ง <span class="required-asterisk">*</span>
                            </label>
                            <select name="position_id" id="position_id" class="form-input w-full" required>
                                <option value="">เลือกตำแหน่ง</option>
                                <?php while ($row = $positions_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?php echo htmlspecialchars($row['id']); ?>" <?php echo (isset($employee->position_id) && $employee->position_id == $row['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['name_th']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="department_id" class="form-label">
                                <i class="fas fa-building"></i>แผนก <span class="required-asterisk">*</span>
                            </label>
                            <select name="department_id" id="department_id" class="form-input w-full" required>
                                <option value="">เลือกแผนก</option>
                                <?php while ($row = $departments_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?php echo htmlspecialchars($row['id']); ?>" <?php echo (isset($employee->department_id) && $employee->department_id == $row['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['name_th']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="supervisor_id" class="form-label">
                                <i class="fas fa-user-crown"></i>หัวหน้างาน
                            </label>
                            <select name="supervisor_id" id="supervisor_id" class="form-input w-full">
                                <option value="">ไม่มีหัวหน้างาน</option>
                                <?php while ($row = $supervisor_list_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?php echo htmlspecialchars($row['id']); ?>" <?php echo (isset($employee->supervisor_id) && $employee->supervisor_id == $row['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['full_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="role_id" class="form-label">
                                <i class="fas fa-user-shield"></i>บทบาท (Role) <span class="required-asterisk">*</span>
                            </label>
                            <select name="role_id" id="role_id" class="form-input w-full" required>
                                <option value="">เลือกบทบาท</option>
                                <?php while ($row = $roles_stmt->fetch(PDO::FETCH_ASSOC)): ?>
                                    <option value="<?php echo htmlspecialchars($row['id']); ?>" <?php echo (isset($employee->role_id) && $employee->role_id == $row['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($row['role_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="salary" class="form-label">
                                <i class="fas fa-money-bill-wave"></i>เงินเดือน
                            </label>
                            <input type="number" step="0.01" name="salary" id="salary" class="form-input w-full" value="<?php echo get_field_value($employee, 'salary', '0.00'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="bank_name" class="form-label">
                                <i class="fas fa-university"></i>ชื่อธนาคาร
                            </label>
                            <input type="text" name="bank_name" id="bank_name" class="form-input w-full" value="<?php echo get_field_value($employee, 'bank_name'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="bank_account_number" class="form-label">
                                <i class="fas fa-credit-card"></i>เลขบัญชีธนาคาร
                            </label>
                            <input type="text" name="bank_account_number" id="bank_account_number" class="form-input w-full" value="<?php echo get_field_value($employee, 'bank_account_number'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="tax_id" class="form-label">
                                <i class="fas fa-receipt"></i>เลขประจำตัวผู้เสียภาษี
                            </label>
                            <input type="text" name="tax_id" id="tax_id" class="form-input w-full" value="<?php echo get_field_value($employee, 'tax_id'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="provident_fund_rate_employee" class="form-label">
                                <i class="fas fa-piggy-bank"></i>อัตรากองทุนสำรองเลี้ยงชีพ (พนักงาน) (%)
                            </label>
                            <input type="number" step="0.01" name="provident_fund_rate_employee" id="provident_fund_rate_employee" class="form-input w-full" value="<?php echo get_field_value($employee, 'provident_fund_rate_employee', '0.00'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="provident_fund_rate_company" class="form-label">
                                <i class="fas fa-building"></i>อัตรากองทุนสำรองเลี้ยงชีพ (บริษัท) (%)
                            </label>
                            <input type="number" step="0.01" name="provident_fund_rate_company" id="provident_fund_rate_company" class="form-input w-full" value="<?php echo get_field_value($employee, 'provident_fund_rate_company', '0.00'); ?>">
                        </div>
                        <div class="form-group">
                            <label for="status" class="form-label">
                                <i class="fas fa-info-circle"></i>สถานะ <span class="required-asterisk">*</span>
                            </label>
                            <select name="status" id="status" class="form-input w-full" required>
                                <option value="ทำงาน" <?php echo (isset($employee->status) && $employee->status == 'ทำงาน') ? 'selected' : ''; ?>>ทำงาน</option>
                                <option value="พักงาน" <?php echo (isset($employee->status) && $employee->status == 'พักงาน') ? 'selected' : ''; ?>>พักงาน</option>
                                <option value="ลาออก" <?php echo (isset($employee->status) && $employee->status == 'ลาออก') ? 'selected' : ''; ?>>ลาออก</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Form Footer -->
            <div class="form-footer">
                <a href="<?php echo BASE_URL; ?>/employee" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>ยกเลิก
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>บันทึกข้อมูล
                </button>
            </div>
        </div>
    </form>
</main>

<script>
    // เพิ่ม JavaScript ที่ปรับปรุงแล้วใน form.php (แทนที่ script เดิม)

    document.addEventListener('DOMContentLoaded', function() {
        // Enhanced Image preview and validation script
        const profileImageInput = document.getElementById('profile_image');
        const imagePreview = document.getElementById('image-preview');
        const form = document.querySelector('form');

        // File validation function
        function validateImageFile(file) {
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            const allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

            // ตรวจสอบประเภทไฟล์
            if (!allowedTypes.includes(file.type)) {
                return 'ประเภทไฟล์ไม่ถูกต้อง กรุณาเลือกไฟล์ JPG, PNG หรือ GIF เท่านั้น';
            }

            // ตรวจสอบนามสกุลไฟล์
            const extension = file.name.split('.').pop().toLowerCase();
            if (!allowedExtensions.includes(extension)) {
                return 'นามสกุลไฟล์ไม่ถูกต้อง กรุณาเลือกไฟล์ .jpg, .jpeg, .png หรือ .gif เท่านั้น';
            }

            // ตรวจสอบขนาดไฟล์
            if (file.size > maxSize) {
                return 'ไฟล์ใหญ่เกินไป กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 5MB';
            }

            return null; // ไม่มี error
        }

        // Show error message
        function showError(message) {
            // ลบ error message เก่า
            const existingError = document.querySelector('.image-upload-error');
            if (existingError) {
                existingError.remove();
            }

            // สร้าง error message ใหม่
            const errorDiv = document.createElement('div');
            errorDiv.className = 'image-upload-error alert-error mt-2';
            errorDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-600 mr-2"></i>
                <span class="text-red-800">${message}</span>
            </div>
        `;

            // แสดง error ใต้ upload area
            const uploadArea = document.querySelector('.profile-upload-area');
            uploadArea.appendChild(errorDiv);

            // Auto remove after 5 seconds
            setTimeout(() => {
                if (errorDiv.parentNode) {
                    errorDiv.remove();
                }
            }, 5000);
        }

        // Show success message
        function showSuccess(message) {
            // ลบ message เก่า
            const existingMessages = document.querySelectorAll('.image-upload-error, .image-upload-success');
            existingMessages.forEach(msg => msg.remove());

            const successDiv = document.createElement('div');
            successDiv.className = 'image-upload-success alert-success mt-2';
            successDiv.innerHTML = `
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 mr-2"></i>
                <span class="text-green-800">${message}</span>
            </div>
        `;

            const uploadArea = document.querySelector('.profile-upload-area');
            uploadArea.appendChild(successDiv);

            setTimeout(() => {
                if (successDiv.parentNode) {
                    successDiv.remove();
                }
            }, 3000);
        }

        profileImageInput.addEventListener('change', function() {
            const file = this.files[0];

            if (file) {
                // ตรวจสอบไฟล์
                const error = validateImageFile(file);
                if (error) {
                    showError(error);
                    // รีเซ็ต input
                    this.value = '';
                    // คืนค่ารูปเดิม
                    imagePreview.src = '<?php echo $profile_image_url; ?>';
                    return;
                }

                // แสดงรูปตัวอย่าง
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        imagePreview.style.transform = 'scale(1)';
                    }, 300);
                }
                reader.readAsDataURL(file);

                showSuccess('เลือกรูปภาพสำเร็จ');
            } else {
                imagePreview.src = '<?php echo $profile_image_url; ?>';
            }
        });

        // Enhanced Tab switching script
        const tabButtons = document.querySelectorAll('.tab-button');
        const tabContents = document.querySelectorAll('.tab-content');

        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('tab-active'));
                tabContents.forEach(content => content.classList.remove('active'));

                // Add active class to clicked button and corresponding content
                this.classList.add('tab-active');
                const tabId = this.getAttribute('data-tab');
                const targetContent = document.getElementById(tabId);
                targetContent.classList.add('active');

                // Add animation effect
                targetContent.style.opacity = '0';
                targetContent.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    targetContent.style.opacity = '1';
                    targetContent.style.transform = 'translateY(0)';
                }, 50);
            });
        });

        // Enhanced form validation
        const inputs = form.querySelectorAll('input[required], select[required]');

        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });

            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.style.borderColor = '#10b981';
                    this.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.1)';
                }
            });
        });

        function validateField(field) {
            if (field.value.trim() === '') {
                field.style.borderColor = '#ef4444';
                field.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.1)';
                return false;
            } else {
                field.style.borderColor = '#10b981';
                field.style.boxShadow = '0 0 0 4px rgba(16, 185, 129, 0.1)';
                return true;
            }
        }

        // Form submission validation
        form.addEventListener('submit', function(e) {
            let isValid = true;
            const requiredInputs = form.querySelectorAll('input[required], select[required]');

            // ตรวจสอบ required fields
            requiredInputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });

            // ตรวจสอบอีเมล
            const emailInput = document.getElementById('email');
            if (emailInput.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(emailInput.value)) {
                    emailInput.style.borderColor = '#ef4444';
                    emailInput.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.1)';
                    showError('รูปแบบอีเมลไม่ถูกต้อง');
                    isValid = false;
                }
            }

            // ตรวจสอบเบอร์โทรศัพท์
            const phoneInput = document.getElementById('phone_number');
            if (phoneInput.value) {
                const phoneRegex = /^[0-9-+\s()]+$/;
                if (!phoneRegex.test(phoneInput.value)) {
                    phoneInput.style.borderColor = '#ef4444';
                    phoneInput.style.boxShadow = '0 0 0 4px rgba(239, 68, 68, 0.1)';
                    showError('รูปแบบเบอร์โทรศัพท์ไม่ถูกต้อง');
                    isValid = false;
                }
            }

            if (!isValid) {
                e.preventDefault();

                // แสดง error message
                showError('กรุณากรอกข้อมูลให้ครบถ้วนและถูกต้อง');

                // หาก field ที่ผิดอยู่ใน tab อื่น ให้เปลี่ยน tab
                const invalidField = form.querySelector('input[required]:invalid, select[required]:invalid, input[style*="border-color: rgb(239, 68, 68)"]');
                if (invalidField) {
                    const tabContent = invalidField.closest('.tab-content');
                    if (tabContent && !tabContent.classList.contains('active')) {
                        const tabId = tabContent.id;
                        const tabButton = document.querySelector(`[data-tab="${tabId}"]`);
                        if (tabButton) {
                            tabButton.click();
                        }
                    }

                    // Focus ไปที่ field ที่ผิด
                    setTimeout(() => {
                        invalidField.focus();
                    }, 100);
                }

                return false;
            }

            // แสดง loading state
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            submitButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>กำลังบันทึก...';
            submitButton.disabled = true;

            // หากมีการอัปโหลดรูป แสดงข้อความเตือน
            if (profileImageInput.files.length > 0) {
                const uploadMessage = document.createElement('div');
                uploadMessage.className = 'fixed top-4 right-4 bg-blue-500 text-white p-4 rounded-lg shadow-lg z-50';
                uploadMessage.innerHTML = `
                <div class="flex items-center">
                    <i class="fas fa-upload fa-spin mr-2"></i>
                    กำลังอัปโหลดรูปภาพ...
                </div>
            `;
                document.body.appendChild(uploadMessage);
            }
        });

        // Auto-dismiss alerts
        const alerts = document.querySelectorAll('.alert-success, .alert-error');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(-20px)';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 300);
            }, 5000);
        });

        // Add drag and drop functionality for image upload
        const uploadArea = document.querySelector('.profile-upload-area');

        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, preventDefaults, false);
        });

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadArea.addEventListener(eventName, unhighlight, false);
        });

        function highlight() {
            uploadArea.style.borderColor = '#667eea';
            uploadArea.style.backgroundColor = 'rgba(102, 126, 234, 0.1)';
        }

        function unhighlight() {
            uploadArea.style.borderColor = '#cbd5e1';
            uploadArea.style.backgroundColor = '';
        }

        uploadArea.addEventListener('drop', handleDrop, false);

        function handleDrop(e) {
            const dt = e.dataTransfer;
            const files = dt.files;

            if (files.length > 0) {
                profileImageInput.files = files;
                profileImageInput.dispatchEvent(new Event('change'));
            }
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
=======
<?php 
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/app.php';
}
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title ?? 'จัดการข้อมูลพนักงาน'); ?></h1>
            <p class="text-gray-500 mt-1">กรุณากรอกข้อมูลให้ครบถ้วน</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/employee" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">กลับ</a>
    </div>

    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
        <form action="<?php echo BASE_URL; ?><?php echo isset($employee->id) ? '/employee/update' : '/employee/store'; ?>" method="POST">
            <?php if (isset($employee->id)): ?>
                <input type="hidden" name="id" value="<?php echo $employee->id; ?>">
            <?php endif; ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- รหัสพนักงาน -->
                <div class="md:col-span-2">
                    <label for="employee_code" class="block text-sm font-medium text-gray-700">รหัสพนักงาน (สร้างอัตโนมัติ)</label>
                    <input 
                        type="text" 
                        name="employee_code" 
                        id="employee_code" 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-100" 
                        value="<?php echo htmlspecialchars($employee->employee_code ?? ''); ?>" 
                        readonly>
                </div>
                <!-- คำนำหน้าชื่อ -->
                <div>
                    <label for="prefix" class="block text-sm font-medium text-gray-700">คำนำหน้าชื่อ <span class="text-red-500">*</span></label>
                    <select id="prefix" name="prefix" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="นาย" <?php echo (isset($employee->prefix) && $employee->prefix == 'นาย') ? 'selected' : ''; ?>>นาย</option>
                        <option value="นาง" <?php echo (isset($employee->prefix) && $employee->prefix == 'นาง') ? 'selected' : ''; ?>>นาง</option>
                        <option value="นางสาว" <?php echo (isset($employee->prefix) && $employee->prefix == 'นางสาว') ? 'selected' : ''; ?>>นางสาว</option>
                    </select>
                </div>
                <!-- ชื่อ -->
                <div>
                    <label for="first_name_th" class="block text-sm font-medium text-gray-700">ชื่อ (ไทย) <span class="text-red-500">*</span></label>
                    <input type="text" name="first_name_th" id="first_name_th" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($employee->first_name_th ?? ''); ?>" required>
                </div>
                <!-- นามสกุล -->
                <div>
                    <label for="last_name_th" class="block text-sm font-medium text-gray-700">นามสกุล (ไทย) <span class="text-red-500">*</span></label>
                    <input type="text" name="last_name_th" id="last_name_th" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($employee->last_name_th ?? ''); ?>" required>
                </div>
                <!-- อีเมล -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">อีเมล (สำหรับ Login) <span class="text-red-500">*</span></label>
                    <input type="email" name="email" id="email" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($employee->email ?? ''); ?>" required>
                </div>
                <!-- รหัสผ่าน -->
                <div class="md:col-span-2">
                    <label for="password" class="block text-sm font-medium text-gray-700">รหัสผ่านใหม่</label>
                    <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="เว้นว่างไว้หากไม่ต้องการเปลี่ยน">
                </div>
                <hr class="md:col-span-2 my-4">
                <!-- ตำแหน่ง -->
                <div>
                    <label for="position_id" class="block text-sm font-medium text-gray-700">ตำแหน่ง <span class="text-red-500">*</span></label>
                    <select id="position_id" name="position_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">-- กรุณาเลือก --</option>
                        <?php while ($row = $positions->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo (isset($employee->position_id) && $employee->position_id == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['name_th']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <!-- หน่วยงาน/ฝ่าย -->
                <div>
                    <label for="department_id" class="block text-sm font-medium text-gray-700">หน่วยงาน/ฝ่าย <span class="text-red-500">*</span></label>
                    <select id="department_id" name="department_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                         <option value="">-- กรุณาเลือก --</option>
                        <?php while ($row = $departments->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo (isset($employee->department_id) && $employee->department_id == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['name_th']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <!-- หัวหน้างาน -->
                <div>
                    <label for="supervisor_id" class="block text-sm font-medium text-gray-700">หัวหน้างาน</label>
                    <select id="supervisor_id" name="supervisor_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">-- ไม่มี --</option>
                        <?php while ($row = $supervisors->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo (isset($employee->supervisor_id) && $employee->supervisor_id == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['first_name_th'] . ' ' . $row['last_name_th']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                 <!-- ระดับสิทธิ์ -->
                <div>
                    <label for="role_id" class="block text-sm font-medium text-gray-700">ระดับสิทธิ์ <span class="text-red-500">*</span></label>
                    <select id="role_id" name="role_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="">-- กรุณาเลือก --</option>
                        <?php while ($row = $roles->fetch(PDO::FETCH_ASSOC)): ?>
                            <option value="<?php echo $row['id']; ?>" <?php echo (isset($employee->role_id) && $employee->role_id == $row['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($row['role_name']); ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <!-- วันที่เริ่มงาน -->
                <div>
                    <label for="start_date" class="block text-sm font-medium text-gray-700">วันที่เริ่มงาน <span class="text-red-500">*</span></label>
                    <input type="date" name="start_date" id="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($employee->start_date ?? ''); ?>" required>
                </div>
                <!-- สถานะ -->
                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700">สถานะ <span class="text-red-500">*</span></label>
                    <select id="status" name="status" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" required>
                        <option value="ทดลองงาน" <?php echo (isset($employee->status) && $employee->status == 'ทดลองงาน') ? 'selected' : ''; ?>>ทดลองงาน</option>
                        <option value="ทำงาน" <?php echo (isset($employee->status) && $employee->status == 'ทำงาน') ? 'selected' : ''; ?>>ทำงาน</option>
                        <option value="พักงาน" <?php echo (isset($employee->status) && $employee->status == 'พักงาน') ? 'selected' : ''; ?>>พักงาน</option>
                        <option value="ลาออก" <?php echo (isset($employee->status) && $employee->status == 'ลาออก') ? 'selected' : ''; ?>>ลาออก</option>
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
