import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { IPaginatedResponse } from "@/types";
import { IWallet } from "@/types/generated";
import useSharedData from "@/hooks/use-shared-data";
import useWalletFormatting from "@/hooks/use-wallet-formatting";
import Percentage from "@/Components/General/Percentage";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import Clipboard from "@/Components/General/Clipboard";
import BookmarkButton from "@/Components/General/BookmarkButton";

function Row({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const { network } = useSharedData();
    const { formattedBalanceFullWithoutSuffix } = useWalletFormatting(wallet.balance);

    return (
        <MobileTableRow
            header={
                <>
                    <Link className="link min-w-0 sm:hidden" href={route("wallet", wallet.address)}>
                        <TruncateMiddle>{wallet.address}</TruncateMiddle>
                    </Link>
                    <Link className="link hidden min-w-0 sm:block" href={route("wallet", wallet.address)}>
                        {wallet.address}
                    </Link>

                    <div className="flex shrink-0 items-center space-x-2">
                        <Clipboard
                            value={wallet.address}
                            noStyling
                            className="transition-default flex items-center text-theme-secondary-700 hover:text-theme-primary-700 dark:text-theme-dark-300 dark:hover:text-theme-dark-50"
                            tooltipContent={t("pages.wallet.address_copied")}
                            checkmarksClass=""
                        />

                        <BookmarkButton type="addresses" id={wallet.address} variant="inline" />
                    </div>
                </>
            }
        >
            <TableCell
                label={t("tables.wallets.balance_currency", {
                    currency: network?.currency,
                })}
            >
                {formattedBalanceFullWithoutSuffix}
            </TableCell>

            <TableCell label={t("general.wallet.percentage")}>
                <Percentage>{wallet.balancePercentage}</Percentage>
            </TableCell>
        </MobileTableRow>
    );
}

export default function BookmarkAddressesMobileTable({ addresses }: { addresses: IPaginatedResponse<IWallet> }) {
    return (
        <div className="px-6 md:hidden">
            <MobileTable noResultsMessage={addresses.noResultsMessage} resultCount={addresses.total ?? 0}>
                {addresses.data.map((wallet: IWallet) => (
                    <Row key={wallet.address} wallet={wallet} />
                ))}
            </MobileTable>
        </div>
    );
}
