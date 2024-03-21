const Wallet = () => {
    return Alpine.reactive({
        isConnected: false,
        isLoading: true,

        async init() {
            if (!this.hasExtension()) {
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

        hasExtension() {
            return window.arkconnect !== undefined;
        },

        async copy() {
            const address = await this.address();
            if (!address) {
                return;
            }

            window.clipboard(false).copy(address);
        },

        connect() {
            if (!this.hasExtension()) {
                return;
            }

            window.arkconnect.connect();
        },

        disconnect() {
            if (!this.hasExtension()) {
                return;
            }

            window.arkconnect.disconnect();
        },
    });
};

export default Wallet;
