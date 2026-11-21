import mount from './partials/app.js';
import Faq from './components/faq.vue';

mount('#faq', Faq, { questions: JSON.parse(document.getElementById('faq').dataset.questions) });
