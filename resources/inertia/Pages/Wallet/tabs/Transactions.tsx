import TransactionsTableWrapper from "@/Components/Tables/Desktop/Wallet/Transactions";
import TransactionsMobileTableWrapper from "@/Components/Tables/Mobile/Wallet/Transactions";
import FilterProvider from "@/Providers/Filter/FilterProvider";
import { IFilters, IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";
import { useTranslation } from "react-i18next";

export default function WalletTransactionsTab({
    transactions,
    filters,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
    filters: IFilters;
}) {
    const { t } = useTranslation();

    return (
        <FilterProvider
            initialOptions={[
                {
                    label: t("tables.filters.transactions.addressing"),
                    options: [
                        {
                            label: t("tables.filters.transactions.outgoing"),
                            value: "outgoing",
                            selected: filters.outgoing,
                        },
                        {
                            label: t("tables.filters.transactions.incoming"),
                            value: "incoming",
                            selected: filters.incoming,
                        },
                    ],
                },
                {
                    label: t("tables.filters.transactions.types"),
                    options: [
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
                    ],
                },
            ]}
            onChange={() => {}}
        >
            <TransactionsTableWrapper
                transactions={transactions}
                mobile={<TransactionsMobileTableWrapper transactions={transactions} />}
            />
        </FilterProvider>
    );
}
