import Vue from 'vue'

import VueFetcher from './fetcherVue.js'

window.app = new Vue({
  mixins: [VueFetcher]
});


