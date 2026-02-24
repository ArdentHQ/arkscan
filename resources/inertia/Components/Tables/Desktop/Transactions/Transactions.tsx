import TableCell from "../TableCell";
import LoadingTable, { ILoadingTableColumn } from "../LoadingTable";
import { ITransaction } from "@/types/generated";
import { IPaginatedResponse } from "@/types";
import { useTranslation } from "react-i18next";
import Age from "@/Components/Model/Age";
import ID from "@/Components/Transaction/ID";
import Amount from "@/Components/Transaction/Amount";
import Fee from "@/Components/Transaction/Fee";
import { Table } from "../Table";
import Method from "@/Components/Transaction/Method";
import TableHeader from "../TableHeader";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import Filter from "@/Components/Tables/Filter";
import useSharedData from "@/hooks/use-shared-data";
import AddressingGeneric from "@/Components/Transaction/AddressingGeneric";
import LoadingText from "@/Components/Loading/Text";
import { Transaction } from "@/models/Transaction";

export function Row({ row, noAge }: { row: ITransaction; noAge?: boolean }) {
    const { network } = useSharedData();
    const transaction = Transaction.make(row, network);

    return (
        <tr className="text-sm font-semibold">
            <TableCell className="w-[60px]">
                <ID transaction={transaction} />
            </TableCell>

            {!noAge && (
                <TableCell breakpoint="xl" responsive>
                    <Age timestamp={row.timestamp} />
                </TableCell>
            )}

            <TableCell>
                <Method transaction={transaction} />
            </TableCell>

            <TableCell>
                <AddressingGeneric transaction={transaction} />
            </TableCell>

            <TableCell className="text-right" lastOn="lg">
                <Amount
                    testId={`transaction:${transaction.hash}:amount`}
                    transaction={transaction}
                    breakpoint="lg"
                    hideCurrency
                />
            </TableCell>

            <TableCell className="text-right" breakpoint="lg" responsive>
                <Fee transaction={transaction} hideCurrency />
            </TableCell>
        </tr>
    );
}

export function TransactionsTable({
    noMargins = false,
    noAge = false,
    transactions,
    mobile,
    withHeader = true,
    withFooter = true,
    hidePagination = false,
    headerActions,
}: {
    noMargins?: boolean;
    noAge?: boolean;
    transactions: IPaginatedResponse<ITransaction>;
    mobile?: React.ReactNode;
    withHeader?: boolean;
    withFooter?: boolean;
    hidePagination?: boolean;
    headerActions?: React.ReactNode;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <Table
            noMargins={noMargins}
            withHeader={withHeader}
            withFooter={withFooter}
            hidePagination={hidePagination}
            paginator={transactions}
            rowComponent={({ row, key }: { row: ITransaction; key?: React.Key }) => (
                <Row row={row} noAge={noAge} key={key ?? undefined} />
            )}
            mobile={mobile}
            headerActions={headerActions}
            noResultsMessage={transactions.noResultsMessage}
            columns={
                <>
                    <TableHeader>{t("tables.transactions.id")}</TableHeader>

                    {!noAge && (
                        <TableHeader breakpoint="xl" responsive>
                            {t("tables.transactions.age")}
                        </TableHeader>
                    )}

                    <TableHeader>{t("tables.transactions.method")}</TableHeader>

                    <TableHeader>{t("tables.transactions.addressing")}</TableHeader>

                    <TableHeader className="last-until-lg text-right" last-on="lg">
                        {t("tables.transactions.amount", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    <TableHeader className="text-right" responsive breakpoint="lg">
                        {t("tables.transactions.fee", {
                            currency: network!.currency,
                        })}
                    </TableHeader>
                </>
            }
        />
    );
}

export function TransactionsListLoadingState({
    noMargins = false,
    noAge = false,
    header,
    transactions,
    mobile,
    rowCount = 20,
}: {
    noMargins?: boolean;
    header?: React.ReactNode;
    transactions?: IPaginatedResponse<ITransaction>;
    mobile?: React.ReactNode;
    rowCount?: number;
    noAge?: boolean;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    const columns: ILoadingTableColumn[] = [
        {
            name: t("tables.transactions.id"),
            type: "string",
            className: "w-[60px]",
        },

        {
            name: t("tables.transactions.method"),
            indicatorHeight: "h-[21px]",
            className: "text-left",
        },
        {
            name: t("tables.transactions.addressing"),
            type: "address",
            indicatorHeight: "h-[21px]",
            className: "text-left",
            render: () => (
                <div className="flex flex-col space-y-2 text-sm font-semibold sm:space-y-1 md:space-y-2 md-lg:flex-row md-lg:items-center md-lg:space-x-9 md-lg:space-y-0">
                    <div className="flex flex-row space-x-2">
                        <LoadingText width="w-[39px]" />
                        <LoadingText />
                    </div>

                    <div className="flex flex-row space-x-2">
                        <LoadingText width="w-[39px]" />
                        <LoadingText />
                    </div>
                </div>
            ),
        },
        {
            name: t("tables.transactions.amount", {
                currency: network!.currency,
            }),
            className: "text-right w-[100px]",
            lastOn: "lg",
        },
        {
            name: t("tables.transactions.fee", {
                currency: network!.currency,
            }),
            className: "text-right w-[100px]",
            responsive: true,
            breakpoint: "lg",
        },
    ];

    if (!noAge) {
        // push as the second item
        columns.splice(1, 0, {
            name: t("tables.transactions.age"),
            type: "string",
            className: "w-[60px] lg:hidden",
            responsive: true,
            breakpoint: "xl",
            lastOn: "lg",
        });
    }

    return (
        <>
            <LoadingTable
                mobile={mobile}
                paginator={transactions}
                rowCount={rowCount}
                header={header}
                noMargins={noMargins}
                columns={columns}
            />
        </>
    );
}

export default function TransactionsTableWrapper({
    transactions,
    mobile,
    rowCount = 20,
}: {
    transactions?: IPaginatedResponse<ITransaction>;
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();
    if (!transactions || isLoading) {
        return (
            <TransactionsListLoadingState
                transactions={transactions}
                mobile={mobile}
                rowCount={rowCount}
                header={<TransactionsHeaderActions />}
            />
        );
    }

    return (
        <div>
            <TransactionsTable
                headerActions={<TransactionsHeaderActions />}
                transactions={transactions}
                mobile={mobile}
            />
        </div>
    );
}

export function TransactionsHeaderActions() {
    return (
        <div className="flex items-center justify-end space-x-3">
            <div className="flex-1">
                <Filter testId="transactions:filter" withSelectAll />
            </div>
        </div>
    );
}
