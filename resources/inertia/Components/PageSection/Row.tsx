import classNames from "classnames";
import React from "react";
import { useTranslation } from "react-i18next";
import Tooltip from "@/Components/General/Tooltip";

interface SectionDetailRowProps {
    title: string;
    value?: React.ReactNode;
    children?: React.ReactNode;
    headerWidthClass?: string;
    valueClassName?: string;
    tooltip?: string;
    allowEmpty?: boolean;
    className?: string;
}

export default function SectionDetailRow({
    title,
    value,
    children,
    headerWidthClass = "w-[106px]",
    valueClassName,
    tooltip,
    allowEmpty = false,
    className,
}: SectionDetailRowProps) {
    const { t } = useTranslation();

    const hasValue =
        value !== null && value !== undefined && value !== "" && value !== 0 && value !== "0" && value !== false;
    const hasChildren = (children !== null && children !== undefined) || React.Children.count(children) > 0;
    const shouldShowValue = hasValue || (allowEmpty && (value === 0 || value === "0"));

    const content = shouldShowValue ? value : children;

    return (
        <div className={classNames("flex items-center space-x-4", className)}>
            <div className={classNames("whitespace-nowrap", headerWidthClass)}>{title}</div>

            <div
                className={classNames(
                    "text-theme-secondary-900 dark:text-theme-dark-50 flex flex-1 justify-end space-y-3 text-right sm:justify-start sm:text-left",
                    valueClassName,
                )}
            >
                {!hasValue && !hasChildren && !allowEmpty && <span>{t("general.na")}</span>}

                {(hasValue || hasChildren || allowEmpty) && (
                    <>
                        {!!tooltip && <Tooltip content={tooltip}>{content}</Tooltip>}
                        {!tooltip && <div className="inline-block max-w-full">{content}</div>}
                    </>
                )}
            </div>
        </div>
    );
}
