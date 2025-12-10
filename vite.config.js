import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

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
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
