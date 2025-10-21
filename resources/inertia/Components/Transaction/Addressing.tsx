import { ITransaction, IWallet } from "@/types/generated";
import classNames from "@/utils/class-names";
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

    let direction = t('tables.transactions.from');
    if (transaction.isSentToSelf) {
        direction = t('tables.transactions.return');
    } else if (transaction.isSent) {
        direction = t('tables.transactions.to');
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
            <div className={classNames({
                'w-[47px] h-[21px] rounded border text-center leading-5 text-xs': true,
                'text-theme-secondary-700 bg-theme-secondary-200 border-theme-secondary-200 dark:bg-transparent dark:border-theme-dark-700 dark:text-theme-dark-200 encapsulated-badge': transaction.isSentToSelf,
                'text-theme-success-700 border-theme-success-100 dark:border-theme-success-700 dark:text-theme-success-500 bg-theme-success-100 dark:bg-transparent': ! transaction.isSent && ! isGeneric && ! transaction.isSentToSelf,
                'text-theme-orange-dark border-theme-orange-light dark:border-theme-failed-state-bg dim:border-theme-failed-state-bg dark:text-theme-failed-state-text dim:text-theme-failed-state-text bg-theme-orange-light dark:bg-transparent': transaction.isSent && ! isGeneric && ! transaction.isSentToSelf,
                'bg-theme-secondary-200 border-theme-secondary-200 dark:bg-transparent dark:border-theme-dark-700 dark:text-theme-dark-200': isGeneric && ! transaction.isSentToSelf,
            })}>
                {direction}
            </div>

            <div>
                {!! interactedWallet ? (
                    <>
                        {withoutLink ? (
                            <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                {interactedWallet!.hasUsername && interactedWallet!.username}
                                {! interactedWallet!.hasUsername && withoutTruncate && interactedWallet!.address}
                                {! interactedWallet!.hasUsername && ! withoutTruncate && (
                                    <TruncateMiddle>{interactedWallet!.address}</TruncateMiddle>
                                )}
                            </span>
                        ) : (
                            <a
                                className="link"
                                href={`/addresses/${interactedWallet!.address}`}
                            >
                                {interactedWallet!.hasUsername && interactedWallet!.username}
                                {! interactedWallet!.hasUsername && withoutTruncate && interactedWallet!.address}
                                {! interactedWallet!.hasUsername && ! withoutTruncate && (
                                    <TruncateMiddle>{interactedWallet!.address}</TruncateMiddle>
                                )}
                            </a>
                        )}
                    </>
                ) : (
                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                        {t('tables.transactions.contract')}
                    </span>
                )}
            </div>
        </div>
    );
}
