import Echo from "laravel-echo"
window.Pusher = require('pusher-js');

export default new Echo({
    broadcaster: 'pusher',
    key: process.env.MIX_PUSHER_APP_KEY,
    wsHost: process.env.MIX_PUSHER_APP_HOST,
    wsPort: 443,
    disableStats: true,
    enabledTransports: ['ws', 'wss']
});