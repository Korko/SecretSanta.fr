import Vue from 'vue';

import VueI18n from 'vue-i18n';
Vue.use(VueI18n);

const lang = document.documentElement.lang.substr(0, 2);
import Locale from './vue-i18n-locales.generated.js';

const i18n = new VueI18n({
    locale: lang,
    messages: Locale
});

import DearSantaForm from './components/dearSantaForm.vue';
import { VueFetcher } from './mixins/vueFetcher.js';

window.app = new Vue({
    mixins: [VueFetcher(DearSantaForm)],
    i18n,
    data: {
        routes: window.global.routes
    },
    mounted: function() {
        document.body.classList.add('cssLoading');
        setTimeout(() => document.body.classList.remove('cssLoading'), 0);
    }
});
