import { useTranslation } from "react-i18next";
import { IBlock } from "@/types/generated";
import { PageSection } from "@/Components/PageSection";
import DoubleCheckMarkIcon from "@ui/icons/double-check-mark.svg?react";
import Number from "@/Components/General/Number";

export default function Confirmations({ block }: { block: IBlock }) {
    const { t } = useTranslation();

    const confirmationsKey =
        block.confirmations === 1 ? "general.confirmation_singular" : "general.confirmation_plural";

    return (
        <PageSection
            title={t("pages.transaction.status.header")}
            borderClass="border-theme-success-200 dark:border-theme-success-500"
            wrapperContainerClass="mx-2 rounded-lg border bg-theme-success-100 py-2 dark:bg-theme-success-900 sm:mx-0"
        >
            <div className="divide-theme-success-200 dark:divide-theme-success-800 flex items-center space-x-2 divide-x sm:space-x-3">
                <div className="text-theme-success-700 dark:text-theme-success-500 flex items-center space-x-2 pr-2 sm:pr-3">
                    <DoubleCheckMarkIcon className="h-4 w-4 sm:h-5 sm:w-5" />
                    <div>{t("general.success")}</div>
                </div>

                <div className="dark:text-theme-dark-50">
                    {block.confirmations > 1000 ? (
                        <>
                            <Number>1000</Number>+ {t("general.confirmations_only")}
                        </>
                    ) : (
                        t(confirmationsKey, { count: block.confirmations })
                    )}
                </div>
            </div>
        </PageSection>
    );
}
