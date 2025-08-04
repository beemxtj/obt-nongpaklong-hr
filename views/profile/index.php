<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title); ?></h1>
            <p class="text-gray-500 mt-1">ดูและแก้ไขข้อมูลส่วนตัวของคุณ</p>
        </div>
    </div>

    <!-- Display Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?php echo $_SESSION['success_message']; ?></p>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <!-- Profile Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
        <form action="<?php echo BASE_URL; ?>/profile/update" method="POST">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Profile Picture -->
                <div class="md:col-span-1 flex flex-col items-center">
                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($employee->first_name_th); ?>&size=128&background=e0e7ff&color=4f46e5" alt="User Avatar" class="w-32 h-32 rounded-full mb-4">
                    <button type="button" class="text-sm text-indigo-600 hover:underline">เปลี่ยนรูปโปรไฟล์</button>
                </div>

                <!-- User Details -->
                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label for="employee_code" class="block text-sm font-medium text-gray-500">รหัสพนักงาน</label>
                        <p class="mt-1 text-lg font-semibold"><?php echo htmlspecialchars($employee->employee_code); ?></p>
                    </div>
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-500">อีเมล</label>
                        <p class="mt-1 text-lg font-semibold"><?php echo htmlspecialchars($employee->email); ?></p>
                    </div>
                    <div>
                        <label for="first_name_th" class="block text-sm font-medium text-gray-700">ชื่อ</label>
                        <input type="text" name="first_name_th" id="first_name_th" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($employee->first_name_th); ?>">
                    </div>
                    <div>
                        <label for="last_name_th" class="block text-sm font-medium text-gray-700">นามสกุล</label>
                        <input type="text" name="last_name_th" id="last_name_th" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($employee->last_name_th); ?>">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="phone_number" class="block text-sm font-medium text-gray-700">เบอร์โทรศัพท์</label>
                        <input type="text" name="phone_number" id="phone_number" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($employee->phone_number ?? ''); ?>">
                    </div>
                    <div class="sm:col-span-2">
                        <label for="password" class="block text-sm font-medium text-gray-700">เปลี่ยนรหัสผ่านใหม่</label>
                        <input type="password" name="password" id="password" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" placeholder="เว้นว่างไว้หากไม่ต้องการเปลี่ยน">
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 pt-6 border-t flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                    บันทึกการเปลี่ยนแปลง
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
