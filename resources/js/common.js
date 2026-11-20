// Header sizing, banner centering, arrow animation and smooth anchor scrolling are pure CSS now
// (randomForm.scss / layout.scss). Only the compact menu after 200px of scroll needs JS.
document.addEventListener('scroll', () => document.body.classList.toggle('scrolled', window.scrollY > 200), { passive: true });

import alertify from './partials/alertify.js';
if (window.global && window.global.alert) {
    alertify.alert(window.global.alert);
}
