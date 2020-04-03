import Vue from 'vue';

import Vuex from 'vuex';
Vue.use(Vuex);

export default new Vuex.Store({
    state: {
        csrf: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        key: window.location.hash.substr(1)
    }
});
