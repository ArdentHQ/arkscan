import { IExchange } from "@/types/generated";
import { useTranslation } from "react-i18next";

export default function ExchangePrice({ exchange }: { exchange: IExchange }) {
    const { t } = useTranslation();

    return (
        <>
            {exchange.price !== null ? (
                <span className="text-theme-secondary-900 dark:text-theme-dark-50">{exchange.priceFiat}</span>
            ) : (
                <span className="text-theme-secondary-500 dark:text-theme-dark-500">{t("general.na")}</span>
            )}
        </>
    );
}
