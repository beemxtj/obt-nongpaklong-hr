<?php
// views/auth/login.php

<<<<<<< HEAD
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// กำหนดค่าเริ่มต้นจาก system_settings พร้อม fallback value หากไม่มีค่า
$org_name = $system_settings['org_name'] ?? 'HRM System';
$org_logo_path = $system_settings['org_logo'] ?? '';
$favicon = $system_settings['favicon'] ?? '';
$login_bg_image = $system_settings['login_bg_image'] ?? '';
$primary_color = $system_settings['primary_color'] ?? '#4f46e5';
$secondary_color = $system_settings['secondary_color'] ?? '#7c3aed';

// สร้าง URL ที่สมบูรณ์สำหรับไฟล์ asset พร้อมตรวจสอบว่าไฟล์มีอยู่จริงหรือไม่
$logo_url = (!empty($org_logo_path) && file_exists($org_logo_path)) ? BASE_URL . '/' . $org_logo_path : '';
$favicon_url = (!empty($favicon) && file_exists($favicon)) ? BASE_URL . '/' . $favicon : '';
$bg_image_url = (!empty($login_bg_image) && file_exists($login_bg_image)) ? BASE_URL . '/' . $login_bg_image : '';

?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เข้าสู่ระบบ - <?php echo htmlspecialchars($org_name); ?></title>

    <?php if ($favicon_url): ?>
        <link rel="icon" href="<?php echo $favicon_url; ?>" type="image/x-icon">
    <?php endif; ?>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

=======
// เริ่ม session เพื่อแสดงข้อความ error (ถ้ามี)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - ระบบ HRM อบต.หนองปากโลง</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
<<<<<<< HEAD
        body {
            font-family: 'Sarabun', sans-serif;
            <?php if ($bg_image_url): ?>
            background-image: url('<?php echo $bg_image_url; ?>');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            <?php else: ?>
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            <?php endif; ?>
        }

        :root {
            --primary-color: <?php echo $primary_color; ?>;
            --secondary-color: <?php echo $secondary_color; ?>;
        }

        .btn-gradient {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            transition: all 0.3s ease;
        }

        .btn-gradient:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .input-focus:focus {
            --tw-ring-color: var(--primary-color);
        }

        .link-primary {
            color: var(--primary-color);
        }
        .link-primary:hover {
            color: var(--secondary-color);
        }

        .login-container-bg {
            <?php if ($bg_image_url): ?>
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            <?php else: ?>
            background-color: white;
            <?php endif; ?>
        }
    </style>
</head>

<body class="antialiased text-gray-800">

    <div class="min-h-screen flex items-center justify-center p-4">

        <div class="w-full max-w-md">

            <div class="login-container-bg rounded-2xl shadow-2xl p-8">

                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-block bg-gray-100 p-3 rounded-full mb-4 shadow-inner">
                        <?php if ($logo_url): ?>
                            <img src="<?php echo $logo_url; ?>" alt="Logo" class="w-16 h-16 object-contain">
                        <?php else: ?>
                             <i class="fas fa-sitemap text-4xl text-gray-400"></i>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800"><?php echo htmlspecialchars($org_name); ?></h1>
                    <p class="text-gray-500">ระบบบริหารทรัพยากรบุคคล</p>
                </div>

                <!-- แสดงข้อความ Error หรือ Success -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border-l-4 border-red-500 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                        <strong class="font-bold">ผิดพลาด!</strong>
                        <span class="block sm:inline"><?php echo $_SESSION['error']; ?></span>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                         <strong class="font-bold">สำเร็จ!</strong>
                        <span class="block sm:inline"><?php echo $_SESSION['success']; ?></span>
                    </div>
                    <?php unset($_SESSION['success']); ?>
                <?php endif; ?>

                <!-- Form Login -->
                <form action="<?php echo BASE_URL; ?>/auth/login" method="POST">
=======
        body { font-family: 'Sarabun', sans-serif; }
    </style>
