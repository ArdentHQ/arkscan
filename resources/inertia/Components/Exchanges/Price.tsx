import { IExchange } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { currencyWithDecimals } from "@/utils/number-formatter";
import useSharedData from "@/hooks/use-shared-data";

export default function ExchangePrice({ exchange }: { exchange: IExchange }) {
    const { t } = useTranslation();
    const { settings } = useSharedData();

    return (
        <>
            {exchange.priceFiat !== null ? (
                <span className="text-theme-secondary-900 dark:text-theme-dark-50">
                    {currencyWithDecimals({ value: exchange.priceFiat, currency: settings!.currency, decimals: 4 })}
                </span>
            ) : (
                <span className="text-theme-secondary-500 dark:text-theme-dark-500">{t("general.na")}</span>
            )}
        </>
    );
}
