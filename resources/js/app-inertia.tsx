import { createRoot } from "react-dom/client";
import { createInertiaApp } from "@inertiajs/react";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import loadI18n from "../inertia/i18n";
import ArkConnectProvider from "@/Providers/ArkConnect/ArkConnectProvider";
import WebhooksProvider from "@/Providers/Webhooks/WebhooksProvider";
import { IConfigArkconnect, INetwork } from "@/types/generated";
import { ArkConnectConfiguration } from "@/Providers/ArkConnect/types";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

loadI18n();

createInertiaApp({
    id: "inertia-body",
    resolve: (name) =>
        resolvePageComponent(`../inertia/Pages/${name}.tsx`, import.meta.glob("../inertia/Pages/**/*.tsx")),
    setup({ el, App, props }) {
        const root = createRoot(el); // This requires react-dom/client

        const configuration: ArkConnectConfiguration = {
            network: props.initialPage.props.network as INetwork,
            arkconnectConfig: props.initialPage.props.arkconnectConfig as IConfigArkconnect,
        };
        root.render(
            <WebhooksProvider
                broadcasting={props.initialPage.props.broadcasting as string}
                currency={props.initialPage.props.currency as string}
            >
                <ArkConnectProvider configuration={configuration}>
                    <App {...props} />
                </ArkConnectProvider>
            </WebhooksProvider>,
        );
    },
    progress: {
        color: "#4B5563",
    },
});
