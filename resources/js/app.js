import './bootstrap';

import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';

Alpine.plugin(collapse);

window.Alpine = Alpine;

window.getPreferredAppTheme = () => {
    const savedTheme = localStorage.getItem('theme');

    if (savedTheme === 'dark' || savedTheme === 'light') {
        return savedTheme;
    }

    return window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
};

window.setAppTheme = (theme) => {
    const resolvedTheme = theme === 'dark' ? 'dark' : 'light';
    const isDark = resolvedTheme === 'dark';

    document.documentElement.classList.toggle('dark', isDark);
    document.documentElement.dataset.theme = resolvedTheme;
    localStorage.setItem('theme', resolvedTheme);
    document.dispatchEvent(new CustomEvent('theme:changed', {
        detail: {
            theme: resolvedTheme,
        },
    }));

    return resolvedTheme;
};

window.getAppChartTheme = () => {
    const isDark = document.documentElement.classList.contains('dark');

    return {
        isDark,
        gridColor: isDark ? 'rgba(148, 163, 184, 0.18)' : 'rgba(148, 163, 184, 0.22)',
        tickColor: isDark ? '#cbd5e1' : '#475569',
        borderColor: isDark ? 'rgba(51, 65, 85, 0.82)' : 'rgba(226, 232, 240, 0.92)',
        tooltipBg: isDark ? '#0f172a' : '#ffffff',
        tooltipBorder: isDark ? 'rgba(71, 85, 105, 0.9)' : 'rgba(203, 213, 225, 0.95)',
        tooltipText: isDark ? '#f8fafc' : '#0f172a',
        surface: isDark ? 'rgba(11, 18, 32, 0.88)' : 'rgba(255, 255, 255, 0.92)',
        accent: '#14b8a6',
        accentSoft: isDark ? 'rgba(20, 184, 166, 0.2)' : 'rgba(20, 184, 166, 0.12)',
        warn: '#f59e0b',
        primary: '#2563eb',
        success: '#10b981',
        textMuted: isDark ? '#94a3b8' : '#64748b',
    };
};

window.refreshLucideIcons = () => {
    if (window.lucide && typeof window.lucide.createIcons === 'function') {
        window.lucide.createIcons();
    }
};

document.addEventListener('DOMContentLoaded', () => {
    window.refreshLucideIcons();
});

Alpine.start();
