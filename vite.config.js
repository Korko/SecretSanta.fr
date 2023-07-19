import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    server: {
        hmr: {
            host: 'localhost',
        },
        host: 'secretsanta.localhost',
    },
    plugins: [
        laravel({
            input: [
                'resources/css/layout.css',
                'resources/css/hero.css',
                'resources/js/app.js', // main js
            ],
            refresh: true,
        })
    ],
    build: {
        assetsInlineLimit: 0
    }
});
