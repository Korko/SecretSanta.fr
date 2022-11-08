import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3'
import { createI18n } from 'vue-i18n';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';

createInertiaApp({
    resolve: name => import(`./Pages/${name}.vue`),
    setup({ el, App, props, plugin }) {
        const i18n = createI18n({
            locale: props.initialPage.props.app.locale,
            messages: props.initialPage.props.app.translations,
            globalInjection: true
        });

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(i18n)
            .use(ZiggyVue, Ziggy)
            .mount(el)
    },
});

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
