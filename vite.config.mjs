import { defineConfig, loadEnv } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig(({ mode }) => ({
    plugins: [
        laravel({
            input: [
                'resources/sass/404.scss',
                'resources/sass/app.scss',
                'resources/sass/vendor.scss',
                'resources/js/common.js',
                'resources/js/dearSanta.js',
                'resources/js/faq.js',
                'resources/js/fixOrganizer.js',
                'resources/js/organizer.js',
                'resources/js/randomForm.js',
            ],
        }),
        vue(),
    ],
    resolve: {
        alias: [
            // Only bundle the translations of the site locale (APP_LOCALE)
            // Webpack-style "~package" imports in scss
            { find: /^~(.+)/, replacement: '$1' },
            // In-DOM templates in blade need the template compiler
            { find: /^vue$/, replacement: 'vue/dist/vue.esm-bundler.js' },
        ],
    },
}));
