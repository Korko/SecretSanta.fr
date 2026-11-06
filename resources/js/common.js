// Header sizing, banner centering, arrow animation and smooth anchor scrolling are pure CSS now
// (randomForm.scss / layout.scss). Only the compact menu after 200px of scroll needs JS.
document.addEventListener('scroll', () => document.body.classList.toggle('scrolled', window.scrollY > 200), { passive: true });

import alertify from './partials/alertify.js';
if (window.global && window.global.alert) {
    alertify.alert(window.global.alert);
}

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