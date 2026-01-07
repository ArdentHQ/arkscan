import HomeStat from "@/Components/Home/Stat";
import useSharedData from "@/hooks/use-shared-data";
import useSettings from "@/Providers/Settings/useSettings";
import { currency, hasSymbol } from "@/utils/number-formatter";
import { useTranslation } from "react-i18next";

export default function PriceTicker() {
    const { t } = useTranslation();
    const { network } = useSharedData();
    const { currency: selectedCurrency, priceExchangeRate } = useSettings();

    const formattedPrice = currency(priceExchangeRate ?? 0, selectedCurrency);
    const showCurrencyCode = hasSymbol(selectedCurrency);

    return (
        <div>
            <HomeStat
                title={t("pages.home.statistics.currency_price", {
                    currency: network?.currency,
                })}
                className="md:hidden"
            >
                <span>{formattedPrice}</span>
                {showCurrencyCode && <span className="ml-1">{selectedCurrency}</span>}
            </HomeStat>

            <p className="hidden items-center space-x-2 sm:space-x-3 md:inline-flex">
                <span className="text-sm font-semibold text-theme-secondary-900 dark:text-theme-dark-50 sm:text-3xl md:text-2xl lg:text-xl xl:text-2xl">
                    <span>
                        {formattedPrice}
                        {showCurrencyCode && ` ${selectedCurrency}`}
                    </span>
                </span>
            </p>
        </div>
    );
}
