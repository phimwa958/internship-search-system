document.addEventListener('DOMContentLoaded', function () {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const toggleIcon = document.getElementById('toggleIcon');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            const isCollapsed = sidebar.classList.contains('w-16');

            if (isCollapsed) {
                // Expand
                sidebar.classList.remove('w-16', 'min-w-[4rem]');
                sidebar.classList.add('w-64', 'min-w-[16rem]');
            } else {
                // Collapse
                sidebar.classList.remove('w-64', 'min-w-[16rem]');
                sidebar.classList.add('w-16', 'min-w-[4rem]');
            }

            // Toggle texts
            const texts = sidebar.querySelectorAll('.sidebar-text');
            texts.forEach(text => text.classList.toggle('hidden'));

            // Center icons when collapsed
            const links = sidebar.querySelectorAll('.sidebar-link');
            links.forEach(link => {
                link.classList.toggle('justify-center');
            });

            const brand = sidebar.querySelector('.sidebar-brand');
            if (brand) {
                brand.classList.toggle('justify-center');
            }

            // Arrow direction
            if (isCollapsed) {
                toggleIcon.classList.remove('fa-angle-double-right');
                toggleIcon.classList.add('fa-angle-double-left');
            } else {
                toggleIcon.classList.remove('fa-angle-double-left');
                toggleIcon.classList.add('fa-angle-double-right');
            }
        });
    }

    // Mobile backdrop/sidebar logic if needed in future
});