</head>
<body class="antialiased text-gray-800">

    <div class="min-h-screen flex items-center justify-center bg-gray-100 p-4">
        
        <div class="w-full max-w-md">
            
            <div class="bg-white rounded-2xl shadow-2xl p-8">

                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="inline-block bg-indigo-100 p-4 rounded-full mb-4">
                        <i class="fas fa-sitemap text-4xl text-indigo-600"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-800">ระบบ HRM</h1>
                    <p class="text-gray-500">อบต.หนองปากโลง</p>
                </div>

                <!-- แสดงข้อความ Error ถ้า Login ไม่สำเร็จ -->
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $_SESSION['error']; ?></span>
                    </div>
                    <?php unset($_SESSION['error']); // เคลียร์ข้อความหลังจากแสดงผลแล้ว ?>
                <?php endif; ?>

                <!-- ===== จุดที่แก้ไข ===== -->
                <!-- เปลี่ยน action จาก "#" เป็น "auth/login" -->
                <form action="auth/login" method="POST">
                    <!-- Email Input -->
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">อีเมล (Email)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3"><i class="fas fa-envelope text-gray-400"></i></span>
<<<<<<< HEAD
                            <input type="email" id="email" name="email" placeholder="กรุณากรอกอีเมลของคุณ" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 input-focus transition duration-200">
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">รหัสผ่าน (Password)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3"><i class="fas fa-lock text-gray-400"></i></span>
                            <input type="password" id="password" name="password" placeholder="กรุณากรอกรหัสผ่าน" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 input-focus transition duration-200">
                        </div>
                    </div>

                    <div class="mb-4">
                        <button type="submit" class="w-full btn-gradient font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline">
                            <i class="fas fa-sign-in-alt mr-2"></i>เข้าสู่ระบบ
                        </button>
                    </div>
                </form>

                <!-- Divider -->
                <div class="my-6 flex items-center">
                    <div class="flex-grow border-t border-gray-300"></div>
                    <span class="flex-shrink mx-4 text-gray-500 text-sm">หรือ</span>
                    <div class="flex-grow border-t border-gray-300"></div>
                </div>

                <!-- LINE Login Button -->
                <div class="mb-6">
                     <a href="<?php echo BASE_URL; ?>/auth/line" class="w-full flex items-center justify-center bg-[#00B900] hover:bg-[#00A300] text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-all duration-300">
                        <i class="fab fa-line text-2xl mr-3"></i> เข้าสู่ระบบด้วย LINE
                    </a>
                </div>

                <div class="text-center">
                    <a href="#" class="inline-block align-baseline font-bold text-sm link-primary">
                        ลืมรหัสผ่าน?
                    </a>
                </div>
            </div>

            <p class="text-center text-gray-500 text-xs mt-8">&copy;<?php echo date('Y'); ?> <?php echo htmlspecialchars($org_name); ?>. สงวนลิขสิทธิ์.</p>
        </div>
    </div>
</body>

=======
                            <input type="email" id="email" name="email" placeholder="กรุณากรอกอีเมลของคุณ" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="mb-6">
                        <label for="password" class="block text-gray-700 text-sm font-bold mb-2">รหัสผ่าน (Password)</label>
                        <div class="relative">
                             <span class="absolute inset-y-0 left-0 flex items-center pl-3"><i class="fas fa-lock text-gray-400"></i></span>
                            <input type="password" id="password" name="password" placeholder="กรุณากรอกรหัสผ่าน" required class="w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-6">
                        <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-transform transform hover:scale-105 duration-300">
                            เข้าสู่ระบบ
                        </button>
                    </div>

                    <!-- Forgot Password Link -->
                    <div class="text-center">
                        <a href="#" class="inline-block align-baseline font-bold text-sm text-indigo-600 hover:text-indigo-800">
                            ลืมรหัสผ่าน?
                        </a>
                    </div>
                </form>
            </div>

             <p class="text-center text-gray-500 text-xs mt-8">&copy;2024 อบต.หนองปากโลง. สงวนลิขสิทธิ์.</p>
        </div>
    </div>
</body>
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
</html>
