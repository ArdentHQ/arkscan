import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { TransactionsListLoadingState, TransactionsTable } from "@/Components/Tables/Desktop/Transactions/Transactions";
import TransactionsMobileTableWrapper from "@/Components/Tables/Mobile/Transactions/Transactions";
import { MobileTransactionsSkeletonTable } from "@/Components/Tables/Mobile/Skeleton/Transactions/Transactions";
import ViewAllFooter from "./ViewAllFooter";

export default function HomeTransactionsTableWrapper({
    transactions,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
}) {
    const { t } = useTranslation();
    const { pagination } = useSharedData();
    const rowCount = transactions?.per_page ?? pagination?.per_page ?? 25;

    if (!transactions) {
        return (
            <TransactionsListLoadingState
                mobile={<MobileTransactionsSkeletonTable rowCount={rowCount} />}
                rowCount={rowCount}
            />
        );
    }

    return (
        <>
            <TransactionsTable
                transactions={transactions}
                mobile={<TransactionsMobileTableWrapper transactions={transactions} />}
                withHeader={false}
                hidePagination={true}
            />

            <div className="px-6 md:mx-auto md:max-w-7xl md:px-10">
                <ViewAllFooter
                    total={transactions.total ?? 0}
                    suffix={t("tables.home.transactions")}
                    href={route("transactions", { page: 2 })}
                />
            </div>
        </>
    );
}
