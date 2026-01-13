import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { MobileTopAccountsSkeletonTable } from "../Skeleton/Wallet/TopAccounts";
import { IPaginatedResponse } from "@/types";
import { IWallet } from "@/types/generated";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import Number from "@/Components/General/Number";
import Percentage from "@/Components/General/Percentage";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import useSharedData from "@/hooks/use-shared-data";

export function TopAccountsMobileTable({ wallets }: { wallets: IPaginatedResponse<IWallet> }) {
    const { t } = useTranslation();
    const { network } = useSharedData();
    const baseRank = (wallets.from ?? 1) - 1;

    return (
        <MobileTable noResultsMessage={wallets.noResultsMessage} resultCount={wallets.total ?? 0}>
            {wallets.data.map((wallet: IWallet, index) => {
                const rank = baseRank + index + 1;

                return (
                    <MobileTableRow
                        key={wallet.address}
                        header={
                            <div className="flex items-center space-x-3">
                                <Number className="min-w-[32px]">{rank}</Number>

                                <Link className="link min-w-0" href={route("wallet", wallet.address)}>
                                    <TruncateMiddle>{wallet.address}</TruncateMiddle>
                                </Link>
                            </div>
                        }
                    >
                        <TableCell label={t("labels.name")}>
                            {wallet.hasUsername ? (
                                <div className="inline-block text-theme-secondary-900 dark:text-theme-dark-50">
                                    {wallet.username}
                                </div>
                            ) : (
                                <div className="text-theme-secondary-500 dark:text-theme-dark-500">
                                    {t("general.na")}
                                </div>
                            )}
                        </TableCell>

                        <TableCell
                            label={t("tables.wallets.balance_currency", {
                                currency: network?.currency,
                            })}
                        >
                            {wallet.formattedBalanceFull}
                        </TableCell>

                        <TableCell label={t("general.wallet.percentage")}>
                            <Percentage>{wallet.balancePercentage}</Percentage>
                        </TableCell>
                    </MobileTableRow>
                );
            })}
        </MobileTable>
    );
}

export default function TopAccountsMobileTableWrapper({
    wallets,
    rowCount = 10,
}: {
    wallets?: IPaginatedResponse<IWallet>;
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();

    if (!wallets || isLoading) {
        return (
            <div>
                <TableHeaderWrapper resultCount={0} />

                <MobileTopAccountsSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <TopAccountsMobileTable wallets={wallets} />
        </div>
    );
}
