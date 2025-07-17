<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title); ?></h1>
            <p class="text-gray-500 mt-1">กำหนดบทบาทและสิทธิ์การเข้าถึงเมนูต่างๆ</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/role/create" class="mt-4 sm:mt-0 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i>
            เพิ่มบทบาทใหม่
        </a>
    </div>

    <!-- Roles Table -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">ID</th>
                        <th scope="col" class="px-6 py-3">ชื่อบทบาท (Role Name)</th>
                        <th scope="col" class="px-6 py-3">สิทธิ์การใช้งาน</th>
                        <th scope="col" class="px-6 py-3 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($num > 0): ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4"><?php echo $row['id']; ?></td>
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($row['role_name']); ?></td>
                                <td class="px-6 py-4">
                                    <?php 
                                        $permissions = json_decode($row['permissions'], true);
                                        if (is_array($permissions) && !empty($permissions)) {
                                            foreach($permissions as $p) {
                                                echo '<span class="bg-gray-200 text-gray-700 text-xs font-medium mr-2 px-2.5 py-0.5 rounded">' . htmlspecialchars($p) . '</span>';
                                            }
                                        } else {
                                            echo '<span class="text-gray-400">ไม่มีสิทธิ์เฉพาะ</span>';
                                        }
                                    ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?php echo BASE_URL; ?>/role/edit/<?php echo $row['id']; ?>" class="font-medium text-yellow-600 hover:underline mr-3" title="แก้ไข"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo BASE_URL; ?>/role/destroy/<?php echo $row['id']; ?>" class="font-medium text-red-600 hover:underline" title="ลบ" onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบบทบาทนี้?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="bg-white border-b">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูลบทบาท</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
