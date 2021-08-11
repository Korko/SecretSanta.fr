export default function(url, method, data, headers) {
    let body = undefined;
    if(data) {
        body = new FormData();
        Object.entries(data).forEach(([k, v]) => body.append(k, v));
    }

    return window.fetch(new Request(url, {
        method: method || 'GET',
        headers: {
            // Transmit Hash IV via request headers
            'X-HASH-IV': window.location.hash.substr(1),
            ...(headers || {})
        },
        body: body
    }))
    .then(response => {
        if((headers || {}).responseType) {
            return response.text();
        } else {
            return response.json();
        }
    });
};