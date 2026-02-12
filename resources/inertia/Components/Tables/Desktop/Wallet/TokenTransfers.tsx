import TableCell from "../TableCell";
import LoadingTable from "../LoadingTable";
import { ITokenTransfer } from "@/types/generated";
import { useTranslation } from "react-i18next";
import Age from "@/Components/Model/Age";
import ID from "@/Components/Transaction/ID";
import { Table } from "../Table";
import Method from "@/Components/Transaction/Method";
import Addressing from "@/Components/Tokens/Addressing";
import TableHeader from "../TableHeader";
import { WalletProps } from "@/Pages/Wallet.contracts";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import useSharedData from "@/hooks/use-shared-data";
import Amount from "@/Components/Tokens/Amount";

export function Row({ row }: { row: ITokenTransfer }) {
    const { wallet } = useSharedData<WalletProps>();

    return (
        <tr className="text-sm font-semibold">
            <TableCell className="w-[60px]">
                <ID transaction={row.transaction!} />
            </TableCell>

            <TableCell breakpoint="xl" responsive>
                <Age timestamp={row.transaction!.timestamp} />
            </TableCell>

            <TableCell>
                <Method transaction={row.transaction!} />
            </TableCell>

            <TableCell>
                <Addressing tokenTransfer={row} wallet={wallet} />
            </TableCell>

            <TableCell className="text-right" lastOn="md-lg">
                <Amount
                    testId={`transaction:${row.transaction!.hash}:amount`}
                    tokenTransfer={row}
                    hideCurrency
                    wallet={wallet}
                />
            </TableCell>

            <TableCell className="text-right" breakpoint="md-lg" responsive>
                {row.token.symbol}
            </TableCell>
        </tr>
    );
}

export function TokenTransfersTable({ mobile }: { mobile?: React.ReactNode }) {
    const { t } = useTranslation();
    const { tokenTransfers } = useSharedData<WalletProps>();

    return (
        <Table
            withHeader
            withFooter
            paginator={tokenTransfers}
            rowComponent={Row}
            mobile={mobile}
            noResultsMessage={tokenTransfers.noResultsMessage}
            columns={
                <>
                    <TableHeader>{t("tables.transactions.id")}</TableHeader>

                    <TableHeader breakpoint="xl" responsive>
                        {t("tables.transactions.age")}
                    </TableHeader>

                    <TableHeader>{t("tables.transactions.method")}</TableHeader>

                    <TableHeader>{t("tables.transactions.addressing")}</TableHeader>

                    <TableHeader className="last-until-md-lg text-right" last-on="md-lg">
                        {t("tables.tokens.amount_generic")}
                    </TableHeader>

                    <TableHeader className="text-right" responsive breakpoint="md-lg">
                        {t("tables.tokens.token")}
                    </TableHeader>
                </>
            }
        />
    );
}

export default function TokenTransfersTableWrapper({
    mobile,
    rowCount = 20,
}: {
    mobile?: React.ReactNode;
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();
    const { t } = useTranslation();
    const { tokenTransfers } = useSharedData<WalletProps>();

    if (!tokenTransfers || isLoading) {
        return (
            <>
                <LoadingTable
                    header
                    mobile={mobile}
                    paginator={tokenTransfers}
                    rowCount={rowCount}
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
                            name: t("tables.tokens.amount_generic"),
                            className: "text-right w-[100px]",
                            lastOn: "md-lg",
                        },
                        {
                            name: t("tables.tokens.token"),
                            className: "w-[100px]",
                            lastOn: "md-lg",
                        },
                    ]}
                />
            </>
        );
    }

    return (
        <div>
            <TokenTransfersTable mobile={mobile} />
        </div>
    );
}
