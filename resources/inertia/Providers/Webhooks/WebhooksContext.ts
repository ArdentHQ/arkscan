import { createContext } from "react";

export type WebhookHandler = (...args: unknown[]) => void;

export interface IWebhooksContext {
    listen: (channel: string, event: string, handler: WebhookHandler) => () => void;
    remove: (channel: string, event: string, handler: WebhookHandler) => void;
    enabled: boolean;
}

const WebhooksContext = createContext<IWebhooksContext | null>(null);

export default WebhooksContext;
