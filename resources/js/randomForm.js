import mount from './partials/app.js';
import RandomForm from './components/randomForm.vue';
import './kraftMenu.js';

mount('#random-form', RandomForm);

// "Organiser mon tirage" from the top card: the event name is already copied, the next field to fill is the first participant
document.querySelectorAll('[data-focus-form]').forEach(link => link.addEventListener('click', () => {
    setTimeout(() => document.querySelector('#randomForm input:not([type=hidden])')?.focus({ preventScroll: true }), 0);
}));
