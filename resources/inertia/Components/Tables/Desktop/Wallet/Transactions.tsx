import TableCell from "../TableCell";
import LoadingTable from "../LoadingTable";
import { INetwork, IPaginatedResponse, ITransaction } from "@/types";
import { useTranslation } from "react-i18next";
import Age from "@/Components/Transaction/Age";
import ID from "@/Components/Transaction/ID";
import Amount from "@/Components/Transaction/Amount";
import Fee from "@/Components/Transaction/Fee";
import { Table } from "../Table";
import Method from "@/Components/Transaction/Method";

export function Row({ row }: {
    row: ITransaction;
    withFavoriteBorder?: boolean;
}) {
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
    network,
}: {
    transactions: IPaginatedResponse<ITransaction>;
    network: INetwork;
}) {
    const { t } = useTranslation();

    return (
        <Table
            withHeader
            withFooter
            paginator={transactions}
            rowComponent={Row}
            resultCount={transactions.total ?? 0}
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
                    {t('tables.transactions.amount', { currency: network.currency })}
                </th>

                <th className="text-right">
                    {t('tables.transactions.fee', { currency: network.currency })}
                </th>
            </>}
        />
    );
}

export default function TransactionsTableWrapper({
    transactions,
    rowCount = 20,
    network,
}: {
    transactions: IPaginatedResponse<ITransaction>;
    rowCount?: number;
    totalCount?: number;
    network: INetwork;
}) {
    if (!transactions || transactions.total === 0) {
        const { t } = useTranslation();

        return (
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
                            name: t('tables.transactions.amount', { currency: network.currency }),
                            className: "text-right w-[100px]",
                        },
                        {
                            name: t('tables.transactions.fee', { currency: network.currency }),
                            className: "text-right w-[100px]",
                        },
                    ]}
                />
            </div>
        );
    }

    return (
        <div className="hidden md:block">
            <TransactionsTable
                transactions={transactions}
                network={network}
            />
        </div>
    );
}
