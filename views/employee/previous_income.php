<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 

// Helper function to get previous income value
function get_previous_income_value($previous_income, $field_name, $default = '0.00') {
    return (isset($previous_income) && is_array($previous_income) && isset($previous_income[$field_name])) 
        ? htmlspecialchars($previous_income[$field_name]) 
        : $default;
}

// Get current tax year (Buddhist calendar)
$current_tax_year_be = date('Y') + 543;
$current_tax_year = date('Y');
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900"><?php echo htmlspecialchars($page_title); ?></h1>
            <p class="text-gray-500 mt-1">
                สำหรับพนักงาน: 
                <span class="font-medium text-indigo-700">
                    <?php echo htmlspecialchars($employee->prefix . ' ' . $employee->first_name_th . ' ' . $employee->last_name_th); ?>
                </span>
                (<?php echo htmlspecialchars($employee->employee_code); ?>)
            </p>
        </div>
        <div class="flex gap-2">
            <a href="<?php echo BASE_URL; ?>/employee/edit/<?php echo $employee->id; ?>" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-user-edit mr-1"></i> ข้อมูลพนักงาน
            </a>
            <a href="<?php echo BASE_URL; ?>/employee" class="bg-indigo-200 hover:bg-indigo-300 text-indigo-800 font-bold py-2 px-4 rounded-lg">
                <i class="fas fa-list mr-1"></i> รายการพนักงาน
            </a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-lg" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-2"></i>
                <p><?php echo $_SESSION['success_message']; ?></p>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-lg" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-red-500 mr-2"></i>
                <p><?php echo $_SESSION['error_message']; ?></p>
            </div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-lg p-6 md:p-8">
        
        <!-- Information Box -->
        <div class="bg-blue-50 border border-blue-200 text-blue-800 p-4 rounded-lg mb-6">
            <div class="flex items-start">
                <i class="fas fa-info-circle text-blue-500 mr-3 mt-0.5 flex-shrink-0"></i>
                <div>
                    <h4 class="font-bold mb-1">วัตถุประสงค์ของข้อมูลนี้</h4>
                    <p class="text-sm leading-relaxed">
                        ข้อมูลนี้ใช้สำหรับคำนวณภาษีเงินได้บุคคลธรรมดาให้ถูกต้อง กรณีที่พนักงานมีรายได้จากที่ทำงานเก่าในปีภาษีปัจจุบัน 
                        (ตามเอกสาร 50 ทวิ) เพื่อนำไปรวมคำนวณกับรายได้จากที่นี่ และป้องกันการหักภาษี ณ ที่จ่ายซ้ำซ้อน
                    </p>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="<?php echo BASE_URL; ?>/employee/previous_income/<?php echo $employee->id; ?>" method="POST" class="space-y-6">
            <input type="hidden" name="action" value="save_previous_income">
            <input type="hidden" name="employee_id" value="<?php echo $employee->id; ?>">
            <input type="hidden" name="tax_year" value="<?php echo $current_tax_year; ?>">
            
            <!-- Form Header -->
            <div class="border-b border-gray-200 pb-4">
                <h2 class="text-xl font-bold text-gray-800 flex items-center">
                    <i class="fas fa-calculator text-indigo-600 mr-2"></i>
                    ข้อมูลสำหรับปีภาษี <?php echo $current_tax_year_be; ?> (<?php echo $current_tax_year; ?>)
                </h2>
                <p class="text-sm text-gray-600 mt-1">กรอกข้อมูลรายได้และภาษีที่หักไว้แล้วจากที่ทำงานเก่า</p>
            </div>
            
            <!-- Form Fields -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label for="total_income" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-money-bill-wave text-green-500 mr-1"></i>
                        รายได้สะสม (บาท)
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="total_income" 
                           id="total_income" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                           value="<?php echo get_previous_income_value($previous_income, 'total_income'); ?>"
                           placeholder="0.00">
                    <p class="text-xs text-gray-500">รายได้รวมทั้งหมดจากที่ทำงานเก่าในปีนี้</p>
                </div>

                <div class="space-y-2">
                    <label for="total_tax" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-receipt text-red-500 mr-1"></i>
                        ภาษีหัก ณ ที่จ่ายสะสม (บาท)
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="total_tax" 
                           id="total_tax" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                           value="<?php echo get_previous_income_value($previous_income, 'total_tax'); ?>"
                           placeholder="0.00">
                    <p class="text-xs text-gray-500">ภาษีที่หักไว้แล้วจากที่ทำงานเก่า</p>
                </div>

                <div class="space-y-2">
                    <label for="social_security" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-shield-alt text-blue-500 mr-1"></i>
                        ประกันสังคมสะสม (บาท)
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="social_security" 
                           id="social_security" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                           value="<?php echo get_previous_income_value($previous_income, 'social_security'); ?>"
                           placeholder="0.00">
                    <p class="text-xs text-gray-500">เงินสมทบประกันสังคมที่จ่ายแล้วจากที่ทำงานเก่า</p>
                </div>

                <div class="space-y-2">
                    <label for="provident_fund" class="block text-sm font-medium text-gray-700">
                        <i class="fas fa-piggy-bank text-purple-500 mr-1"></i>
                        กองทุนสำรองเลี้ยงชีพสะสม (บาท)
                    </label>
                    <input type="number" 
                           step="0.01" 
                           name="provident_fund" 
                           id="provident_fund" 
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500" 
                           value="<?php echo get_previous_income_value($previous_income, 'provident_fund'); ?>"
                           placeholder="0.00">
                    <p class="text-xs text-gray-500">เงินสมทบกองทุนสำรองเลี้ยงชีพจากที่ทำงานเก่า</p>
                </div>
            </div>

            <!-- Additional Information -->
            <div class="bg-gray-50 p-4 rounded-lg">
                <h4 class="font-medium text-gray-800 mb-2">
                    <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                    ข้อมูลเพิ่มเติม
                </h4>
                <div class="text-sm text-gray-600 space-y-1">
                    <p>• ข้อมูลเหล่านี้จะถูกนำไปใช้ในการคำนวณภาษีเงินได้ประจำปี</p>
                    <p>• หากไม่มีรายได้จากที่ทำงานเก่า สามารถเว้นว่างหรือใส่ 0 ได้</p>
                    <p>• ข้อมูลสามารถแก้ไขได้ตลอดเวลาก่อนปิดปีภาษี</p>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="flex justify-end space-x-3 pt-6 border-t border-gray-200">
                <a href="<?php echo BASE_URL; ?>/employee" 
                   class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    <i class="fas fa-times mr-2"></i>
                    ยกเลิก
                </a>
                <button type="submit" 
                        class="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-200">
                    <i class="fas fa-save mr-2"></i>
                    บันทึกข้อมูล
                </button>
            </div>
        </form>

        <!-- Status Display (if data exists) -->
        <?php if (isset($previous_income) && is_array($previous_income) && !empty(array_filter($previous_income))): ?>
            <div class="mt-8 pt-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-800 mb-4">
                    <i class="fas fa-chart-line text-green-500 mr-2"></i>
                    ข้อมูลปัจจุบันในระบบ
                </h3>
                <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                        <div>
                            <span class="text-gray-600">รายได้สะสม:</span>
                            <p class="font-medium text-green-700">
                                <?php echo number_format(floatval($previous_income['total_income'] ?? 0), 2); ?> บาท
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-600">ภาษีหัก ณ ที่จ่าย:</span>
                            <p class="font-medium text-red-700">
                                <?php echo number_format(floatval($previous_income['total_tax'] ?? 0), 2); ?> บาท
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-600">ประกันสังคม:</span>
                            <p class="font-medium text-blue-700">
                                <?php echo number_format(floatval($previous_income['social_security'] ?? 0), 2); ?> บาท
                            </p>
                        </div>
                        <div>
                            <span class="text-gray-600">กองทุนสำรองเลี้ยงชีพ:</span>
                            <p class="font-medium text-purple-700">
                                <?php echo number_format(floatval($previous_income['provident_fund'] ?? 0), 2); ?> บาท
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Format number inputs on blur
    const numberInputs = document.querySelectorAll('input[type="number"]');
    numberInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
            if (this.value) {
                const value = parseFloat(this.value);
                if (!isNaN(value)) {
                    this.value = value.toFixed(2);
                }
            }
        });
    });

    // Form validation
    const form = document.querySelector('form');
    form.addEventListener('submit', function(e) {
        const totalIncome = parseFloat(document.getElementById('total_income').value) || 0;
        const totalTax = parseFloat(document.getElementById('total_tax').value) || 0;
        
        if (totalTax > totalIncome && totalIncome > 0) {
            e.preventDefault();
            alert('ภาษีหัก ณ ที่จ่ายไม่สามารถมากกว่ารายได้รวมได้');
            return false;
        }
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>