<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <div class="mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title); ?></h1>
        <p class="text-gray-500 mt-1">ประวัติการใช้งานและการเปลี่ยนแปลงข้อมูลทั้งหมดในระบบ</p>
    </div>

    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">เวลา</th>
                        <th class="px-6 py-3">ผู้ใช้งาน</th>
                        <th class="px-6 py-3">กิจกรรม</th>
                        <th class="px-6 py-3">รายละเอียด</th>
                        <th class="px-6 py-3">IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($num > 0): ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4"><?php echo date('d/m/Y H:i:s', strtotime($row['timestamp'])); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['first_name_th'] . ' ' . $row['last_name_th']); ?></td>
                                <td class="px-6 py-4 font-mono text-xs"><?php echo htmlspecialchars($row['action']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['details']); ?></td>
                                <td class="px-6 py-4"><?php echo htmlspecialchars($row['ip_address']); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center p-4">ไม่พบข้อมูลกิจกรรม</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
