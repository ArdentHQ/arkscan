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
import { currency, currencyShortNotation, isFiat } from "@/utils/number-formatter";
import { formatGwei } from "@/utils/number-formatter";
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
                    "border-theme-secondary-300 border-b border-dashed pb-3 group-first/statistics:border-b group-first/statistics:pb-3 group-first/statistics:last:border-b-0",
                withBorder &&
                    "dark:border-theme-dark-700 sm:group-last/statistics:border-b-0 sm:group-last/statistics:pb-0 sm:group-first/statistics:last:border-b",
            ])}
        >
            <div className="dark:text-theme-dark-200 text-sm font-semibold">{label}</div>

            <div
                className={classNames([
                    "text-sm leading-5! font-semibold md:text-base",
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
                "gap group/statistics md-lg:px-6 space-y-3 px-4 sm:grid sm:grid-cols-3 sm:gap-3 sm:space-y-0",
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
                <div key={level} className="flex items-center justify-between">
                    <div className="flex space-x-1">
                        <span>{t(`pages.home.statistics.gas-levels.${level}`)}:</span>
                        <span className="text-theme-secondary-700 dark:text-theme-dark-200">
                            {t("pages.home.statistics.30_seconds")}
                        </span>
                    </div>

                    <span>
                        {formatGwei((statistics.gas as any)[level].amount)} {t("general.gwei")}
                    </span>
                </div>
            ))}
        </div>
    );
}

function FormatGasValue({ value }: { value: number }) {
    const { settings } = useSharedData();
    const userCurrency = settings!.currency;
    const isFiatCurrency = isFiat(userCurrency);

    if (!isFiatCurrency) {
        return <>{currency(value, userCurrency, true)}</>;
    }

    if (value > 0 && value < 0.01) {
        return <>{`< ${currency(0.01, userCurrency)}`}</>;
    }

    return <>{currency(value, userCurrency)}</>;
}

export default function Statistics({ statistics }: { statistics: IHomeStatistics }) {
    const { t } = useTranslation();
    const { network, chart } = useSharedData<HomeProps>();

    return (
        <div className="px-6 md:mx-auto md:max-w-7xl md:border-0 md:px-10">
            <div className="flex flex-col space-y-3 lg:flex-row lg:space-y-0 lg:space-x-3">
                <div className="border-theme-secondary-300 dark:border-theme-dark-700 flex min-w-0 flex-1 flex-col rounded-xl border pt-3 sm:pb-3 md:py-4">
                    <div className="border-theme-secondary-300 dark:border-theme-dark-700 mb-3 flex items-center justify-between border-b px-4 pb-3 sm:px-6 md:mb-4 md:pb-4">
                        <h2 className="mb-0 text-lg font-semibold md:text-2xl md:leading-[29px]">
                            <span className="hidden leading-5.25 sm:inline">{t("pages.home.statistics.title")}</span>
                            <span className="leading-5.25 sm:hidden">{t("pages.home.statistics.title_mobile")}</span>
                        </h2>

                        <Link
                            href={route("statistics")}
                            className="link hover:bg-theme-primary-200 hover:text-theme-primary-700 hover:dark:bg-theme-dark-700 hover:dark:text-theme-dark-50! rounded px-2 py-1.5 font-semibold"
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
                                value={`${currencyShortNotation(statistics.totalSupply)} ${network.currency}`}
                            />

                            <StatEntry
                                label={t("pages.home.statistics.voting", {
                                    percentage: `${statistics.voting.percentage.toFixed(2)}%`,
                                })}
                                value={`${currencyShortNotation(statistics.voting.amount)} ${network.currency}`}
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
                                        value={<FormatGasValue value={statistics.gas.low.value} />}
                                        tooltip={`${formatGwei(statistics.gas.low.amount)} ${t("general.gwei")}`}
                                    />

                                    <StatEntry
                                        label={t("pages.home.statistics.gas_average")}
                                        value={<FormatGasValue value={statistics.gas.average.value} />}
                                        tooltip={`${formatGwei(statistics.gas.average.amount)} ${t("general.gwei")}`}
                                    />

                                    <StatEntry
                                        label={t("pages.home.statistics.gas_high")}
                                        value={<FormatGasValue value={statistics.gas.high.value} />}
                                        tooltip={`${formatGwei(statistics.gas.high.amount)} ${t("general.gwei")}`}
                                    />
                                </>
                            ) : (
                                <>
                                    <StatEntry
                                        label={t("pages.home.statistics.gas_low")}
                                        value={`${formatGwei(statistics.gas.low.amount)} ${t("general.gwei")}`}
                                    />

                                    <StatEntry
                                        label={t("pages.home.statistics.gas_average")}
                                        value={`${formatGwei(statistics.gas.average.amount)} ${t("general.gwei")}`}
                                    />

                                    <StatEntry
                                        label={t("pages.home.statistics.gas_high")}
                                        value={`${formatGwei(statistics.gas.high.amount)} ${t("general.gwei")}`}
                                    />
                                </>
                            )}
                        </StatRow>
                    </div>

                    <div className="bg-theme-secondary-100 dark:bg-theme-dark-950 rounded-b-xl sm:hidden">
                        <StatEntry
                            label={t("pages.home.statistics.gas_tracker")}
                            className="space-y-2 px-4 py-3"
                            withBorder={false}
                            value={
                                <div className="flex items-center space-x-2">
                                    <span>
                                        {t("pages.home.statistics.gas_average_value", {
                                            value: network.canBeExchanged
                                                ? statistics.gas.average.value
                                                : `${formatGwei(statistics.gas.average.amount)} ${t("general.gwei")}`,
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
                        "border-theme-secondary-300 dark:border-theme-dark-700 min-w-0 flex-1 flex-col rounded-xl border",
                        network.canBeExchanged && "flex pt-3 sm:pb-3 md:py-4",
                        !network.canBeExchanged && "md-lg:px-6 md-lg:py-6 md-lg:flex hidden",
                    ])}
                >
                    <div className={classNames(["relative h-full w-full"])}>
                        {!network.canBeExchanged && (
                            <div className="text-theme-secondary-500 dark:text-theme-dark-400 absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 text-sm font-semibold whitespace-nowrap">
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
