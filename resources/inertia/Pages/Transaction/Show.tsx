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
} from "@/Components/Transaction/Page";

export default function Show({ transaction, details }: PageProps<TransactionShowProps>) {
    const headerWidthClass = details.recipientIsContract ? "sm:w-[151px]" : "sm:w-[132px]";

    return (
        <Layout>
            <TransactionHeader transaction={transaction} />

            <div>
                <TransactionDetails transaction={transaction} headerWidthClass={headerWidthClass} />

                <TransactionAction transaction={transaction} details={details} headerWidthClass={headerWidthClass} />

                <TransactionAddressing transaction={transaction} details={details} headerWidthClass={headerWidthClass} />

                {transaction.isTokenTransfer && details.tokenTransfer && (
                    <TransactionToken details={details} headerWidthClass={headerWidthClass} />
                )}

                <TransactionSummary transaction={transaction} details={details} headerWidthClass={headerWidthClass} />

                <TransactionStatus transaction={transaction} details={details} />

                {transaction.isMultiPayment && details.multiPaymentRecipients.length > 0 && (
                    <TransactionRecipients recipients={details.multiPaymentRecipients} />
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
