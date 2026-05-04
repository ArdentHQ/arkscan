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
    testId,
}: React.HTMLAttributes<HTMLDivElement> & {
    wallet?: Pick<IWallet, "address" | "username"> | null;
    address?: string | null;
    isContract?: boolean;
    testId?: string;
}) {
    const { t } = useTranslation();

    const resolvedAddress = address ?? wallet?.address ?? "";
    const hasUsername = wallet?.username !== null && wallet?.username !== undefined;
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

                <div
                    className={classNames({
                        "hidden sm:block md:hidden": isContract,
                        "block md:hidden": !isContract,
                    })}
                >
                    {hasUsername ? username : <TruncateMiddle>{resolvedAddress}</TruncateMiddle>}
                </div>

                {isContract && <div className="sm:hidden">{t("general.contract")}</div>}
            </Link>

            {isContract && (
                <div className="ml-3 h-5 w-5 sm:hidden">
                    <Info tooltip={resolvedAddress} type="info" />
                </div>
            )}

            <Clipboard
                value={resolvedAddress}
                noStyling
                className="transition-default text-theme-secondary-700 hover:text-theme-primary-700 dark:text-theme-dark-300 dark:hover:text-theme-dark-50 ml-2 flex h-auto w-auto items-center"
                tooltipContent={t("pages.wallet.address_copied")}
                checkmarksClass=""
                testId={testId ? `${testId}:address` : undefined}
            />

            {isContract && (
                <div className="hidden items-center sm:flex">
                    <div className="border-theme-secondary-300 dark:border-theme-dark-700 mx-2 h-[17px] border-l" />

                    <Badge className="bg-theme-secondary-200 text-theme-secondary-700 dark:border-theme-dark-700 dark:text-theme-dark-200 flex items-center space-x-1.5 border-transparent">
                        <ContractIcon className="h-3 w-3" />

                        <span>{t("general.contract")}</span>
                    </Badge>
                </div>
            )}
        </div>
    );
}
