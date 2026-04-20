import { INetwork, ITransaction } from "@/types/generated";
import { TransactionTypeIdentifier } from "@arkecosystem/typescript-crypto";
import { TFunction } from "i18next";

export class TransactionMethod {
    private readonly transaction: ITransaction;

    private readonly network: INetwork;

    public methodHash: string | null = null;

    public methodName: string | null = null;

    private readonly types: Record<string, string> = {
        isTransfer: "transfer",
        isTokenTransfer: "transfer",
        isMultiPayment: "multipayment",
        isUnvote: "unvote",
        isVote: "vote",
        isValidatorRegistration: "validator-registration",
        isValidatorResignation: "validator-resignation",
        isValidatorUpdate: "validator-update",
        isUsernameRegistration: "username-registration",
        isUsernameResignation: "username-resignation",
        isApprove: "approve",
        isBatchTransfer: "batch-transfer",
        isContractDeployment: "contract-deployment",
    };

    constructor(transaction: ITransaction, network: INetwork) {
        this.transaction = transaction;
        this.network = network;

        ({ functionName: this.methodName, methodId: this.methodHash } = transaction.methodData);
    }

    name({ t }: { t: TFunction<"translation", undefined>; i18n?: unknown }): string {
        for (const [method, name] of Object.entries(this.types)) {
            if (this[method as keyof TransactionMethod]) {
                return t(`general.transaction.types.${name}`);
            }
        }

        if (this.methodName !== null) {
            return this.methodName
                .replace(/\(.+\)$/, "")
                .replace(/([a-z])([A-Z])/g, "$1 $2")
                .split(/[\s_]+/)
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                .join(" ");
        }

        return `0x${this.methodHash}`;
    }

    get isTransfer(): boolean {
        return this.methodHash === null;
    }

    get isTokenTransfer(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isTokenTransfer(this.transaction.payload.raw);
    }

    get isMultiPayment(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isMultiPayment(this.transaction.payload.raw);
    }

    get isVote(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isVote(this.transaction.payload.raw);
    }

    get isUnvote(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isUnvote(this.transaction.payload.raw);
    }

    get isValidatorRegistration(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isValidatorRegistration(this.transaction.payload.raw);
    }

    get isValidatorResignation(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isValidatorResignation(this.transaction.payload.raw);
    }

    get isValidatorUpdate(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isUpdateValidator(this.transaction.payload.raw);
    }

    get isUsernameRegistration(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isUsernameRegistration(this.transaction.payload.raw);
    }

    get isUsernameResignation(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isUsernameResignation(this.transaction.payload.raw);
    }

    get isApprove(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isApprove(this.transaction.payload.raw);
    }

    get isRevoke(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isRevoke(this.transaction.payload.raw);
    }

    get isBatchTransfer(): boolean {
        if (!this.transaction.payload || !this.transaction.payload.raw) {
            return false;
        }

        return TransactionTypeIdentifier.isBatchTransfer(this.transaction.payload.raw);
    }

    get isContractDeployment(): boolean {
        return this.transaction.to === null;
    }

    get isSelfReceiving(): boolean {
        if (this.isValidatorRegistration) {
            return true;
        }

        if (this.isValidatorResignation) {
            return true;
        }

        if (this.isValidatorUpdate) {
            return true;
        }

        if (this.isVote) {
            return true;
        }

        if (this.isUnvote) {
            return true;
        }

        return false;
    }
}
