import type { IConfigArkconnect, INetwork, IWallet } from "@/types/generated";

export type ArkConnectIgnoredToastType = "network" | "resigned" | "standby";

export type ArkConnectEvent = "addressChanged" | "connected" | "disconnected" | "lockToggled";

export interface SignTransactionRequest {
    value: string;
    gasPrice?: string;
    gasLimit?: string;
    to: string;
}

export interface SignTransactionResponse {
    id: string;
    sender: string;
    receiver: string;
    exchangeCurrency: string;
    amount: number;
    convertedAmount: number;
    fee: number;
    convertedFee: number;
    total: number;
    convertedTotal: number;
}

export interface SignVoteRequest {
    votes: string[];
    unvotes: string[];
    gasPrice?: string;
    gasLimit?: string;
}

export interface SignVoteResponse {
    id: string;
    sender: string;
    voteAddress?: string;
    voteName?: string;
    votePublicKey?: string;
    unvoteAddress?: string;
    unvoteName?: string;
    unvotePublicKey?: string;
    exchangeCurrency: string;
    fee: number;
    convertedFee: number;
}

export enum NetworkType {
    DEVNET = "Devnet",
    MAINNET = "Mainnet",
}

export enum ExtensionSupportedEvent {
    AddressChanged = "addressChanged",
    Disconnected = "disconnected",
    Connected = "connected",
    LockToggled = "lockToggled",
}

export interface AddressChangedEventData {
    type: ExtensionSupportedEvent.AddressChanged;
    data: {
        wallet: {
            address: string;
            coin: string;
            network: NetworkType;
        };
    };
}

interface EventResponse {
    [ExtensionSupportedEvent.AddressChanged]: AddressChangedEventData;
    [ExtensionSupportedEvent.LockToggled]: LockToggledEventData;
    [ExtensionSupportedEvent.Connected]: never;
    [ExtensionSupportedEvent.Disconnected]: never;
}

export interface LockToggledEventData {
    type: ExtensionSupportedEvent.AddressChanged;
    data: {
        isLocked: boolean;
    };
}

export interface ArkConnectExtension {
    connect: () => Promise<void>;
    disconnect: () => Promise<void>;
    isConnected: () => Promise<boolean>;
    getAddress: () => Promise<string>;
    getNetwork: () => Promise<string>;
    signTransaction: (transactionRequest: SignTransactionRequest) => Promise<SignTransactionResponse>;
    signVote: (voteRequest: SignVoteRequest) => Promise<SignVoteResponse>;
    on: <T extends ExtensionSupportedEvent>(eventName: T, callback: (data: EventResponse[T]) => void) => void;
    off?: (event: ArkConnectEvent, callback: (payload: any) => void) => void;
    version?: () => string;
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
    configure: (configuration: ArkConnectConfiguration) => void;
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
