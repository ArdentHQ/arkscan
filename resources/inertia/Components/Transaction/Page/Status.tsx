import classNames from "classnames";
import { useTranslation } from "react-i18next";
import { ITransaction } from "@/types/generated";
import { PageSection } from "@/Components/PageSection";
import Number from "@/Components/General/Number";
import DoubleCheckMarkIcon from "@ui/icons/double-check-mark.svg?react";
import CircleCrossIcon from "@ui/icons/circle/cross.svg?react";
import { TransactionDetails } from "@/Pages/Transaction.contracts";

export default function TransactionStatus({
    transaction,
    details,
}: {
    transaction: ITransaction;
    details: TransactionDetails;
}) {
    const { t } = useTranslation();

    const hasFailedStatus = transaction.hasFailedStatus;
    const confirmationsKey =
        details.confirmations === 1 ? "general.confirmation_singular" : "general.confirmation_plural";

    const wrapperContainerClass = classNames("mx-2 rounded-lg border py-2 sm:mx-0", {
        "bg-theme-success-100 dark:bg-theme-success-900": !hasFailedStatus,
        "bg-theme-danger-50 dark:bg-transparent": hasFailedStatus,
    });

    const borderClass = hasFailedStatus
        ? "border-theme-danger-200 dark:border-theme-danger-400"
        : "border-theme-success-200 dark:border-theme-success-500";

    const errorMessage = details.transactionError
        ? t("pages.transaction.status.failed_message", { error: details.transactionError })
        : t("pages.transaction.status.failed_no_message");

    return (
        <PageSection title={t("pages.transaction.status.header")} borderClass={borderClass} wrapperContainerClass={wrapperContainerClass}>
            <div
                className={classNames("flex items-center space-x-2 divide-x sm:space-x-3", {
                    "divide-theme-success-200 dark:divide-theme-success-800": !hasFailedStatus,
                    "divide-theme-danger-200 dark:divide-theme-dark-700": hasFailedStatus,
                })}
            >
                <div
                    className={classNames("flex items-center space-x-2", {
                        "text-theme-success-700 dark:text-theme-success-500": !hasFailedStatus,
                        "text-theme-danger-700 dark:text-theme-danger-400": hasFailedStatus,
                    })}
                >
                    {hasFailedStatus ? (
                        <CircleCrossIcon className="h-4 w-4 sm:h-5 sm:w-5" />
                    ) : (
                        <DoubleCheckMarkIcon className="h-4 w-4 sm:h-5 sm:w-5" />
                    )}

                    <div>{hasFailedStatus ? t("general.failed") : t("general.success")}</div>
                </div>

                <div className="pl-2 dark:text-theme-dark-50 sm:pl-3">
                    {details.confirmations > 1000 ? (
                        <>
                            <Number>1000</Number>+ {t("general.confirmations_only")}
                        </>
                    ) : (
                        t(confirmationsKey, { count: details.confirmations })
                    )}
                </div>

                {hasFailedStatus && (
                    <div className="hidden pl-2 sm:pl-3 lg:block">
                        {errorMessage}
                    </div>
                )}
            </div>

            {hasFailedStatus && (
                <div className="mt-2 whitespace-normal border-t border-theme-danger-200 px-3 pt-2 sm:mt-3 sm:pl-6 sm:pt-3 lg:hidden dark:border-theme-dark-700">
                    {errorMessage}
                </div>
            )}
        </PageSection>
    );
}
