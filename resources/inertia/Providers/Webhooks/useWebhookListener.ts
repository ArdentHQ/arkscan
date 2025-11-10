import { useEffect } from "react";
import useWebhooks from "./useWebhooks";
import { WebhookHandler } from "./WebhooksContext";

export default function useWebhookListener(channel: string | null, event: string | null, handler: WebhookHandler) {
    const { listen, enabled } = useWebhooks();

    useEffect(() => {
        if (!channel || !event || !enabled) {
            return;
        }

        const cleanup = listen(channel, event, handler);

        return () => {
            cleanup?.();
        };
    }, [channel, event, handler, listen, enabled]);
}
