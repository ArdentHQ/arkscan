"use client";

import { useCallback, useEffect, useMemo, useRef, useState } from "react";
import ArkConnectContext from "./ArkConnectContext";
import type {
    ArkConnectConfiguration,
    ArkConnectExtension,
    ArkConnectIgnoredToastType,
    ArkConnectState,
    IArkConnectContextType,
    SignTransactionRequest,
    SignVoteRequest,
} from "./types";
import { WalletsApi } from "@js/api/wallets";
import type { IWallet } from "@/types/generated";

declare global {
    interface Window {
        arkconnect?: ArkConnectExtension;
        clipboard: (silent?: boolean) => { copy: (value: string) => void };
    }
}

const MAX_VOTE_CHECK_ATTEMPTS = 10;
const VOTE_CHECK_TIMEOUT = 5000;

const INITIAL_STATE: ArkConnectState = {
    arkconnectConfig: null,
    network: null,
    hasExtension: false,
    isConnected: false,
    address: null,
    votingFor: null,
    isOnSameNetwork: null,
    isWrongNetworkMessageIgnored: null,
    isVotedValidatorOnStandby: null,
    isVotedValidatorResigned: null,
    isVotedValidatorResignedIgnored: null,
    isVotedValidatorOnStandbyIgnored: null,
    version: null,
    isExtensionReady: false,
};

function getWalletPublicKey(wallet: IWallet | null): string | null {
    if (!wallet) {
        return null;
    }

    return (wallet as unknown as { publicKey?: string })?.publicKey ?? wallet.public_key ?? null;
}

