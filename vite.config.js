import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        vue(),
        laravel({
            input: [
                // Fichiers compilés par vite (point d'entrée du front)
                'resources/css/app.css',
                'resources/js/poll-dashboard.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: true,
        hmr: {
            host: 'localhost'
        },
        watch: {
            ignored: ['**/storage/framework/views/**'],
        },
    },
});
