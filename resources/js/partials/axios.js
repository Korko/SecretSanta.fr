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

/**
 * Transmit CSRF via request headers
 * Also, to avoid timeout, request csrf frequently
 */
const setCsrf = function(csrf) { axios.defaults.headers.common['X-CSRF-TOKEN'] = csrf; };

import store from './store.js';
if (store.csrf) {
    setCsrf(store.csrf);
}

setInterval(function() {
    axios.get('/csrf').then(response => setCsrf(response.data));
}, 5 * 60 * 1000); // Call every 5min

import alertify from '../partials/alertify.js';

var myAxios = function (config) {
    return new Promise((resolve, reject) => {
        axios(config)
            .then(response => {
                if (response.data?.message)
                    alertify.success(response.data.message);

                resolve(response);
            })
            .catch(error => {
                if (error.response?.data?.message)
                    alertify.errorAlert(error.response.data.message);

                reject(error);
            });
    });
};

['get', 'delete', 'head', 'options'].forEach(method => {
    myAxios[method] = (url, config) => myAxios({
        method: method,
        url: url,
        ...config
    });
});

['post', 'put', 'patch'].forEach(method => {
    myAxios[method] = (url, data, config) => myAxios({
        method: method,
        url: url,
        data: data,
        ...config
    });
});

myAxios.request = myAxios;

export default myAxios;