import DearSantaForm from './components/dearSantaForm.vue';
import { VueFetcher } from './mixins/vueFetcher.js';

window.app = new Vue({
    mixins: [VueFetcher(DearSantaForm)],
    data: {
        routes: window.global.routes
    },
    mounted: function() {
        document.body.classList.add('cssLoading');
        setTimeout(() => document.body.classList.remove('cssLoading'), 0);
    }
});
