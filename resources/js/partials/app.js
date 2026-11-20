import { createApp, markRaw } from 'vue';
import { createI18n } from 'vue-i18n';
import de from '../langs/de.js';
import en from '../langs/en.js';
import fr from '../langs/fr.js';
import Fetched from '../components/fetched.vue';

// vue-i18n >= 9 parses "@" as a linked message and drops unknown {params}:
// keep literal "@" (emails) and the {SANTA}/{TARGET} keywords
const escape = m => typeof m === 'string'
    ? m.replace(/@/g, "{'@'}").replace(/\{([A-Z]+)\}/g, "{'{'}$1{'}'}")
    : Array.isArray(m) ? m.map(escape) : Object.fromEntries(Object.entries(m).map(([k, v]) => [k, escape(v)]));

// The page's component, mounted on its element: no template is compiled in the browser, which lets the CSP
// forbid eval on the pages holding the draw secret
export default function mount(selector, component, props = {}) {
    const app = createApp(component, props);
    app.use(createI18n({
        legacy: true,
        locale: document.documentElement.lang.substr(0, 2),
        fallbackLocale: 'fr',
        messages: escape({ ...de, ...en, ...fr }),
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
