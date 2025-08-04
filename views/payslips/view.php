<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
?>
<style>
    .btn-view { background-color: #eef2ff; color: #4338ca; transition: all 0.2s ease; }
    .btn-view:hover { background-color: #e0e7ff; color: #3730a3; }
</style>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50">
    <h1 class="text-2xl md:text-3xl font-bold text-indigo-900 mb-6">ประวัติสลิปเงินเดือน</h1>
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-slate-100">
                    <tr>
                        <th scope="col" class="px-6 py-3">รอบการจ่าย</th>
                        <th scope="col" class="px-6 py-3">วันที่ออกสลิป</th>
                        <th scope="col" class="px-6 py-3 text-right">รายรับสุทธิ (บาท)</th>
                        <th scope="col" class="px-6 py-3 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($num > 0): ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="bg-white border-b hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-gray-900">
                                    <?php 
                                        $month_num = date('n', strtotime($row['pay_period_start']));
                                        $year_num = date('Y', strtotime($row['pay_period_start'])) + 543;
                                        echo htmlspecialchars($thai_months[$month_num] . ' ' . $year_num); 
                                    ?>
                                </td>
                                <td class="px-6 py-4">
                                    <?php echo date('d/m/Y H:i', strtotime($row['generated_at'])); ?>
                                </td>
                                <td class="px-6 py-4 text-right font-semibold text-indigo-600">
                                    <?php echo number_format($row['net_salary'], 2); ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?php echo BASE_URL; ?>/payslip/view/<?php echo $row['id']; ?>" class="btn-view font-bold py-2 px-4 rounded-lg text-xs">
                                        <i class="fas fa-eye mr-1"></i> ดูรายละเอียด
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="bg-white border-b">
                            <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-file-invoice-dollar text-4xl text-gray-300 mb-3"></i>
                                <p>ไม่พบประวัติสลิปเงินเดือน</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>