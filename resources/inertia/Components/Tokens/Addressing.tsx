import { IMemoryWallet, ITokenAction, IWallet } from "@/types/generated";
import classNames from "classnames";
import TruncateMiddle from "../General/TruncateMiddle";
import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";
import { useMemo } from "react";

export default function Addressing({
    tokenAction,
    className,
    wallet,
    ...props
}: React.HTMLAttributes<HTMLDivElement> & {
    tokenAction: ITokenAction;
    wallet: IWallet;
}) {
    const { t } = useTranslation();

    let interactedWallet: IMemoryWallet | null = tokenAction.from;

    const isSent = useMemo(() => {
        return wallet && tokenAction.from.address === wallet.address;
    }, [tokenAction.from, wallet]);

    const isReceived = useMemo(() => {
        return (
            wallet && tokenAction.to.address !== tokenAction.from.address && tokenAction.to.address === wallet.address
        );
    }, [tokenAction.to, wallet]);

    const isSentToSelf = useMemo(() => {
        return (
            wallet && tokenAction.to.address === tokenAction.from.address && tokenAction.to.address === wallet.address
        );
    }, [isSent, isReceived]);

    const directionIsSent = useMemo(() => {
        return isSent && !isSentToSelf;
    }, [isSent, isSentToSelf]);

    if (isSent) {
        interactedWallet = tokenAction.to;
    }

    const direction = useMemo(() => {
        if (isSentToSelf) {
            return t("tables.transactions.return");
        } else if (directionIsSent) {
            return t("tables.transactions.to");
        }

        return t("tables.transactions.from");
    }, [isSentToSelf, directionIsSent]);

    return (
        <div className={classNames("flex items-center space-x-2 text-sm font-semibold", className)} {...props}>
            <div
                className={classNames({
                    "h-[21px] w-[47px] rounded border text-center text-xs leading-5": true,
                    "encapsulated-badge border-theme-secondary-200 bg-theme-secondary-200 text-theme-secondary-700 dark:border-theme-dark-700 dark:text-theme-dark-200 dark:bg-transparent":
                        isSentToSelf,
                    "border-theme-success-100 bg-theme-success-100 text-theme-success-700 dark:border-theme-success-700 dark:text-theme-success-500 dark:bg-transparent":
                        (!isSent && !isSentToSelf) || isReceived,
                    "border-theme-orange-light bg-theme-orange-light text-theme-orange-dark dim:border-theme-failed-state-bg dim:text-theme-failed-state-text dark:border-theme-failed-state-bg dark:text-theme-failed-state-text dark:bg-transparent":
                        isSent && !isSentToSelf,
                    "border-theme-secondary-200 bg-theme-secondary-200 dark:border-theme-dark-700 dark:text-theme-dark-200 dark:bg-transparent":
                        !isSent && !isSentToSelf,
                })}
            >
                {direction}
            </div>

            <div>
                {isSentToSelf ? (
                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                        {!!interactedWallet.username && interactedWallet.username}
                        {!interactedWallet.username && <TruncateMiddle>{interactedWallet.address}</TruncateMiddle>}
                    </span>
                ) : (
                    <Link className="link" href={route("wallet", interactedWallet.address)}>
                        {!!interactedWallet.username && interactedWallet.username}
                        {!interactedWallet.username && <TruncateMiddle>{interactedWallet.address}</TruncateMiddle>}
                    </Link>
                )}
            </div>
        </div>
    );
}
