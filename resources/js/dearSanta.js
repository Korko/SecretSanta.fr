import Vue from 'vue';

import VueFetcher from './mixins/fetcherVue.js';

window.app = new Vue({
    mixins: [VueFetcher]
});
