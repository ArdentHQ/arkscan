import { IExchange } from "@/types/generated";
import { useTranslation } from "react-i18next";

export default function ExchangeVolume({ exchange }: { exchange: IExchange }) {
    const { t } = useTranslation();

    return (
        <>
            {exchange.volume !== null ? (
                <span>{exchange.volumeFiat}</span>
            ) : (
                <span className="text-theme-secondary-500 dark:text-theme-dark-500">{t("general.na")}</span>
            )}
        </>
    );
}
