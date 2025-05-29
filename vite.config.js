import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import tailwindcss from "@tailwindcss/vite"

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
            refresh: true,
            detectTls: 'piepjack.test',
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
            '@components': '/resources/js/components',
            '@pages': '/resources/js/pages',
            '@layouts': '/resources/js/layouts',
            '@ui': '/resources/js/components/ui',
            '@/lib/utils': '/resources/js/lib/utils.ts',
            'vue': 'vue/dist/vue.esm-bundler.js',
        },
    },
    base: '/',
    // server: {
    //     host: 'localhost',
    //     port: 8000,
    //     hmr: {
    //         host: 'localhost',
    //     },
    //     proxy: {
    //         '^/(?!hot-update|@vite|node_modules|resources|vendor|storage|public).*': {
    //             target: 'http://localhost:8000', // Your Laravel dev server
    //             changeOrigin: true,
    //             secure: false,
    //         },
    //     },
    // },
});
