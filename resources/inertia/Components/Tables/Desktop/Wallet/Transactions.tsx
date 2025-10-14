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

export function Row({ row }: { row: ITransaction }) {
    return (
        <tr className="text-sm font-semibold">
            <TableCell className="w-[60px]">
                <ID transaction={row} />
            </TableCell>

            <TableCell>
                <Age transaction={row} />
            </TableCell>

            <TableCell>
                <Method transaction={row} />
            </TableCell>

            <TableCell>
                Addressing
            </TableCell>

            <TableCell className="text-right">
                <Amount transaction={row} />
            </TableCell>

            <TableCell className="text-right">
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
            columns={<>
                <th sorting-id="header-order">
                    {t('tables.transactions.id')}
                </th>

                <th>
                    {t('tables.transactions.age')}
                </th>

                <th>
                    {t('tables.transactions.method')}
                </th>

                <th>
                    {t('tables.transactions.addressing')}
                </th>

                <th className="text-right">
                    {t('tables.transactions.amount', { currency: network!.currency })}
                </th>

                <th className="text-right">
                    {t('tables.transactions.fee', { currency: network!.currency })}
                </th>
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
    if (!transactions || transactions.total === 0) {
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
