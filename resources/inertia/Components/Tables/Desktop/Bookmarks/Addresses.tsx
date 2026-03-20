import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { Table } from "../Table";
import TableHeader from "../TableHeader";
import TableCell from "../TableCell";
import LoadingTable, { ILoadingTableColumn } from "../LoadingTable";
import { IPaginatedResponse } from "@/types";
import { IWallet } from "@/types/generated";
import { Wallet } from "@/models/Wallet";
import useWalletFormatting from "@/hooks/use-wallet-formatting";
import Tooltip from "@/Components/General/Tooltip";
import Clipboard from "@/Components/General/Clipboard";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import Percentage from "@/Components/General/Percentage";
import BookmarkButton from "@/Components/General/BookmarkButton";
import BookmarkAddressesMobileTable from "@/Components/Tables/Mobile/Bookmarks/Addresses";
import { MobileBookmarkAddressesSkeletonTable } from "@/Components/Tables/Mobile/Skeleton/Bookmarks/Addresses";
import VoteTooltipContent from "@/Components/Transaction/VoteTooltipContent";
import VerifiedCheckmarkIcon from "@ui/icons/verified-checkmark.svg?react";
import ExchangeIcon from "@ui/icons/exchanges.svg?react";
import SecondSignatureIcon from "@ui/icons/transaction/second-signature.svg?react";
import CheckMarkBoxIcon from "@ui/icons/check-mark-box.svg?react";

function WalletTypeIcons({ wallet }: { wallet: IWallet }) {
    const { t } = useTranslation();
    const { network } = useSharedData();
    const walletModel = Wallet.from(wallet, {
        validatorCount: network!.validatorCount,
        knownWallets: network!.knownWallets,
    });

    return (
        <div className="flex w-full items-center justify-center space-x-2 text-theme-secondary-700 dark:text-theme-dark-200">
            {walletModel.isKnown && (
                <Tooltip content={t("labels.verified_address")}>
                    <VerifiedCheckmarkIcon className="h-4 w-4" />
                </Tooltip>
            )}
            {walletModel.isOwnedByExchange && (
                <Tooltip content={t("labels.exchange")}>
                    <ExchangeIcon className="h-4 w-4" />
                </Tooltip>
            )}
            {walletModel.hasSecondSignature && (
                <Tooltip content={t("labels.second_signature")}>
                    <SecondSignatureIcon className="h-4 w-4" />
                </Tooltip>
            )}
        </div>
    );
}

function WalletVotingIndicator({ wallet }: { wallet: IWallet }) {
    const isVoting = wallet.attributes?.vote !== null && wallet.attributes?.vote !== undefined;
    if (!isVoting) return null;

    const validatorLabel = wallet.vote?.username ?? wallet.vote?.address;
    const tooltip = validatorLabel ? <VoteTooltipContent variant="voting" validator={validatorLabel} /> : null;

    return (
        <div className="flex w-full items-center justify-center">
            {tooltip ? (
                <Tooltip content={tooltip}>
                    <CheckMarkBoxIcon className="h-4 w-4" />
                </Tooltip>
            ) : (
                <CheckMarkBoxIcon className="h-4 w-4" />
            )}
        </div>
    );
}

