import useShareData from "@/hooks/use-shared-data";
import { ITransaction } from "@/types/generated";
import { useTranslation } from "react-i18next";

export class TransactionMethod {
    private readonly transaction: ITransaction;

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

    constructor(transaction: ITransaction) {
        this.transaction = transaction;

        ({ functionName: this.methodName, methodId: this.methodHash } = transaction.methodData);
    }

    get name(): string {
        const { t, i18n } = useTranslation();

        for (const [method, name] of Object.entries(this.types)) {
            if (this[method as keyof TransactionMethod]) {
                return t(`general.transaction.types.${name}`);
            }
        }

        if (i18n.exists(`contracts.${this.methodHash}`)) {
            return t(`contracts.${this.methodHash}`).replace(/\(.+\)$/, '');
        }

        if (this.methodName !== null) {
            return this.methodName.replace(/_/g, ' ').split(' ').map(word => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase()).join(' ');
        }

        return `0x${this.methodHash}`;
    }

    get isTransfer(): boolean {
        return this.methodHash === null;
    }

    get isTokenTransfer(): boolean {
        const { network } = useShareData();

        return this.methodHash === network.contractMethods.transfer;
    }

    get isMultiPayment(): boolean {
        const { network } = useShareData();

        return this.methodHash === network.contractMethods.multipayment;
    }

    get isVote(): boolean {
        const { network } = useShareData();

        return this.methodHash === network.contractMethods.vote;
    }

    get isUnvote(): boolean {
        const { network } = useShareData();

        return this.methodHash === network.contractMethods.unvote;
    }

    get isValidatorRegistration(): boolean {
        const { network } = useShareData();

        return this.methodHash === network.contractMethods.validator_registration;
    }

    get isValidatorResignation(): boolean {
        const { network } = useShareData();

        return this.methodHash === network.contractMethods.validator_resignation;
    }

    get isValidatorUpdate(): boolean {
        const { network } = useShareData();

        return this.methodHash === network.contractMethods.validator_update;
    }

    get isUsernameRegistration(): boolean {
        const { network } = useShareData();

        return this.methodHash === network.contractMethods.username_registration;
    }

    get isUsernameResignation(): boolean {
        const { network } = useShareData();

        return this.methodHash === network.contractMethods.username_resignation;
    }

    get isApprove(): boolean {
        const { network } = useShareData();

        return this.methodHash === network.contractMethods.approve;
    }

    get isRevoke(): boolean {
        if (! this.isApprove) {
            return false;
        }

        return this.transaction.tokenApprovalDetails?.isRevoke === true;
    }

    get isBatchTransfer(): boolean {
        const { network } = useShareData();

        return this.methodHash === network.contractMethods.batch_transfer;
    }

    get isContractDeployment(): boolean {
        return this.transaction.to === null;
    }
}