export default function ArkConnectProvider({
    children,
    configuration,
}: {
    children: React.ReactNode;
    configuration: ArkConnectConfiguration;
}) {
    const [state, setState] = useState<ArkConnectState>(() => ({
        ...INITIAL_STATE,
        network: configuration.network,
        arkconnectConfig: configuration.arkconnectConfig ?? null,
    }));

    const stateRef = useRef(state);
    const voteTimerRef = useRef<ReturnType<typeof setTimeout> | null>(null);
    const extensionListenersRef = useRef<Array<() => void>>([]);
    const configurationRef = useRef<ArkConnectConfiguration | null>(configuration);

    const getExtension = useCallback(() => {
        if (typeof window === "undefined") {
            return null;
        }

        return window.arkconnect ?? null;
    }, []);

    const getIgnoredToastAddresses = useCallback((type: ArkConnectIgnoredToastType) => {
        if (typeof window === "undefined") {
            return [];
        }

        const rawValue = window.localStorage.getItem(`ignoredToastAddresses:${type}`);
        if (!rawValue) {
            return [];
        }

        try {
            const parsed = JSON.parse(rawValue);
            if (Array.isArray(parsed)) {
                return parsed;
            }

            return [];
        } catch {
            return [];
        }
    }, []);

    const ignoreToastAddress = useCallback(
        (address: string | null, type: ArkConnectIgnoredToastType) => {
            if (!address) {
                return;
            }

            const ignoredAddresses = getIgnoredToastAddresses(type);
            if (ignoredAddresses.includes(address)) {
                return;
            }

            ignoredAddresses.push(address);

            if (typeof window === "undefined") {
                return;
            }

            window.localStorage.setItem(`ignoredToastAddresses:${type}`, JSON.stringify(ignoredAddresses));
        },
        [getIgnoredToastAddresses],
    );

    const resetVotingState = useCallback(() => {
        setState((previous) => ({
            ...previous,
            votingFor: null,
            isVotedValidatorOnStandby: null,
            isVotedValidatorResigned: null,
            isVotedValidatorResignedIgnored: null,
            isVotedValidatorOnStandbyIgnored: null,
        }));
    }, []);

    const updateVersion = useCallback(async () => {
        const extension = getExtension();
        if (!extension) {
            return;
        }

        let resolvedVersion: string | null = "1.0.0";

        if (typeof extension.version === "function") {
            try {
                const versionResult = extension.version();
                resolvedVersion = versionResult instanceof Promise ? await versionResult : (versionResult ?? "1.0.0");
            } catch {
                resolvedVersion = "1.0.0";
            }
        }

        setState((previous) => ({
            ...previous,
            version: resolvedVersion,
        }));
    }, [getExtension]);

    const updateVote = useCallback(
        async (address: string | null) => {
            if (!address) {
                resetVotingState();

                return;
            }

            const configuration = configurationRef.current;
            if (!configuration?.network) {
                resetVotingState();

                return;
            }

            try {
                const publicKey = await WalletsApi.getVote(configuration.network.api, address);
                if (!publicKey) {
                    resetVotingState();

                    return;
                }

                const currentVotePublicKey = getWalletPublicKey(stateRef.current.votingFor);
                if (publicKey === currentVotePublicKey) {
                    return;
                }

                const votingForWallet = await WalletsApi.wallet(configuration.network.api, publicKey);
                const delegate = votingForWallet?.attributes?.delegate;
                const isOnStandby =
                    typeof delegate?.rank === "number" ? delegate.rank > configuration.network.validatorCount : null;
                const isResigned = delegate?.resigned === true;
                const standbyIgnored = getIgnoredToastAddresses("standby").includes(votingForWallet?.address ?? "");
                const resignedIgnored = getIgnoredToastAddresses("resigned").includes(votingForWallet?.address ?? "");

                setState((previous) => ({
                    ...previous,
                    votingFor: votingForWallet,
                    isVotedValidatorOnStandby: isOnStandby,
                    isVotedValidatorResigned: isResigned,
                    isVotedValidatorOnStandbyIgnored: standbyIgnored,
                    isVotedValidatorResignedIgnored: resignedIgnored,
                }));
            } catch {
                resetVotingState();
            }
        },
        [getIgnoredToastAddresses, resetVotingState],
    );

    const updateCurrentNetwork = useCallback(
        async (address: string | null) => {
            const extension = getExtension();
            const configuration = configurationRef.current;
            if (!extension || !configuration) {
                setState((previous) => ({
                    ...previous,
                    isOnSameNetwork: null,
                    isWrongNetworkMessageIgnored: null,
                }));

                return false;
            }

            try {
                const extensionNetwork = await extension.getNetwork();
                const isOnSameNetwork = extensionNetwork?.toLowerCase() === configuration.network.alias.toLowerCase();
                const ignoredWrongNetwork = address
                    ? getIgnoredToastAddresses("network").includes(address)
                    : stateRef.current.isWrongNetworkMessageIgnored;

                setState((previous) => ({
                    ...previous,
                    network: configuration.network,
                    arkconnectConfig: configuration.arkconnectConfig ?? previous.arkconnectConfig,
                    isOnSameNetwork,
                    isWrongNetworkMessageIgnored: ignoredWrongNetwork ?? false,
                }));

                return isOnSameNetwork;
            } catch {
                setState((previous) => ({
                    ...previous,
                    isOnSameNetwork: null,
                    isWrongNetworkMessageIgnored: null,
                }));

                return false;
            }
        },
        [getExtension, getIgnoredToastAddresses],
    );

    const updateAddress = useCallback(async () => {
        const extension = getExtension();
        if (!extension) {
            setState((previous) => ({
                ...previous,
                address: null,
            }));

            return null;
        }

        try {
            const address = await extension.getAddress();
            setState((previous) => ({
                ...previous,
                address,
            }));

            return address;
        } catch {
            setState((previous) => ({
                ...previous,
                address: null,
            }));

            return null;
        }
    }, [getExtension]);

    const storeData = useCallback(async () => {
        if (!stateRef.current.isConnected) {
            return;
        }

        const address = await updateAddress();
        const isOnSameNetwork = await updateCurrentNetwork(address);

        if (!isOnSameNetwork) {
            resetVotingState();

            return;
        }

        await updateVote(address);
    }, [resetVotingState, updateAddress, updateCurrentNetwork, updateVote]);

    const setConnectedStatus = useCallback(
        async (connected: boolean) => {
            setState((previous) => ({
                ...previous,
                isConnected: connected,
                isOnSameNetwork: null,
                isWrongNetworkMessageIgnored: null,
            }));

            if (!connected) {
                setState((previous) => ({
                    ...previous,
                    address: null,
                }));

                resetVotingState();

                return;
            }

            await storeData();
        },
        [resetVotingState, storeData],
    );

    const handleConnectionEvent = useCallback(
        async (event: { type: "connected" | "disconnected" }) => {
            await setConnectedStatus(event?.type === "connected");
        },
        [setConnectedStatus],
    );

    const handleAddressChangedEvent = useCallback(async () => {
        const extension = getExtension();
        if (!extension) {
            return;
        }

        try {
            const connected = await extension.isConnected();
            await setConnectedStatus(connected);
        } catch {
            await setConnectedStatus(false);
        }
    }, [getExtension, setConnectedStatus]);

    const handleLockToggledEvent = useCallback(
        async (event: { data: { isLocked: boolean } }) => {
            if (event?.data?.isLocked) {
                await setConnectedStatus(false);

                return;
            }

            const extension = getExtension();
            if (!extension) {
                return;
            }

            try {
                const connected = await extension.isConnected();
                await setConnectedStatus(connected);
            } catch {
                await setConnectedStatus(false);
            }
        },
        [getExtension, setConnectedStatus],
    );

    const registerExtensionListeners = useCallback(
        (extension: ArkConnectExtension) => {
            const listeners: Array<{
                event: Parameters<ArkConnectExtension["on"]>[0];
                handler: (payload: any) => void;
            }> = [
                { event: "connected", handler: handleConnectionEvent },
                { event: "disconnected", handler: handleConnectionEvent },
                { event: "addressChanged", handler: handleAddressChangedEvent },
                { event: "lockToggled", handler: handleLockToggledEvent },
            ];

            listeners.forEach(({ event, handler }) => {
                try {
                    extension.on(event, handler);

                    extensionListenersRef.current.push(() => {
                        if (typeof extension.off === "function") {
                            extension.off(event, handler);
                        }
                    });
                } catch {
                    // noop
                }
            });
        },
        [handleAddressChangedEvent, handleConnectionEvent, handleLockToggledEvent],
    );

    const handleExtensionLoadEvent = useCallback(async () => {
        const extension = getExtension();
        if (!extension) {
            return;
        }

        registerExtensionListeners(extension);

        setState((previous) => ({
            ...previous,
            hasExtension: true,
            isExtensionReady: true,
        }));

        await updateVersion();

        try {
            const connected = await extension.isConnected();
            await setConnectedStatus(connected);
        } catch {
            await setConnectedStatus(false);
        }
    }, [getExtension, registerExtensionListeners, setConnectedStatus, updateVersion]);

    const configure = useCallback(
        (configuration: ArkConnectConfiguration) => {
            const currentConfig = configurationRef.current;
            const hasSameNetwork =
                currentConfig?.network?.alias === configuration.network?.alias &&
                currentConfig?.network?.api === configuration.network?.api;
            const hasSameArkConnectConfig =
                currentConfig?.arkconnectConfig?.enabled === configuration.arkconnectConfig?.enabled &&
                currentConfig?.arkconnectConfig?.vaultUrl === configuration.arkconnectConfig?.vaultUrl;

            if (hasSameNetwork && hasSameArkConnectConfig) {
                configurationRef.current = configuration;

                return;
            }

            configurationRef.current = configuration;

            setState((previous) => ({
                ...previous,
                network: configuration.network,
                arkconnectConfig: configuration.arkconnectConfig ?? previous.arkconnectConfig,
            }));

            if (stateRef.current.isConnected) {
                void storeData();
            }
        },
        [storeData],
    );

    const refresh = useCallback(async () => {
        await storeData();
    }, [storeData]);

    const connect = useCallback(async () => {
        const extension = getExtension();
        if (!extension) {
            return;
        }

        try {
            await extension.connect();
        } catch {
            // noop
        }
    }, [getExtension]);

    const disconnect = useCallback(async () => {
        const extension = getExtension();
        if (!extension) {
            return;
        }

        try {
            await extension.disconnect();
        } catch {
            // noop
        }
    }, [getExtension]);

    const computeIsSupported = useCallback(() => {
        if (typeof window === "undefined") {
            return false;
        }

        if (window.arkconnect !== undefined) {
            return true;
        }

        const userAgent = window.navigator?.userAgent?.toLowerCase() ?? "";
        const isCompatible = /chrome|firefox/.test(userAgent);
        const isMobile = /android|iphone|ipad|ipod/.test(userAgent);

        return isCompatible && !isMobile;
    }, []);

    const delegateAddressKey = useMemo<"delegateAddress" | "address">(() => {
        const version = state.version;
        if (!version || ["1.8.0", "1.0.0"].includes(version)) {
            return "delegateAddress";
        }

        return "address";
    }, [state.version]);

    const votingForAddress = useMemo(() => state.votingFor?.address ?? null, [state.votingFor]);

    const showWrongNetworkMessage = useMemo(() => {
        return !!state.isConnected && state.isOnSameNetwork === false && state.isWrongNetworkMessageIgnored === false;
    }, [state.isConnected, state.isOnSameNetwork, state.isWrongNetworkMessageIgnored]);

    const showValidatorResignedMessage = useMemo(() => {
        return (
            !!state.isConnected && !!state.isVotedValidatorResigned && state.isVotedValidatorResignedIgnored === false
        );
    }, [state.isConnected, state.isVotedValidatorResigned, state.isVotedValidatorResignedIgnored]);

    const showValidatorOnStandbyMessage = useMemo(() => {
        return (
            !!state.isConnected && !!state.isVotedValidatorOnStandby && state.isVotedValidatorOnStandbyIgnored === false
        );
    }, [state.isConnected, state.isVotedValidatorOnStandby, state.isVotedValidatorOnStandbyIgnored]);

    const isSupported = useMemo(() => computeIsSupported(), [computeIsSupported, state.hasExtension]);

    const performVote = useCallback(
        async (address: string) => {
            const extension = getExtension();
            if (!extension || !stateRef.current.hasExtension) {
                return;
            }

            const votes: string[] = [];
            const unvotes: string[] = [];
            const currentVotingFor = stateRef.current.votingFor?.address ?? null;

            if (address !== currentVotingFor) {
                votes.push(address);

                if (currentVotingFor) {
                    unvotes.push(currentVotingFor);
                }
            } else if (currentVotingFor) {
                unvotes.push(currentVotingFor);
            }

            if (votes.length === 0 && unvotes.length === 0) {
                return;
            }

            const voteRequest: SignVoteRequest = {
                votes,
                unvotes,
            };

            try {
                await extension.signVote(voteRequest);

                if (voteTimerRef.current) {
                    clearTimeout(voteTimerRef.current);
                }

                let attempts = 0;
                const initialVotingForAddress = currentVotingFor;

                const updateLoop = async () => {
                    attempts++;

                    await updateVote(stateRef.current.address);

                    const updatedVotingFor = stateRef.current.votingFor?.address ?? null;
                    if (attempts > MAX_VOTE_CHECK_ATTEMPTS || updatedVotingFor !== initialVotingForAddress) {
                        if (voteTimerRef.current) {
                            clearTimeout(voteTimerRef.current);
                            voteTimerRef.current = null;
                        }

                        return;
                    }

                    voteTimerRef.current = setTimeout(updateLoop, VOTE_CHECK_TIMEOUT);
                };

                voteTimerRef.current = setTimeout(updateLoop, VOTE_CHECK_TIMEOUT);
            } catch {
                // noop
            }
        },
        [getExtension, updateVote],
    );

    const performSend = useCallback(
        async (address: string, amount: number | string) => {
            const extension = getExtension();
            if (!extension) {
                return;
            }

            const value = typeof amount === "number" ? amount.toString() : amount;
            if (!value || Number.isNaN(Number(value))) {
                throw new Error(`There was a problem determining Transaction Amount "${amount}"`);
            }

            const transactionRequest: SignTransactionRequest = {
                to: address,
                value,
            };

            try {
                await extension.signTransaction(transactionRequest);
            } catch {
                // noop
            }
        },
        [getExtension],
    );

    const copyAddress = useCallback(() => {
        if (typeof window === "undefined") {
            return;
        }

        const address = stateRef.current.address;
        if (!address) {
            return;
        }

        try {
            window.clipboard?.(false)?.copy?.(address);
        } catch {
            // noop
        }
    }, []);

    const ignoreWrongNetworkAddress = useCallback(
        (address: string) => {
            ignoreToastAddress(address, "network");

            setState((previous) => ({
                ...previous,
                isWrongNetworkMessageIgnored: true,
            }));
        },
        [ignoreToastAddress],
    );

    const ignoreResignedAddress = useCallback(() => {
        const votingForAddress = stateRef.current.votingFor?.address ?? null;
        if (!votingForAddress) {
            return;
        }

        ignoreToastAddress(votingForAddress, "resigned");

        setState((previous) => ({
            ...previous,
            isVotedValidatorResignedIgnored: true,
        }));
    }, [ignoreToastAddress]);

    const ignoreStandbyAddress = useCallback(() => {
        const votingForAddress = stateRef.current.votingFor?.address ?? null;
        if (!votingForAddress) {
            return;
        }

        ignoreToastAddress(votingForAddress, "standby");

        setState((previous) => ({
            ...previous,
            isVotedValidatorOnStandbyIgnored: true,
        }));
    }, [ignoreToastAddress]);

    useEffect(() => {
        stateRef.current = state;
    }, [state]);

    useEffect(() => {
        if (typeof window === "undefined") {
            return;
        }

        const onExtensionLoaded = () => {
            void handleExtensionLoadEvent();
        };

        if (window.arkconnect !== undefined) {
            void handleExtensionLoadEvent();
        } else {
            setState((previous) => ({
                ...previous,
                hasExtension: false,
                isExtensionReady: true,
            }));
        }

        window.addEventListener("ARKConnectLoaded", onExtensionLoaded);

        return () => {
            window.removeEventListener("ARKConnectLoaded", onExtensionLoaded);

            extensionListenersRef.current.forEach((unsubscribe) => {
                try {
                    unsubscribe();
                } catch {
                    // noop
                }
            });

            extensionListenersRef.current = [];

            if (voteTimerRef.current) {
                clearTimeout(voteTimerRef.current);
                voteTimerRef.current = null;
            }
        };
    }, [handleExtensionLoadEvent]);

    useEffect(() => {
        if (!state.isConnected) {
            return;
        }

        void storeData();
    }, [state.isConnected, storeData]);

    useEffect(() => {
        if (!configuration.network) {
            return;
        }

        configure(configuration);
    }, [configuration, configure]);

    const isArkConnectEnabled = useMemo(() => Boolean(state.arkconnectConfig?.enabled), [state.arkconnectConfig]);

    const addressUrl = useMemo(() => {
        return `/addresses/${state.address}`;
    }, [state.address]);

    const contextValue = useMemo<IArkConnectContextType>(
        () => ({
            ...state,
            votingForAddress,
            showWrongNetworkMessage,
            showValidatorResignedMessage,
            showValidatorOnStandbyMessage,
            isSupported,
            delegateAddressKey,
            configure,
            refresh,
            connect,
            disconnect,
            performVote,
            performSend,
            copyAddress,
            ignoreWrongNetworkAddress,
            ignoreResignedAddress,
            ignoreStandbyAddress,
            isArkConnectEnabled,
            addressUrl,
        }),
        [
            connect,
            copyAddress,
            delegateAddressKey,
            disconnect,
            ignoreResignedAddress,
            ignoreStandbyAddress,
            ignoreWrongNetworkAddress,
            isSupported,
            performSend,
            performVote,
            refresh,
            showValidatorOnStandbyMessage,
            showValidatorResignedMessage,
            showWrongNetworkMessage,
            state,
            votingForAddress,
            isArkConnectEnabled,
            configure,
        ],
    );

    return <ArkConnectContext.Provider value={contextValue}>{children}</ArkConnectContext.Provider>;
}
