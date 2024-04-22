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
            votingForPublicKey: null,
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
                this.updateVote();
            }
        },

        async updateVote() {
            const publicKey = await WalletsApi.getVote(
                network.api,
                await this.address()
            );
            if (publicKey === this.cache.votingForPublicKey) {
                return;
            }

            this.cache.votingForPublicKey = publicKey;
            this.cache.votingFor = (
                await WalletsApi.wallet(network.api, publicKey)
            ).address;
        },

        async copy() {
            const address = await this.address();
            if (!address) {
                return;
            }

            window.clipboard(false).copy(address);
        },

        async performVote(address) {
            if (!this.hasExtension) {
                return;
            }

            const votingFor = this.votingFor;

            const voteData = {};
            if (address !== votingFor) {
                voteData.unvote = {
                    amount: 0,
                    delegateAddress: votingFor,
                };

                voteData.vote = {
                    amount: 0,
                    delegateAddress: address,
                };
            } else {
                voteData.unvote = {
                    amount: 0,
                    delegateAddress: address,
                };
            }

            try {
                await window.arkconnect.signVote(voteData);

                const updateVoteTimer = setInterval(async () => {
                    await this.updateVote();

                    if (this.votingFor !== votingFor) {
                        clearInterval(updateVoteTimer);
                    }
                }, 5000);
            } catch (e) {
                //
            }
        },

        async performSend(address, amount, memo) {
            if (!this.hasExtension) {
                return;
            }

            const transactionData = {
                amount: parseFloat(amount),
                receiverAddress: address,
                memo: memo || null,
            };

            if (transactionData.amount === NaN) {
                throw new Error(`There was a problem determining Transaction Amount "${amount}"`)
            }

            try {
                await window.arkconnect.signTransaction(transactionData);
            } catch (e) {
                //
            }
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
