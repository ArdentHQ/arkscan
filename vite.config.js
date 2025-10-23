import { defineConfig, loadEnv } from "vite";
import { resolve } from "path";
import laravel from "laravel-vite-plugin";
import { detectServerConfig } from "./vendor/arkecosystem/foundation/resources/vite.config";
import i18n from './resources/js/vite/i18n/i18n';
import svgr from "vite-plugin-svgr";

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
            paths: [
                'resources/lang',
                {
                    src: 'vendor/arkecosystem/foundation/resources/lang',
                    dest: 'resources/lang/ui',
                },
            ],
        }),
        svgr(),
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
