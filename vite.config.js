import { defineConfig, loadEnv } from "vite";
import path from "path";
import laravel from "laravel-vite-plugin";

export default ({ mode }) => defineConfig({
    plugins: [
        laravel([
            'resources/css/app.css',
            'resources/js/app.js',
            'resources/js/chart-tooltip.js',
        ]),
    ],
    resolve: {
        alias: {
            "@ui": path.resolve(
                __dirname,
                "vendor/arkecosystem/foundation/resources/assets/"
            ),
        },
    },
    server: {
        host: loadEnv(mode, process.cwd()).VITE_HOST ?? 'localhost',
        port: 3000,
   },
});
