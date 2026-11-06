import { createApp } from 'vue';
import { createI18n } from 'vue-i18n';
import Locale from '../vue-i18n-locales.generated.js';

// vue-i18n >= 9 parses "@" as a linked message and drops unknown {params}:
// keep literal "@" (emails) and the {SANTA}/{TARGET} keywords
const escape = m => typeof m === 'string'
    ? m.replace(/@/g, "{'@'}").replace(/\{([A-Z]+)\}/g, "{'{'}$1{'}'}")
    : Array.isArray(m) ? m.map(escape) : Object.fromEntries(Object.entries(m).map(([k, v]) => [k, escape(v)]));

export default function mount(selector, options) {
    const app = createApp({
        ...options,
        mounted() {
            document.body.classList.add('cssLoading');
            setTimeout(() => document.body.classList.remove('cssLoading'), 0);
        },
    });
    app.use(createI18n({
        legacy: true,
        locale: document.documentElement.lang.substr(0, 2),
        messages: escape(Locale),
    }));
    window.app = app.mount(selector);
}
