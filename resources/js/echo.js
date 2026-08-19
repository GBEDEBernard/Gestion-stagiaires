/**
 * Echo instantiation with Reverb + fallback.
 *
 * - Attempts to connect to Laravel Reverb WebSocket on first load
 * - Falls back to polling if WebSocket unavailable
 * - Handles ngrok tunneling (WebSocket port 8080 may not pass through)
 * - Gracefully handles missing dependencies (polling still works)
 *
 * Usage:
 *   window.Echo.private(`task.${task_id}`)
 *       .listen('message.created', (event) => { ... })
 *       .listen('reaction.added', (event) => { ... });
 */

let Echo = null;
window.Echo = null;

async function initEcho() {
    const wsHost = import.meta.env.VITE_REVERB_HOST || import.meta.env.VITE_PUSHER_HOST;
    const wsKey  = import.meta.env.VITE_REVERB_APP_KEY || import.meta.env.VITE_PUSHER_APP_KEY;
    if (!wsHost || !wsKey) {
        console.log('[Echo] Skipped — Reverb host not configured');
        Echo = null;
        window.Echo = null;
        return;
    }

    try {
        const [{ default: EchoLib }, Pusher] = await Promise.all([
            import('laravel-echo'),
            import('pusher-js'),
        ]);

        window.Pusher = Pusher.default;

        const wsScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';
        const wsPort   = parseInt(import.meta.env.VITE_REVERB_PORT || '8080', 10);

        const echoConfig = {
            broadcaster: 'reverb',
            key: wsKey,
            wsHost,
            wsPort,
            wssPort: wsPort,
            forceTLS: wsScheme === 'https',
            enabledTransports: ['ws', 'wss'],
            disableStats: true,
            csrfToken: document.querySelector('meta[name="csrf-token"]')?.content,
        };

        Echo = new EchoLib(echoConfig);
        window.Echo = Echo;
        console.log('[Echo] Initialized');
    } catch (e) {
        console.log('[Echo] Not available - polling will handle updates');
        Echo = null;
        window.Echo = null;
    }
}

initEcho();

export default Echo;