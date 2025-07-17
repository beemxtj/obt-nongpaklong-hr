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
