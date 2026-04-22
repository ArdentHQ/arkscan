import { useTranslation } from "react-i18next";
import classNames from "classnames";
import InsightsContainer from "./Container";
import { StatisticsMarketDataInsights } from "@/Pages/Statistics.contracts";
import useSettings from "@/Providers/Settings/useSettings";
import { currency as formatCurrency, currencyWithDecimals, isFiat } from "@/utils/number-formatter";
import { formatDate } from "@/utils/formatter";

function useMarketFormatters() {
    const { currency: userCurrency } = useSettings();
    const isFiatCurrency = isFiat(userCurrency);

    const formatPrice = (value: number | null) => {
        if (value === null) return null;
        return currencyWithDecimals({
            value,
            currency: userCurrency,
            decimals: isFiatCurrency ? 2 : 8,
        });
    };

    const formatVolume = (value: number | null) => {
        if (value === null) return null;
        return currencyWithDecimals({ value, currency: userCurrency, decimals: 0 });
    };

    return { formatPrice, formatVolume };
}

function RangeValue({ low, high }: { low: number | null; high: number | null }) {
    const { formatPrice } = useMarketFormatters();
    const formattedLow = formatPrice(low);
    const formattedHigh = formatPrice(high);

    if (!formattedLow || !formattedHigh) {
        return null;
    }

    return (
        <>
            {formattedLow} - {formattedHigh}
        </>
    );
}

