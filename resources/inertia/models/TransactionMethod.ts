import { INetwork, ITransaction } from "@/types/generated";
import { i18n, TFunction } from "i18next";

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

    name({ t, i18n }: { t: TFunction<"translation", undefined>; i18n: i18n }): string {
        for (const [method, name] of Object.entries(this.types)) {
            if (this[method as keyof TransactionMethod]) {
                return t(`general.transaction.types.${name}`);
            }
        }

        if (i18n.exists(`contracts.${this.methodHash}`)) {
            return t(`contracts.${this.methodHash}`).replace(/\(.+\)$/, "");
        }

        if (this.methodName !== null) {
            return this.methodName
                .replace(/_/g, " ")
                .split(" ")
                .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                .join(" ");
        }

        return `0x${this.methodHash}`;
    }

    get isTransfer(): boolean {
        return this.methodHash === null;
    }

    get isTokenTransfer(): boolean {
        return this.methodHash === this.network.contractMethods.transfer;
    }

    get isMultiPayment(): boolean {
        return this.methodHash === this.network.contractMethods.multipayment;
    }

    get isVote(): boolean {
        return this.methodHash === this.network.contractMethods.vote;
    }

    get isUnvote(): boolean {
        return this.methodHash === this.network.contractMethods.unvote;
    }

    get isValidatorRegistration(): boolean {
        return this.methodHash === this.network.contractMethods.validator_registration;
    }

    get isValidatorResignation(): boolean {
        return this.methodHash === this.network.contractMethods.validator_resignation;
    }

    get isValidatorUpdate(): boolean {
        return this.methodHash === this.network.contractMethods.validator_update;
    }

    get isUsernameRegistration(): boolean {
        return this.methodHash === this.network.contractMethods.username_registration;
    }

    get isUsernameResignation(): boolean {
        return this.methodHash === this.network.contractMethods.username_resignation;
    }

    get isApprove(): boolean {
        return this.methodHash === this.network.contractMethods.approve;
    }

    get isRevoke(): boolean {
        if (!this.isApprove) {
            return false;
        }

        return this.transaction.tokenApprovalDetails?.isRevoke === true;
    }

    get isBatchTransfer(): boolean {
        return this.methodHash === this.network.contractMethods.batch_transfer;
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
