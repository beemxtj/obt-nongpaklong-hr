<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
?>
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    /* Custom styles for Tabs and FullCalendar */
    .fc-event { border: none !important; cursor: pointer; }
    .fc-daygrid-event { padding: 2px 5px; font-size: 0.75rem; }
    .fc-toolbar-title { font-size: 1.25rem !important; }
    .fc-button { text-transform: capitalize !important; }
    .tab-active { 
        border-color: #4f46e5; 
        color: #4f46e5;
        background-color: #eef2ff;
    }
    /* Styles for Image Modal */
    .image-modal-backdrop {
        transition: opacity 0.3s ease;
    }
</style>
<?php
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900">ประวัติการลงเวลา</h1>
            <p class="text-gray-500 mt-1">ตรวจสอบบันทึกเวลาทำงานของคุณ</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">กลับ</a>
    </div>

    <!-- Tab Navigation -->
    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-4" aria-label="Tabs">
                <button id="tab-card" class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm tab-active">
                    <i class="fas fa-list mr-2"></i>มุมมองการ์ด
                </button>
                <button id="tab-calendar" class="whitespace-nowrap py-3 px-4 border-b-2 font-medium text-sm text-gray-500 hover:text-gray-700">
                    <i class="fas fa-calendar-alt mr-2"></i>มุมมองปฏิทิน
                </button>
            </nav>
        </div>
    </div>

    <!-- Tab Content -->
    <div>
        <!-- Card View -->
        <div id="view-card">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php if ($num > 0): ?>
                    <?php 
                        $attendance_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php foreach ($attendance_logs as $row): ?>
                        <?php
                            $status_color = 'bg-blue-100 text-blue-800 border-blue-400'; // ปกติ
                            if ($row['status'] == 'สาย') {
                                $status_color = 'bg-yellow-100 text-yellow-800 border-yellow-400';
                            } elseif ($row['status'] == 'ขาดงาน') {
                                $status_color = 'bg-red-100 text-red-800 border-red-400';
                            }
                        ?>
                        <div class="bg-white rounded-xl shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300">
                            <div class="p-5">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-bold text-indigo-800"><?php echo date('d M Y', strtotime($row['clock_in_time'])); ?></h3>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full border <?php echo $status_color; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </div>
                                <div class="mt-4 space-y-3 text-sm">
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500"><i class="fas fa-sign-in-alt text-green-500 w-5"></i> เวลาเข้า</span>
                                        <span class="font-semibold"><?php echo date('H:i:s', strtotime($row['clock_in_time'])); ?> น.</span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500"><i class="fas fa-sign-out-alt text-red-500 w-5"></i> เวลาออก</span>
                                        <span class="font-semibold"><?php echo $row['clock_out_time'] ? date('H:i:s', strtotime($row['clock_out_time'])) . ' น.' : '-'; ?></span>
                                    </div>
                                    <!-- ===== จุดที่แก้ไข: เพิ่มการแสดงผลชั่วโมงทำงาน ===== -->
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500"><i class="fas fa-hourglass-half text-blue-500 w-5"></i> ชั่วโมงทำงาน</span>
                                        <span class="font-semibold"><?php echo !empty($row['work_hours']) ? number_format($row['work_hours'], 2) . ' ชม.' : '-'; ?></span>
                                    </div>
                                    <div class="flex justify-between items-center">
                                        <span class="text-gray-500"><i class="fas fa-plus-circle text-purple-500 w-5"></i> ชั่วโมง OT</span>
                                        <span class="font-semibold"><?php echo !empty($row['ot_hours']) ? number_format($row['ot_hours'], 2) . ' ชม.' : '-'; ?></span>
                                    </div>
                                    <!-- ======================================= -->
                                </div>
                                <div class="mt-4 border-t pt-4 text-sm text-gray-500">
                                    <div class="flex items-center justify-between">
                                        <div class="flex items-center space-x-2">
                                            <span class="font-semibold">รูปถ่าย:</span>
                                            <?php if (!empty($row['clock_in_image_path'])): ?>
                                                <div class="flex items-center" title="ภาพเข้างาน">
                                                    <!-- ===== จุดที่แก้ไข: เพิ่ม class และ cursor-pointer ===== -->
                                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($row['clock_in_image_path']); ?>" alt="ภาพเข้างาน" class="view-image-trigger w-8 h-8 rounded-full object-cover border-2 border-green-400 cursor-pointer">
                                                </div>
                                            <?php endif; ?>
                                            <?php if (!empty($row['clock_out_image_path'])): ?>
                                                <div class="flex items-center" title="ภาพออกงาน">
                                                    <!-- ===== จุดที่แก้ไข: เพิ่ม class และ cursor-pointer ===== -->
                                                    <img src="<?php echo BASE_URL . '/' . htmlspecialchars($row['clock_out_image_path']); ?>" alt="ภาพออกงาน" class="view-image-trigger w-8 h-8 rounded-full object-cover border-2 border-red-400 cursor-pointer">
                                                </div>
                                            <?php endif; ?>
                                            <?php if (empty($row['clock_in_image_path']) && empty($row['clock_out_image_path'])): ?>
                                                <span class="text-xs text-gray-400">ไม่มีรูป</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="flex items-center space-x-3">
                                            <?php if (!empty($row['clock_in_latitude'])): ?>
                                                <a href="https://www.google.com/maps?q=<?php echo $row['clock_in_latitude']; ?>,<?php echo $row['clock_in_longitude']; ?>" target="_blank" class="text-green-600 hover:underline flex items-center" title="แผนที่เข้างาน">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>เข้า
                                                </a>
                                            <?php endif; ?>
                                            <?php if (!empty($row['clock_out_latitude'])): ?>
                                                <a href="https://www.google.com/maps?q=<?php echo $row['clock_out_latitude']; ?>,<?php echo $row['clock_out_longitude']; ?>" target="_blank" class="text-red-600 hover:underline flex items-center" title="แผนที่ออกงาน">
                                                    <i class="fas fa-map-marker-alt mr-1"></i>ออก
                                                </a>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-gray-500 md:col-span-3">ไม่พบประวัติการลงเวลา</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Calendar View -->
        <div id="view-calendar" class="hidden bg-white p-4 rounded-2xl shadow-lg">
            <div id='calendar'></div>
        </div>
    </div>
