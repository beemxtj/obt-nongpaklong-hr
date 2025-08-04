<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>
<style>
    /* Add styles for the modal */
    .modal-enter { transition: opacity 0.3s ease; }
    .modal-leave { transition: opacity 0.3s ease; }
    .modal-enter-from, .modal-leave-to { opacity: 0; }
    .modal-enter-to, .modal-leave-from { opacity: 1; }
</style>

<!-- ===== Main Content ===== -->
<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Header and notification messages -->
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-indigo-900">Dashboard</h1>
        <div class="flex items-center gap-4">
            <span class="hidden sm:inline text-gray-600">คุณ <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'ผู้ใช้งาน'); ?></span>
            <a href="<?php echo BASE_URL; ?>/auth/logout" class="flex items-center gap-2 text-red-500 hover:text-red-700 font-semibold">
                <i class="fas fa-sign-out-alt"></i>
                <span class="hidden md:inline">ออกจากระบบ</span>
            </a>
        </div>
    </div>
    
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4" role="alert">
            <p><?php echo $_SESSION['success_message']; ?></p>
        </div>
        <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4" role="alert">
            <p><?php echo $_SESSION['error_message']; ?></p>
        </div>
        <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <!-- ===== Welcome & Clock-in Section ===== -->
            <div class="bg-white p-6 rounded-2xl shadow-lg text-center">
                <div class="flex items-center justify-center gap-4 mb-4">
                    <div class="w-16 h-16 rounded-full bg-indigo-100 flex items-center justify-center">
                        <i class="fas fa-user text-3xl text-indigo-500"></i>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold text-gray-800">สวัสดี, คุณ <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'ผู้ใช้งาน'); ?></h2>
                        <p class="text-gray-500">ยินดีต้อนรับสู่ระบบลงเวลาทำงาน</p>
                    </div>
                </div>
                
                <div id="clock" class="text-5xl font-bold text-indigo-600 my-4"></div>
                <p id="date" class="text-gray-600 text-lg"></p>
                
                <!-- ===== Clock-in/out Buttons Logic ===== -->
                <?php if (!$today_log): ?>
                    <form id="clockInForm" action="<?php echo BASE_URL; ?>/attendance/clockIn" method="POST">
                        <input type="hidden" name="latitude" id="latitude">
                        <input type="hidden" name="longitude" id="longitude">
                        <input type="hidden" name="image_data" id="image_data">
                        <button type="submit" id="clockInButton" class="w-full max-w-sm mx-auto mt-6 py-4 px-6 text-white font-bold rounded-xl shadow-md btn-gradient text-xl">
                            <i class="fas fa-fingerprint mr-2"></i> ลงเวลาเข้างาน
                        </button>
                    </form>
                    <p class="mt-4 text-lg">สถานะ: <span class="font-semibold text-red-500">ยังไม่เข้างาน</span></p>
                <?php elseif (empty($today_log['clock_out_time'])): ?>
                    <form id="clockOutForm" action="<?php echo BASE_URL; ?>/attendance/clockOut" method="POST">
                         <input type="hidden" name="latitude_out" id="latitude_out">
                         <input type="hidden" name="longitude_out" id="longitude_out">
                         <input type="hidden" name="image_data_out" id="image_data_out">
                         <button type="submit" id="clockOutButton" class="w-full max-w-sm mx-auto mt-6 py-4 px-6 text-white font-bold rounded-xl shadow-md bg-red-500 hover:bg-red-600 text-xl">
                            <i class="fas fa-sign-out-alt mr-2"></i> ลงเวลาออกงาน
                        </button>
                    </form>
                    <p class="mt-4 text-lg">สถานะ: <span class="font-semibold text-green-500">เข้างานแล้ว (<?php echo date('H:i', strtotime($today_log['clock_in_time'])); ?> น.)</span></p>
                <?php else: ?>
                    <div class="mt-6 py-4 px-6 bg-gray-100 rounded-xl">
                        <p class="text-lg font-semibold text-gray-700">คุณได้ลงเวลาทำงานวันนี้เรียบร้อยแล้ว</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- ===== Today's Work & Leave Balance (Combined for Mobile) ===== -->
            <div class="bg-white p-6 rounded-2xl shadow-lg">
                <h3 class="text-xl font-bold mb-4 border-b pb-2 text-indigo-900">การทำงานวันนี้</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 text-gray-600">
                    <?php
                        // Prepare variables to avoid errors
                        $clock_in_display = '--:--';
                        $clock_out_display = '--:--';
                        $work_hours_display = '--:--';
                        $loc_in_display = 'ยังไม่ได้บันทึก';
                        $loc_out_display = 'ยังไม่ได้บันทึก';

                        // Check if $today_log is not false
                        if ($today_log) { 
                            // Display Clock-in time
                            if (!empty($today_log['clock_in_time'])) {
                                $clock_in_display = date('H:i', strtotime($today_log['clock_in_time']));
                            }
                            // Display Clock-out time
                            if (!empty($today_log['clock_out_time'])) {
                                $clock_out_display = date('H:i', strtotime($today_log['clock_out_time']));
                            }
                            // Calculate and display Work Hours
                            if (!empty($today_log['clock_in_time']) && !empty($today_log['clock_out_time'])) {
                                $clock_in_dt = new DateTime($today_log['clock_in_time']);
                                $clock_out_dt = new DateTime($today_log['clock_out_time']);
                                $interval = $clock_in_dt->diff($clock_out_dt);
                                $work_hours_display = $interval->format('%h ชม. %i น.');
                            }
                            // Display Location status
                            if (!empty($today_log['clock_in_latitude'])) {
                                $loc_in_display = 'บันทึกแล้ว';
                            }
                            if (!empty($today_log['clock_out_latitude'])) {
                                $loc_out_display = 'บันทึกแล้ว';
                            }
                        }
                    ?>
                    <div><p>เวลาเข้า:</p><p class="font-semibold text-lg text-gray-800"><?php echo $clock_in_display; ?></p></div>
                    <div><p>เวลาออก:</p><p class="font-semibold text-lg text-gray-800"><?php echo $clock_out_display; ?></p></div>
                    <div><p>ชั่วโมงทำงาน:</p><p class="font-semibold text-lg text-gray-800"><?php echo $work_hours_display; ?></p></div>
                    <div class="col-span-2 sm:col-span-3 grid grid-cols-2 gap-4">
                        <div><p>ตำแหน่งเข้า:</p><p class="font-semibold text-gray-800 flex items-center gap-2"><i class="fas fa-map-marker-alt text-red-400"></i><?php echo $loc_in_display; ?></p></div>
                        <div><p>ตำแหน่งออก:</p><p class="font-semibold text-gray-800 flex items-center gap-2"><i class="fas fa-map-marker-alt text-red-400"></i><?php echo $loc_out_display; ?></p></div>
                    </div>
                </div>
                <!-- Action Buttons -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                    <a href="<?php echo BASE_URL; ?>/leave/create" class="w-full bg-green-500 text-white py-3 rounded-lg hover:bg-green-600 transition duration-300 font-semibold text-base text-center inline-flex items-center justify-center">
                        <i class="fas fa-file-signature mr-2"></i>ยื่นใบลา
                    </a>
                    <a href="<?php echo BASE_URL; ?>/attendance/history" class="w-full bg-blue-500 text-white py-3 rounded-lg hover:bg-blue-600 transition duration-300 font-semibold text-base text-center inline-flex items-center justify-center">
                        <i class="fas fa-history mr-2"></i>ดูประวัติลงเวลา
                    </a>
                                        <a href="<?php echo BASE_URL; ?>/leave/history" class="w-full bg-red-500 text-white py-3 rounded-lg hover:bg-blue-600 transition duration-300 font-semibold text-base text-center inline-flex items-center justify-center">
                        <i class="fas fa-history mr-2"></i>ดูประวัติลา
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Right Column -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-lg h-full">
                <h3 class="text-xl font-bold mb-4 border-b pb-2 text-indigo-900">ยอดวันลาคงเหลือ</h3>
