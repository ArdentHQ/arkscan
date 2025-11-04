import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { MobileTransactionsSkeletonTable } from "../Skeleton/Wallet/Transactions";
import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";
import { useTranslation } from "react-i18next";
import ID from "@/Components/Transaction/ID";
import Age from "@/Components/Model/Age";
import Amount from "@/Components/Transaction/Amount";
import { useConfig } from "@/Providers/Config/ConfigContext";
import Fee from "@/Components/Transaction/Fee";
import Addressing from "@/Components/Transaction/Addressing";
import { HeaderActions } from "@/Components/Tables/Desktop/Wallet/Transactions";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";

export function TransactionsMobileTable({ transactions }: { transactions: IPaginatedResponse<ITransaction> }) {
    const { t } = useTranslation();
    const { network } = useConfig();

    return (
        <MobileTable noResultsMessage={transactions.noResultsMessage} resultCount={transactions.total ?? 0}>
            {transactions.data.map((transaction: ITransaction, index) => (
                <MobileTableRow
                    key={index}
                    header={
                        <>
                            <ID transaction={transaction} />

                            <Age timestamp={transaction.timestamp} />
                        </>
                    }
                >
                    <TableCell label={transaction.type} className="sm:flex-1">
                        <Addressing transaction={transaction} withoutLink={transaction.isSentToSelf} />
                    </TableCell>

                    <TableCell
                        label={t("tables.transactions.amount", {
                            currency: network?.currency,
                        })}
                    >
                        <Amount transaction={transaction} />
                    </TableCell>

                    <div className="sm:flex sm:flex-1 sm:justify-end">
                        <TableCell
                            label={t("tables.transactions.fee", {
                                currency: network?.currency,
                            })}
                        >
                            <Fee transaction={transaction} />
                        </TableCell>
                    </div>
                </MobileTableRow>
            ))}
        </MobileTable>
    );
}

export default function TransactionsMobileTableWrapper({
    transactions,
    rowCount = 10,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
    rowCount?: number;
}) {
    if (!transactions) {
        return (
            <div className="pt-6">
                <TableHeaderWrapper resultCount={0} breakpoint="md">
                    <HeaderActions hasTransactions={false} />
                </TableHeaderWrapper>

                <MobileTransactionsSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <TransactionsMobileTable transactions={transactions} />
        </div>
    );
}
