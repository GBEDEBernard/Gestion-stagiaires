import './bootstrap';
import './echo';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import focus from '@alpinejs/focus';

Alpine.plugin(collapse);
Alpine.plugin(focus);

window.Alpine = Alpine;

// Global sidebar toggle function
window.toggleSidebar = function() {
    if (window.innerWidth < 1024) {
        const isOpen = document.documentElement.classList.toggle('sidebar-mobile-open');
        window.dispatchEvent(new CustomEvent('toggle-sidebar-mobile', { detail: { open: isOpen } }));
    } else {
        const isCollapsed = document.documentElement.classList.toggle('sidebar-collapsed');
        localStorage.setItem('sidebar-collapsed', isCollapsed ? 'true' : 'false');
        if (window.Alpine && Alpine.store('sidebar')) {
            Alpine.store('sidebar').collapsed = isCollapsed;
        }
    }
};

window.closeSidebarMobile = function() {
    document.documentElement.classList.remove('sidebar-mobile-open');
    window.dispatchEvent(new CustomEvent('close-sidebar'));
};

Alpine.store('sidebar', {
    collapsed: localStorage.getItem('sidebar-collapsed') === 'true',
    toggle() {
        window.toggleSidebar();
    }
});

Alpine.start();
