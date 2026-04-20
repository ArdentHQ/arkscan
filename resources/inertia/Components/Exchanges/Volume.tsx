import { IExchange } from "@/types/generated";
import { useTranslation } from "react-i18next";
import { currencyWithDecimals } from "@/utils/number-formatter";
import useSharedData from "@/hooks/use-shared-data";

export default function ExchangeVolume({ exchange }: { exchange: IExchange }) {
    const { t } = useTranslation();
    const { settings } = useSharedData();

    return (
        <>
            {exchange.volumeFiat !== null ? (
                <span>
                    {currencyWithDecimals({ value: exchange.volumeFiat, currency: settings!.currency, decimals: 2 })}
                </span>
            ) : (
                <span className="text-theme-secondary-500 dark:text-theme-dark-500">{t("general.na")}</span>
            )}
        </>
    );
}
