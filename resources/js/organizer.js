import Vue from 'vue';
import OrganizerForm from './components/organizerForm.vue';
import { VueFetcher } from './mixins/vueFetcher.js';
window.app = new Vue({
    mixins: [VueFetcher(OrganizerForm)]
});
