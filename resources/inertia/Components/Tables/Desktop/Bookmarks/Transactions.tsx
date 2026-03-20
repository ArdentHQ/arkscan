import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { Table } from "../Table";
import TableHeader from "../TableHeader";
import TableCell from "../TableCell";
import LoadingTable, { ILoadingTableColumn } from "../LoadingTable";
import { IPaginatedResponse } from "@/types";
import { ITransaction } from "@/types/generated";
import Age from "@/Components/Model/Age";
import ID from "@/Components/Transaction/ID";
import Amount from "@/Components/Transaction/Amount";
import Fee from "@/Components/Transaction/Fee";
import Method from "@/Components/Transaction/Method";
import AddressingGeneric from "@/Components/Transaction/AddressingGeneric";
import { Transaction } from "@/models/Transaction";
import BookmarkButton from "@/Components/General/BookmarkButton";
import BookmarkTransactionsMobileTable from "@/Components/Tables/Mobile/Bookmarks/Transactions";
import { MobileBookmarkTransactionsSkeletonTable } from "@/Components/Tables/Mobile/Skeleton/Bookmarks/Transactions";
import { TableHeaderWrapper } from "../Table";

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

            <TableCell className="text-right" lastOn="lg">
                <Amount transaction={transaction} breakpoint="lg" hideCurrency />
            </TableCell>

            <TableCell className="text-right" breakpoint="lg" responsive>
                <Fee transaction={transaction} hideCurrency />
            </TableCell>

            <TableCell>
                <BookmarkButton type="transactions" id={transaction.hash} variant="inline" />
            </TableCell>
        </tr>
    );
}

export default function BookmarkTransactionsTable({
    transactions,
    rowCount = 3,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
    rowCount?: number;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    if (!transactions) {
        const columns: ILoadingTableColumn[] = [
            { name: t("tables.transactions.id"), type: "string", className: "w-[60px]" },
            { name: t("tables.transactions.age"), responsive: true, breakpoint: "xl" },
            { name: t("tables.transactions.method") },
            { name: t("tables.transactions.addressing"), type: "address" },
            {
                name: t("tables.transactions.amount", { currency: network!.currency }),
                type: "number",
                lastOn: "lg",
            },
            {
                name: t("tables.transactions.fee", { currency: network!.currency }),
                type: "number",
                responsive: true,
                breakpoint: "lg",
            },
            { name: "" },
        ];

        return (
            <>
                <LoadingTable rowCount={rowCount} columns={columns} header />
                <div className="px-6 md:hidden">
                    <TableHeaderWrapper resultCount={resultCount} />
                    <MobileBookmarkTransactionsSkeletonTable rowCount={rowCount} />
                </div>
            </>
        );
    }

    return (
        <Table
            withHeader
            withFooter
            paginator={transactions}
            rowComponent={Row}
            noResultsMessage={transactions.noResultsMessage}
            mobile={<BookmarkTransactionsMobileTable transactions={transactions} />}
            columns={
                <>
                    <TableHeader>{t("tables.transactions.id")}</TableHeader>
                    <TableHeader breakpoint="xl" responsive>
                        {t("tables.transactions.age")}
                    </TableHeader>
                    <TableHeader>{t("tables.transactions.method")}</TableHeader>
                    <TableHeader>{t("tables.transactions.addressing")}</TableHeader>
                    <TableHeader className="last-until-lg text-right" lastOn="lg">
                        {t("tables.transactions.amount", { currency: network!.currency })}
                    </TableHeader>
                    <TableHeader className="text-right" responsive breakpoint="lg">
                        {t("tables.transactions.fee", { currency: network!.currency })}
                    </TableHeader>
                    <TableHeader>{""}</TableHeader>
                </>
            }
        />
    );
}
