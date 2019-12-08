import Vue from 'vue'

import VueAjax from './ajaxVue.js'
import VueStates from './statesVue.js'

import { mapState } from 'vuex'

import store from './store.js'

window.app = new Vue({
  mixins: [VueStates],
  components: {
    DearSantaFetcher: {
      mixins: [VueAjax],
      props: ['formurl'],
      computed: mapState(['csrf', 'key']),

      mounted() {
        this.$nextTick(function () {
          this.call(this.formurl, {
            data: {
              _token: this.csrf,
              key: this.key
            },
            success: (json) => {
              this.$emit('success',json);
            }
          });
        });
      }
    },
    DearSantaForm: {
      template: '#form-template',
      mixins: [VueAjax],
      props: ['formurl'],
      computed: mapState(['csrf', 'key', 'data'])
    }
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


