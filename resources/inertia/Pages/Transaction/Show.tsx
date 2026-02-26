import { PageProps } from "@inertiajs/core";
import Layout from "@/Layout";
import { TransactionShowProps } from "@/Pages/Transaction.contracts";
import {
    TransactionHeader,
    TransactionDetails,
    TransactionAction,
    TransactionAddressing,
    TransactionToken,
    TransactionSummary,
    TransactionStatus,
    TransactionRecipients,
    TransactionMoreDetails,
    TransferDetails,
} from "@/Components/Transaction/Page";
import useSharedData from "@/hooks/use-shared-data";
import { Transaction } from "@/models/Transaction";

export default function Show({ transaction: transactionData, details }: PageProps<TransactionShowProps>) {
    const { network } = useSharedData();
    const transaction = Transaction.make(transactionData, network);
    const headerWidthClass = details.recipientIsContract ? "sm:w-[151px]" : "sm:w-[132px]";

    const tokenSymbol = details.token?.symbol ?? network?.currency ?? "";

    return (
        <Layout>
            <TransactionHeader transaction={transaction} />

            <div>
                <TransactionDetails transaction={transaction} details={details} headerWidthClass={headerWidthClass} />

                <TransactionAction transaction={transaction} details={details} headerWidthClass={headerWidthClass} />

                <TransactionAddressing
                    transaction={transaction}
                    details={details}
                    headerWidthClass={headerWidthClass}
                />

                {(transaction.method.isTokenTransfer || transaction.method.isBatchTransfer) && (
                    <TransactionToken transaction={transaction} details={details} headerWidthClass={headerWidthClass} />
                )}

                <TransactionSummary transaction={transaction} details={details} headerWidthClass={headerWidthClass} />

                <TransactionStatus transaction={transaction} details={details} />

                {transaction.method.isMultiPayment && transaction.multiPaymentRecipients.length > 0 && (
                    <TransactionRecipients recipients={transaction.multiPaymentRecipients} />
                )}

                {transaction.method.isBatchTransfer && details.batchTokenTransfers.length > 0 && (
                    <TransferDetails
                        transfers={details.batchTokenTransfers}
                        tokenSymbol={tokenSymbol}
                        fullTokenSymbol={details.token?.symbolFull}
                    />
                )}
            </div>

            <div className="mb-8">
                <TransactionMoreDetails
                    transaction={transaction}
                    details={details}
                    headerWidthClass={headerWidthClass}
                />
            </div>
        </Layout>
    );
}
