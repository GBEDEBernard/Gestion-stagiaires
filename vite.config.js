import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    server: {
        // Force IPv4 binding to avoid browsers trying IPv6 [::1] which can
        // cause HMR WebSocket failures on some Windows setups / firewalls.
        host: '127.0.0.1',
        port: 5173,
        hmr: {
            host: '127.0.0.1',
            protocol: 'ws',
        },
    },
});
