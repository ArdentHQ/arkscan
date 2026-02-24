import { IWallet } from "@/types/generated";
import classNames from "classnames";
import TruncateMiddle from "../General/TruncateMiddle";
import { useTranslation } from "react-i18next";
import { Link } from "@inertiajs/react";
import { useMemo } from "react";
import { Transaction } from "@/models/Transaction";

export default function Addressing({
    transaction,
    withoutLink = false,
    alwaysShowAddress = false,
    withoutTruncate = false,
    isGeneric = false,
    className,
    wallet,
    isReceived = false,
    ...props
}: React.HTMLAttributes<HTMLDivElement> & {
    transaction: Transaction;
    withoutLink?: boolean;
    alwaysShowAddress?: boolean;
    withoutTruncate?: boolean;
    isGeneric?: boolean;
    wallet?: IWallet;
    isReceived?: boolean;
}) {
    const { t } = useTranslation();

    let interactedWallet: IWallet | null = null;

    const isSent = useMemo(() => {
        return wallet && transaction.isSent(wallet?.address) && !transaction.isSentToSelf(wallet?.address);
    }, [transaction.isSent, transaction.isSentToSelf, wallet]);

    const isSentToSelf = useMemo(() => {
        return wallet && transaction.isSentToSelf(wallet?.address);
    }, [transaction.isSentToSelf, wallet]);

    if (transaction.method.isTransfer || transaction.method.isTokenTransfer || alwaysShowAddress) {
        interactedWallet = transaction.sender;

        if (isSent) {
            interactedWallet = transaction.recipient;
        }
    }

    const direction = useMemo(() => {
        if (isSentToSelf) {
            return t("tables.transactions.return");
        } else if (isSent) {
            return t("tables.transactions.to");
        }

        return t("tables.transactions.from");
    }, [isSentToSelf, isSent]);

    return (
        <div className={classNames("flex items-center space-x-2 text-sm font-semibold", className)} {...props}>
            <div
                className={classNames({
                    "h-[21px] w-[47px] rounded border text-center text-xs leading-5": true,
                    "encapsulated-badge border-theme-secondary-200 bg-theme-secondary-200 text-theme-secondary-700 dark:border-theme-dark-700 dark:bg-transparent dark:text-theme-dark-200":
                        isSentToSelf,
                    "border-theme-success-100 bg-theme-success-100 text-theme-success-700 dark:border-theme-success-700 dark:bg-transparent dark:text-theme-success-500":
                        (!isSent && !isGeneric && !isSentToSelf) || isReceived,
                    "border-theme-orange-light bg-theme-orange-light text-theme-orange-dark dim:border-theme-failed-state-bg dim:text-theme-failed-state-text dark:border-theme-failed-state-bg dark:bg-transparent dark:text-theme-failed-state-text":
                        isSent && !isGeneric,
                    "border-theme-secondary-200 bg-theme-secondary-200 dark:border-theme-dark-700 dark:bg-transparent dark:text-theme-dark-200":
                        isGeneric && !isSentToSelf,
                })}
            >
                {direction}
            </div>

            <div>
                {!!interactedWallet ? (
                    <>
                        {withoutLink ? (
                            <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                {interactedWallet!.username}
                                {!interactedWallet!.username && withoutTruncate && interactedWallet!.address}
                                {!interactedWallet!.username && !withoutTruncate && (
                                    <TruncateMiddle>{interactedWallet!.address}</TruncateMiddle>
                                )}
                            </span>
                        ) : (
                            <Link className="link" href={route("wallet", interactedWallet!.address)}>
                                {interactedWallet!.username}
                                {!interactedWallet!.username && withoutTruncate && interactedWallet!.address}
                                {!interactedWallet!.username && !withoutTruncate && (
                                    <TruncateMiddle>{interactedWallet!.address}</TruncateMiddle>
                                )}
                            </Link>
                        )}
                    </>
                ) : (
                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                        {t("tables.transactions.contract")}
                    </span>
                )}
            </div>
        </div>
    );
}
