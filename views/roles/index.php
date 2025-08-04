<?php 
<<<<<<< HEAD
if (!defined('BASE_URL')) { 
    require_once __DIR__ . '/../../config/app.php'; 
}
require_once __DIR__ . '/../layouts/header.php'; 
?>

<style>
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 12px 24px;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        color: white;
        text-decoration: none;
    }

    .btn-secondary {
        background-color: #f1f5f9;
        color: #374151;
        transition: all 0.3s ease;
        border-radius: 12px;
        padding: 8px 16px;
        font-weight: 600;
        border: 2px solid #e2e8f0;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
    }

    .btn-secondary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border-color: #cbd5e1;
        background-color: #fff;
        color: #374151;
        text-decoration: none;
    }
    
    .section-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-weight: 700;
    }

    .floating-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 32px;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        border-left: 4px solid #10b981;
        color: #065f46;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .alert-error {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-left: 4px solid #ef4444;
        color: #991b1b;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 24px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
    
    .table-container {
        background: white;
        border-radius: 24px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.07);
        overflow: hidden;
    }
    
    .table-action-link {
        color: #4f46e5;
        font-weight: 600;
        transition: color 0.3s;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        margin: 0 2px;
    }
    .table-action-link:hover {
        color: #3730a3;
        background: #f0f9ff;
        text-decoration: none;
    }
    
    .table-action-delete {
        color: #dc2626;
        font-weight: 600;
        transition: color 0.3s;
        text-decoration: none;
        padding: 8px 12px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        margin: 0 2px;
    }
    .table-action-delete:hover {
        color: #991b1b;
        background: #fef2f2;
        text-decoration: none;
    }

    .permission-badge {
        background: linear-gradient(135deg, #e0e7ff 0%, #c7d2fe 100%);
        color: #3730a3;
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 11px;
        font-weight: 600;
        margin: 2px;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .no-permissions {
        color: #9ca3af;
        font-style: italic;
        padding: 8px 12px;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px dashed #d1d5db;
    }

    .role-card {
        transition: all 0.3s ease;
    }

    .role-card:hover {
        transform: translateY(-2px);
    }

    .role-id-badge {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #374151;
        padding: 4px 8px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 700;
        min-width: 40px;
        text-align: center;
    }

    .role-name {
        font-weight: 700;
        color: #1f2937;
        font-size: 1rem;
        margin-bottom: 4px;
    }

    .permissions-container {
        max-height: 100px;
        overflow-y: auto;
        padding: 8px;
        background: #f8fafc;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
    }

    .permissions-container::-webkit-scrollbar {
        width: 4px;
    }

    .permissions-container::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 4px;
    }

    .permissions-container::-webkit-scrollbar-thumb {
        background: #c1c1c1;
        border-radius: 4px;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: #6b7280;
    }

    .empty-state i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .stats-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        border: 1px solid #e2e8f0;
        margin-bottom: 2rem;
    }

    .stats-content {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 2rem;
        text-align: center;
    }

    .stat-item {
        padding: 1rem;
    }

    .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #667eea;
        margin-bottom: 0.5rem;
    }

    .stat-label {
        color: #6b7280;
        font-size: 0.875rem;
        font-weight: 500;
    }
</style>

<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 p-4 sm:p-6 lg:p-8 bg-slate-50 min-h-screen">
    <div class="floating-header">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold section-header">
                <i class="fas fa-user-shield mr-3" style="-webkit-text-fill-color: #667eea;"></i>
                <?php echo htmlspecialchars($page_title ?? 'จัดการบทบาทและสิทธิ์'); ?>
            </h1>
            <p class="text-gray-500 mt-2">กำหนดบทบาทและสิทธิ์การเข้าถึงเมนูต่างๆ ในระบบ</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/role/create" class="btn-primary">
=======
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
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
            <i class="fas fa-plus mr-2"></i>
            เพิ่มบทบาทใหม่
        </a>
    </div>

