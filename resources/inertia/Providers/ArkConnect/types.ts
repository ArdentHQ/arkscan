import type { IConfigArkconnect, INetwork, IWallet } from "@/types/generated";

export type ArkConnectIgnoredToastType = "network" | "resigned" | "standby";

export type ArkConnectEvent = "addressChanged" | "connected" | "disconnected" | "lockToggled";

export interface ArkConnectVoteTransaction {
    amount: number;
    delegateAddress?: string;
    address?: string;
}

export interface ArkConnectVotePayload {
    vote?: ArkConnectVoteTransaction;
    unvote?: ArkConnectVoteTransaction;
}

export interface ArkConnectTransferPayload {
    amount: number;
    receiverAddress: string;
}

export interface ArkConnectExtension {
    connect: () => Promise<void>;
    disconnect: () => Promise<void>;
    isConnected: () => Promise<boolean>;
    getAddress: () => Promise<string>;
    getNetwork: () => Promise<string>;
    signVote: (payload: ArkConnectVotePayload) => Promise<void>;
    signTransaction: (payload: ArkConnectTransferPayload) => Promise<void>;
    on: (event: ArkConnectEvent, callback: (payload: any) => void) => void;
    off?: (event: ArkConnectEvent, callback: (payload: any) => void) => void;
    version?: () => string | Promise<string> | undefined;
}

export interface ArkConnectConfiguration {
    network: INetwork;
    arkconnectConfig?: IConfigArkconnect;
}

export interface ArkConnectState {
    arkconnectConfig: IConfigArkconnect | null;
    network: INetwork | null;
    hasExtension: boolean;
    isConnected: boolean;
    address: string | null;
    votingFor: IWallet | null;
    isOnSameNetwork: boolean | null;
    isWrongNetworkMessageIgnored: boolean | null;
    isVotedValidatorOnStandby: boolean | null;
    isVotedValidatorResigned: boolean | null;
    isVotedValidatorResignedIgnored: boolean | null;
    isVotedValidatorOnStandbyIgnored: boolean | null;
    version: string | null;
    isExtensionReady: boolean;
}

export interface ArkConnectComputedState {
    votingForAddress: string | null;
    showWrongNetworkMessage: boolean;
    showValidatorResignedMessage: boolean;
    showValidatorOnStandbyMessage: boolean;
    delegateAddressKey: "delegateAddress" | "address";
    isSupported: boolean;
    isArkConnectEnabled: boolean;
}

export interface ArkConnectActions {
    refresh: () => Promise<void>;
    connect: () => Promise<void>;
    disconnect: () => Promise<void>;
    performVote: (address: string) => Promise<void>;
    performSend: (address: string, amount: number | string) => Promise<void>;
    copyAddress: () => void;
    ignoreWrongNetworkAddress: (address: string) => void;
    ignoreResignedAddress: () => void;
    ignoreStandbyAddress: () => void;
}

export interface IArkConnectContextType extends ArkConnectState, ArkConnectComputedState, ArkConnectActions {}
