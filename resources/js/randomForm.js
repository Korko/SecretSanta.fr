import { createApp } from 'vue'
import { i18nVue } from 'laravel-vue-i18n'

import RandomForm from './components/randomForm.vue';

window.app = createApp({
    components: {
        RandomForm
    },

    mounted: function() {
        document.body.classList.add('cssLoading');
        setTimeout(() => document.body.classList.remove('cssLoading'), 0);
    }
})
.use(i18nVue, {
    resolve: async lang => {
        const langs = import.meta.glob('../../lang/*.json');
        return await langs[`../../lang/${lang}.json`]();
    }
})
.mount('#wrap');
