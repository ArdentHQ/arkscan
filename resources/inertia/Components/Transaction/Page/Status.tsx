import classNames from "classnames";
import { useTranslation } from "react-i18next";
import { Transaction } from "@/models/Transaction";
import { PageSection } from "@/Components/PageSection";
import Number from "@/Components/General/Number";
import DoubleCheckMarkIcon from "@ui/icons/double-check-mark.svg?react";
import CircleCrossIcon from "@ui/icons/circle/cross.svg?react";
import { TransactionDetails } from "@/Pages/Transaction.contracts";

export default function TransactionStatus({
    transaction,
    details,
}: {
    transaction: Transaction;
    details: TransactionDetails;
}) {
    const { t } = useTranslation();

    const confirmationsKey =
        details.confirmations === 1 ? "general.confirmation_singular" : "general.confirmation_plural";

    const wrapperContainerClass = classNames("mx-2 rounded-lg border py-2 sm:mx-0", {
        "bg-theme-success-100 dark:bg-theme-success-900": !transaction.hasFailed,
        "bg-theme-danger-50 dark:bg-transparent": transaction.hasFailed,
    });

    const borderClass = transaction.hasFailed
        ? "border-theme-danger-200 dark:border-theme-danger-400"
        : "border-theme-success-200 dark:border-theme-success-500";

    const errorMessage = details.transactionError
        ? t("pages.transaction.status.failed_message", { error: details.transactionError })
        : t("pages.transaction.status.failed_no_message");

    return (
        <PageSection
            title={t("pages.transaction.status.header")}
            borderClass={borderClass}
            wrapperContainerClass={wrapperContainerClass}
        >
            <div
                className={classNames("flex items-center space-x-2 divide-x sm:space-x-3", {
                    "divide-theme-success-200 dark:divide-theme-success-800": !transaction.hasFailed,
                    "divide-theme-danger-200 dark:divide-theme-dark-700": transaction.hasFailed,
                })}
            >
                <div
                    className={classNames("flex items-center space-x-2 pr-2 sm:pr-3", {
                        "text-theme-success-700 dark:text-theme-success-500": !transaction.hasFailed,
                        "text-theme-danger-700 dark:text-theme-danger-400": transaction.hasFailed,
                    })}
                >
                    {transaction.hasFailed ? (
                        <CircleCrossIcon className="h-4 w-4 sm:h-5 sm:w-5" />
                    ) : (
                        <DoubleCheckMarkIcon className="h-4 w-4 sm:h-5 sm:w-5" />
                    )}

                    <div>{transaction.hasFailed ? t("general.failed") : t("general.success")}</div>
                </div>

                <div className="dark:text-theme-dark-50">
                    {details.confirmations > 1000 ? (
                        <>
                            <Number>1000</Number>+ {t("general.confirmations_only")}
                        </>
                    ) : (
                        t(confirmationsKey, { count: details.confirmations })
                    )}
                </div>

                {transaction.hasFailed && <div className="hidden pl-2 sm:pl-3 lg:block">{errorMessage}</div>}
            </div>

            {transaction.hasFailed && (
                <div className="border-theme-danger-200 dark:border-theme-dark-700 mt-2 border-t px-3 pt-2 whitespace-normal sm:mt-3 sm:pt-3 sm:pl-6 lg:hidden">
                    {errorMessage}
                </div>
            )}
        </PageSection>
    );
}
