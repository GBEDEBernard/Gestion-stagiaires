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

function probeReachable(url, timeoutMs = 2000) {
    return new Promise((resolve) => {
        const controller = new AbortController();
        const timer = setTimeout(() => controller.abort(), timeoutMs);
        fetch(url, { mode: 'no-cors', cache: 'no-store', signal: controller.signal })
            .then(() => resolve(true))
            .catch(() => resolve(false))
            .finally(() => clearTimeout(timer));
    });
}

async function initEcho() {
    const wsHost = import.meta.env.VITE_REVERB_HOST || import.meta.env.VITE_PUSHER_HOST;
    const wsKey  = import.meta.env.VITE_REVERB_APP_KEY || import.meta.env.VITE_PUSHER_APP_KEY;
    if (!wsHost || !wsKey) {
        console.log('[Echo] Skipped — Reverb host not configured');
        Echo = null;
        window.Echo = null;
        return;
    }

    const wsScheme = import.meta.env.VITE_REVERB_SCHEME || 'http';
    const wsPort   = parseInt(import.meta.env.VITE_REVERB_PORT || '8080', 10);

    // Probes Reverb before instantiating Echo to avoid a flood of failed
    // WebSocket attempts when the server is not running (falls back to polling).
    const healthUrl = `${wsScheme}://${wsHost}:${wsPort}/app/health`;
    const reachable = await probeReachable(healthUrl);
    if (!reachable) {
        console.log('[Echo] Skipped — Reverb injoignable, le polling prend le relais.');
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