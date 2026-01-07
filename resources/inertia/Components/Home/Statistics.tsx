import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import classNames from "classnames";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";
import { IHomeStatistics } from "@/Pages/Home.contracts";
import Tooltip from "../General/Tooltip";
import { currency } from "@/utils/number-formatter";

function StatEntry({
    label,
    value,
    tooltip,
    disabled,
    className = "space-y-2",
}: {
    label: string;
    value: string | number;
    tooltip?: string;
    disabled?: boolean;
    className?: string;
}) {
    const { t } = useTranslation();

    const valueOutput = <>{disabled ? t("general.na") : value}</>;

    return (
        <div
            className={classNames([
                className,
                "border-b border-dashed border-theme-secondary-300 pb-3 group-last/statistics:last:border-b-0 group-last/statistics:last:pb-0",
                "dark:border-theme-dark-700 sm:group-last/statistics:border-b-0 sm:group-last/statistics:pb-0",
            ])}
        >
            <div className="text-sm font-semibold dark:text-theme-dark-200">{label}</div>

            <div
                className={classNames([
                    "text-sm font-semibold !leading-5 md:text-base",
                    !disabled && "text-theme-secondary-900 dark:text-theme-dark-50",
                    disabled && "text-theme-secondary-500 dark:text-theme-dark-500",
                ])}
            >
                {tooltip ? (
                    <div className="flex">
                        <Tooltip content={tooltip}>{valueOutput}</Tooltip>
                    </div>
                ) : (
                    valueOutput
                )}
            </div>
        </div>
    );
}

function StatRow({ children }: { children: React.ReactNode }) {
    return (
        <div className="gap group/statistics space-y-3 px-4 sm:grid sm:grid-cols-3 sm:gap-3 sm:space-y-0 md-lg:px-6">
            {children}
        </div>
    );
}

export default function Statistics({ statistics }: { statistics: IHomeStatistics }) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <div className="px-6 md:mx-auto md:max-w-7xl md:border-0 md:px-10">
            <div className="flex flex-col space-y-3 lg:flex-row lg:space-x-3 lg:space-y-0">
                <div className="flex min-w-0 flex-1 flex-col rounded-xl border border-theme-secondary-300 py-3 dark:border-theme-dark-700 md:py-4">
                    <div className="mb-3 flex items-center justify-between border-b border-theme-secondary-300 px-4 pb-3 dark:border-theme-dark-700 sm:px-6 md:mb-4 md:pb-4">
                        <h2 className="mb-0 text-xl font-semibold leading-[29px] md:text-2xl">
                            {t("pages.home.statistics.title")}
                        </h2>

                        <a href={route("statistics")} className="link group font-semibold">
                            <div className="inline-flex items-center space-x-2 group-hover:underline">
                                <span className="leading-5">{t("actions.view")}</span>

                                <ChevronRightSmallIcon className="h-3 w-3" />
                            </div>
                        </a>
                    </div>

                    <div className="space-y-3">
                        <StatRow>
                            <StatEntry
                                label={t("pages.home.statistics.total_supply")}
                                value={`${statistics.totalSupply} ${network.currency}`}
                            />

                            <StatEntry
                                label={t("pages.home.statistics.voting", { percentage: statistics.voting.percentage })}
                                value={`${statistics.voting.amount} ${network.currency}`}
                            />

                            <StatEntry label={t("pages.home.statistics.addresses")} value={statistics.addresses} />
                        </StatRow>

                        <StatRow>
                            {network.canBeExchanged ? (
                                <>
                                    <StatEntry
                                        label={t("pages.home.statistics.gas_low")}
                                        value={statistics.gas.low.value}
                                        tooltip={`${statistics.gas.low.amount} ${t("general.gwei")}`}
                                    />

                                    <StatEntry
                                        label={t("pages.home.statistics.gas_average")}
                                        value={statistics.gas.average.value}
                                        tooltip={`${statistics.gas.average.amount} ${t("general.gwei")}`}
                                    />

                                    <StatEntry
                                        label={t("pages.home.statistics.gas_high")}
                                        value={statistics.gas.high.value}
                                        tooltip={`${statistics.gas.high.amount} ${t("general.gwei")}`}
                                    />
                                </>
                            ) : (
                                <>
                                    <StatEntry
                                        label={t("pages.home.statistics.gas_low")}
                                        value={`${statistics.gas.low.amount} ${t("general.gwei")}`}
                                    />

                                    <StatEntry
                                        label={t("pages.home.statistics.gas_average")}
                                        value={`${statistics.gas.average.amount} ${t("general.gwei")}`}
                                    />

                                    <StatEntry
                                        label={t("pages.home.statistics.gas_high")}
                                        value={`${statistics.gas.high.amount} ${t("general.gwei")}`}
                                    />
                                </>
                            )}
                        </StatRow>
                    </div>
                </div>

                <div
                    className={classNames([
                        "flex-1 rounded-xl border border-theme-secondary-300 bg-theme-secondary-100 dark:border-theme-dark-700 dark:bg-theme-dark-950",
                        network.canBeExchanged && "px-4 py-3 sm:px-6 sm:pb-4 md:py-6",
                        !network.canBeExchanged && "md-lg:px-6 md-lg:py-6",
                    ])}
                >
                    <div
                        className={classNames([
                            "relative h-full w-full",
                            !network.canBeExchanged && "hidden md-lg:block",
                        ])}
                    >
                        {network.canBeExchanged && (
                            <div className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 whitespace-nowrap text-sm font-semibold text-theme-secondary-500 dark:text-theme-dark-400">
                                {t("pages.home.statistics.chart_not_supported")}
                            </div>
                        )}

                        <div className={classNames([!network.canBeExchanged && "pointer-events-none blur-md"])}>
                            {/* <livewire:home.chart /> */}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
