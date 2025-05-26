import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from "@tailwindcss/vite"

export default defineConfig({
    plugins: [
        laravel({
            input: [ 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    build: {
        target: 'es2022',
      },
    resolve: {
        alias: {
            '@': '/resources',
            '@lib': '/resources/js/lib',
            '@assets': '/resources/assets',
            '@img': '/resources/assets/img',
            '@components': '/resources/js/Components',
            '@layouts': '/resources/js/Layouts',
            '@ui': '/resources/js/Components/ui',
            '@/lib/utils': '/resources/js/lib/utils.ts',
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },
    server: {
        host: 'localhost',
        port: 3000,
        hmr: {
            host: 'localhost',
        }
    }
});
