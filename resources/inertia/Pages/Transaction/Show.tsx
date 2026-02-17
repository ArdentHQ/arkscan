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

export default function Show({ transaction, details }: PageProps<TransactionShowProps>) {
    const headerWidthClass = details.recipientIsContract ? "sm:w-[151px]" : "sm:w-[132px]";
    const { network } = useSharedData();

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

                {(transaction.isTokenTransfer || transaction.isBatchTransfer) && (
                    <TransactionToken transaction={transaction} details={details} headerWidthClass={headerWidthClass} />
                )}

                <TransactionSummary transaction={transaction} details={details} headerWidthClass={headerWidthClass} />

                <TransactionStatus transaction={transaction} details={details} />

                {transaction.isMultiPayment && details.multiPaymentRecipients.length > 0 && (
                    <TransactionRecipients recipients={details.multiPaymentRecipients} />
                )}

                {transaction.isBatchTransfer && details.batchTokenTransfers.length > 0 && (
                    <TransferDetails transfers={details.batchTokenTransfers} tokenSymbol={tokenSymbol} />
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
