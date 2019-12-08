import { mapState } from 'vuex'

import VueAjax from './ajaxVue.js'
import VueStates from './statesVue.js'
import store from './store.js'
import Timer from './timer.vue'

export default {
  mixins: [VueStates],
  components: {
    Failure: {
      template: '#error-template'
    },
    Fetcher: {
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
    Form: {
      template: '#form-template',
      mixins: [VueAjax],
      props: ['formurl'],
      computed: mapState(['csrf', 'key', 'data'])
    }
  },

  el: '#form',

  store,

  data: {
    state: 'Fetcher',
    states: Object.freeze({
        'Fetcher': {
            'success': 'Form',
            'failure': 'Failure'
        }
    })
  }
};
