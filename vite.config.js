import { defineConfig } from 'vite'
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
            hmr: {
                host: 'dev.secretsanta.fr',
            },
        },
        plugins: [
            laravel({
                input: [
                    'resources/sass/app.scss', // main sass
                    'resources/sass/webfonts.scss', // mails layout

                    // pages
                    'resources/js/randomForm.js',
                    'resources/sass/randomForm.scss',

                    // components
                    'resources/sass/buy-me-coffee.scss',
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
