import { WalletsApi } from "./api/wallets";

Object.defineProperties(window, {
    _arkconnect: {
        writable: true,
        configurable: true,
        value: window.arkconnect,
    },

    arkconnect: {
        get: function () {
            return this._arkconnect;
        },

        set: function (arkconnect) {
            this._arkconnect = arkconnect;

            window.dispatchEvent(new Event("ARKConnectLoaded"));
        },
    },
});

const Wallet = (network, xData = {}) => {
    return Alpine.reactive({
        ...xData,
        network,

        isConnected: false,
        hasExtension: false,
        cache: {
            votingFor: null,
        },

        async init() {
            if (window.arkconnect) {
                return this.handleExtensionLoadEvent();
            }

            window.addEventListener(
                "ARKConnectLoaded",
                this.handleExtensionLoadEvent.bind(this)
            );
        },

        async handleExtensionLoadEvent() {
            this.extension().on(
                "connected",
                this.handleConnectionEvent.bind(this)
            );

            this.extension().on(
                "disconnected",
                this.handleConnectionEvent.bind(this)
            );

            this.extension().on(
                "lockToggled",
                this.handleLockToggledEvent.bind(this)
            );

            this.hasExtension = window.arkconnect !== undefined;

            try {
                this.setConnectedStatus(await this.extension().isConnected());
            } catch (e) {
                //
            }
        },

        setConnectedStatus(isConnected) {
            this.isConnected = isConnected;

            if (this.isConnected && this.network !== undefined) {
                this.cacheData();
            }
        },

        handleConnectionEvent(data) {
            this.setConnectedStatus(data.type === "connected");
        },

        async handleLockToggledEvent(data) {
            if (data.data.isLocked) {
                this.isConnected = false;
            } else {
                this.isConnected = await this.extension().isConnected();
            }
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

        get votingFor() {
            if (!this.isConnected) {
                return null;
            }

            if (!this.cache.votingFor) {
                return null;
            }

            return this.cache.votingFor;
        },

        async cacheData() {
            if (!this.isConnected) {
                return null;
            }

            if (!this.cache.votingFor) {
                this.cache.votingFor = await WalletsApi.getVote(network.api, await this.address());
            }
        },

        async copy() {
            const address = await this.address();
            if (!address) {
                return;
            }

            window.clipboard(false).copy(address);
        },

        async connect() {
            if (!this.hasExtension) {
                return;
            }

            try {
                await window.arkconnect.connect();
            } catch (e) {
                //
            }
        },

        async disconnect() {
            if (!this.hasExtension) {
                return;
            }

            window.arkconnect.disconnect();
        },
    });
};

export default Wallet;
