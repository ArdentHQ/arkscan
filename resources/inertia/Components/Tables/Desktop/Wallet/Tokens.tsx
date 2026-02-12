import TableCell from "../TableCell";
import LoadingTable, { ILoadingTableColumn } from "../LoadingTable";
import { ITokenHolder } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { Table } from "../Table";
import TableHeader from "../TableHeader";
import { WalletProps } from "@/Pages/Wallet.contracts";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import useSharedData from "@/hooks/use-shared-data";
import Address from "@/Components/Wallet/Address";
import AmountGeneric from "@/Components/Tokens/AmountGeneric";

export function Row({ row }: { row: ITokenHolder }) {
    const { network } = useSharedData<WalletProps>();

    return (
        <tr className="text-sm font-semibold">
            <TableCell className="max-w-[200px]">
                <div className="flex w-full min-w-0 items-center space-x-1">
                    <div className="min-w-0 truncate text-theme-secondary-900 dark:text-theme-dark-50">
                        {row.token.name}
                    </div>

                    <div className="md-lg:hidden">{row.token.symbol}</div>
                </div>
            </TableCell>

            <TableCell breakpoint="md-lg" responsive>
                {row.token.symbol}
            </TableCell>

            <TableCell>
                <Address wallet={row.token.address} truncate />
            </TableCell>

            <TableCell className="text-right">
                <AmountGeneric testId={`token:${row.token.symbol}:amount`} amount={row.balance} />
            </TableCell>

            {network.canBeExchanged && <TableCell className="text-right" breakpoint="md-lg" responsive></TableCell>}
        </tr>
    );
}

export function TokensTable({ mobile }: { mobile?: React.ReactNode }) {
    const { t } = useTranslation();
    const { network, tokens } = useSharedData<WalletProps>();

    return (
        <Table
            withHeader
            withFooter
            paginator={tokens}
            rowComponent={Row}
            mobile={mobile}
            noResultsMessage={tokens.noResultsMessage}
            columns={
                <>
                    <TableHeader className="max-w-[200px]">{t("tables.tokens.token")}</TableHeader>

                    <TableHeader breakpoint="md-lg" responsive>
                        {t("tables.tokens.symbol")}
                    </TableHeader>

                    <TableHeader>{t("tables.tokens.contract")}</TableHeader>

                    <TableHeader className="text-right">{t("tables.tokens.token_balance")}</TableHeader>

                    {network.canBeExchanged && (
                        <TableHeader className="last-until-md-lg text-right" last-on="md-lg">
                            {t("tables.tokens.value")}
                        </TableHeader>
                    )}
                </>
            }
        />
    );
}

export default function TokensTableWrapper({ mobile, rowCount = 20 }: { mobile?: React.ReactNode; rowCount?: number }) {
    const { isLoading } = usePageHandler();
    const { t } = useTranslation();
    const { network, tokens } = useSharedData<WalletProps>();

    if (!tokens || isLoading) {
        const columns: ILoadingTableColumn[] = [
            {
                name: t("tables.tokens.token"),
                type: "string",
                className: "w-[60px]",
            },
            {
                name: t("tables.tokens.symbol"),
                type: "string",
                className: "w-[60px]",
                responsive: true,
                breakpoint: "md-lg",
            },
            {
                name: t("tables.tokens.contract"),
                indicatorHeight: "h-[21px]",
                className: "text-left",
            },
            {
                name: t("tables.tokens.token_balance"),
                type: "address",
                indicatorHeight: "h-[21px]",
                className: "text-left",
            },
        ];

        if (network.canBeExchanged) {
            columns.push({
                name: t("tables.tokens.value"),
                className: "text-right w-[100px]",
                lastOn: "md-lg",
            });
        }

        return (
            <>
                <LoadingTable mobile={mobile} paginator={tokens} rowCount={rowCount} columns={columns} header />
            </>
        );
    }

    return (
        <div>
            <TokensTable mobile={mobile} />
        </div>
    );
}
