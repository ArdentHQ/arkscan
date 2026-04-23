import { IWallet } from "@/types/generated";
import { toFloat } from "@/utils/big-number";

interface WalletNetworkConfig {
    validatorCount: number;
    knownWallets: Array<{ address: string; type?: string; [key: string]: any }>;
}

export class Wallet {
    private _network?: WalletNetworkConfig;

    constructor(data: IWallet, network?: WalletNetworkConfig) {
        Object.assign(this, data);
        this._network = network;
    }

    static from(data: IWallet, network?: WalletNetworkConfig): Wallet {
        return new Wallet(data, network);
    }

    static fromArray(data: IWallet[], network?: WalletNetworkConfig): Wallet[] {
        return data.map((item) => Wallet.from(item, network));
    }

    get isCold(): boolean {
        return this.public_key === null;
    }

    get isValidator(): boolean {
        return this.attributes?.validatorPublicKey !== undefined;
    }

    get isResigned(): boolean {
        return this.attributes?.validatorResigned === true;
    }

    get isDormant(): boolean {
        if (!this.isValidator) {
            return false;
        }

        if (this.isResigned) {
            return false;
        }

        const publicKey = this.attributes?.validatorPublicKey;

        return publicKey === null || publicKey === "" || publicKey === undefined;
    }

    get isStandby(): boolean {
        if (!this._network) return false;
        return (this.rank ?? 0) > this._network.validatorCount;
    }

    get isActive(): boolean {
        return this.isValidator && !this.isResigned && !this.isDormant && !this.isStandby;
    }

    get hasUsername(): boolean {
        return this.username !== null && this.username !== undefined;
    }

    get hasSecondSignature(): boolean {
        return this.attributes?.secondPublicKey !== undefined;
    }

    get isLegacy(): boolean {
        return this.attributes?.isLegacy === true;
    }

    get isKnown(): boolean {
        return this._findKnownWallet() !== undefined;
    }

    get isOwnedByExchange(): boolean {
        return this._findKnownWallet()?.type === "exchange";
    }

    get rank(): number | null {
        return this.attributes?.validatorRank ?? null;
    }

    get balanceFloat(): number {
        return toFloat(this.balance);
    }

    private _findKnownWallet(): { address: string; type?: string } | undefined {
        if (!this._network?.knownWallets) return undefined;
        return this._network.knownWallets.find((w) => w.address === this.address);
    }
}

// Declaration merging: makes TS treat Wallet instances as having IWallet properties
// since we Object.assign them in the constructor
export interface Wallet extends IWallet {}
