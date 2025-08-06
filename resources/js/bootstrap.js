import Echo from 'laravel-echo'
import Pusher from 'pusher-js'
import axios from 'axios'

// Configuration Axios
window.axios = axios;
axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.withCredentials = true

// Configuration WebSocket avec Laravel Echo
window.Pusher = Pusher
window.Echo = new Echo({
    broadcaster: 'pusher',
    key: import.meta.env.VITE_PUSHER_APP_KEY,
    cluster: import.meta.env.VITE_PUSHER_APP_CLUSTER,
    forceTLS: true,
    encrypted: true,
    authEndpoint: '/broadcasting/auth',
    auth: {
        headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
        }
    }
})
