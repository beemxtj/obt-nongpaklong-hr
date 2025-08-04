<?php
// views/settings/leave_policy.php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>
<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <h1 class="text-2xl md:text-3xl font-bold text-indigo-900 mb-6"><?php echo htmlspecialchars($page_title); ?></h1>

    <form action="<?php echo BASE_URL; ?>/leavepolicy/update" method="POST">
        <div class="space-y-6">
            <?php while ($policy = $policies_stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <div class="bg-white rounded-2xl shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 border-b pb-3 mb-4">
                    <?php echo htmlspecialchars($policy['leave_type_name']); ?>
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 items-center">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">จำนวนวันลาต่อปี</label>
                        <input type="number" step="0.5" name="policies[<?php echo $policy['leave_type_id']; ?>][days_allowed_per_year]"
                               value="<?php echo htmlspecialchars($policy['days_allowed_per_year']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                    <div class="pt-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="policies[<?php echo $policy['leave_type_id']; ?>][is_unlimited]" value="1"
                                   <?php echo $policy['is_unlimited'] ? 'checked' : ''; ?> class="h-4 w-4 text-indigo-600 rounded">
                            <span class="ml-2 text-sm text-gray-700">ไม่จำกัดวันลา</span>
                        </label>
                    </div>
                    <div class="pt-6">
                        <label class="flex items-center">
                            <input type="checkbox" name="policies[<?php echo $policy['leave_type_id']; ?>][can_be_carried_over]" value="1"
                                   <?php echo $policy['can_be_carried_over'] ? 'checked' : ''; ?> class="h-4 w-4 text-indigo-600 rounded">
                            <span class="ml-2 text-sm text-gray-700">ทบวันลาไปปีถัดไป</span>
                        </label>
                    </div>
                     <div>
                        <label class="block text-sm font-medium text-gray-700">จำนวนวันที่ทบได้สูงสุด</label>
                        <input type="number" step="0.5" name="policies[<?php echo $policy['leave_type_id']; ?>][max_carry_over_days]"
                               value="<?php echo htmlspecialchars($policy['max_carry_over_days']); ?>" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>

        <div class="mt-8 flex justify-end">
            <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-6 rounded-lg">
                บันทึกการตั้งค่าทั้งหมด
            </button>
        </div>
    </form>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>