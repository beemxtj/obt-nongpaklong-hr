<?php 
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/../../config/app.php';
}
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
<<<<<<< HEAD

// Get search and filter values from URL (these are handled in the controller)
$search_term = $_GET['search'] ?? '';
$status_filter = $_GET['status'] ?? '';
?>

<style>
/* Custom CSS for enhanced UI */
.gradient-bg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

.btn-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    transition: all 0.3s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(102, 126, 234, 0.4);
}

.btn-success {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    transition: all 0.3s ease;
}

.btn-success:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 15px -3px rgba(72, 187, 120, 0.4);
}

.search-card {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    border: 1px solid #e2e8f0;
}

.table-row {
    transition: all 0.2s ease;
}

.table-row:hover {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    transform: scale(1.01);
}

.status-badge {
    transition: all 0.2s ease;
}

.status-badge:hover {
    transform: scale(1.05);
}

.action-btn {
    transition: all 0.2s ease;
    padding: 8px;
    border-radius: 8px;
}

.action-btn:hover {
    transform: scale(1.1);
    background-color: rgba(0, 0, 0, 0.05);
}

.profile-img {
    transition: all 0.3s ease;
    border: 3px solid #e2e8f0;
}

.profile-img:hover {
    border-color: #667eea;
    transform: scale(1.05);
}

.floating-header {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(0, 0, 0, 0.1);
}

