import { createApp, markRaw } from 'vue';
import { createI18n } from 'vue-i18n';
import { setLabels } from './alertify.js';
import Fetched from '../components/fetched.vue';

// One chunk per language: a page only loads its own (and French, the fallback)
const langs = import.meta.glob('../langs/*.js', { import: 'default' });
const locale = langs[`../langs/${document.documentElement.lang.substr(0, 2)}.js`] ? document.documentElement.lang.substr(0, 2) : 'fr';

// vue-i18n >= 9 parses "@" as a linked message and drops unknown {params}:
// keep literal "@" (emails) and the {SANTA}/{TARGET} keywords
const escape = m => typeof m === 'string'
    ? m.replace(/@/g, "{'@'}").replace(/\{([A-Z]+)\}/g, "{'{'}$1{'}'}")
    : Array.isArray(m) ? m.map(escape) : Object.fromEntries(Object.entries(m).map(([k, v]) => [k, escape(v)]));

// The page's component, mounted on its element: no template is compiled in the browser, which lets the CSP
// forbid eval on the pages holding the draw secret
export default async function mount(selector, component, props = {}) {
    const messages = Object.assign({}, ...await Promise.all([...new Set([locale, 'fr'])].map(lang => langs[`../langs/${lang}.js`]())));
    setLabels(messages[locale].common);

    const app = createApp(component, props);
    app.use(createI18n({
        legacy: true,
        locale,
        fallbackLocale: 'fr',
        messages: escape(messages),
    }));
    app.mount(selector);

    document.body.classList.add('cssLoading');
    setTimeout(() => document.body.classList.remove('cssLoading'), 0);
}

// Organizer and participant pages: their data comes from a signed URL, then the form shows
export function mountFetched(selector, form) {
    mount(selector, Fetched, {
        form: markRaw(form),
        url: document.querySelector(selector).dataset.url,
        routes: window.global.routes,
    });
}
