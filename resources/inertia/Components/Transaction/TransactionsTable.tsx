import { TransactionsProps } from "@/Pages/Transactions.contracts";
import FilterProvider from "@/Providers/Filter/FilterProvider";
import { useTranslation } from "react-i18next";
import TransactionsTableWrapper from "@/Components/Tables/Desktop/Transactions/Transactions";
import TransactionsMobileTableWrapper from "@/Components/Tables/Mobile/Transactions/Transactions";
import { router } from "@inertiajs/react";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { useEffect } from "react";
import useSharedData from "@/hooks/use-shared-data";

export default function TransactionsTable() {
    const { t } = useTranslation();
    const { setRefreshPage } = usePageHandler();
    const { transactions, filters } = useSharedData<TransactionsProps>();

    const updateTable = (callback?: CallableFunction) => {
        router.reload({
            only: ["transactions", "filters"],
            onSuccess: () => {
                if (callback) {
                    callback();
                }
            },
        });
    };

    useEffect(() => {
        updateTable();

        setRefreshPage((callback: CallableFunction) => {
            updateTable(callback);
        });
    }, []);

    return (
        <FilterProvider
            initialOptions={[
                {
                    label: t("tables.filters.transactions.transfers"),
                    value: "transfers",
                    selected: filters.transfers,
                },
                {
                    label: t("tables.filters.transactions.multipayments"),
                    value: "multipayments",
                    selected: filters.multipayments,
                },
                {
                    label: t("tables.filters.transactions.votes"),
                    value: "votes",
                    selected: filters.votes,
                },
                {
                    label: t("tables.filters.transactions.validator"),
                    value: "validator",
                    selected: filters.validator,
                },
                {
                    label: t("tables.filters.transactions.username"),
                    value: "username",
                    selected: filters.username,
                },
                {
                    label: t("tables.filters.transactions.contract_deployment"),
                    value: "contract_deployment",
                    selected: filters.contract_deployment,
                },
                {
                    label: t("tables.filters.transactions.others"),
                    value: "others",
                    selected: filters.others,
                },
            ]}
        >
            <TransactionsTableWrapper
                transactions={transactions}
                mobile={<TransactionsMobileTableWrapper transactions={transactions} />}
            />
        </FilterProvider>
    );
}