.glass-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.animate-fade-in {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-slide-up {
    animation: slideUp 0.6s ease-out;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50 min-h-screen">
    <!-- Header Section with Enhanced Design -->
    <div class="floating-header rounded-2xl p-6 mb-8 animate-fade-in">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
            <div>
                <h1 class="text-3xl md:text-4xl font-bold bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">
                    <?php echo htmlspecialchars($page_title); ?>
                </h1>
                <p class="text-gray-600 mt-2 text-lg">รายการพนักงานทั้งหมดในระบบ</p>
                <div class="flex items-center mt-2 text-sm text-gray-500">
                    <i class="fas fa-users mr-2"></i>
                    <span>จัดการข้อมูลพนักงานอย่างมีประสิทธิภาพ</span>
                </div>
            </div>
            <div class="flex items-center gap-3 mt-6 sm:mt-0">
                <a href="<?php echo BASE_URL; ?>/employee/import" class="btn-success text-white font-semibold py-3 px-6 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-file-excel mr-2"></i> นำเข้าข้อมูล
                </a>
                <a href="<?php echo BASE_URL; ?>/employee/create" class="btn-primary text-white font-semibold py-3 px-6 rounded-xl flex items-center justify-center shadow-lg">
                    <i class="fas fa-plus mr-2"></i> เพิ่มพนักงานใหม่
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages with Enhanced Design -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="glass-card border-l-4 border-green-500 text-green-700 p-6 mb-6 rounded-xl animate-slide-up" role="alert">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-500 mr-3 text-xl"></i>
                <p class="font-medium"><?php echo $_SESSION['success_message']; ?></p>
            </div>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="glass-card border-l-4 border-red-500 text-red-700 p-6 mb-6 rounded-xl animate-slide-up" role="alert">
            <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3 text-xl"></i>
                <p class="font-medium"><?php echo $_SESSION['error_message']; ?></p>
            </div>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <!-- Enhanced Search and Filter Form -->
    <div class="search-card rounded-2xl shadow-xl mb-8 p-6 card-hover animate-fade-in">
        <div class="flex items-center mb-4">
            <i class="fas fa-search text-indigo-600 mr-3 text-xl"></i>
            <h3 class="text-lg font-semibold text-gray-800">ค้นหาและกรองข้อมูล</h3>
        </div>
        <form method="GET" action="<?php echo BASE_URL; ?>/employee" class="grid sm:grid-cols-4 gap-4 items-end">
            <div class="sm:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">ค้นหาพนักงาน</label>
                <div class="relative">
                    <input type="text" name="search" id="search" 
                           placeholder="ค้นหาตามชื่อ หรือรหัสพนักงาน..." 
                           value="<?php echo htmlspecialchars($search_term); ?>" 
                           class="w-full border-gray-300 rounded-xl shadow-sm pl-10 py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-2">สถานะการทำงาน</label>
                <select name="status" id="status" class="w-full border-gray-300 rounded-xl shadow-sm py-3 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all">
                    <option value="">สถานะทั้งหมด</option>
                    <option value="ทำงาน" <?php echo ($status_filter == 'ทำงาน') ? 'selected' : ''; ?>>ทำงาน</option>
                    <option value="ทดลองงาน" <?php echo ($status_filter == 'ทดลองงาน') ? 'selected' : ''; ?>>ทดลองงาน</option>
                    <option value="พักงาน" <?php echo ($status_filter == 'พักงาน') ? 'selected' : ''; ?>>พักงาน</option>
                    <option value="ลาออก" <?php echo ($status_filter == 'ลาออก') ? 'selected' : ''; ?>>ลาออก</option>
                </select>
            </div>
            <div class="flex gap-3">
                <button type="submit" class="flex-1 btn-primary text-white font-semibold py-3 px-6 rounded-xl shadow-lg">
                    <i class="fas fa-search mr-2"></i>ค้นหา
                </button>
                <a href="<?php echo BASE_URL; ?>/employee" class="flex-1 text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold py-3 px-6 rounded-xl transition-all shadow-lg">
                    <i class="fas fa-refresh mr-2"></i>ล้าง
                </a>
            </div>
        </form>
    </div>

    <!-- Enhanced Employee List Table -->
    <div class="glass-card rounded-2xl shadow-2xl overflow-hidden card-hover animate-slide-up">
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center text-white">
                    <i class="fas fa-users mr-3 text-2xl"></i>
                    <h3 class="text-xl font-semibold">รายชื่อพนักงาน</h3>
                </div>
                <div class="text-white text-sm">
                    <i class="fas fa-database mr-2"></i>
                    ข้อมูลล่าสุด
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-600">
                <thead class="text-xs text-gray-700 uppercase bg-gradient-to-r from-gray-50 to-gray-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 font-semibold">
                            <div class="flex items-center">
                                <i class="fas fa-user mr-2 text-indigo-600"></i>
                                พนักงาน
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold">
                            <div class="flex items-center">
                                <i class="fas fa-briefcase mr-2 text-indigo-600"></i>
                                ตำแหน่ง
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold">
                            <div class="flex items-center">
                                <i class="fas fa-building mr-2 text-indigo-600"></i>
                                หน่วยงาน/ฝ่าย
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-4 font-semibold">
                            <div class="flex items-center">
                                <i class="fas fa-info-circle mr-2 text-indigo-600"></i>
                                สถานะ
                            </div>
                        </th>
                        <th scope="col" class="px-6 py-4 text-center font-semibold">
                            <div class="flex items-center justify-center">
                                <i class="fas fa-cogs mr-2 text-indigo-600"></i>
                                จัดการ
                            </div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (isset($num) && $num > 0): ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <?php extract($row); ?>
                            <tr class="bg-white border-b table-row">
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <img class="h-12 w-12 rounded-full object-cover mr-4 profile-img shadow-md" 
                                             src="<?php echo BASE_URL . '/' . (htmlspecialchars($profile_image_path ?? 'assets/images/default-profile.png')); ?>" 
                                             alt="Profile image of <?php echo htmlspecialchars($full_name ?? ($first_name_th . ' ' . $last_name_th)); ?>">
                                        <div>
                                            <div class="font-semibold text-gray-900 text-base">
                                                <?php echo htmlspecialchars($full_name ?? ($first_name_th . ' ' . $last_name_th)); ?>
                                            </div>
                                            <div class="text-sm text-gray-500 flex items-center">
                                                <i class="fas fa-id-badge mr-1"></i>
                                                <?php echo htmlspecialchars($employee_code); ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <i class="fas fa-user-tie mr-2 text-gray-400"></i>
                                        <span class="font-medium"><?php echo htmlspecialchars($position_name ?? '-'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center">
                                        <i class="fas fa-sitemap mr-2 text-gray-400"></i>
                                        <span class="font-medium"><?php echo htmlspecialchars($department_name ?? '-'); ?></span>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <?php 
                                        $status_class = 'bg-gray-100 text-gray-800 border-gray-200';
                                        $status_icon = 'fas fa-question-circle';
                                        if ($status == 'ทำงาน') {
                                            $status_class = 'bg-green-100 text-green-800 border-green-200';
                                            $status_icon = 'fas fa-check-circle';
                                        }
                                        if ($status == 'ทดลองงาน') {
                                            $status_class = 'bg-yellow-100 text-yellow-800 border-yellow-200';
                                            $status_icon = 'fas fa-clock';
                                        }
                                        if ($status == 'ลาออก') {
                                            $status_class = 'bg-red-100 text-red-800 border-red-200';
                                            $status_icon = 'fas fa-times-circle';
                                        }
                                        if ($status == 'พักงาน') {
                                            $status_class = 'bg-orange-100 text-orange-800 border-orange-200';
                                            $status_icon = 'fas fa-pause-circle';
                                        }
                                    ?>
                                    <span class="px-3 py-2 inline-flex items-center text-sm leading-5 font-semibold rounded-full border status-badge <?php echo $status_class; ?>">
                                        <i class="<?php echo $status_icon; ?> mr-2"></i>
                                        <?php echo htmlspecialchars($status); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center whitespace-nowrap">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="<?php echo BASE_URL; ?>/employee/previous_income/<?php echo $id; ?>" 
                                           class="action-btn font-medium text-green-600 hover:text-green-800" 
                                           title="บันทึกรายได้ที่เก่า">
                                            <i class="fas fa-money-bill-wave text-lg"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/employee/edit/<?php echo $id; ?>" 
                                           class="action-btn font-medium text-blue-600 hover:text-blue-800" 
                                           title="แก้ไข">
                                            <i class="fas fa-edit text-lg"></i>
                                        </a>
                                        <a href="<?php echo BASE_URL; ?>/employee/destroy/<?php echo $id; ?>" 
                                           class="action-btn font-medium text-red-600 hover:text-red-800" 
                                           title="ลบ" 
                                           onclick="return confirm('คุณแน่ใจหรือไม่ว่าต้องการลบข้อมูลพนักงานคนนี้?')">
                                            <i class="fas fa-trash text-lg"></i>
                                        </a>
                                    </div>
=======
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
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr class="bg-white border-b">
<<<<<<< HEAD
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="bg-gray-100 rounded-full p-6 mb-4">
                                        <i class="fas fa-user-slash text-5xl text-gray-400"></i>
                                    </div>
                                    <p class="text-xl font-semibold text-gray-700 mb-2">ไม่พบข้อมูลพนักงาน</p>
                                    <p class="text-gray-500">ไม่พบข้อมูลพนักงานที่ตรงกับเงื่อนไขการค้นหา</p>
                                    <a href="<?php echo BASE_URL; ?>/employee/create" class="mt-4 btn-primary text-white font-semibold py-2 px-4 rounded-lg">
                                        <i class="fas fa-plus mr-2"></i>เพิ่มพนักงานใหม่
                                    </a>
                                </div>
                            </td>
=======
                            <td colspan="6" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูลพนักงาน</td>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<<<<<<< HEAD

    <!-- Enhanced Footer Information -->
    <?php if (isset($num) && $num > 0): ?>
        <div class="mt-8 text-center">
            <div class="glass-card rounded-xl p-4 inline-flex items-center space-x-4">
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-chart-bar mr-2 text-indigo-600"></i>
                    <span class="font-medium">แสดง <?php echo $num; ?> รายการ</span>
                </div>
                <div class="h-4 w-px bg-gray-300"></div>
                <div class="flex items-center text-gray-600">
                    <i class="fas fa-clock mr-2 text-indigo-600"></i>
                    <span class="text-sm">อัปเดตล่าสุด: <?php echo date('d/m/Y H:i'); ?></span>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

=======
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
