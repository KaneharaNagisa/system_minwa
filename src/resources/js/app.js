document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.querySelector('[data-admin-sidebar]');
    const sidebarToggle = document.querySelector('[data-admin-sidebar-toggle]');
    const sidebarBackdrop = document.querySelector('[data-admin-sidebar-backdrop]');

    if (sidebar && sidebarToggle) {
        const closeSidebar = () => {
            sidebar.classList.remove('is-open');
            sidebarBackdrop?.classList.remove('is-open');
            sidebarToggle.setAttribute('aria-expanded', 'false');
        };

        sidebarToggle.addEventListener('click', () => {
            const isOpen = sidebar.classList.toggle('is-open');
            sidebarBackdrop?.classList.toggle('is-open', isOpen);
            sidebarToggle.setAttribute('aria-expanded', String(isOpen));
        });

        sidebarBackdrop?.addEventListener('click', closeSidebar);
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 768) closeSidebar();
        });
    }

    const body = document.querySelector('#story-body');
    if (!body) return;

    document.querySelectorAll('[data-font-size]').forEach((button) => {
        button.addEventListener('click', () => {
            const size = button.dataset.fontSize;
            body.classList.remove('text-small', 'text-medium', 'text-large');
            body.classList.add(`text-${size}`);

            document.querySelectorAll('[data-font-size]').forEach((item) => item.classList.remove('active'));
            button.classList.add('active');
        });
    });
});
