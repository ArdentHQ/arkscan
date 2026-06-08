/// <reference types="vite-plugin-svgr/client" />

import { IRequestData } from "./types/generated";
import { route as routeFn } from "ziggy-js";

type ValidationErrors = Record<string, string>;

declare module "@inertiajs/core" {
    export interface PageProps<T extends object = {}> extends IRequestData, T {
        honeypot?: {
            enabled: boolean;
            nameFieldName: string;
            validFromFieldName: string;
            encryptedValidFrom: string;
        };

        errors: ValidationErrors;
        metaPage?: string;
        metaDetail?: Record<string, string | number>;
    }
}

type WebhookHandler = (...args: unknown[]) => void;

declare global {
    var route: typeof routeFn;

    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    var Echo: any;
    var emitter: WebhookHandler;

    interface Window {
        Pusher: typeof import("pusher-js").default;

        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        Echo?: any;

        Webhook?: {
            listeners: Record<string, Record<string, Record<string, WebhookHandler | undefined>>>;
            listen: (channel: string, event: string, emit: string) => void;
            remove: (channel: string, event: string, emit: string) => void;
        };

        sa_event?: (event: string, callback?: () => void) => void;

        chartTooltip?: (context: any) => void;
    }
}
