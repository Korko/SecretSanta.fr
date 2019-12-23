import Vue from 'vue'

import VueFetcher from './fetcherVue.js'

import InputEdit from './inputEdit.vue'
Vue.component('InputEdit', InputEdit);

window.app = new Vue({
  mixins: [VueFetcher]
});


