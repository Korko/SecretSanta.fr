import Vue from 'vue'

import VueStates from './statesVue.js'

import DearSantaFetcher from './dearSantaFetcher.vue'
import DearSantaForm from './dearSantaForm.vue'

import store from './store.js'

window.app = new Vue({
  mixins: [VueStates],
  components: {
    DearSantaFetcher,
    DearSantaForm
  },

  el: '#form',

  store,

  data: {
    state: 'DearSantaFetcher',
    states: Object.freeze({
        'DearSantaFetcher': {
            'success': 'DearSantaForm',
            'failure': 'FetcherFailure'
        }
    })
  }
});


