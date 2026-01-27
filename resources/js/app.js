import "focus-visible";
import "@ui/js/clipboard.js";
import "@ui/js/tippy.js";

import Echo from "laravel-echo";

if (import.meta.env.VITE_BROADCAST_DRIVER === "reverb") {
    const options = {
        broadcaster: "reverb",
        key: import.meta.env.VITE_REVERB_APP_KEY,
        wsHost: import.meta.env.VITE_REVERB_HOST,
        wsPort: import.meta.env.VITE_REVERB_PORT,
        forceTLS: false,
        enabledTransports: ["ws"],
    };

    if ((import.meta.env.VITE_REVERB_SCHEME ?? "https") === "https") {
        options.enabledTransports = ["ws", "wss"];
        options.forceTLS = true;
        options.wssPort = import.meta.env.VITE_REVERB_PORT_TLS;
    }

    window.Echo = new Echo(options);
}
