import Vue from 'vue';

import VueI18n from 'vue-i18n';
Vue.use(VueI18n);

const lang = document.documentElement.lang.substr(0, 2);
import Locale from './vue-i18n-locales.generated.js';

const i18n = new VueI18n({
    locale: lang,
    messages: Locale
});

import c404 from './components/404.vue';

window.app = new Vue({
    el: '#content',
    components: {
        c404
    },
    i18n
});
