import { isString, isObject, isArray } from './helpers.js';

export default function(url, method, data, headers) {
    let body = undefined;
    if(isObject(data) || isArray(data)) {
        body = new FormData();
        Object.entries(data).forEach(([k, v]) => body.append(k, v));
    } else if(data) {
        body = new URLSearchParams(data);
    }

    return window.fetch(new Request(url, {
        method: method || 'GET',
        headers: {
            // Transmit Hash IV via request headers
            'X-HASH-IV': window.location.hash.substr(1),
            'X-Requested-With': 'XMLHttpRequest',
            ...(headers || {})
        },
        body: body
    }))
    .then(response => {
        var data;
        if((headers || {}).responseType) {
            data = response.text();
        } else {
            data = response.json();
        }

        if(!response.ok) {
            // Triggering an error will not allow to pass object as parameter
            // Also, we need to await the Promise of the response parsing
            // before being able to return a failed Promise
            return data.then(data => Promise.reject(data));
        }
        return data;
    });
};