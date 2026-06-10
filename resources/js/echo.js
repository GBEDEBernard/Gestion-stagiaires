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
    try {
        // Attempt to import dependencies lazily so Vite can target older browsers.
        const [{ default: EchoLib }, Pusher] = await Promise.all([
            import('laravel-echo'),
            import('pusher-js'),
        ]);

        window.Pusher = Pusher.default;

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
