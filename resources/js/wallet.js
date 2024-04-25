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

const MAX_VOTE_CHECK_ATTEMPTS = 10;
const VOTE_CHECK_TIMEOUT = 5000;

const Wallet = (network, xData = {}) => {
    return Alpine.reactive({
        ...xData,
        network,

        address: null,
        isConnected: false,
        hasExtension: false,
        votingFor: null,

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
                "addressChanged",
                this.handleAddressChangedEvent.bind(this)
            );

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
                await this.setConnectedStatus(
                    await this.extension().isConnected()
                );
            } catch (e) {
                //
            }
        },

        async setConnectedStatus(isConnected) {
            this.isConnected = isConnected;

            if (this.isConnected && this.network !== undefined) {
                if (!(await this.isOnSameNetwork())) {
                    this.isConnected = false;
                    this.votingFor = null;

                    return;
                }

                await this.storeData();
            } else {
                this.votingFor = null;
            }
        },

        async isOnSameNetwork() {
            const extensionNetwork = await this.extension().getNetwork();

            return (
                extensionNetwork.toLowerCase() ===
                this.network.alias.toLowerCase()
            );
        },

        async handleConnectionEvent(data) {
            await this.setConnectedStatus(data.type === "connected");
        },

        async handleAddressChangedEvent() {
            await this.setConnectedStatus(await this.extension().isConnected());
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

        // Used for TruncateDynamic
        truncateValue() {
            return this.address;
        },

        get votingForAddress() {
            if (!this.votingFor) {
                return null;
            }

            return this.votingFor.address;
        },

        get votedDelegateName() {
            if (!this.votingFor) {
                return null;
            }

            return this.votingFor.attributes?.delegate?.username;
        },

        get isVotedDelegateResigned() {
            if (!this.votingFor) {
                return false;
            }

            return this.votingFor.attributes?.delegate?.resigned === true;
        },

        async storeData() {
            await this.updateAddress();
            await this.updateVote();
        },

        async updateAddress() {
            if (!this.isConnected) {
                return null;
            }

            this.address = await this.extension().getAddress();
        },

        async updateVote() {
            const publicKey = await WalletsApi.getVote(
                network.api,
                await this.address
            );

            if (!publicKey) {
                this.votingFor = null;

                return;
            }

            if (publicKey === this.votingFor?.publicKey) {
                return;
            }

            this.votingFor = await WalletsApi.wallet(network.api, publicKey);
        },

        async copy() {
            if (!this.address) {
                return;
            }

            window.clipboard(false).copy(this.address);
        },

        async performVote(address) {
            if (!this.hasExtension) {
                return;
            }

            const votingForAddress = this.votingForAddress;

            const voteData = {};
            if (address !== votingForAddress) {
                if (votingForAddress) {
                    voteData.unvote = {
                        amount: 0,
                        delegateAddress: votingForAddress,
                    };
                }

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

                let updateVoteTimer = null;
                let loopCounter = 0;
                const updateVoteLoop = async () => {
                    loopCounter++;

                    await this.updateVote();

                    if (
                        loopCounter > MAX_VOTE_CHECK_ATTEMPTS ||
                        this.votingForAddress !== votingForAddress
                    ) {
                        clearTimeout(updateVoteTimer);

                        return;
                    }

                    updateVoteTimer = setTimeout(
                        updateVoteLoop,
                        VOTE_CHECK_TIMEOUT
                    );
                };

                updateVoteLoop();
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
                throw new Error(
                    `There was a problem determining Transaction Amount "${amount}"`
                );
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
