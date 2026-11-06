import mount from './partials/app.js';
import DearSantaForm from './components/dearSantaForm.vue';
import { VueFetcher } from './mixins/vueFetcher.js';

mount('#wrap', {
    mixins: [VueFetcher(DearSantaForm)],
    data: () => ({ routes: window.global.routes }),
});
