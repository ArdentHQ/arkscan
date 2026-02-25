import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { MobileTransactionsSkeletonTable } from "@/Components/Tables/Mobile/Skeleton/Transactions/Transactions";
import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";
import { useTranslation } from "react-i18next";
import ID from "@/Components/Transaction/ID";
import Age from "@/Components/Model/Age";
import Amount from "@/Components/Transaction/Amount";
import useSharedData from "@/hooks/use-shared-data";
import Fee from "@/Components/Transaction/Fee";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { TransactionsHeaderActions } from "@/Components/Tables/Desktop/Transactions/Transactions";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import AddressingGeneric from "@/Components/Transaction/AddressingGeneric";
import { Transaction } from "@/models/Transaction";

export function TransactionsMobileTable({
    transactions,
    noAge,
}: {
    transactions: IPaginatedResponse<ITransaction>;
    noAge?: boolean;
}) {
    const { t, i18n } = useTranslation();
    const { network } = useSharedData();

    return (
        <MobileTable noResultsMessage={transactions.noResultsMessage} resultCount={transactions.total ?? 0}>
            {transactions.data.map((row: ITransaction, index) => {
                const transaction = Transaction.make(row, network);

                return (
                    <MobileTableRow
                        key={index}
                        header={
                            <>
                                <ID transaction={transaction} />

                                {!noAge && (
                                    <Age
                                        className="text-theme-secondary-700 dark:text-theme-dark-200"
                                        timestamp={transaction.timestamp}
                                    />
                                )}
                            </>
                        }
                    >
                        <TableCell label={transaction.method.name({ t, i18n })} className="sm:flex-1">
                            <AddressingGeneric transaction={transaction} />
                        </TableCell>

                        <TableCell
                            label={t("tables.transactions.amount", {
                                currency: network?.currency,
                            })}
                        >
                            <Amount
                                testId={`transaction:mobile:${transaction.hash}:amount`}
                                transaction={transaction}
                                hideCurrency={true}
                            />
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
                );
            })}
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
    const { isLoading } = usePageHandler();

    if (!transactions || isLoading) {
        return (
            <div>
                <TableHeaderWrapper resultCount={0}>
                    <TransactionsHeaderActions />
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
