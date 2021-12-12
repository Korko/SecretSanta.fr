import { createApp, defineAsyncComponent } from 'vue';

const app = createApp({
    mounted: function() {
        document.body.classList.add('cssLoading');
        setTimeout(() => document.body.classList.remove('cssLoading'), 0);
    },
    components: {
        RandomForm: defineAsyncComponent(() => import('./components/random/form.vue')),
        OrganizerForm: defineAsyncComponent(() => import('./components/organizer/form.vue')),
        Dashboard: defineAsyncComponent(() => import('./components/dashboard.vue')),
        Faq: defineAsyncComponent(() => import('./components/faq.vue')),
        VueFetcher: defineAsyncComponent(() => import('./components/vueFetcher.vue'))
    }
});
app.mount('#main');

const lang = document.documentElement.lang.substr(0, 2);
import Locale from './vue-i18n-locales.generated.js';

import { createI18n } from 'vue-i18n';
const i18n = createI18n({
    locale: lang,
    messages: Locale,
    globalInjection: true
});

app.use(i18n);