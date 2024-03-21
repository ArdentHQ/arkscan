const Wallet = () => {
    return Alpine.reactive({
        isConnected: false,
        isLoading: true,

        async init() {
            if (! await this.hasExtension()) {
                return;
            }

            this.isConnected = await this.extension().isConnected();

            this.extension().on(
                "connected",
                this.handleConnectionEvent.bind(this)
            );

            this.extension().on(
                "disconnected",
                this.handleConnectionEvent.bind(this)
            );

            this.isLoading = false;
        },

        handleConnectionEvent(data) {
            this.isConnected = data.type === "connected";
        },

        extension() {
            return window.arkconnect;
        },

        async truncateValue() {
            return this.address();
        },

        async address() {
            if (!this.isConnected) {
                return null;
            }

            return await this.extension().getAddress();
        },

        async hasExtension() {
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve(window.arkconnect !== undefined);
                }, 500);
            })
        },

        async copy() {
            const address = await this.address();
            if (!address) {
                return;
            }

            window.clipboard(false).copy(address);
        },

        async connect() {
            if (! await this.hasExtension()) {
                return;
            }

            window.arkconnect.connect();
        },

        async disconnect() {
            if (! await this.hasExtension()) {
                return;
            }

            window.arkconnect.disconnect();
        },
    });
};

export default Wallet;
