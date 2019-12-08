import Vue from 'vue'

import { mapState } from 'vuex'

import VueAjax from './ajaxVue.js'
import VueStates from './statesVue.js'
import store from './store.js'
import Timer from './timer.vue'

window.app = new Vue({
  mixins: [VueStates],
  components: {
    DearSantaFailure: {
      template: '#error-template'
    },
    DearSantaFetcher: {
      template: '#fetcher-template',
      mixins: [VueAjax],
      components: {Timer},
      props: ['formurl'],
      data: () => {
        return {
          loading: false
        };
      },
      computed: mapState(['csrf', 'key']),

      mounted() {
        this.$nextTick(function () {
          this.loading = true;
          this.submitForm('#fetch', {
            success: (json) => {
              this.$emit('success', json);
            },
            error: () => {
              this.$emit('error');
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
            'failure': 'DearSantaFailure'
        }
    })
  }
});


