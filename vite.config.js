import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    plugins: [
        laravel([
            'resources/js/common.js',
            'resources/js/dearSanta.js',
            'resources/js/faq.js',
            'resources/js/organizer.js',
            'resources/js/randomForm.js',
        ]),
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
