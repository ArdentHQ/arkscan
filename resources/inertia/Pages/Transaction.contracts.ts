import { ITransaction, ITransactionDetails } from "@/types/generated";

export type TransactionDetails = ITransactionDetails;

export type TransactionTokenAction = NonNullable<ITransactionDetails["tokenTransfer"]>;
export type TransactionTokenApproval = NonNullable<ITransactionDetails["tokenApproval"]>;
export type TransactionPayload = NonNullable<ITransactionDetails["payload"]>;
export type TransactionRecipient = ITransaction["multiPaymentRecipients"][number];

export interface TransactionShowProps {
    transaction: ITransaction;
    details: ITransactionDetails;
    recipients: TransactionRecipient[];
}
