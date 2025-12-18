import TableCell from "../TableCell";
import LoadingTable from "../LoadingTable";
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

export function Row({ row }: { row: ITransaction }) {
    return (
        <tr className="text-sm font-semibold">
            <TableCell className="w-[60px]">
                <ID transaction={row} />
            </TableCell>

            <TableCell breakpoint="xl" responsive>
                <Age timestamp={row.timestamp} />
            </TableCell>

            <TableCell>
                <Method transaction={row} />
            </TableCell>

            <TableCell>
                <AddressingGeneric transaction={row} />
            </TableCell>

            <TableCell className="text-right" lastOn="lg">
                <Amount testId={`transaction:${row.hash}:amount`} transaction={row} breakpoint="lg" />
            </TableCell>

            <TableCell className="text-right" breakpoint="lg" responsive>
                <Fee transaction={row} />
            </TableCell>
        </tr>
    );
}

export function TransactionsTable({
    transactions,
    mobile,
}: {
    transactions: IPaginatedResponse<ITransaction>;
    mobile?: React.ReactNode;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <Table
            withHeader
            withFooter
            paginator={transactions}
            rowComponent={Row}
            mobile={mobile}
            headerActions={<TransactionsHeaderActions />}
            noResultsMessage={transactions.noResultsMessage}
            columns={
                <>
                    <TableHeader>{t("tables.transactions.id")}</TableHeader>

                    <TableHeader breakpoint="xl" responsive>
                        {t("tables.transactions.age")}
                    </TableHeader>

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
    const { t } = useTranslation();
    const { network } = useSharedData();

    if (!transactions || isLoading) {
        return (
            <>
                <LoadingTable
                    mobile={mobile}
                    paginator={transactions}
                    rowCount={rowCount}
                    header={<TransactionsHeaderActions />}
                    columns={[
                        {
                            name: t("tables.transactions.id"),
                            type: "string",
                            className: "w-[60px]",
                        },
                        {
                            name: t("tables.transactions.age"),
                            type: "string",
                            className: "w-[60px]",
                            responsive: true,
                            breakpoint: "xl",
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
                    ]}
                />
            </>
        );
    }

    return (
        <div>
            <TransactionsTable transactions={transactions} mobile={mobile} />
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
