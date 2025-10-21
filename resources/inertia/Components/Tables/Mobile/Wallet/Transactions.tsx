import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { MobileTransactionsSkeletonTable } from "../Skeleton/Wallet/Transactions";
import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";
import { useTranslation } from "react-i18next";
import ID from "@/Components/Transaction/ID";
import Age from "@/Components/Transaction/Age";
import Amount from "@/Components/Transaction/Amount";
import { useConfig } from "@/Providers/Config/ConfigContext";
import Fee from "@/Components/Transaction/Fee";
import Addressing from "@/Components/Transaction/Addressing";

export function TransactionsMobileTable({ transactions }: { transactions: ITransaction[] }) {
    const { t } = useTranslation();
    const { network } = useConfig();

    return (
        <MobileTable className="">
            {transactions.map((transaction: ITransaction, index) => (
                <MobileTableRow
                    key={index}
                    header={(
                        <>
                            <ID transaction={transaction} />

                            <Age transaction={transaction} />
                        </>
                    )}
                >
                    <TableCell label={transaction.type}>
                        <Addressing
                            transaction={transaction}
                            withoutLink={transaction.isSentToSelf}
                        />
                    </TableCell>

                    <TableCell label={t('tables.transactions.amount', { currency: network?.currency })}>
                        <Amount transaction={transaction} />
                    </TableCell>

                    <TableCell label={t('tables.transactions.fee', { currency: network?.currency })}>
                        <Fee transaction={transaction} />
                    </TableCell>
                </MobileTableRow>
            ))}
        </MobileTable>
    );
}

export default function TransactionsMobileTableWrapper({ transactions, rowCount = 10 }: {
    transactions: IPaginatedResponse<ITransaction>;
    rowCount?: number;
}) {
    if (!transactions || transactions.total === 0) {
        return (
            <MobileTransactionsSkeletonTable rowCount={rowCount} />
        );
    }

    return (
        <div className="md:hidden">
            <TransactionsMobileTable transactions={transactions.data} />
        </div>
    );
}
