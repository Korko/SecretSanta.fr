import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3'

const lang = document.documentElement.lang.substr(0, 2);
import Locale from './vue-i18n-locales.generated.js';

import { createI18n } from 'vue-i18n';
const i18n = createI18n({
    locale: lang,
    messages: Locale,
    globalInjection: true
});

createInertiaApp({
    resolve: name => import(`./Pages/${name}`),
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .mount(el)
    },
});

//import VuejsDialog from 'vuejs-dialog';
//app.use(VueJsDialog);

Array.prototype.remove = function() {
    var what, a = arguments, L = a.length, ax;
    while (L && this.length) {
        what = a[--L];
        while ((ax = this.indexOf(what)) !== -1) {
            this.splice(ax, 1);
        }
    }
    return this;
};
