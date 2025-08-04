<?php
// views/layouts/footer.php
?>
        </main> <!-- ปิด <main> ที่เปิดในไฟล์ View แต่ละหน้า -->
    </div> <!-- ปิด <div class="flex-1 flex flex-col"> -->
</div> <!-- ปิด <div> wrapper หลัก -->

<!-- JavaScript หลักของระบบ -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Logic สำหรับเมนูบนมือถือ ---
    const mobileMenuButton = document.getElementById('mobile-menu-button');
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
                notificationPanel.classList.add('hidden');
            }
        });
    }
    
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
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('th-TH', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
            const dateString = now.toLocaleDateString('th-TH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            if(clockEl) clockEl.textContent = timeString;
            if(dateEl) dateEl.textContent = dateString;
        }
        updateClock();
        setInterval(updateClock, 1000);
    }
});
</script>
</body>
</html>
