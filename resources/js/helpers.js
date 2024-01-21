/*export const getMimeTypeFromData = function(data) {

    var mimes = [
        {
            mime: 'image/jpeg',
            pattern: [0xFF, 0xD8, 0xFF],
            mask: [0xFF, 0xFF, 0xFF],
        },
        {
            mime: 'image/png',
            pattern: [0x89, 0x50, 0x4E, 0x47],
            mask: [0xFF, 0xFF, 0xFF, 0xFF],
        }
        // you can expand this list @see https://mimesniff.spec.whatwg.org/#matching-an-image-type-pattern
    ];

    var bytes = new Uint8Array(data);
    return mimes.find(mime => {
        for (var i = 0, l = mime.mask.length; i < l; ++i) {
            if ((bytes[i] & mime.mask[i]) - mime.pattern[i] !== 0) {
                return false;
            }
        }
        return true;
    }).mime || "application/unknown";

};

export const getExtensionFromMime = function(mime) {
    switch(mime) {
        case 'text/csv':
        case 'image/png':
        case 'image/jpeg':
            return mime.split('/')[1];
    }
};*/

export const download = (data, fileName, mimeType) => {
    var blob = new Blob([data]);
    blob = blob.slice(0, blob.size, mimeType);

    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', fileName);
    document.body.appendChild(link);
    link.click();
};

export const isString = arg => {
    return typeof arg === 'string' || arg instanceof String;
};

export const isObject = arg => {
    return Object.prototype.toString.call(arg) === '[object Object]';
};

export const isArray = arg => {
    return Object.prototype.toString.call(arg) === '[object Array]';
};

export const isBoolean = arg => {
    return typeof arg === 'boolean';
};

export const has = (object, key) => {
    return isObject(object) && object.hasOwnProperty(key);
};

export const get = (object, key, defaultValue) => {
    return has(object, key) ? object[key] : defaultValue;
};

export const px = value => {
    return `${value}px`;
};

export const translate = (x, y) => {
    return `translate(${x}, ${y})`;
};

import _ from 'lodash';
export const deepMerge = (object, ...sources) => {
    return _.mergeWith(
        object,
        ...sources,
        function (objValue, srcValue) {
            if (_.isArray(objValue)) {
                return objValue.concat(srcValue);
            }
        }
    );
};

Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};
