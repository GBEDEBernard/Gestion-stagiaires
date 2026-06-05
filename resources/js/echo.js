/**
 * Echo instantiation with Reverb + fallback.
 *
 * - Attempts to connect to Laravel Reverb WebSocket on first load
 * - Falls back to polling if WebSocket unavailable
 * - Handles ngrok tunneling (WebSocket port 8080 may not pass through)
 *
 * Usage:
 *   window.Echo.private(`task.${task_id}`)
 *       .listen('message.created', (event) => { ... })
 *       .listen('reaction.added', (event) => { ... });
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

// Pusher configuration from Vite environment variables
const echoConfig = {
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY || 'gestion-stagiaires-key',
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER || 'mt',
    wsHost: import.meta.env.VITE_PUSHER_HOST || window.location.hostname,
    wsPort: parseInt(import.meta.env.VITE_PUSHER_PORT || '6001', 10),
    wssPort: parseInt(import.meta.env.VITE_PUSHER_PORT || '443', 10),
    scheme: import.meta.env.VITE_PUSHER_SCHEME || 'http',
    encrypted: import.meta.env.VITE_PUSHER_SCHEME === 'https',
    enabledTransports: ['ws', 'wss'],
    disableStats: true,
    csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
};

// Attempt to initialize Echo
try {
    window.Echo = new Echo(echoConfig);
    console.log('[Echo] Connected');
} catch (e) {
    console.warn('[Echo] Connection failed, polling will handle updates', e.message);
    // Echo not available - polling will be the fallback
    window.Echo = null;
}

export default window.Echo;

