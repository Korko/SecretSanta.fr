import Echo from "laravel-echo"
import Pusher from 'pusher-js';
window.Pusher = Pusher;

// ponytail: no websocket key configured -> no live mail status updates (reload shows them). Configure Reverb to enable.
const noop = { channel: () => noop, listen: () => noop };

export default window.global.pusher.key ? new Echo({
    broadcaster: 'pusher',
    key: window.global.pusher.key,
    wsHost: window.global.pusher.host,
    wsPort: 443,
    disableStats: true,
    enabledTransports: ['ws', 'wss']
}) : noop;
