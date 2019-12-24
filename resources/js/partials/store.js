import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

import Lang from '../partials/lang.js';

export default new Vuex.Store({
    state: {
        csrf: document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute('content'),
        key: window.location.hash.substr(1),
        lang: Lang
    }
});
