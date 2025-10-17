import TableCell from "../TableCell";
import LoadingTable from "../LoadingTable";
import { IPaginatedResponse, ITransaction } from "@/types";
import { useTranslation } from "react-i18next";
import Age from "@/Components/Transaction/Age";
import ID from "@/Components/Transaction/ID";
import Amount from "@/Components/Transaction/Amount";
import Fee from "@/Components/Transaction/Fee";
import { Table } from "../Table";
import Method from "@/Components/Transaction/Method";
import { useConfig } from "@/Providers/Config/ConfigContext";
import Addressing from "@/Components/Transaction/Addressing";
import UnderlineArrowDownIcon from "@ui/icons/arrows/underline-arrow-down.svg?react";
import FilterIcon from "@ui/icons/filter.svg?react";
import TableHeader from "../TableHeader";

export function Row({ row }: { row: ITransaction }) {
    return (
        <tr className="text-sm font-semibold">
            <TableCell className="w-[60px]">
                <ID transaction={row} />
            </TableCell>

            <TableCell
                breakpoint="xl"
                responsive
            >
                <Age transaction={row} />
            </TableCell>

            <TableCell>
                <Method transaction={row} />
            </TableCell>

            <TableCell>
                <Addressing
                    transaction={row}
                    withoutLink={row.isSentToSelf}
                />
            </TableCell>

            <TableCell
                className="text-right"
                lastOn="md-lg"
            >
                <Amount transaction={row} />
            </TableCell>

            <TableCell
                className="text-right"
                breakpoint="md-lg"
                responsive
            >
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
    const { network } = useConfig();

    return (
        <Table
            withHeader
            withFooter
            paginator={transactions}
            rowComponent={Row}
            resultCount={transactions.total ?? 0}
            mobile={mobile}
            headerActions={<HeaderActions />}
            noResultsMessage={transactions.noResultsMessage}
            columns={<>
                <TableHeader>
                    {t('tables.transactions.id')}
                </TableHeader>

                <TableHeader
                    breakpoint="xl"
                    responsive
                >
                    {t('tables.transactions.age')}
                </TableHeader>

                <TableHeader>
                    {t('tables.transactions.method')}
                </TableHeader>

                <TableHeader>
                    {t('tables.transactions.addressing')}
                </TableHeader>

                <TableHeader
                    className="text-right last-until-md-lg"
                    last-on="md-lg"
                >
                    {t('tables.transactions.amount', { currency: network!.currency })}
                </TableHeader>

                <TableHeader
                    className="text-right"
                    responsive
                    breakpoint="md-lg"
                >
                    {t('tables.transactions.fee', { currency: network!.currency })}
                </TableHeader>
            </>}
        />
    );
}

export default function TransactionsTableWrapper({
    transactions,
    mobile,
    rowCount = 20,
}: {
    transactions: IPaginatedResponse<ITransaction>;
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    if (!transactions) {
        const { t } = useTranslation();
        const { network } = useConfig();

        return (
            <>
                <div className="hidden md:block">
                    <LoadingTable
                        rowCount={rowCount}
                        columns={[
                            {
                                name: t('tables.transactions.id'),
                                type: "string",
                                className: "w-[60px]",
                            },
                            {
                                name: t('tables.transactions.age'),
                                type: "string",
                                className: "w-[60px]",
                            },
                            {
                                name: t('tables.transactions.method'),
                                indicatorHeight: "h-[21px]",
                                className: "text-left",
                            },
                            {
                                name: t('tables.transactions.addressing'),
                                type: "address",
                                indicatorHeight: "h-[21px]",
                                className: "text-left",
                            },
                            {
                                name: t('tables.transactions.amount', { currency: network!.currency }),
                                className: "text-right w-[100px]",
                            },
                            {
                                name: t('tables.transactions.fee', { currency: network!.currency }),
                                className: "text-right w-[100px]",
                            },
                        ]}
                    />
                </div>

                {!! mobile && (
                    <div className="px-6 md:px-10 md:hidden">
                        {mobile}
                    </div>
                )}
            </>
        );
    }

    return (
        <div>
            <TransactionsTable
                transactions={transactions}
                mobile={mobile}
            />
        </div>
    );
}

function HeaderActions() {
    const { t } = useTranslation();

    return (
        <div className="flex items-center justify-end space-x-3">
            <div className="flex-1">
                <button
                    type="button"
                    className="flex justify-center items-center py-1.5 space-x-2 w-full sm:px-4 button-secondary"
                    disabled
                >
                    <UnderlineArrowDownIcon className="h-4 w-4" />

                    <span>{t('actions.export')}</span>
                </button>
            </div>

            <div className="flex-1">
                <button
                    type="button"
                    className="flex items-center focus:outline-none dropdown-button transition-default flex-1 justify-center rounded sm:flex-none button-secondary w-full py-1.5 sm:px-4 md:p-2"
                    disabled
                >
                    <div className="inline-flex items-center mx-auto whitespace-nowrap">
                        <FilterIcon className="h-4 w-4" />

                        <div className="ml-2 md:hidden">
                            {t('actions.filter')}
                        </div>
                    </div>
                </button>
            </div>
        </div>
    );
}
