/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Transmit Hash Key via request headers
 */
const key = window.location.hash.substr(1);
if (key) {
    axios.defaults.headers.common['X-HASH-KEY'] = key;
}

setInterval(function() {
    // Nothing to do, this call will update the cookie if needed
    axios.get('/xsrf');
}, 5 * 60 * 1000); // Call every 5min

import alertify from '../partials/alertify.js';

axios.interceptors.response.use(function (response) {
    if (response.data?.message)
        alertify.success(response.data.message);

    return response;
}, function (error) {
    if (error.response?.data?.message)
        alertify.errorAlert(error.response.data.message);

    return error;
});

['get', 'delete', 'head', 'options'].forEach(method => {
    axios[method] = (url, config) => axios({
        method: method,
        url: url,
        ...config
    });
});

['post', 'put', 'patch'].forEach(method => {
    axios[method] = (url, data, config) => axios({
        method: method,
        url: url,
        data: data,
        ...config
    });
});

axios.request = axios;

export default axios;