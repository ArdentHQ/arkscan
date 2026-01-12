import { ITransaction } from "@/types/generated";

export interface TransactionTokenTransfer {
    recipient: string;
    amount: string | null;
}

export interface TransactionPayload {
    formatted: string | null;
    utf8: string | null;
    raw: string | null;
}

export interface TransactionRecipient {
    address: string;
    amount: string;
}

export interface TransactionDetails {
    confirmations: number;
    transactionError: string | null;
    recipientIsContract: boolean;
    validatorPublicKey: string | null;
    username: string | null;
    tokenTransfer: TransactionTokenTransfer | null;
    payload: TransactionPayload | null;
    multiPaymentRecipients: TransactionRecipient[];
    totalFiat: string | null;
    totalFiatValue: number | null;
}

export interface TransactionShowProps {
    transaction: ITransaction;
    details: TransactionDetails;
}
