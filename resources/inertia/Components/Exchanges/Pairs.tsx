import { IExchange } from "@/types/generated";
import classNames from "classnames";
import { useTranslation } from "react-i18next";

export default function ExchangePair({ exchange, className }: { exchange: IExchange; className?: string }) {
    const { t } = useTranslation();

    const pairs: string[] = [];

    if (exchange.btc) {
        pairs.push("btc");
    }

    if (exchange.eth) {
        pairs.push("eth");
    }

    if (exchange.stablecoins) {
        pairs.push("stablecoins");
    }

    if (exchange.other) {
        pairs.push("other");
    }

    return (
        <div
            className={classNames([
                "divide-theme-secondary-300 text-theme-secondary-900 dark:divide-theme-dark-800 dark:text-theme-dark-50 flex space-x-2 divide-x font-semibold",
                className,
            ])}
        >
            {pairs.map((pair: string, index) => (
                <div key={index} className="pl-2 first:pl-0">
                    {t(`pages.exchanges.pair.${pair}`)}
                </div>
            ))}
        </div>
    );
}
