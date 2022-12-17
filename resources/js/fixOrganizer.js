import Vue from 'vue';

import VueI18n from 'vue-i18n';
Vue.use(VueI18n);

const lang = document.documentElement.lang.substr(0, 2);
import Locale from './vue-i18n-locales.generated.js';

const i18n = new VueI18n({
    locale: lang,
    messages: Locale
});

import FixOrganizerForm from './components/fixOrganizerForm.vue';

window.app = new Vue({
    el: '#content',
    components: {
        FixOrganizerForm
    },
    i18n,
    mounted: function() {
        document.body.classList.add('cssLoading');
        setTimeout(() => document.body.classList.remove('cssLoading'), 0);
    }
});
