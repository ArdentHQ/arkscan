import { ITransaction, ITransactionDetails } from "@/types/generated";

export type TransactionDetails = ITransactionDetails;

export type TransactionTokenTransfer = NonNullable<ITransactionDetails["tokenTransfer"]>;
export type TransactionPayload = NonNullable<ITransactionDetails["payload"]>;
export type TransactionRecipient = ITransactionDetails["multiPaymentRecipients"][number];

export interface TransactionShowProps {
    transaction: ITransaction;
    details: ITransactionDetails;
}
