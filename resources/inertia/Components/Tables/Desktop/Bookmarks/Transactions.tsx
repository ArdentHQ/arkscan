import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { Table } from "../Table";
import TableHeader from "../TableHeader";
import TableCell from "../TableCell";
import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";
import Age from "@/Components/Model/Age";
import ID from "@/Components/Transaction/ID";
import Amount from "@/Components/Transaction/Amount";
import Method from "@/Components/Transaction/Method";
import AddressingGeneric from "@/Components/Transaction/AddressingGeneric";
import { Transaction } from "@/models/Transaction";
import BookmarkButton from "@/Components/General/BookmarkButton";

function Row({ row }: { row: ITransaction }) {
    const { network } = useSharedData();
    const transaction = Transaction.make(row, network);

    return (
        <tr className="text-sm font-semibold">
            <TableCell className="w-[60px]">
                <ID transaction={transaction} />
            </TableCell>

            <TableCell breakpoint="xl" responsive>
                <Age timestamp={row.timestamp} />
            </TableCell>

            <TableCell>
                <Method transaction={transaction} />
            </TableCell>

            <TableCell>
                <AddressingGeneric transaction={transaction} />
            </TableCell>

            <TableCell className="text-right">
                <Amount transaction={transaction} hideCurrency />
            </TableCell>

            <TableCell className="text-center">
                <BookmarkButton type="transactions" id={transaction.hash} />
            </TableCell>
        </tr>
    );
}

export default function BookmarkTransactionsTable({
    transactions,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    if (!transactions) {
        return null;
    }

    return (
        <Table
            withHeader
            withFooter
            paginator={transactions}
            rowComponent={Row}
            noResultsMessage={transactions.noResultsMessage}
            columns={
                <>
                    <TableHeader>{t("tables.transactions.id")}</TableHeader>

                    <TableHeader breakpoint="xl" responsive>
                        {t("tables.transactions.age")}
                    </TableHeader>

                    <TableHeader>{t("tables.transactions.method")}</TableHeader>

                    <TableHeader>{t("tables.transactions.addressing")}</TableHeader>

                    <TableHeader className="text-right">
                        {t("tables.transactions.amount", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    <TableHeader className="text-center">{""}</TableHeader>
                </>
            }
        />
    );
}
