import classNames from "classnames";
import { Link } from "@inertiajs/react";
import { useTranslation } from "react-i18next";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import TruncateDynamic from "@/Components/General/TruncateDynamic";
import Clipboard from "@/Components/General/Clipboard";
import Info from "@/Components/General/Info";
import Badge from "@/Components/General/Badge";
import ContractIcon from "@ui/icons/transaction/contract.svg?react";
import { IWallet } from "@/types/generated";

export default function TransactionAddress({
    wallet,
    address,
    isContract = false,
    className,
}: React.HTMLAttributes<HTMLDivElement> & {
    wallet?: IWallet | null;
    address?: string | null;
    isContract?: boolean;
}) {
    const { t } = useTranslation();

    const resolvedAddress = address ?? wallet?.address ?? "";
    const hasUsername = wallet?.hasUsername ?? false;
    const username = wallet?.username ?? null;

    if (!resolvedAddress) {
        return (
            <span className={classNames("text-theme-secondary-900 dark:text-theme-dark-50", className)}>
                {t("general.na")}
            </span>
        );
    }

    return (
        <div className={classNames("flex items-center justify-end sm:justify-start", className)}>
            <Link href={route("wallet", resolvedAddress)} className="link min-w-0">
                <div className="hidden md:inline">
                    {hasUsername ? username : <TruncateDynamic value={resolvedAddress} />}
                </div>

                <div className="md:hidden">
                    {hasUsername ? (
                        username
                    ) : !isContract ? (
                        <TruncateMiddle>{resolvedAddress}</TruncateMiddle>
                    ) : (
                        t("general.contract")
                    )}
                </div>
            </Link>

            {isContract && (
                <div className="ml-3 h-5 w-5 md:hidden">
                    <Info tooltip={resolvedAddress} type="info" />
                </div>
            )}

            <Clipboard
                value={resolvedAddress}
                noStyling
                className="transition-default ml-2 flex h-auto w-auto items-center text-theme-secondary-700 hover:text-theme-primary-700 dark:text-theme-dark-300 dark:hover:text-theme-dark-50"
                tooltipContent={t("pages.wallet.address_copied")}
                checkmarksClass=""
            />

            {isContract && (
                <div className="hidden items-center md:flex">
                    <div className="mx-2 h-[17px] border-l border-theme-secondary-300 dark:border-theme-dark-700" />

                    <Badge className="inline flex items-center space-x-1.5 border-transparent bg-theme-secondary-200 text-theme-secondary-700 dark:border-theme-dark-700 dark:text-theme-dark-200">
                        <ContractIcon className="h-3 w-3" />

                        <span>{t("general.contract")}</span>
                    </Badge>
                </div>
            )}
        </div>
    );
}
