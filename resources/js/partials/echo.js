import Echo from "laravel-echo"
import Pusher from 'pusher-js';
import fetch from './fetch.js';
window.Pusher = Pusher;

// no websocket key configured -> no live updates, the statuses are polled instead (see follow()). Configure Reverb to enable.
const noop = { private: () => noop, listen: () => noop, subscribed: () => noop };

// No accounts: each private channel is authorized by the signed URL its page was given (BroadcastAuthController)
const authUrls = {};

const echo = window.global.pusher.key ? new Echo({
    broadcaster: 'reverb',
    key: window.global.pusher.key,
    wsHost: window.global.pusher.host,
    wsPort: window.global.pusher.port || 443,
    wssPort: window.global.pusher.port || 443,
    forceTLS: (window.global.pusher.scheme || 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
    authorizer: channel => ({
        authorize: (socketId, callback) => {
            const { url, headers } = authUrls[channel.name.replace(/^private-/, '')] || {};
            if(!url) return callback(new Error('No authorization URL for '+channel.name), null);
            fetch(url, 'POST', { socket_id: socketId }, headers)
                .then(data => callback(null, data))
                .catch(error => callback(error instanceof Error ? error : new Error('Channel authorization failed'), null));
        }
    })
}) : noop;

export default echo;

export const POLL_DELAY = 10e3;

const connected = () => echo.connector?.pusher?.connection?.state === 'connected';

/**
 * Listens to a private channel, authorized by `authUrl` (with `authHeaders`, if any), without losing anything:
 * - once subscribed (and again after each reconnection), `refresh` fetches what changed in between
 *   (between the page render and the subscription, or while disconnected)
 * - without websockets (none configured, unsupported or unreachable), `refresh` is called every POLL_DELAY
 *   while `pending()` says some status may still change
 */
export function follow(channel, authUrl, listeners, refresh, pending, authHeaders) {
    authUrls[channel] = { url: authUrl, headers: authHeaders };
    let subscription = echo.private(channel);
    Object.entries(listeners).forEach(([event, callback]) => subscription = subscription.listen(event, callback));
    subscription.subscribed(() => refresh());

    let polling = false;
    setInterval(() => {
        if (polling || connected() || document.hidden || !pending()) return;
        polling = true;
        Promise.resolve(refresh()).catch(() => {}).finally(() => polling = false);
    }, POLL_DELAY);
}
