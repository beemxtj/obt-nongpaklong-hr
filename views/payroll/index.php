<?php
require_once __DIR__ . '/../layouts/header.php';
ini_set('display_errors', 1); // <-- เพิ่มบรรทัดนี้
error_reporting(E_ALL);     // <-- เพิ่มบรรทัดนี้

// Style block (copy from other modern UI files if needed)
?>
<style>
    /* Add modern UI styles here for buttons, tables, etc. */
    .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; transition: all 0.3s ease; border-radius: 12px; padding: 10px 20px; font-weight: 600; box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3); border: none; }
    .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4); }
    .floating-header { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; margin-bottom: 2rem; gap: 1rem; }
    .section-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700; }
    .table-container { background: white; border-radius: 24px; box-shadow: 0 20px 40px rgba(0, 0, 0, 0.07); overflow: hidden; }
    .form-input { border-radius: 12px; border: 2px solid #e2e8f0; padding: 10px 16px; }
</style>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-money-bill-wave mr-3" style="-webkit-text-fill-color: #667eea;"></i><?php echo $page_title; ?>
            </h1>
            <p class="text-gray-500 mt-2">สร้างและดูข้อมูลเงินเดือนพนักงาน</p>
        </div>
        
        <form action="<?php echo BASE_URL; ?>/payroll/generate" method="POST" class="flex items-center gap-2 p-4 bg-white rounded-2xl shadow-sm">
            <select name="month" class="form-input">
                <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php echo ($i == date('m')) ? 'selected' : ''; ?>><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-input">
                <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                    <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
            <button type="submit" class="btn-primary whitespace-nowrap"><i class="fas fa-cogs mr-2"></i>สร้างข้อมูล</button>
        </form>
    </div>

    <div class="my-6">
        <form method="GET" class="flex items-center gap-2">
            <label class="font-semibold">ดูข้อมูลงวด:</label>
            <select name="month" class="form-input" onchange="this.form.submit()">
                 <?php for ($i = 1; $i <= 12; $i++): ?>
                    <option value="<?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?>" <?php echo ($i == $month) ? 'selected' : ''; ?>><?php echo str_pad($i, 2, '0', STR_PAD_LEFT); ?></option>
                <?php endfor; ?>
            </select>
            <select name="year" class="form-input" onchange="this.form.submit()">
                 <?php for ($i = date('Y'); $i >= date('Y') - 5; $i--): ?>
                    <option value="<?php echo $i; ?>" <?php echo ($i == $year) ? 'selected' : ''; ?>><?php echo $i; ?></option>
                <?php endfor; ?>
            </select>
        </form>
    </div>

    <div class="table-container">
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-slate-100">
                    <tr>
                        <th class="px-6 py-4">พนักงาน</th>
                        <th class="px-6 py-4 text-right">เงินเดือนพื้นฐาน</th>
                        <th class="px-6 py-4 text-right">รายรับอื่น</th>
                        <th class="px-6 py-4 text-right">รายการหัก</th>
                        <th class="px-6 py-4 text-right">เงินเดือนสุทธิ</th>
                        <th class="px-6 py-4 text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($is_generated): ?>
                        <?php while ($row = $payrolls_stmt->fetch(PDO::FETCH_ASSOC)): 
                            $total_earnings = $row['overtime_pay'] + $row['allowances'];
                            $total_deductions = $row['late_deductions'] + $row['absence_deductions'] + $row['social_security'] + $row['tax'];
                        ?>
                            <tr class="bg-white border-b hover:bg-slate-50">
                                <td class="px-6 py-4 font-medium text-gray-900"><?php echo htmlspecialchars($row['employee_name']); ?></td>
                                <td class="px-6 py-4 text-right"><?php echo number_format($row['base_salary'], 2); ?></td>
                                <td class="px-6 py-4 text-right text-green-600"><?php echo number_format($total_earnings, 2); ?></td>
                                <td class="px-6 py-4 text-right text-red-600"><?php echo number_format($total_deductions, 2); ?></td>
                                <td class="px-6 py-4 text-right font-bold text-indigo-600"><?php echo number_format($row['net_salary'], 2); ?></td>
                                <td class="px-6 py-4 text-center">
                                    <a href="<?php echo BASE_URL; ?>/payroll/view/<?php echo $row['id']; ?>" class="font-medium text-indigo-600 hover:underline">ดูสลิป</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-12 text-gray-500">ยังไม่มีการสร้างข้อมูลเงินเดือนสำหรับงวดนี้</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>