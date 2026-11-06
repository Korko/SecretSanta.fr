import mount from './partials/app.js';
import OrganizerForm from './components/organizerForm.vue';
import { VueFetcher } from './mixins/vueFetcher.js';

mount('#wrap', {
    mixins: [VueFetcher(OrganizerForm)],
    data: () => ({ routes: window.global.routes }),
});
