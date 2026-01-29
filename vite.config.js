import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";
import tailwindcss from "@tailwindcss/vite";
import path from "path";

export default defineConfig({
    plugins: [
        laravel({
            input: ["resources/js/app.js"],
            refresh: true,
            // detectTls: 'piepjack.test',
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
    // root: "resources/js",
    build: {
        // outDir: "../../public/build",
        // emptyOutDir: true,
        // manifest: false,
        target: "esnext",
        // rollupOptions: {
        //     input: {
        //         app: "./resources/js/index.html",
        //     },
        // },
    },
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "resources"),
            "@lib": path.resolve(__dirname, "resources/js/lib"),
            "@assets": path.resolve(__dirname, "resources/assets"),
            "@img": path.resolve(__dirname, "resources/assets/img"),
            "@components": path.resolve(__dirname, "resources/js/components"),
            "@pages": path.resolve(__dirname, "resources/js/pages"),
            "@layouts": path.resolve(__dirname, "resources/js/layouts"),
            "@ui": path.resolve(__dirname, "resources/js/components/ui"),
            "@/lib/utils": path.resolve(__dirname, "resources/js/lib/utils.ts"),
            vue: "vue/dist/vue.esm-bundler.js",
        },
    },
    // base: '/',
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
