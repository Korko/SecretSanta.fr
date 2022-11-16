import { defineConfig, loadEnv } from 'vite'
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import manifestSRI from 'vite-plugin-manifest-sri';
import path from 'path';

export default defineConfig(({ command, mode }) => {
    // Load env file based on `mode` in the current working directory.
    // Set the third parameter to '' to load all env regardless of the `VITE_` prefix.
    // const env = loadEnv(mode, process.cwd(), '')

    return {
        server: {
            host: 'secretsanta.localhost',
        },
        plugins: [
            laravel({
                input: [
                    'resources/js/app.js', // main js
                    'resources/sass/app.scss', // main sass
                    'resources/sass/webfonts.scss', // mails layout
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
            manifestSRI(),
        ],
        resolve: {
            alias: {
                '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap'),
                '~jschardet': path.resolve(__dirname, 'node_modules/jschardet'),
            }
        },
    };
});
