import { useContext } from "react";
import WebhooksContext from "./WebhooksContext";

export default function useWebhooks() {
    const context = useContext(WebhooksContext);
    if (!context) {
        throw new Error("useWebhooks must be used within a WebhooksProvider");
    }

    return context;
}