<ul class="space-y-4 text-gray-600">
                    <?php if (!empty($leave_balances)): ?>
                        <?php foreach ($leave_balances as $balance): ?>
                            <li>
                                <div class="flex justify-between items-center mb-1 text-sm">
                                    <span class="font-semibold"><i class="<?php echo $balance['icon']; ?> mr-2"></i><?php echo htmlspecialchars($balance['name']); ?></span>
                                    <span>ใช้ไป <?php echo $balance['days_used']; ?>, คงเหลือ <?php echo $balance['remaining']; ?> วัน</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-1.5">
                                    <?php $percentage_used = ($balance['max_days'] > 0) ? ($balance['days_used'] / $balance['max_days']) * 100 : 0; ?>
                                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: <?php echo $percentage_used; ?>%"></div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-gray-500">ไม่สามารถคำนวณวันลาได้</p>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</main>

<!-- ===== Face Scan Modal ===== -->
<div id="faceScanModal" class="fixed inset-0 bg-gray-900 bg-opacity-75 flex items-center justify-center z-50 hidden modal-enter">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4 transform transition-all">
        <h3 id="modalTitle" class="text-xl font-bold mb-4 text-center">สแกนใบหน้าเพื่อลงเวลา</h3>
        <div class="relative bg-gray-200 rounded-md overflow-hidden">
            <video id="video" class="w-full h-auto" autoplay playsinline></video>
            <canvas id="canvas" class="hidden"></canvas>
        </div>
        <p id="modalMessage" class="text-center text-sm text-red-500 my-2 h-4"></p>
        <button id="captureButton" class="w-full mt-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-3 rounded-lg">
            <i class="fas fa-camera mr-2"></i>ถ่ายภาพ
        </button>
        <button id="cancelScanButton" class="w-full mt-2 bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-3 rounded-lg">
            ยกเลิก
        </button>
    </div>
