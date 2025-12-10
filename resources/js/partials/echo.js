import Echo from "laravel-echo"
import Pusher from 'pusher-js';
window.Pusher = Pusher;

export default new Echo({
    broadcaster: 'pusher',
    key: window.global.pusher.key,
    wsHost: window.global.pusher.host,
    wsPort: 443,
    disableStats: true,
    enabledTransports: ['ws', 'wss']
});