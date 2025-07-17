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
