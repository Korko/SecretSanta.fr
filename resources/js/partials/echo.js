import Echo from "laravel-echo"
import Pusher from 'pusher-js';
window.Pusher = Pusher;

// no websocket key configured -> no live mail status updates (reload shows them). Configure Reverb to enable.
const noop = { channel: () => noop, listen: () => noop };

export default window.global.pusher.key ? new Echo({
    broadcaster: 'reverb',
    key: window.global.pusher.key,
    wsHost: window.global.pusher.host,
    wsPort: window.global.pusher.port || 443,
    wssPort: window.global.pusher.port || 443,
    forceTLS: (window.global.pusher.scheme || 'https') === 'https',
    enabledTransports: ['ws', 'wss']
}) : noop;