export default function MarketDataInsights({
    data,
    activeTab,
}: {
    data: StatisticsMarketDataInsights;
    activeTab: string;
}) {
    const { t } = useTranslation();
    const { currency } = useSettings();
    const isFiatCurrency = isFiat(currency);
    const { formatPrice, formatVolume } = useMarketFormatters();

    const fmtPriceVal = (v: number | null) => (v !== null ? formatPrice(v) : null);
    const fmtVolVal = (v: number | null) => (v !== null ? formatVolume(v) : null);
    const fmtCapVal = (v: number | null) => (v !== null ? formatVolume(v) : null);
    const fmtDate = (ts: number | null) => (ts !== null ? formatDate(ts) : null);

    return (
        <div className={activeTab !== "market_data" ? "hidden md:block" : undefined}>
            <div className="text-theme-secondary-900 dark:text-theme-dark-50 hidden px-6 font-semibold md:mx-auto md:block md:max-w-7xl md:px-10">
                {t("pages.statistics.insights.market_data.title")}
            </div>

            <div>
                <InsightsContainer title={t("pages.statistics.insights.market_data.price")} fullWidth>
                    {(["daily", "atl", "ath"] as const).map((item) => (
                        <div key={item} className="flex first:-mt-3 md:hidden">
                            <div
                                className={classNames(
                                    "flex w-full flex-col justify-between space-y-3 pt-3",
                                    isFiatCurrency ? "sm:flex-row sm:space-y-0" : "md:flex-row md:space-y-0",
                                )}
                            >
                                <div className="flex flex-col space-y-2">
                                    <span>{t(`pages.statistics.insights.market_data.header.${item}`)}</span>

                                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                        {item === "daily" ? (
                                            <RangeValue low={data.prices.daily.low} high={data.prices.daily.high} />
                                        ) : (
                                            (fmtPriceVal(data.prices[item].value) ?? t("general.na"))
                                        )}
                                    </span>
                                </div>

                                <div className="flex w-[130px] flex-col space-y-2">
                                    {item === "daily" && (
                                        <>
                                            <span>{t("pages.statistics.insights.market_data.header.year")}</span>
                                            <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                <RangeValue low={data.prices.year.low} high={data.prices.year.high} />
                                            </span>
                                        </>
                                    )}

                                    {(item === "atl" || item === "ath") && (
                                        <>
                                            <span>{t("pages.statistics.insights.market_data.header.date")}:</span>
                                            <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                {fmtDate(data.prices[item].timestamp) ?? t("general.na")}
                                            </span>
                                        </>
                                    )}
                                </div>
                            </div>
                        </div>
                    ))}

                    {(["daily", "year", "atl", "ath"] as const).map((item) => (
                        <div
                            key={item}
                            className={classNames(
                                "hidden w-full justify-between md:flex",
                                isFiatCurrency ? "xl:w-[770px]" : "xl:w-[950px]",
                            )}
                        >
                            <div
                                className={classNames(
                                    "flex",
                                    isFiatCurrency ? "flex-1" : "md-lg:w-[150px] md-lg:flex-none flex-1",
                                )}
                            >
                                {t(`pages.statistics.insights.market_data.header.${item}`)}
                            </div>

                            <div className="md-lg:flex-2 md-lg:flex-row md-lg:space-y-0 flex flex-1 flex-col justify-between space-y-3">
                                <div className="text-theme-secondary-900 dark:text-theme-dark-50 flex flex-1 justify-end">
                                    {item === "daily" && (
                                        <RangeValue low={data.prices.daily.low} high={data.prices.daily.high} />
                                    )}
                                    {item === "year" && (
                                        <RangeValue low={data.prices.year.low} high={data.prices.year.high} />
                                    )}
                                    {(item === "atl" || item === "ath") &&
                                        (fmtPriceVal(data.prices[item].value) ?? t("general.na"))}
                                </div>

                                <div className="md-lg:pl-16 flex w-full flex-1 justify-between space-x-2">
                                    {(item === "atl" || item === "ath") && (
                                        <>
                                            <div>{t("pages.statistics.insights.market_data.header.date")}:</div>
                                            <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                {fmtDate(data.prices[item].timestamp) ?? t("general.na")}
                                            </div>
                                        </>
                                    )}
                                </div>
                            </div>
                        </div>
                    ))}
                </InsightsContainer>

                <InsightsContainer title={t("pages.statistics.insights.market_data.exchanges_volume")} fullWidth>
                    {(["today_volume", "atl", "ath"] as const).map((item) => (
                        <div key={item} className="first:-mt-3 md:first:mt-0">
                            <div className="flex md:hidden">
                                <div className="flex w-full flex-col justify-between space-y-3 pt-3 sm:flex-row sm:space-y-0">
                                    <div className="flex flex-col space-y-2">
                                        <span>{t(`pages.statistics.insights.market_data.header.${item}`)}</span>

                                        <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                            {item === "today_volume"
                                                ? fmtVolVal(data.volume.today)
                                                : fmtVolVal(data.volume[item].value)}
                                        </span>
                                    </div>

                                    {(item === "atl" || item === "ath") && (
                                        <div className="flex w-[130px] flex-col space-y-2">
                                            <span>{t("pages.statistics.insights.market_data.header.date")}:</span>

                                            <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                {fmtDate(data.volume[item].timestamp) ?? t("general.na")}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>

                            <div
                                className={classNames(
                                    "hidden w-full justify-between md:flex",
                                    isFiatCurrency ? "xl:w-[770px]" : "xl:w-[950px]",
                                )}
                            >
                                <div
                                    className={classNames(
                                        "flex",
                                        isFiatCurrency ? "flex-1" : "md-lg:w-[150px] md-lg:flex-none flex-1",
                                    )}
                                >
                                    {t(`pages.statistics.insights.market_data.header.${item}`)}
                                </div>

                                <div className="md-lg:flex-2 md-lg:flex-row md-lg:space-y-0 flex flex-1 flex-col justify-between space-y-3">
                                    <div className="text-theme-secondary-900 dark:text-theme-dark-50 flex flex-1 justify-end">
                                        {item === "today_volume"
                                            ? fmtVolVal(data.volume.today)
                                            : fmtVolVal(data.volume[item].value)}
                                    </div>

                                    <div className="md-lg:pl-16 flex w-full flex-1 justify-between space-x-2">
                                        {(item === "atl" || item === "ath") && (
                                            <>
                                                <div>{t("pages.statistics.insights.market_data.header.date")}:</div>
                                                <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                    {fmtDate(data.volume[item].timestamp) ?? t("general.na")}
                                                </div>
                                            </>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </InsightsContainer>

                <InsightsContainer title={t("pages.statistics.insights.market_data.market_cap")} fullWidth>
                    {(["today_value", "atl", "ath"] as const).map((item) => (
                        <div key={item} className="first:-mt-3 md:first:mt-0">
                            <div className="flex md:hidden">
                                <div className="flex w-full flex-col justify-between space-y-3 pt-3 sm:flex-row sm:space-y-0">
                                    <div className="flex flex-col space-y-2">
                                        <span>{t(`pages.statistics.insights.market_data.header.${item}`)}</span>

                                        <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                                            {item === "today_value"
                                                ? fmtCapVal(data.caps.today)
                                                : fmtCapVal(data.caps[item].value)}
                                        </span>
                                    </div>

                                    {(item === "atl" || item === "ath") && (
                                        <div className="flex w-[130px] flex-col space-y-2">
                                            <span>{t("pages.statistics.insights.market_data.header.date")}:</span>

                                            <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                {fmtDate(data.caps[item].timestamp) ?? t("general.na")}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>

                            <div
                                className={classNames(
                                    "hidden w-full justify-between md:flex",
                                    isFiatCurrency ? "xl:w-[770px]" : "xl:w-[950px]",
                                )}
                            >
                                <div
                                    className={classNames(
                                        "flex",
                                        isFiatCurrency ? "flex-1" : "md-lg:w-[150px] md-lg:flex-none flex-1",
                                    )}
                                >
                                    {t(`pages.statistics.insights.market_data.header.${item}`)}
                                </div>

                                <div className="md-lg:flex-2 md-lg:flex-row md-lg:space-y-0 flex flex-1 flex-col justify-between space-y-3">
                                    <div className="text-theme-secondary-900 dark:text-theme-dark-50 flex flex-1 justify-end">
                                        {item === "today_value"
                                            ? fmtCapVal(data.caps.today)
                                            : fmtCapVal(data.caps[item].value)}
                                    </div>

                                    <div className="md-lg:pl-16 flex w-full flex-1 justify-between space-x-2">
                                        {(item === "atl" || item === "ath") && (
                                            <>
                                                <div>{t("pages.statistics.insights.market_data.header.date")}:</div>
                                                <div className="text-theme-secondary-900 dark:text-theme-dark-50">
                                                    {fmtDate(data.caps[item].timestamp) ?? t("general.na")}
                                                </div>
                                            </>
                                        )}
                                    </div>
                                </div>
                            </div>
                        </div>
                    ))}
                </InsightsContainer>
            </div>
        </div>
    );
}
