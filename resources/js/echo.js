/**
 * Laravel Echo initialization (Pusher protocol — works with Pusher,
 * a Pusher Fake Server, or Laravel Reverb, which speaks the same protocol).
 *
 * Design goals:
 *  - Real-time updates when a WebSocket server is reachable.
 *  - Graceful degradation: if the server is down or unreachable (e.g. behind
 *    an ngrok tunnel that doesn't forward the WS port), the app keeps working
 *    because the task chat falls back to polling.
 *
 * Usage from Alpine:
 *   window.Echo?.private(`task.${id}`)
 *       .listen('message.created', (e) => { ... });
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const key = import.meta.env.VITE_PUSHER_APP_KEY;

// Only attempt a connection when a key is configured. Without one there is no
// server to talk to, so we skip Echo entirely and let polling do the work.
if (key) {
    try {
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key,
            cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER ?? 'mt',
            wsHost: import.meta.env.VITE_PUSHER_HOST ?? window.location.hostname,
            wsPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 6001),
            wssPort: Number(import.meta.env.VITE_PUSHER_PORT ?? 443),
            forceTLS: (import.meta.env.VITE_PUSHER_SCHEME ?? 'http') === 'https',
            encrypted: (import.meta.env.VITE_PUSHER_SCHEME ?? 'http') === 'https',
            enabledTransports: ['ws', 'wss'],
            disableStats: true,
        });

        // Don't let connection errors bubble up as uncaught — polling covers us.
        window.Echo.connector?.pusher?.connection?.bind('error', () => {
            // Silent: the chat component continues polling.
        });
    } catch (e) {
        console.warn('[Echo] init failed, falling back to polling:', e?.message ?? e);
        window.Echo = null;
    }
} else {
    window.Echo = null;
}

export default window.Echo;
