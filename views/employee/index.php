<?php 
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/app.php';
}
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900">จัดการข้อมูลพนักงาน</h1>
            <p class="text-gray-500 mt-1">รายการพนักงานทั้งหมดในระบบ</p>
        </div>
        <!-- ===== เพิ่มปุ่มนำเข้าข้อมูล ===== -->
        <div class="flex items-center gap-2 mt-4 sm:mt-0">
            <a href="<?php echo BASE_URL; ?>/employee/import" class="w-full sm:w-auto bg-green-500 hover:bg-green-600 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center">
                <i class="fas fa-file-excel mr-2"></i>
                นำเข้าข้อมูล
            </a>
            <a href="<?php echo BASE_URL; ?>/employee/create" class="w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center">
                <i class="fas fa-plus mr-2"></i>
                เพิ่มพนักงานใหม่
            </a>
        </div>
    </div>

    <!-- Employee Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">รหัสพนักงาน</th>
                        <th scope="col" class="px-6 py-3">ชื่อ - นามสกุล</th>
                        <th scope="col" class="px-6 py-3">ตำแหน่ง</th>
                        <th scope="col" class="px-6 py-3">หน่วยงาน/ฝ่าย</th>
                        <th scope="col" class="px-6 py-3">สถานะ</th>
                        <th scope="col" class="px-6 py-3 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($num > 0): ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <?php extract($row); ?>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($employee_code); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($first_name_th . ' ' . $last_name_th); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($position_name ?? '-'); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($department_name ?? '-'); ?></td>
                                <td class="px-6 py-4">
                                    <?php 
                                        $status_class = 'bg-gray-100 text-gray-800';
                                        if ($status == 'ทำงาน') $status_class = 'bg-green-100 text-green-800';
                                        if ($status == 'ทดลองงาน') $status_class = 'bg-yellow-100 text-yellow-800';
                                        if ($status == 'ลาออก') $status_class = 'bg-red-100 text-red-800';
                                    ?>
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                     <!-- ===== จุดที่แก้ไข: แก้ไขลิงก์ทั้งหมด ===== -->
                                    <a href="<?php echo BASE_URL; ?>/employee/edit/<?php echo $id; ?>" class="font-medium text-yellow-600 hover:underline mr-3" title="แก้ไข"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo BASE_URL; ?>/employee/destroy/<?php echo $id; ?>" class="font-medium text-red-600 hover:underline" title="ลบ" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลพนักงานคนนี้?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="bg-white border-b">
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูลพนักงาน</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
