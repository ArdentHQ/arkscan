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
        sa_event: (event: string, callback?: () => void) => void;
    }
}
