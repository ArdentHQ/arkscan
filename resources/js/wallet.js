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
        isOnSameNetwork: null,
        isWrongNetworkMessageIgnored: null,
        isVotedDelegateOnStandby: null,
        isVotedDelegateResigned: null,
        isVotedDelegateResignedIgnored: null,
        isVotedDelegateOnStandbyIgnored: null,

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
            this.isOnSameNetwork = null;
            this.isWrongNetworkMessageIgnored = null;

            if (this.isConnected && this.network !== undefined) {
                await this.storeData();
            } else {
                this.votingFor = null;
                this.isVotedDelegateOnStandby = null;
                this.isVotedDelegateResigned = null;
                this.isVotedDelegateResignedIgnored = null;
                this.isVotedDelegateOnStandbyIgnored = null;
            }
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

        async storeData() {
            if (!this.isConnected) {
                return null;
            }

            await this.updateAddress();
            await this.updateCurrentNetwork();

            if (!this.isOnSameNetwork) {
                this.votingFor = null;
                this.isVotedDelegateOnStandby = null;
                this.isVotedDelegateResigned = null;
                this.isVotedDelegateResignedIgnored = null;
                this.isVotedDelegateOnStandbyIgnored = null;

                return;
            }

            await this.updateVote();
        },

        async updateCurrentNetwork() {
            const extensionNetwork = await this.extension().getNetwork();

            this.isOnSameNetwork =
                extensionNetwork.toLowerCase() ===
                this.network.alias.toLowerCase();
            this.isWrongNetworkMessageIgnored = this.getIgnoredToastAddresses('network').includes(this.address);
        },

        async updateAddress() {
            this.address = await this.extension().getAddress();
        },

        async updateVote() {
            const publicKey = await WalletsApi.getVote(
                network.api,
                await this.address
            );

            if (!publicKey) {
                this.votingFor = null;
                this.isVotedDelegateOnStandby = null;
                this.isVotedDelegateResigned = null;
                this.isVotedDelegateResignedIgnored = null;
                this.isVotedDelegateOnStandbyIgnored = null;

                return;
            }

            if (publicKey === this.votingFor?.publicKey) {
                return;
            }

            this.votingFor = await WalletsApi.wallet(network.api, publicKey);
            this.isVotedDelegateOnStandby = this.votingFor.attributes?.delegate?.rank > this.network.delegateCount;
            this.isVotedDelegateResigned = this.votingFor.attributes?.delegate?.resigned === true;
            this.isVotedDelegateResignedIgnored = this.getIgnoredToastAddresses('resigned').includes(this.votingFor.address);
            this.isVotedDelegateOnStandbyIgnored = this.getIgnoredToastAddresses('standby').includes(this.votingFor.address);
        },

        getIgnoredToastAddresses(type) {
            let ignoredAddresses = localStorage.getItem(
                `ignoredToastAddresses:${type}`
            );
            if (ignoredAddresses) {
                return JSON.parse(ignoredAddresses);
            }

            return [];
        },

        ignoreToastAddress(address, type) {
            if (this.isWrongNetworkMessageIgnored) {
                return;
            }

            const ignoredAddresses = this.getIgnoredToastAddresses(type);
            if (ignoredAddresses.includes(address)) {
                return;
            }

            ignoredAddresses.push(address);

            localStorage.setItem(
                `ignoredToastAddresses:${type}`,
                JSON.stringify(ignoredAddresses)
            );
        },

        ignoreWrongNetworkAddress(address) {
            this.ignoreToastAddress(address, 'network');

            this.isWrongNetworkMessageIgnored = true;
        },

        ignoreResignedAddress() {
            this.ignoreToastAddress(this.votingFor.address, 'resigned');

            this.isVotedDelegateResignedIgnored = true;
        },

        ignoreStandbyAddress() {
            this.ignoreToastAddress(this.votingFor.address, 'standby');

            this.isVotedDelegateOnStandbyIgnored = true;
        },

        get showWrongNetworkMessage() {
            if (!this.isConnected) {
                return false;
            }

            if (this.isOnSameNetwork) {
                return false;
            }

            return this.isWrongNetworkMessageIgnored === false;
        },

        get showDelegateResignedMessage() {
            if (!this.isConnected) {
                return false;
            }

            if (this.isVotedDelegateResignedIgnored) {
                return false;
            }

            return this.isVotedDelegateResigned;
        },

        get showDelegateOnStandbyMessage() {
            if (!this.isConnected) {
                return false;
            }

            if (this.isVotedDelegateOnStandbyIgnored) {
                return false;
            }

            return this.isVotedDelegateOnStandby;
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
