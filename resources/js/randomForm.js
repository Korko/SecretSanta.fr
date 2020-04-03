import Vue from 'vue';
import RandomForm from './components/randomForm.vue';

import VueI18n from 'vue-i18n';
Vue.use(VueI18n);

const lang = document.documentElement.lang.substr(0, 2);
import Locale from './vue-i18n-locales.generated.js';

const i18n = new VueI18n({
    locale: lang,
    messages: Locale
});

window.app = new Vue({
    el: '#form',

    components: {
        RandomForm
    },

    i18n
});
