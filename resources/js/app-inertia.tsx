import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import loadI18n from "../inertia/i18n";
import ArkConnectProvider from "@/Providers/ArkConnect/ArkConnectProvider";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

loadI18n();

createInertiaApp({
    id: "inertia-body",
    resolve: (name) =>
        resolvePageComponent(`../inertia/Pages/${name}.tsx`, import.meta.glob("../inertia/Pages/**/*.tsx")),
    setup({ el, App, props }) {
        const root = createRoot(el); // This requires react-dom/client
        root.render(
            <ArkConnectProvider>
                <App {...props} />
            </ArkConnectProvider>,
        );
    },
    progress: {
        color: "#4B5563",
    },
});
