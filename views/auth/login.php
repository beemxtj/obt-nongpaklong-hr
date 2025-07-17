<?php
// views/auth/login.php

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
    
    <!-- Google Fonts: Sarabun -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sarabun:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
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
                    <div class="mb-4">
                        <label for="email" class="block text-gray-700 text-sm font-bold mb-2">อีเมล (Email)</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3"><i class="fas fa-envelope text-gray-400"></i></span>
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
</html>