</div>

<!-- ===== JavaScript (Revised) ===== -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const clockInForm = document.getElementById('clockInForm');
    const clockOutForm = document.getElementById('clockOutForm');
    const modal = document.getElementById('faceScanModal');
    const video = document.getElementById('video');
    const canvas = document.getElementById('canvas');
    const captureButton = document.getElementById('captureButton');
    const cancelScanButton = document.getElementById('cancelScanButton');
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');

    let currentForm = null;
    let stream = null;

    async function startCamera() {
        console.log("Attempting to start camera...");
        modalMessage.textContent = 'กำลังเปิดกล้อง...';
        try {
            stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user' } });
            video.srcObject = stream;
            modal.classList.remove('hidden');
            modalMessage.textContent = '';
            console.log("Camera started successfully.");
        } catch (err) {
            modalMessage.textContent = 'ไม่สามารถเข้าถึงกล้องได้';
            console.error("Error accessing camera: ", err);
            alert('ไม่สามารถเข้าถึงกล้องได้ กรุณาตรวจสอบการอนุญาตในเบราว์เซอร์ของคุณ');
        }
    }

    function stopCamera() {
        if (stream) {
            stream.getTracks().forEach(track => track.stop());
            console.log("Camera stopped.");
        }
        modal.classList.add('hidden');
    }

    function handleFormSubmit(event) {
        event.preventDefault();
        console.log("Form submission intercepted.");
        currentForm = event.target;
        modalTitle.textContent = currentForm.id === 'clockInForm' ? 'สแกนใบหน้าเพื่อลงเวลาเข้างาน' : 'สแกนใบหน้าเพื่อลงเวลาออกงาน';
        startCamera();
    }

    captureButton.addEventListener('click', () => {
        if (!stream) {
            console.log("Capture button clicked but no stream available.");
            return;
        }
        
        console.log("Capturing image...");
        modalMessage.textContent = 'กำลังถ่ายภาพ...';
        
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageDataUrl = canvas.toDataURL('image/jpeg');
        console.log("Image captured.");
        
        const imageInputId = currentForm.id === 'clockInForm' ? 'image_data' : 'image_data_out';
        document.getElementById(imageInputId).value = imageDataUrl;

        stopCamera();
        getGpsAndSubmit();
    });

    cancelScanButton.addEventListener('click', stopCamera);

    function getGpsAndSubmit() {
        console.log("Attempting to get GPS location...");
        modalMessage.textContent = 'กำลังค้นหาพิกัด...'; // Show status in modal
        
        const mainButton = currentForm.querySelector('button[type="submit"]');
        mainButton.disabled = true;
        mainButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> กำลังหาพิกัด...';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                console.log("GPS location found:", position.coords);
                modalMessage.textContent = 'พบพิกัดแล้ว กำลังบันทึกข้อมูล...';
                
                const latInputId = currentForm.id === 'clockInForm' ? 'latitude' : 'latitude_out';
                const lonInputId = currentForm.id === 'clockInForm' ? 'longitude' : 'longitude_out';
                
                document.getElementById(latInputId).value = position.coords.latitude;
                document.getElementById(lonInputId).value = position.coords.longitude;
                
                console.log("Submitting form...");
                currentForm.submit();
            },
            (error) => {
                console.error("GPS Error:", error);
                alert('ไม่สามารถหาพิกัดได้ กรุณาเปิด GPS และอนุญาตให้เข้าถึงตำแหน่ง');
                mainButton.disabled = false;
                // Reset button text
                mainButton.innerHTML = currentForm.id === 'clockInForm' ? '<i class="fas fa-fingerprint mr-2"></i> ลงเวลาเข้างาน' : '<i class="fas fa-sign-out-alt mr-2"></i> ลงเวลาออกงาน';
            },
            { enableHighAccuracy: true, timeout: 10000, maximumAge: 0 } // Options for better accuracy
        );
    }

    if (clockInForm) clockInForm.addEventListener('submit', handleFormSubmit);
    if (clockOutForm) clockOutForm.addEventListener('submit', handleFormSubmit);
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
