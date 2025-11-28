import Badge from "@/Components/General/Badge";
import classNames from "classnames";
import TimeToForge from "./TimeToForge";
import { useValidatorStatus } from "@/Providers/ValidatorStatus/ValidatorStatusContext";
import { ForgingStatusGenerated, ForgingStatusMissed, ForgingStatusPending } from "@/Providers/ValidatorStatus/types";
import { useTranslation } from "react-i18next";

export default function Status({
    width = "min-w-[8.75rem]",
    withTime = false,
    withText = true,
    className = "",
}: {
    width?: string;
    withTime?: boolean;
    withText?: boolean;
    className?: string;
}) {
    const { t } = useTranslation();
    const { status, validator } = useValidatorStatus();

    const isPending = status === ForgingStatusPending;
    const hasForged = status === ForgingStatusGenerated;
    const justMissed = status === ForgingStatusMissed;

    const wallet = validator.wallet;

    return (
        <Badge
            colors={classNames({
                "inline-flex space-x-2 items-center whitespace-nowrap": true,
                "!px-2": withText,
                "border-transparent bg-theme-secondary-200 dark:border-theme-dark-700 encapsulated-badge":
                    withText && isPending,
                "border-transparent bg-theme-success-100 dark:border-theme-success-700": withText && hasForged,
                "border-transparent bg-theme-danger-100 dark:border-theme-danger-400": withText && justMissed,
                "border-transparent bg-theme-primary-100 dark:border-theme-dark-blue-600 dim:border-theme-dark-blue-800":
                    withText && !isPending && !hasForged && !justMissed,
                "border-none": !withText,
                [width]: withText,
                [className]: true,
            })}
        >
            <div className="flex items-center">
                <div
                    className={classNames({
                        "h-3 w-3 rounded-full": true,
                        "bg-theme-secondary-500 dark:bg-theme-dark-500": isPending,
                        "bg-theme-success-700 dark:bg-theme-success-500": hasForged,
                        "bg-theme-danger-600 dark:bg-theme-danger-300": justMissed,
                        "bg-theme-primary-600 dim:bg-theme-dark-blue-600 dark:bg-theme-dark-blue-400":
                            !isPending && !hasForged && !justMissed,
                    })}
                ></div>
            </div>

            {withText && (
                <div
                    className={classNames({
                        "leading-3.75": true,
                        "text-theme-secondary-700 dark:text-theme-dark-200": isPending,
                        "text-theme-success-700 dark:text-theme-success-500": hasForged,
                        "text-theme-danger-600 dark:text-theme-danger-300": justMissed,
                        "text-theme-primary-600 dim:text-theme-dark-blue-600 dark:text-theme-dark-blue-400":
                            !isPending && !hasForged && !justMissed,
                    })}
                >
                    {isPending && (
                        <>
                            {withTime ? (
                                <TimeToForge className="text-xs font-semibold leading-3.75" />
                            ) : (
                                <span>{t("tables.validator-monitor.forging-status.pending")}</span>
                            )}
                        </>
                    )}

                    {hasForged && <span>{t("tables.validator-monitor.forging-status.block_generated")}</span>}
                    {justMissed && (
                        <span>
                            {t("tables.validator-monitor.forging-status.blocks_missed", { count: wallet.missedCount })}
                        </span>
                    )}

                    {!isPending && !hasForged && !justMissed && (
                        <span>{t("tables.validator-monitor.forging-status.generating")}</span>
                    )}
                </div>
            )}
        </Badge>
    );
}
