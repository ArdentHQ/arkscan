/// <reference types="vite-plugin-svgr/client" />

import { IRequestData } from "./types/generated";

type ValidationErrors = Record<string, string>;

declare module "@inertiajs/core" {
    export interface PageProps<T extends object = {}> extends IRequestData, T {
        errors: ValidationErrors;
    }
}

declare global {
    interface Window {
        Livewire: {
            emit: (event: string) => void;
            on: (event: string, callback: (...args: any[]) => void) => void;
        };

        Echo?: {
            channel: (channel: string) => {
                listen: (event: string, callback: WebhookHandler) => void;
                stopListening: (event: string, callback: WebhookHandler) => void;
                subscribe?: () => void;
                unsubscribe?: () => void;
            };
            leave: (channel: string) => void;
        };
    }
}
