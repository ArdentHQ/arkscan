import { useTranslation } from "react-i18next";
import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";
import { PageSection } from "@/Components/PageSection";
import { TransactionsTable, TransactionsListLoadingState } from "@/Components/Tables/Desktop/Transactions/Transactions";
import { TransactionsMobileTable } from "@/Components/Tables/Mobile/Transactions/Transactions";
import { MobileTransactionsSkeletonTable } from "@/Components/Tables/Mobile/Skeleton/Transactions/Transactions";

function TransactionsMobile({
    transactions,
    noAge,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
    noAge?: boolean;
}) {
    if (!transactions) {
        return (
            <div className="md:hidden">
                <MobileTransactionsSkeletonTable rowCount={10} noAge={noAge} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <TransactionsMobileTable transactions={transactions} noAge={noAge} />
        </div>
    );
}

export default function TransactionList({
    transactions,
    noMargins = false,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
    noMargins?: boolean;
}) {
    const { t } = useTranslation();

    return (
        <PageSection title={t("pages.block.transactions")} noBorder>
            <div>
                <div className="hidden md:block">
                    {!transactions ? (
                        <TransactionsListLoadingState noMargins={noMargins} rowCount={10} noAge />
                    ) : (
                        <TransactionsTable noMargins={noMargins} transactions={transactions} withHeader={false} noAge />
                    )}
                </div>

                <TransactionsMobile transactions={transactions} noAge />
            </div>
        </PageSection>
    );
}
