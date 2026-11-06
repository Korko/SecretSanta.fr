import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue2';

export default defineConfig({
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
        // Webpack-style "~package" imports in scss
        alias: [
            { find: /^~(.+)/, replacement: '$1' },
            // In-DOM templates in blade need the template compiler
            { find: /^vue$/, replacement: 'vue/dist/vue.esm.js' },
        ],
    },
});
