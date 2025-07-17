<?php 
if (!defined('BASE_URL')) { require_once __DIR__ . '/../../config/app.php'; }
require_once __DIR__ . '/../layouts/header.php'; 
?>
<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
<style>
    /* Custom styles for FullCalendar */
    .fc-event { border: none !important; }
    .fc-daygrid-event { padding: 2px 5px; font-size: 0.75rem; }
    .fc-toolbar-title { font-size: 1.25rem !important; }
    .fc-button { text-transform: capitalize !important; }
    .tab-active { 
        border-color: #4f46e5; 
        color: #4f46e5;
        background-color: #eef2ff;
    }
</style>
<?php
require_once __DIR__ . '/../layouts/sidebar.php'; 
?>

<main class="flex-1 p-4 sm:p-6 lg:p-8">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-indigo-900">ประวัติการลา</h1>
            <p class="text-gray-500 mt-1">ตรวจสอบสถานะและประวัติการลาของคุณ</p>
        </div>
        <a href="<?php echo BASE_URL; ?>/dashboard" class="bg-gray-200 hover:bg-gray-300 text-gray-800 font-bold py-2 px-4 rounded-lg">กลับ</a>
        <a href="<?php echo BASE_URL; ?>/leave/create" class="mt-4 sm:mt-0 w-full sm:w-auto bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-lg flex items-center justify-center">
            <i class="fas fa-plus mr-2"></i>
            ยื่นใบลา
        </a>
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
                        // เราต้อง reset pointer ของ $stmt เพราะจะใช้ loop 2 ครั้ง
                        $leave_requests = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <?php foreach ($leave_requests as $row): ?>
                        <?php
                            $status_color = 'bg-yellow-100 text-yellow-800 border-yellow-400';
                            if ($row['status'] == 'อนุมัติ') {
                                $status_color = 'bg-green-100 text-green-800 border-green-400';
                            } elseif ($row['status'] == 'ไม่อนุมัติ') {
                                $status_color = 'bg-red-100 text-red-800 border-red-400';
                            }
                        ?>
                        <div class="bg-white rounded-xl shadow-md overflow-hidden transform hover:scale-105 transition-transform duration-300">
                            <div class="p-5">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-lg font-bold text-indigo-800"><?php echo htmlspecialchars($row['leave_type_name']); ?></h3>
                                    <span class="px-3 py-1 text-xs font-semibold rounded-full border <?php echo $status_color; ?>">
                                        <?php echo htmlspecialchars($row['status']); ?>
                                    </span>
                                </div>
                                <p class="text-sm text-gray-600 mt-2 line-clamp-2">
                                    <?php echo htmlspecialchars($row['reason']); ?>
                                </p>
                                <div class="mt-4 border-t pt-4 text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-check text-green-500 mr-2"></i>
                                        <span><?php echo date('d/m/Y', strtotime($row['start_date'])); ?> - <?php echo date('d/m/Y', strtotime($row['end_date'])); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center text-gray-500 md:col-span-3">ไม่พบประวัติการลา</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Calendar View -->
        <div id="view-calendar" class="hidden bg-white p-4 rounded-2xl shadow-lg">
            <div id='calendar'></div>
        </div>
    </div>
</main>

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

        function switchTab(activeTab) {
            if (activeTab === 'calendar') {
                tabCard.classList.remove('tab-active');
                tabCalendar.classList.add('tab-active');
                viewCard.classList.add('hidden');
                viewCalendar.classList.remove('hidden');
                calendar.render(); // Re-render the calendar when tab is shown
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
        const leaveEvents = <?php
            if (isset($leave_requests)) {
                $events = [];
                foreach ($leave_requests as $row) {
                    $color = '#f59e0b'; // Default: รออนุมัติ (สีเหลือง)
                    if ($row['status'] == 'อนุมัติ') {
                        $color = '#22c55e'; // สีเขียว
                    } elseif ($row['status'] == 'ไม่อนุมัติ') {
                        $color = '#ef4444'; // สีแดง
                    }
                    $events[] = [
                        'title' => $row['leave_type_name'],
                        'start' => $row['start_date'],
                        'end' => date('Y-m-d', strtotime($row['end_date'] . ' +1 day')), // Add 1 day for proper end date rendering
                        'backgroundColor' => $color,
                        'borderColor' => $color
                    ];
                }
                echo json_encode($events);
            } else {
                echo '[]';
            }
        ?>;

        // FullCalendar initialization
        var calendar = new FullCalendar.Calendar(calendarEl, {
            locale: 'th',
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek'
            },
            events: leaveEvents,
            eventDisplay: 'block'
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
