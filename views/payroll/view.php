<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>
<style>
    @media print {
        body, main { margin: 0; padding: 0; background: white; }
        .no-print, .sidebar { display: none; }
        .payslip-container { box-shadow: none; border: 1px solid #ccc; margin: 0; max-width: 100%; border-radius: 0; }
    }
</style>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50">
    <div class="max-w-4xl mx-auto">
        <div class="flex justify-between items-center mb-6 no-print">
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900">
                สลิปเงินเดือน
            </h1>
            <div>
                <a href="javascript:history.back()" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg mr-2">
                    <i class="fas fa-arrow-left mr-2"></i>กลับ
                </a>
                <button onclick="window.print()" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg">
                    <i class="fas fa-print mr-2"></i>พิมพ์
                </button>
            </div>
        </div>
        
        <div class="bg-white rounded-2xl shadow-lg p-6 md:p-10 payslip-container">
            <div class="flex justify-between items-start pb-4 border-b">
                <div>
                    <h2 class="text-xl font-bold text-gray-800">องค์การบริหารส่วนตำบลหนองปากโลง</h2>
                    <p class="text-sm text-gray-500">ที่อยู่: 123 หมู่ 4 ต.หนองปากโลง อ.เมือง จ.นครปฐม 73000</p>
                </div>
                <h3 class="text-2xl font-bold text-indigo-700">สลิปเงินเดือน</h3>
            </div>
            
            <div class="grid grid-cols-2 gap-8 py-6">
                <div>
                    <p class="text-sm text-gray-500">พนักงาน</p>
                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($payslip_data['employee_name']); ?></p>
                    <p class="text-sm text-gray-700">รหัส: <?php echo htmlspecialchars($payslip_data['emp_code']); ?></p>
                    <p class="text-sm text-gray-700">ตำแหน่ง: <?php echo htmlspecialchars($payslip_data['position_name']); ?></p>
                </div>
                <div class="text-right">
                    <p class="text-sm text-gray-500">รอบการจ่าย</p>
                    <p class="font-semibold text-gray-800"><?php echo htmlspecialchars($thai_months[$payslip_data['pay_period_month']] . ' ' . ($payslip_data['pay_period_year'] + 543)); ?></p>
                    <p class="text-sm text-gray-700">วันที่พิมพ์: <?php echo date('d/m/Y'); ?></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div>
                    <h4 class="font-bold text-lg text-green-700 bg-green-50 p-3 rounded-t-lg">รายรับ (Earnings)</h4>
                    <div class="border rounded-b-lg p-4 space-y-2">
                        <div class="flex justify-between items-center"><p>เงินเดือน</p><p class="font-mono"><?php echo number_format($payslip_data['base_salary'], 2); ?></p></div>
                        <div class="flex justify-between items-center"><p>ค่าล่วงเวลา (OT)</p><p class="font-mono"><?php echo number_format($payslip_data['overtime_pay'], 2); ?></p></div>
                        <div class="flex justify-between items-center"><p>เบี้ยเลี้ยง/เงินพิเศษ</p><p class="font-mono"><?php echo number_format($payslip_data['allowances'], 2); ?></p></div>
                        <div class="flex justify-between items-center text-green-600 font-bold pt-2 border-t mt-2"><p>รวมรายรับ</p><p class="font-mono"><?php echo number_format($payslip_data['total_earnings'], 2); ?></p></div>
                    </div>
                </div>
                <div>
                    <h4 class="font-bold text-lg text-red-700 bg-red-50 p-3 rounded-t-lg">รายการหัก (Deductions)</h4>
                    <div class="border rounded-b-lg p-4 space-y-2">
                        <div class="flex justify-between items-center"><p>หักมาสาย</p><p class="font-mono"><?php echo number_format($payslip_data['late_deductions'], 2); ?></p></div>
                        <div class="flex justify-between items-center"><p>หักขาดงาน</p><p class="font-mono"><?php echo number_format($payslip_data['absence_deductions'], 2); ?></p></div>
                        <div class="flex justify-between items-center"><p>ประกันสังคม</p><p class="font-mono"><?php echo number_format($payslip_data['social_security'], 2); ?></p></div>
                        <div class="flex justify-between items-center"><p>ภาษีหัก ณ ที่จ่าย</p><p class="font-mono"><?php echo number_format($payslip_data['tax'], 2); ?></p></div>
                        <div class="flex justify-between items-center text-red-600 font-bold pt-2 border-t mt-2"><p>รวมรายการหัก</p><p class="font-mono"><?php echo number_format($payslip_data['total_deductions'], 2); ?></p></div>
                    </div>
                </div>
            </div>

            <div class="mt-8 pt-6 border-t flex justify-between items-center bg-gray-50 p-4 rounded-lg">
                <h3 class="text-xl font-bold text-indigo-800">รายรับสุทธิ (Net Salary)</h3>
                <p class="text-2xl font-bold text-indigo-800 font-mono"><?php echo number_format($payslip_data['net_salary'], 2); ?></p>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>