function Row({ row: wallet }: { row: IWallet }) {
    const { t } = useTranslation();
    const { network } = useSharedData();
    const { formattedBalanceFullWithoutSuffix, fiatValue } = useWalletFormatting(wallet.balance);

    return (
        <tr className="text-sm font-semibold">
            <TableCell>
                <div className="flex w-full items-center text-sm leading-4.25">
                    <Link className="link min-w-0" href={route("wallet", wallet.address)}>
                        <span className="xl:hidden">
                            <TruncateMiddle>{wallet.address}</TruncateMiddle>
                        </span>
                        <span className="hidden xl:inline">{wallet.address}</span>
                    </Link>
                    <Clipboard
                        value={wallet.address}
                        noStyling
                        className="transition-default ml-2 flex h-auto w-auto shrink-0 items-center text-theme-secondary-700 hover:text-theme-primary-700 dark:text-theme-dark-300 dark:hover:text-theme-dark-50"
                        tooltipContent={t("pages.wallet.address_copied")}
                        checkmarksClass=""
                    />
                </div>
            </TableCell>

            <TableCell>
                <span className="leading-4.25">{wallet.username ?? null}</span>
            </TableCell>

            <TableCell className="text-center" breakpoint="md-lg" responsive>
                <WalletTypeIcons wallet={wallet} />
            </TableCell>

            <TableCell className="text-center" breakpoint="lg" responsive>
                <WalletVotingIndicator wallet={wallet} />
            </TableCell>

            <TableCell className="text-right" lastOn="lg">
                <div className="flex flex-col font-semibold leading-4.25 text-theme-secondary-900 dark:text-theme-dark-50">
                    <Tooltip content={fiatValue} disabled={!network?.canBeExchanged}>
                        <span>{formattedBalanceFullWithoutSuffix}</span>
                    </Tooltip>
                    <span className="mt-1 text-xs font-semibold leading-3.75 text-theme-secondary-500 md-lg:hidden">
                        <Percentage>{wallet.balancePercentage}</Percentage>
                    </span>
                </div>
            </TableCell>

            <TableCell className="text-right" breakpoint="md-lg" responsive>
                <div className="flex font-semibold leading-4.25">
                    <Percentage>{wallet.balancePercentage}</Percentage>
                </div>
            </TableCell>

            <TableCell>
                <BookmarkButton type="addresses" id={wallet.address} variant="inline" />
            </TableCell>
        </tr>
    );
}

export default function BookmarkAddressesTable({
    addresses,
    rowCount = 3,
}: {
    addresses?: IPaginatedResponse<IWallet>;
    rowCount?: number;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    if (!addresses) {
        const columns: ILoadingTableColumn[] = [
            { name: t("general.wallet.address"), type: "address" },
            { name: t("general.wallet.name") },
            { name: t("general.wallet.type"), className: "text-center", responsive: true, breakpoint: "md-lg" },
            { name: t("general.wallet.voting"), className: "text-center", responsive: true, breakpoint: "lg" },
            {
                name: t("general.wallet.balance_currency", { currency: network!.currency }),
                type: "number",
                lastOn: "lg",
            },
            {
                name: t("general.wallet.percentage"),
                type: "number",
                responsive: true,
                breakpoint: "md-lg",
                tooltip: t("pages.wallets.percentage_tooltip"),
            },
            { name: "" },
        ];

        return (
            <>
                <LoadingTable rowCount={rowCount} columns={columns} header />
                <MobileBookmarkAddressesSkeletonTable rowCount={rowCount} />
            </>
        );
    }

    return (
        <Table
            withHeader
            withFooter
            paginator={addresses}
            rowComponent={Row}
            noResultsMessage={addresses.noResultsMessage}
            mobile={<BookmarkAddressesMobileTable addresses={addresses} />}
            columns={
                <>
                    <TableHeader>{t("general.wallet.address")}</TableHeader>
                    <TableHeader>{t("general.wallet.name")}</TableHeader>
                    <TableHeader className="text-center" breakpoint="md-lg" responsive>
                        {t("general.wallet.type")}
                    </TableHeader>
                    <TableHeader className="text-center" breakpoint="lg" responsive>
                        {t("general.wallet.voting")}
                    </TableHeader>
                    <TableHeader className="last-until-lg text-right" lastOn="lg">
                        {t("general.wallet.balance_currency", { currency: network!.currency })}
                    </TableHeader>
                    <TableHeader
                        className="text-right"
                        breakpoint="md-lg"
                        responsive
                        type="number"
                        tooltip={t("pages.wallets.percentage_tooltip")}
                    >
                        {t("general.wallet.percentage")}
                    </TableHeader>
                    <TableHeader>{""}</TableHeader>
                </>
            }
        />
    );
}
