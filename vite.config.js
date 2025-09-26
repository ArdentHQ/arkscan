import { defineConfig, loadEnv } from "vite";
import { resolve } from "path";
import laravel from "laravel-vite-plugin";
import { detectServerConfig } from "./vendor/arkecosystem/foundation/resources/vite.config";
import i18n from 'laravel-react-i18n/vite'

export default ({ mode }) => defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
            'resources/js/app-inertia.tsx',
            'resources/js/chart-tooltip.js',
            'resources/js/webhooks.js',
        ]),
        i18n({
            langDirname: 'resources/lang',
        }),
        i18n({
            langDirname: 'vendor/arkecosystem/foundation/resources/lang',
        }),
    ],
    resolve: {
        alias: {
            "@": resolve(
                __dirname,
                "resources/inertia/"
            ),
            "@icons": resolve(
                __dirname,
                "resources/icons/"
            ),
            "@ui": resolve(
                __dirname,
                "vendor/arkecosystem/foundation/resources/assets/"
            ),
        },
    },
    server: detectServerConfig(mode) || {
        host: loadEnv(mode, process.cwd()).VITE_HOST ?? 'localhost',
        port: 3000,
    },
});
