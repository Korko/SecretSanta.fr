import mount from './partials/app.js';
import FixOrganizerForm from './components/fixOrganizerForm.vue';

mount('#fix-organizer', FixOrganizerForm, { action: document.getElementById('fix-organizer').dataset.action });
