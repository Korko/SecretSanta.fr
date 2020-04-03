export default {
    csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
    key: window.location.hash.substr(1)
};
