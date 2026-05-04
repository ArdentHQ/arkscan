import classNames from "classnames";
import { useTranslation } from "react-i18next";
import Tooltip from "@/Components/General/Tooltip";

export default function InsightsRow({
    title,
    headerWidthClass = "w-[87px]",
    value,
    valueClass,
    tooltip,
    allowEmpty = false,
    children,
}: React.PropsWithChildren<{
    title: string;
    headerWidthClass?: string;
    value?: string | number | null;
    valueClass?: string;
    tooltip?: string | null;
    allowEmpty?: boolean;
}>) {
    const { t } = useTranslation();
    const hasValue = value !== undefined && value !== null && (allowEmpty || value !== "");
    const hasChildren = Boolean(children);

    if (!hasValue && !hasChildren) {
        return (
            <div className="flex flex-col space-y-2 pt-3 first:pt-0 md:flex-row md:items-center md:space-y-0 md:space-x-4 md:pt-0">
                <div className={classNames("whitespace-nowrap", headerWidthClass)}>{title}</div>
                <div
                    className={classNames(
                        "text-theme-secondary-900 dark:text-theme-dark-50 flex-1 space-y-3 md:text-right",
                        valueClass,
                    )}
                >
                    {t("general.na")}
                </div>
            </div>
        );
    }

    const content = hasValue ? value : children;

    return (
        <div className="flex flex-col space-y-2 pt-3 first:pt-0 md:flex-row md:items-center md:space-y-0 md:space-x-4 md:pt-0">
            <div className={classNames("whitespace-nowrap", headerWidthClass)}>{title}</div>
            <div
                className={classNames(
                    "text-theme-secondary-900 dark:text-theme-dark-50 flex-1 space-y-3 md:text-right",
                    valueClass,
                )}
            >
                {tooltip ? (
                    <Tooltip content={tooltip}>
                        <span>{content}</span>
                    </Tooltip>
                ) : (
                    content
                )}
            </div>
        </div>
    );
}
