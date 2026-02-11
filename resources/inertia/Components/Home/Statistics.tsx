import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import classNames from "classnames";
import ChevronRightSmallIcon from "@ui/icons/arrows/chevron-right-small.svg?react";
import { HomeProps, IHomeStatistics } from "@/Pages/Home.contracts";
import Tooltip from "../General/Tooltip";
import Info from "../General/Info";
import ChartContent from "@/Components/Home/Chart/ChartContent";
import Number from "../General/Number";
import { Link } from "@inertiajs/react";
function StatEntry({
    label,
    value,
    tooltip,
    disabled,
    className = "space-y-2",
    withBorder = true,
}: {
    label: string;
    value: string | number | React.ReactNode;
    tooltip?: string;
    disabled?: boolean;
    className?: string;
    withBorder?: boolean;
}) {
    const { t } = useTranslation();

    const valueOutput = <>{disabled ? t("general.na") : value}</>;

    return (
        <div
            className={classNames([
                className,
                withBorder &&
                    "border-b border-dashed border-theme-secondary-300 pb-3 group-first/statistics:border-b group-first/statistics:pb-3 group-first/statistics:last:border-b-0",
                withBorder &&
                    "dark:border-theme-dark-700 sm:group-first/statistics:last:border-b sm:group-last/statistics:border-b-0 sm:group-last/statistics:pb-0",
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

function StatRow({ children, className = "" }: { children: React.ReactNode; className?: string }) {
    return (
        <div
            className={classNames(
                "gap group/statistics space-y-3 px-4 sm:grid sm:grid-cols-3 sm:gap-3 sm:space-y-0 md-lg:px-6",
                className,
            )}
        >
            {children}
        </div>
    );
}

function MobileGasTooltip({ statistics }: { statistics: IHomeStatistics }) {
    const { t } = useTranslation();

    return (
        <div className="w-[196px] space-y-2 font-semibold">
            {["low", "average", "high"].map((level) => (
                <div className="flex items-center justify-between">
                    <div className="flex space-x-1">
                        <span>{t(`pages.home.statistics.gas-levels.${level}`)}:</span>
                        <span className="text-theme-secondary-700 dark:text-theme-dark-200">
                            {t("pages.home.statistics.30_seconds")}
                        </span>
                    </div>

                    <span>
                        {(statistics.gas as any)[level].amount} {t("general.gwei")}
                    </span>
                </div>
            ))}
        </div>
    );
}

export default function Statistics({ statistics }: { statistics: IHomeStatistics }) {
    const { t } = useTranslation();
    const { network, chart } = useSharedData<HomeProps>();

    return (
        <div className="px-6 md:mx-auto md:max-w-7xl md:border-0 md:px-10">
            <div className="flex flex-col space-y-3 lg:flex-row lg:space-x-3 lg:space-y-0">
                <div className="flex min-w-0 flex-1 flex-col rounded-xl border border-theme-secondary-300 pt-3 dark:border-theme-dark-700 sm:pb-3 md:py-4">
                    <div className="mb-3 flex items-center justify-between border-b border-theme-secondary-300 px-4 pb-3 dark:border-theme-dark-700 sm:px-6 md:mb-4 md:pb-4">
                        <h2 className="mb-0 text-lg font-semibold md:text-2xl md:leading-[29px]">
                            <span className="hidden leading-5.25 sm:inline">{t("pages.home.statistics.title")}</span>
                            <span className="leading-5.25 sm:hidden">{t("pages.home.statistics.title_mobile")}</span>
                        </h2>

                        <Link
                            href={route("statistics")}
                            className="link rounded px-2 py-1.5 font-semibold hover:bg-theme-primary-200 hover:text-theme-primary-700 dark:hover:bg-theme-dark-700 dark:hover:text-theme-dark-50"
                        >
                            <div className="inline-flex items-center space-x-2">
                                <span className="leading-5">{t("actions.view")}</span>

                                <ChevronRightSmallIcon className="h-3 w-3" />
                            </div>
                        </Link>
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

                            <StatEntry
                                label={t("pages.home.statistics.block_height")}
                                value={<Number>{statistics.blockHeight}</Number>}
                            />
                        </StatRow>

                        <StatRow className="hidden sm:grid">
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

                    <div className="rounded-b-xl bg-theme-secondary-100 dark:bg-theme-dark-950 sm:hidden">
                        <StatEntry
                            label={t("pages.home.statistics.gas_tracker")}
                            className="space-y-2 px-4 py-3"
                            withBorder={false}
                            value={
                                <div className="flex items-center space-x-2">
                                    <span>
                                        {network.canBeExchanged
                                            ? statistics.gas.average.value
                                            : t("pages.home.statistics.gas_average_value", {
                                                  value: `${statistics.gas.average.amount} ${t("general.gwei")}`,
                                              })}
                                    </span>

                                    <Info
                                        type="info"
                                        className="flex"
                                        tooltip={<MobileGasTooltip statistics={statistics} />}
                                        testId="statistics:gas-tracker"
                                    />
                                </div>
                            }
                        />
                    </div>
                </div>

                <div
                    className={classNames([
                        "flex min-w-0 flex-1 flex-col rounded-xl border border-theme-secondary-300 dark:border-theme-dark-700",
                        network.canBeExchanged && "pt-3 sm:pb-3 md:py-4",
                        !network.canBeExchanged && "md-lg:px-6 md-lg:py-6",
                    ])}
                >
                    <div
                        className={classNames([
                            "relative h-full w-full",
                            !network.canBeExchanged && "hidden md-lg:block",
                        ])}
                    >
                        {!network.canBeExchanged && (
                            <div className="absolute left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 whitespace-nowrap text-sm font-semibold text-theme-secondary-500 dark:text-theme-dark-400">
                                {t("pages.home.statistics.chart_not_supported")}
                            </div>
                        )}

                        <div className={classNames([!network.canBeExchanged && "pointer-events-none blur-md"])}>
                            <ChartContent chart={chart} canBeExchanged={network.canBeExchanged} />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
