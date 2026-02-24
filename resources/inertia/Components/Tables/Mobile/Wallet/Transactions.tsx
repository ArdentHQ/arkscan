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
import useSharedData from "@/hooks/use-shared-data";
import Fee from "@/Components/Transaction/Fee";
import Addressing from "@/Components/Transaction/Addressing";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { TransactionsHeaderActions } from "@/Components/Tables/Desktop/Wallet/Transactions";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import { Transaction } from "@/models/Transaction";
import { WalletProps } from "@/Pages/Wallet.contracts";

export function TransactionsMobileTable({ transactions }: { transactions: IPaginatedResponse<ITransaction> }) {
    const { t, i18n } = useTranslation();
    const { network, wallet } = useSharedData<WalletProps>();

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

                                <Age timestamp={transaction.timestamp} />
                            </>
                        }
                    >
                        <TableCell label={transaction.method.name({ t, i18n })} className="sm:flex-1">
                            <Addressing
                                transaction={transaction}
                                withoutLink={transaction.isSentToSelf(wallet.address)}
                                wallet={wallet}
                            />
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
                                wallet={wallet}
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
                    <TransactionsHeaderActions hasTransactions={false} />
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
