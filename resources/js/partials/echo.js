import Echo from "laravel-echo"
import Pusher from 'pusher-js';
window.Pusher = Pusher;

// no websocket key configured -> no live updates, the statuses are polled instead (see follow()). Configure Reverb to enable.
const noop = { channel: () => noop, listen: () => noop, subscribed: () => noop };

const echo = window.global.pusher.key ? new Echo({
    broadcaster: 'reverb',
    key: window.global.pusher.key,
    wsHost: window.global.pusher.host,
    wsPort: window.global.pusher.port || 443,
    wssPort: window.global.pusher.port || 443,
    forceTLS: (window.global.pusher.scheme || 'https') === 'https',
    enabledTransports: ['ws', 'wss']
}) : noop;

export default echo;

export const POLL_DELAY = 10e3;

const connected = () => echo.connector?.pusher?.connection?.state === 'connected';

/**
 * Listens to a channel without losing anything:
 * - once subscribed (and again after each reconnection), `refresh` fetches what changed in between
 *   (between the page render and the subscription, or while disconnected)
 * - without websockets (none configured, unsupported or unreachable), `refresh` is called every POLL_DELAY
 *   while `pending()` says some status may still change
 */
export function follow(channel, listeners, refresh, pending) {
    let subscription = echo.channel(channel);
    Object.entries(listeners).forEach(([event, callback]) => subscription = subscription.listen(event, callback));
    subscription.subscribed(() => refresh());

    let polling = false;
    setInterval(() => {
        if (polling || connected() || document.hidden || !pending()) return;
        polling = true;
        Promise.resolve(refresh()).catch(() => {}).finally(() => polling = false);
    }, POLL_DELAY);
}