</main>

<!-- ===== จุดที่เพิ่ม: HTML สำหรับ Image Modal ===== -->
<div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden p-4 image-modal-backdrop">
    <div class="relative bg-white p-2 rounded-lg max-w-3xl max-h-full shadow-xl">
        <button id="closeImageModal" class="absolute -top-3 -right-3 text-white bg-gray-800 rounded-full w-8 h-8 flex items-center justify-center text-xl font-bold">&times;</button>
        <img id="modalImage" src="" alt="ขยายรูปภาพ" class="max-w-full max-h-[90vh] object-contain rounded">
    </div>
</div>
<!-- ============================================= -->

<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/th.js'></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab switching logic
        const tabCard = document.getElementById('tab-card');
        const tabCalendar = document.getElementById('tab-calendar');
        const viewCard = document.getElementById('view-card');
        const viewCalendar = document.getElementById('view-calendar');
        const calendarEl = document.getElementById('calendar');
        const imageModal = document.getElementById('imageModal');
        const modalImage = document.getElementById('modalImage');
        const closeImageModal = document.getElementById('closeImageModal');
        const imageTriggers = document.querySelectorAll('.view-image-trigger');
        let calendar; // Declare calendar variable

        // Image modal logic
                imageTriggers.forEach(trigger => {
            trigger.addEventListener('click', function() {
                const imgSrc = this.getAttribute('src');
                if (imgSrc) {
                    modalImage.setAttribute('src', imgSrc);
                    imageModal.classList.remove('hidden');
                }
            });
        });

        function closeModal() {
            imageModal.classList.add('hidden');
            modalImage.setAttribute('src', ''); // Clear src to prevent loading issues
        }

        closeImageModal.addEventListener('click', closeModal);
        
        // Close modal when clicking on the backdrop
        imageModal.addEventListener('click', function(event) {
            if (event.target === imageModal) {
                closeModal();
            }
        });

        function switchTab(activeTab) {
            if (activeTab === 'calendar') {
                tabCard.classList.remove('tab-active');
                tabCalendar.classList.add('tab-active');
                viewCard.classList.add('hidden');
                viewCalendar.classList.remove('hidden');
                if (!calendar) { // Initialize calendar only once
                    initializeCalendar();
                }
                calendar.render();
            } else {
                tabCalendar.classList.remove('tab-active');
                tabCard.classList.add('tab-active');
                viewCalendar.classList.add('hidden');
                viewCard.classList.remove('hidden');
            }
        }

        tabCard.addEventListener('click', () => switchTab('card'));
        tabCalendar.addEventListener('click', () => switchTab('calendar'));

        // Prepare data for FullCalendar
        const attendanceEvents = <?php
            if (isset($attendance_logs)) {
                $events = [];
                foreach ($attendance_logs as $row) {
                    $color = '#3b82f6'; // ปกติ (สีน้ำเงิน)
                    $title = '✓ ' . date('H:i', strtotime($row['clock_in_time']));
                    if ($row['status'] == 'สาย') {
                        $color = '#f59e0b'; // สีเหลือง
                        $title = '⚠ ' . date('H:i', strtotime($row['clock_in_time']));
                    } elseif ($row['status'] == 'ขาดงาน') {
                        $color = '#ef4444'; // สีแดง
                        $title = '✗ ขาดงาน';
                    }
                    $events[] = [
                        'title' => $title,
                        'start' => date('Y-m-d', strtotime($row['clock_in_time'])),
                        'backgroundColor' => $color,
                        'borderColor' => $color
                    ];
                }
                echo json_encode($events);
            } else {
                echo '[]';
            }
        ?>;

        // FullCalendar initialization function
        function initializeCalendar() {
            calendar = new FullCalendar.Calendar(calendarEl, {
                locale: 'th',
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,listWeek'
                },
                events: attendanceEvents,
                eventDisplay: 'block'
            });
        }
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
