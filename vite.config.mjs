import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig(({ mode }) => ({
    plugins: [
        laravel({
            input: [
                'resources/css/kraft.css',
                'resources/sass/404.scss',
                'resources/sass/app.scss',
                'resources/sass/vendor.scss',
                'resources/js/common.js',
                'resources/js/dearSanta.js',
                'resources/js/fixOrganizer.js',
                'resources/js/kraftMenu.js',
                'resources/js/organizer.js',
                'resources/js/randomForm.js',
            ],
        }),
        vue(),
        // Only processes the CSS importing it: resources/css/kraft.css (the other pages stay on Bootstrap)
        tailwindcss(),
    ],
    resolve: {
        alias: [
            // Only bundle the translations of the site locale (APP_LOCALE)
            // Webpack-style "~package" imports in scss
            { find: /^~(.+)/, replacement: '$1' },
        ],
    },
}));