<<<<<<< HEAD
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert-success" role="alert">
            <p class="flex items-center"><i class="fas fa-check-circle mr-3"></i><?php echo $_SESSION['success_message']; ?></p>
            <button type="button" class="text-green-800 hover:text-green-900 text-xl font-bold bg-transparent border-none cursor-pointer" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert-error" role="alert">
            <p class="flex items-center"><i class="fas fa-exclamation-circle mr-3"></i><?php echo $_SESSION['error_message']; ?></p>
            <button type="button" class="text-red-800 hover:text-red-900 text-xl font-bold bg-transparent border-none cursor-pointer" onclick="this.parentElement.style.display='none';">&times;</button>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <!-- Statistics Card -->
    <div class="stats-card">
        <div class="stats-content">
            <div class="stat-item">
                <div class="stat-number"><?php echo $num ?? 0; ?></div>
                <div class="stat-label">บทบาททั้งหมด</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="active-roles">
                    <?php 
                    $active_count = 0;
                    if (isset($stmt) && $num > 0) {
                        // Reset stmt to count
                        $stmt->execute();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $active_count++;
                        }
                        $stmt->execute(); // Reset for table display
                    }
                    echo $active_count;
                    ?>
                </div>
                <div class="stat-label">บทบาทที่ใช้งาน</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="total-permissions">
                    <?php
                    $total_perms = 0;
                    if (isset($stmt) && $num > 0) {
                        $stmt->execute();
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $permissions = json_decode($row['permissions'], true);
                            if (is_array($permissions)) {
                                $total_perms += count($permissions);
                            }
                        }
                        $stmt->execute(); // Reset for table display
                    }
                    echo $total_perms;
                    ?>
                </div>
                <div class="stat-label">สิทธิ์ทั้งหมด</div>
            </div>
        </div>
    </div>

    <!-- Roles Table -->
    <div class="table-container">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-slate-100">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ID
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            ชื่อบทบาท
                        </th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            สิทธิ์การใช้งาน
                        </th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            จัดการ
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (isset($num) && $num > 0 && isset($stmt) && $stmt !== null): ?>
                        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                            <tr class="role-card hover:bg-slate-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="role-id-badge">
                                        #<?php echo str_pad($row['id'], 2, '0', STR_PAD_LEFT); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-purple-400 to-indigo-600 flex items-center justify-center">
                                                <i class="fas fa-user-shield text-white text-sm"></i>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="role-name">
                                                <?php echo htmlspecialchars($row['role_name']); ?>
                                            </div>
                                            <div class="text-sm text-gray-500">
                                                บทบาทในระบบ
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="permissions-container">
                                        <?php 
                                            $permissions = json_decode($row['permissions'], true);
                                            if (is_array($permissions) && !empty($permissions)) {
                                                foreach($permissions as $p) {
                                                    echo '<span class="permission-badge">' . htmlspecialchars($p) . '</span>';
                                                }
                                            } else {
                                                echo '<div class="no-permissions">ไม่มีสิทธิ์เฉพาะ</div>';
                                            }
                                        ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex justify-center items-center space-x-2">
                                        <a href="<?php echo BASE_URL; ?>/role/edit/<?php echo $row['id']; ?>" 
                                           class="table-action-link" 
                                           title="แก้ไขบทบาท">
                                            <i class="fas fa-edit mr-1"></i>
                                            แก้ไข
                                        </a>
                                        <form action="<?php echo BASE_URL; ?>/role/destroy/<?php echo $row['id']; ?>" 
                                              method="POST" 
                                              class="inline-block" 
                                              onsubmit="return confirm('คุณแน่ใจหรือไม่ที่จะลบบทบาท &quot;<?php echo htmlspecialchars(addslashes($row['role_name'])); ?>&quot;?\n\nการลบจะไม่สามารถกู้คืนได้');">
                                            <button type="submit" 
                                                    class="table-action-delete bg-transparent border-none cursor-pointer" 
                                                    title="ลบบทบาท">
                                                <i class="fas fa-trash-alt mr-1"></i>
                                                ลบ
                                            </button>
                                        </form>
                                    </div>
=======
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
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
<<<<<<< HEAD
                        <tr>
                            <td colspan="4" class="empty-state">
                                <i class="fas fa-user-shield"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">ยังไม่มีบทบาทในระบบ</h3>
                                <p class="text-gray-500 mb-4">เริ่มต้นด้วยการเพิ่มบทบาทแรกของคุณ</p>
                                <a href="<?php echo BASE_URL; ?>/role/create" class="btn-primary">
                                    <i class="fas fa-plus mr-2"></i>เพิ่มบทบาทใหม่
                                </a>
                            </td>
=======
                        <tr class="bg-white border-b">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">ไม่พบข้อมูลบทบาท</td>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<<<<<<< HEAD
<script>
// Add some interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Add hover effects for permission badges
    const permissionBadges = document.querySelectorAll('.permission-badge');
    permissionBadges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.05)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert-success, .alert-error');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 300);
        }, 5000);
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
=======
<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
