import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { MobileTokensSkeletonTable } from "../Skeleton/Wallet/Tokens";
import { ITokenHolder } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import Token from "@/Components/Tokens/Token";
import useSharedData from "@/hooks/use-shared-data";
import { WalletProps } from "@/Pages/Wallet.contracts";
import Address from "@/Components/Wallet/Address";
import AmountGeneric from "@/Components/Tokens/AmountGeneric";
import TruncatedValue from "@/Components/Tokens/TruncatedValue";

export function TokensMobileTable() {
    const { t } = useTranslation();
    const { network, tokens } = useSharedData<WalletProps>();

    return (
        <MobileTable noResultsMessage={tokens.noResultsMessage} resultCount={tokens.total ?? 0}>
            {tokens.data.map((token: ITokenHolder, index) => (
                <MobileTableRow
                    key={index}
                    header={
                        <div className="flex min-w-0 items-center justify-between space-x-2">
                            <div className="flex w-full min-w-0 items-center space-x-1">
                                <div className="truncate text-theme-secondary-900 dark:text-theme-dark-50">
                                    <TruncatedValue value={token.token.name} />
                                </div>

                                <div className="md-lg:hidden">
                                    <TruncatedValue value={token.token.symbol} />
                                </div>
                            </div>

                            <Address wallet={token.token.address} truncate className="min-w-auto" />
                        </div>
                    }
                >
                    <TableCell label={t("tables.tokens.token_balance")} className="sm:flex-1">
                        <AmountGeneric testId={`token:mobile:${token.token.symbol}:amount`} amount={token.balance} />
                    </TableCell>

                    {network.canBeExchanged && (
                        <TableCell label={t("tables.tokens.value")}>
                            <Token token={token.token} className="w-full sm:w-[120px]" />
                        </TableCell>
                    )}

                    <TableCell label={t("tables.tokens.token")} className="sm:hidden">
                        <Address wallet={token.token.address} truncate />
                    </TableCell>
                </MobileTableRow>
            ))}
        </MobileTable>
    );
}

export default function TokensMobileTableWrapper({ rowCount = 10 }: { rowCount?: number }) {
    const { isLoading } = usePageHandler();
    const { tokens } = useSharedData<WalletProps>();

    if (!tokens || isLoading) {
        return (
            <div>
                <TableHeaderWrapper resultCount={0} />

                <MobileTokensSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <TokensMobileTable />
        </div>
    );
}
