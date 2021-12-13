import Toastify from 'toastify-js';

export default {
    success: function(text) {
        Toastify({
            text,
            gravity: 'bottom'
        }).showToast();
    }
}