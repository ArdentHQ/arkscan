import { WalletsApi } from "./api/wallets";
import { Alpine } from "../../vendor/livewire/livewire/dist/livewire.esm";

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
        isVotedValidatorOnStandby: null,
        isVotedValidatorResigned: null,
        isVotedValidatorResignedIgnored: null,
        isVotedValidatorOnStandbyIgnored: null,

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
                this.isVotedValidatorOnStandby = null;
                this.isVotedValidatorResigned = null;
                this.isVotedValidatorResignedIgnored = null;
                this.isVotedValidatorOnStandbyIgnored = null;
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

        get votedValidatorName() {
            if (!this.votingFor) {
                return null;
            }

            return truncateMiddle(this.votingForAddress);
        },

        async storeData() {
            if (!this.isConnected) {
                return null;
            }

            await this.updateAddress();
            await this.updateCurrentNetwork();

            if (!this.isOnSameNetwork) {
                this.votingFor = null;
                this.isVotedValidatorOnStandby = null;
                this.isVotedValidatorResigned = null;
                this.isVotedValidatorResignedIgnored = null;
                this.isVotedValidatorOnStandbyIgnored = null;

                return;
            }

            await this.updateVote();
        },

        async updateCurrentNetwork() {
            const extensionNetwork = await this.extension().getNetwork();

            this.isOnSameNetwork =
                extensionNetwork.toLowerCase() ===
                this.network.alias.toLowerCase();
            this.isWrongNetworkMessageIgnored = this.getIgnoredToastAddresses(
                "network"
            ).includes(this.address);
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
                this.isVotedValidatorOnStandby = null;
                this.isVotedValidatorResigned = null;
                this.isVotedValidatorResignedIgnored = null;
                this.isVotedValidatorOnStandbyIgnored = null;

                return;
            }

            if (publicKey === this.votingFor?.publicKey) {
                return;
            }

            this.votingFor = await WalletsApi.wallet(network.api, publicKey);
            this.isVotedValidatorOnStandby =
                this.votingFor.attributes?.delegate?.rank >
                this.network.validatorCount;
            this.isVotedValidatorResigned =
                this.votingFor.attributes?.delegate?.resigned === true;
            this.isVotedValidatorResignedIgnored =
                this.getIgnoredToastAddresses("resigned").includes(
                    this.votingFor.address
                );
            this.isVotedValidatorOnStandbyIgnored =
                this.getIgnoredToastAddresses("standby").includes(
                    this.votingFor.address
                );
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
            this.ignoreToastAddress(address, "network");

            this.isWrongNetworkMessageIgnored = true;
        },

        ignoreResignedAddress() {
            this.ignoreToastAddress(this.votingFor.address, "resigned");

            this.isVotedValidatorResignedIgnored = true;
        },

        ignoreStandbyAddress() {
            this.ignoreToastAddress(this.votingFor.address, "standby");

            this.isVotedValidatorOnStandbyIgnored = true;
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

        get showValidatorResignedMessage() {
            if (!this.isConnected) {
                return false;
            }

            if (this.isVotedValidatorResignedIgnored) {
                return false;
            }

            return this.isVotedValidatorResigned;
        },

        get showValidatorOnStandbyMessage() {
            if (!this.isConnected) {
                return false;
            }

            if (this.isVotedValidatorOnStandbyIgnored) {
                return false;
            }

            return this.isVotedValidatorOnStandby;
        },

        async copy() {
            if (!this.address) {
                return;
            }

            window.clipboard(false).copy(this.address);
        },

        get version() {
            if (!this.hasExtension) {
                return null;
            }

            if (typeof this.extension().version !== "function") {
                return "1.0.0";
            }

            return this.extension().version() || "1.0.0";
        },

        get delegateAddressKey() {
            if (["1.8.0", "1.0.0", null].includes(this.version)) {
                return "delegateAddress";
            }

            return "address";
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
                        [this.delegateAddressKey]: votingForAddress,
                    };
                }

                voteData.vote = {
                    amount: 0,
                    [this.delegateAddressKey]: address,
                };
            } else {
                voteData.unvote = {
                    amount: 0,
                    [this.delegateAddressKey]: address,
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

        get isSupported() {
            // If the user has the extension installed, we can assume they are on a supported browser
            if (window.arkconnect !== undefined) {
                return true;
            }

            const isCompatible = /chrome|firefox/.test(
                navigator.userAgent.toLowerCase()
            );
            const isMobile = /android|iphone|ipad|ipod/.test(
                navigator.userAgent.toLowerCase()
            );

            return isCompatible && !isMobile;
        },

        get addressUrl() {
            return `/addresses/${this.address}`;
        },
    });
};

export default Wallet;
