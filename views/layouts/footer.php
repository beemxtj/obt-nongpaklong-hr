<?php
// views/layouts/footer.php
?>
        </main> <!-- ปิด <main> ที่เปิดในไฟล์ View แต่ละหน้า -->
<<<<<<< HEAD
    </div> <!-- ปิด <div class="flex-1 flex flex-col"> -->
</div> <!-- ปิด <div> wrapper หลัก -->
=======
    </div> <!-- ปิด <div class="flex-1 flex flex-col"> ที่เปิดใน sidebar.php -->
</div> <!-- ปิด <div class="flex min-h-screen"> ที่เปิดใน sidebar.php -->
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335

<!-- JavaScript หลักของระบบ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Logic สำหรับเมนูบนมือถือ ---
    const mobileMenuButton = document.getElementById('mobile-menu-button');
<<<<<<< HEAD
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebar-backdrop');

    if (mobileMenuButton && sidebar && backdrop) {
        const openSidebar = () => {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        };
        const closeSidebar = () => {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        };
        mobileMenuButton.addEventListener('click', openSidebar);
        backdrop.addEventListener('click', closeSidebar);
    }

    // --- Logic สำหรับ Dropdown การแจ้งเตือน ---
    const notificationDropdown = document.getElementById('notification-dropdown');
    if (notificationDropdown) {
        const notificationButton = notificationDropdown.querySelector('#notification-button');
        const notificationPanel = notificationDropdown.querySelector('#notification-panel');

        notificationButton.addEventListener('click', (event) => {
            event.stopPropagation();
            notificationPanel.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (!notificationDropdown.contains(event.target)) {
=======
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
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
                notificationPanel.classList.add('hidden');
            }
        });
    }
<<<<<<< HEAD
    
    // --- Logic สำหรับ Dropdown User ---
    const userDropdown = document.getElementById('user-dropdown');
    if (userDropdown) {
        const userButton = userDropdown.querySelector('#user-button');
        const userPanel = userDropdown.querySelector('#user-panel');

        userButton.addEventListener('click', (event) => {
            event.stopPropagation();
            userPanel.classList.toggle('hidden');
        });

        document.addEventListener('click', (event) => {
            if (!userDropdown.contains(event.target)) {
                userPanel.classList.add('hidden');
            }
        });
    }

    // --- Logic สำหรับเมนูย่อยใน Sidebar ---
    const dropdownToggles = document.querySelectorAll('[data-toggle="dropdown"]');
    dropdownToggles.forEach(toggle => {
        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            const submenu = this.nextElementSibling;
            const icon = this.querySelector('i.fas.fa-chevron-right, i.fas.fa-chevron-down');
            
            if (submenu && submenu.tagName === 'UL') {
                submenu.classList.toggle('hidden');
                if (icon) {
                    icon.classList.toggle('fa-chevron-down');
                    icon.classList.toggle('fa-chevron-right');
                }
            }
        });
    });

    // --- Logic สำหรับนาฬิกาบน Dashboard ---
    const clockEl = document.getElementById('clock');
    if (clockEl) {
        const dateEl = document.getElementById('date');
=======

    // --- Logic สำหรับนาฬิกาบน Dashboard ---
    // จะทำงานเฉพาะหน้าที่มี element id="clock" เท่านั้น
    const clockEl = document.getElementById('clock');
    if (clockEl) {
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
            const dateString = now.toLocaleDateString('th-TH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
<<<<<<< HEAD
            if(clockEl) clockEl.textContent = timeString;
            if(dateEl) dateEl.textContent = dateString;
=======
            document.getElementById('clock').textContent = timeString;
            document.getElementById('date').textContent = dateString;
>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
        }
        updateClock();
        setInterval(updateClock, 1000);
    }
});
</script>
<<<<<<< HEAD
=======

>>>>>>> ff710bbc79b0f85632a2e802010cfe13a0b48335
</body>
</html>
