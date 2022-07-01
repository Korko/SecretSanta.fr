import Echo from "laravel-echo"
import Pusher from 'pusher-js';
window.Pusher = Pusher;

export default new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    wsHost: import.meta.env.VITE_PUSHER_APP_HOST,
    wsPort: 443,
    disableStats: true,
    enabledTransports: ['ws', 'wss']
});