import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { MobileTokenTransfersSkeletonTable } from "../Skeleton/Wallet/TokenTransfers";
import { ITokenAction } from "@/types/generated";
import { useTranslation } from "react-i18next";
import ID from "@/Components/Transaction/ID";
import Age from "@/Components/Model/Age";
import Amount from "@/Components/Tokens/Amount";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import Token from "@/Components/Tokens/Token";
import useSharedData from "@/hooks/use-shared-data";
import { WalletProps } from "@/Pages/Wallet.contracts";
import Addressing from "@/Components/Tokens/Addressing";
import { TokenAction } from "@/models/TokenAction";

export function TokenTransfersMobileTable() {
    const { t, i18n } = useTranslation();
    const { network, tokenActions, wallet } = useSharedData<WalletProps>();

    return (
        <MobileTable noResultsMessage={tokenActions.noResultsMessage} resultCount={tokenActions.total ?? 0}>
            {tokenActions.data.map((row: ITokenAction, index) => {
                const transfer = TokenAction.make(row, network);

                return (
                    <MobileTableRow
                        key={index}
                        header={
                            <>
                                <ID transaction={transfer.transaction} />

                                <Age
                                    className="text-theme-secondary-700 dark:text-theme-dark-200"
                                    timestamp={transfer.transaction.timestamp}
                                />
                            </>
                        }
                    >
                        <TableCell label={transfer.transaction.method.name({ t, i18n })} className="sm:flex-1">
                            <Addressing tokenAction={transfer} wallet={wallet} />
                        </TableCell>

                        <TableCell label={t("tables.tokens.amount_generic")} className="sm:flex-1">
                            <Amount
                                testId={`transaction:mobile:${transfer.transaction.hash}:amount`}
                                tokenAction={transfer}
                                hideCurrency
                                wallet={wallet}
                            />
                        </TableCell>

                        <TableCell label={t("tables.tokens.token")}>
                            <Token token={transfer.token} className="w-full sm:w-[120px]" />
                        </TableCell>
                    </MobileTableRow>
                );
            })}
        </MobileTable>
    );
}

export default function TokenTransfersMobileTableWrapper({ rowCount = 10 }: { rowCount?: number }) {
    const { isLoading } = usePageHandler();
    const { tokenActions } = useSharedData<WalletProps>();

    if (!tokenActions || isLoading) {
        return (
            <div>
                <TableHeaderWrapper resultCount={0} />

                <MobileTokenTransfersSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <TokenTransfersMobileTable />
        </div>
    );
}
