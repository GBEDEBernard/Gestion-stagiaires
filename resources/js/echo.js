/**
 * Laravel Echo + Reverb (protocole Pusher).
 *
 * Objectifs :
 *  - Temps réel quand le serveur Reverb (`php artisan reverb:start`) est joignable.
 *  - Dégradation gracieuse : si aucun serveur n'est configuré/joignable
 *    (clé absente, port WS non exposé via ngrok, contenu mixte HTTPS→ws…),
 *    `window.Echo` vaut null et le chat bascule sur le polling. Rien ne casse.
 *
 * Utilisation côté Alpine :
 *   window.Echo?.private(`task.${id}`)
 *       .listen('message.created', (e) => { ... });
 */

import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;

const key = import.meta.env.VITE_REVERB_APP_KEY;

// Pas de clé => pas de serveur à contacter : on reste en polling (zéro retry WS).
if (key) {
    try {
        const scheme = import.meta.env.VITE_REVERB_SCHEME ?? 'http';
        const forceTLS = scheme === 'https';

        window.Echo = new Echo({
            broadcaster: 'reverb',
            key,
            wsHost: import.meta.env.VITE_REVERB_HOST ?? window.location.hostname,
            wsPort: Number(import.meta.env.VITE_REVERB_PORT ?? 8080),
            wssPort: Number(import.meta.env.VITE_REVERB_PORT ?? 443),
            forceTLS,
            enabledTransports: ['ws', 'wss'],
        });

        // Les erreurs de connexion ne doivent pas remonter : le polling couvre.
        window.Echo.connector?.pusher?.connection?.bind('error', () => {});
    } catch (e) {
        console.warn('[Echo] init échouée, repli sur le polling :', e?.message ?? e);
        window.Echo = null;
    }
} else {
    window.Echo = null;
}

export default window.Echo;
