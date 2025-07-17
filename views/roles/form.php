<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 

// สำหรับหน้าแก้ไข
$current_permissions = isset($role->permissions) ? json_decode($role->permissions, true) : [];
if (!is_array($current_permissions)) {
    $current_permissions = [];
}
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title); ?></h1>
            <p class="text-gray-500 mt-1">กรอกข้อมูลบทบาทและกำหนดสิทธิ์</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/role" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">กลับ</a>
    </div>

    <!-- Role Form -->
    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
        <!-- ===== จุดที่แก้ไข: เปลี่ยน action ของฟอร์ม ===== -->
        <form action="<?php echo isset($role->id) ? BASE_URL . '/role/update' : BASE_URL . '/role/store'; ?>" method="POST">
            <?php if (isset($role->id)): ?>
                <input type="hidden" name="id" value="<?php echo $role->id; ?>">
            <?php endif; ?>

            <!-- Role Name -->
            <div class="mb-6">
                <label for="role_name" class="block text-sm font-medium text-gray-700">ชื่อบทบาท <span class="text-red-500">*</span></label>
                <!-- ===== จุดที่แก้ไข: เพิ่ม value ===== -->
                <input type="text" name="role_name" id="role_name" class="mt-1 block w-full md:w-1/2 border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($role->role_name ?? ''); ?>" required>
            </div>

            <!-- Permissions -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">สิทธิ์การใช้งาน (Permissions)</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                    <?php foreach ($permissions_list as $key => $label): ?>
                        <div class="relative flex items-start">
                            <div class="flex items-center h-5">
                                <!-- ===== จุดที่แก้ไข: เพิ่ม checked ===== -->
                                <input id="perm_<?php echo $key; ?>" name="permissions[]" value="<?php echo $key; ?>" type="checkbox" class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded" <?php echo in_array($key, $current_permissions) ? 'checked' : ''; ?>>
                            </div>
                            <div class="ml-3 text-sm">
                                <label for="perm_<?php echo $key; ?>" class="font-medium text-gray-700"><?php echo $label; ?></label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-8 pt-6 border-t flex justify-end">
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                    บันทึกข้อมูล
                </button>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
