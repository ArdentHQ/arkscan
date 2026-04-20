import { useTranslation } from "react-i18next";
import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";
import useSharedData from "@/hooks/use-shared-data";
import ID from "@/Components/Transaction/ID";
import Age from "@/Components/Model/Age";
import Amount from "@/Components/Transaction/Amount";
import Fee from "@/Components/Transaction/Fee";
import AddressingGeneric from "@/Components/Transaction/AddressingGeneric";
import { Transaction } from "@/models/Transaction";
import BookmarkButton from "@/Components/General/BookmarkButton";

function Row({ row }: { row: ITransaction }) {
    const { t, i18n } = useTranslation();
    const { network } = useSharedData();
    const transaction = Transaction.make(row, network);

    return (
        <MobileTableRow
            header={
                <>
                    <ID transaction={transaction} />

                    <div className="flex items-center space-x-2">
                        <Age
                            className="text-theme-secondary-700 dark:text-theme-dark-200"
                            timestamp={transaction.timestamp}
                        />
                        <BookmarkButton type="transactions" id={transaction.hash} variant="inline" />
                    </div>
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
                    hideCurrency
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
}

export default function BookmarkTransactionsMobileTable({
    transactions,
}: {
    transactions: IPaginatedResponse<ITransaction>;
}) {
    return (
        <div className="md:hidden">
            <MobileTable noResultsMessage={transactions.noResultsMessage} resultCount={transactions.total ?? 0}>
                {transactions.data.map((row: ITransaction, index) => (
                    <Row key={index} row={row} />
                ))}
            </MobileTable>
        </div>
    );
}
