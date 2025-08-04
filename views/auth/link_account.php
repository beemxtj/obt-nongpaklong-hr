<?php
// views/auth/link_account.php

// Ensure the session is started so we can access session variables for the
// linking flow.  The controller already sets `$_SESSION['line_user_id_to_link']`
// and `$_SESSION['line_display_name']` before redirecting here.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Retrieve the LINE display name for a friendly greeting.  Fallback to an
// empty string to avoid undefined index notices.
$line_display_name = $_SESSION['line_display_name'] ?? '';

?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เชื่อมบัญชี LINE - ระบบ HRM อบต.หนองปากโลง</title>

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
                <div class="text-center mb-6">
                    <div class="inline-block bg-indigo-100 p-4 rounded-full mb-4">
                        <i class="fas fa-link text-4xl text-indigo-600"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-800">เชื่อมบัญชี LINE</h1>
                    <p class="text-gray-500">อบต.หนองปากโลง</p>
                </div>

                <!-- Display error message if linking fails -->
                <?php if (isset($_SESSION['link_error'])): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4" role="alert">
                        <span class="block sm:inline"><?php echo $_SESSION['link_error']; ?></span>
                    </div>
                    <?php unset($_SESSION['link_error']); ?>
                <?php endif; ?>

                <!-- Greeting and instructions -->
                <p class="text-center text-gray-700 mb-6">
                    สวัสดี <?php echo htmlspecialchars($line_display_name); ?>!<br>
                    กรุณากรอกรหัสพนักงานของคุณเพื่อเชื่อมบัญชีกับ LINE
                </p>

                <!-- Linking form -->
                <form action="<?php echo BASE_URL; ?>/auth/processLink" method="POST">
                    <div class="mb-4">
                        <label for="employee_code" class="block text-gray-700 text-sm font-bold mb-2">รหัสพนักงาน</label>
                        <input type="text" id="employee_code" name="employee_code" placeholder="กรอกรหัสพนักงาน" required class="w-full p-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition duration-200">
                    </div>
                    <button type="submit" class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition duration-300">
                        เชื่อมบัญชี
                    </button>
                </form>
            </div>
            <p class="text-center text-gray-500 text-xs mt-8">&copy;2024 อบต.หนองปากโลง. สงวนลิขสิทธิ์.</p>
        </div>
    </div>
</body>
</html>