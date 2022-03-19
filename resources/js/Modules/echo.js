import Echo from "laravel-echo"
window.Pusher = require('pusher-js');

export default new Echo({
    broadcaster: 'pusher',
    key: window.global.pusher.key,
    wsHost: window.global.pusher.host,
    wsPort: window.global.pusher.port || 443,
    useTLS: !(window.global.pusher.port === 80),
    disableStats: true,
    enabledTransports: (window.global.pusher.port === 80) ? ['ws'] : ['wss']
});