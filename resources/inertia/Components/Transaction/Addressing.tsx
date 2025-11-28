import { ITransaction, IWallet } from "@/types/generated";
import classNames from "classnames";
import TruncateMiddle from "../General/TruncateMiddle";
import { useTranslation } from "react-i18next";

export default function Addressing({
    transaction,
    withoutLink = false,
    alwaysShowAddress = false,
    withoutTruncate = false,
    isGeneric = false,
}: {
    transaction: ITransaction;
    withoutLink?: boolean;
    alwaysShowAddress?: boolean;
    withoutTruncate?: boolean;
    isGeneric?: boolean;
}) {
    const { t } = useTranslation();

    let direction = t("tables.transactions.from");
    if (transaction.isSentToSelf) {
        direction = t("tables.transactions.return");
    } else if (transaction.isSent) {
        direction = t("tables.transactions.to");
    }

    let interactedWallet: IWallet | null = null;
    if (transaction.isTransfer || transaction.isTokenTransfer || alwaysShowAddress) {
        interactedWallet = transaction.sender;
        if (transaction.isSent) {
            interactedWallet = transaction.recipient;
        }
    }

    return (
        <div className="flex items-center space-x-2 text-sm font-semibold">
            <div
                className={classNames({
                    "h-[21px] w-[47px] rounded border text-center text-xs leading-5": true,
                    "encapsulated-badge border-theme-secondary-200 bg-theme-secondary-200 text-theme-secondary-700 dark:border-theme-dark-700 dark:bg-transparent dark:text-theme-dark-200":
                        transaction.isSentToSelf,
                    "border-theme-success-100 bg-theme-success-100 text-theme-success-700 dark:border-theme-success-700 dark:bg-transparent dark:text-theme-success-500":
                        !transaction.isSent && !isGeneric && !transaction.isSentToSelf,
                    "border-theme-orange-light bg-theme-orange-light text-theme-orange-dark dim:border-theme-failed-state-bg dim:text-theme-failed-state-text dark:border-theme-failed-state-bg dark:bg-transparent dark:text-theme-failed-state-text":
                        transaction.isSent && !isGeneric && !transaction.isSentToSelf,
                    "border-theme-secondary-200 bg-theme-secondary-200 dark:border-theme-dark-700 dark:bg-transparent dark:text-theme-dark-200":
                        isGeneric && !transaction.isSentToSelf,
                })}
            >
                {direction}
            </div>

            <div>
                {!!interactedWallet ? (
                    <>
                        {withoutLink ? (
                            <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                {interactedWallet!.hasUsername && interactedWallet!.username}
                                {!interactedWallet!.hasUsername && withoutTruncate && interactedWallet!.address}
                                {!interactedWallet!.hasUsername && !withoutTruncate && (
                                    <TruncateMiddle>{interactedWallet!.address}</TruncateMiddle>
                                )}
                            </span>
                        ) : (
                            <a className="link" href={`/addresses/${interactedWallet!.address}`}>
                                {interactedWallet!.hasUsername && interactedWallet!.username}
                                {!interactedWallet!.hasUsername && withoutTruncate && interactedWallet!.address}
                                {!interactedWallet!.hasUsername && !withoutTruncate && (
                                    <TruncateMiddle>{interactedWallet!.address}</TruncateMiddle>
                                )}
                            </a>
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
