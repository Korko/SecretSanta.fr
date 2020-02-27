import Vue from 'vue';
import DearSantaForm from './components/dearSantaForm.vue';
import { VueFetcher } from './mixins/vueFetcher.js';
window.app = new Vue({
    mixins: [VueFetcher(DearSantaForm)]
});
