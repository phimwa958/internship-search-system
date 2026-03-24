document.addEventListener('DOMContentLoaded', () => {
    const menuToggle = document.getElementById('menu-toggle');
    const navbar = document.getElementById('navbar');

    if (menuToggle && navbar) {
        menuToggle.addEventListener('click', () => {
            navbar.classList.toggle('hidden');
        });
    }

    document.querySelectorAll('.dropdown-btn').forEach((btn) => {
        btn.addEventListener('click', (e) => {
            const dropdownId = btn.getAttribute('data-dropdown');
            const dropdown = document.getElementById(dropdownId);
            const isHidden = dropdown.classList.contains('hidden');

            document.querySelectorAll('.dropdown').forEach(d => d.classList.add('hidden'));

            if (isHidden) {
                dropdown.classList.remove('hidden');
            } else {
                dropdown.classList.add('hidden');
            }

            e.stopPropagation();
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.dropdown').forEach(d => d.classList.add('hidden'));
    });
});
