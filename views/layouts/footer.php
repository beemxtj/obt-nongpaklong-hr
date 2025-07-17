<?php
// views/layouts/footer.php
?>
        </main> <!-- ปิด <main> ที่เปิดในไฟล์ View แต่ละหน้า -->
    </div> <!-- ปิด <div class="flex-1 flex flex-col"> ที่เปิดใน sidebar.php -->
</div> <!-- ปิด <div class="flex min-h-screen"> ที่เปิดใน sidebar.php -->

<!-- JavaScript หลักของระบบ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Logic สำหรับเมนูบนมือถือ ---
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const sidebar = document.querySelector('aside'); // เลือก <aside> โดยตรง

    if (mobileMenuButton && sidebar) {
        mobileMenuButton.addEventListener('click', function() {
            sidebar.classList.toggle('hidden');
        });
    }

    // --- Logic สำหรับ Dropdown การแจ้งเตือน ---
    const notificationButton = document.getElementById('notification-button');
    const notificationPanel = document.getElementById('notification-panel');

    if (notificationButton && notificationPanel) {
        notificationButton.addEventListener('click', function(event) {
            event.stopPropagation(); // ป้องกันการปิดทันทีเมื่อคลิก
            notificationPanel.classList.toggle('hidden');
            
            // (Optional) หากต้องการให้เมื่อเปิดแล้ว สถานะ unread หายไป
            // สามารถส่ง AJAX request ไปยัง server เพื่อ mark as read ได้ที่นี่
        });

        // ปิด Dropdown เมื่อคลิกที่อื่น
        window.addEventListener('click', function(event) {
            if (!notificationPanel.classList.contains('hidden') && !notificationButton.contains(event.target)) {
                notificationPanel.classList.add('hidden');
            }
        });
    }

    // --- Logic สำหรับนาฬิกาบน Dashboard ---
    // จะทำงานเฉพาะหน้าที่มี element id="clock" เท่านั้น
    const clockEl = document.getElementById('clock');
    if (clockEl) {
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
            const dateString = now.toLocaleDateString('th-TH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            document.getElementById('clock').textContent = timeString;
            document.getElementById('date').textContent = dateString;
        }
        updateClock();
        setInterval(updateClock, 1000);
    }
});
</script>

</body>
</html>
