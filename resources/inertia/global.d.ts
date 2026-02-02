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

declare global {
    var route: typeof routeFn;

    interface Window {
        Echo?: {
            channel: (channel: string) => {
                listen: (event: string, callback: WebhookHandler) => void;
                stopListening: (event: string, callback: WebhookHandler) => void;
                subscribe?: () => void;
                unsubscribe?: () => void;
            };
            leave: (channel: string) => void;
        };

        sa_event: (event: string, callback?: () => void) => void;

        chartTooltip?: (context: any) => void;
    }
}
