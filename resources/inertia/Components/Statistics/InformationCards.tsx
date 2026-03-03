import { useTranslation } from "react-i18next";
import { InformationCardsData } from "@/Pages/Statistics.contracts";
import InformationCard from "./InformationCard";
import Number from "@/Components/General/Number";
import { networkCurrency } from "@/utils/number-formatter";

export default function InformationCards({ data }: { data: InformationCardsData }) {
    const { t } = useTranslation();

    return (
        <div className="grid gap-2 sm:grid-cols-2 sm:grid-rows-1 sm:place-items-stretch md:gap-3">
            <InformationCard
                id="all-time-transactions"
                mainTitle={t("pages.statistics.information-cards.all-time-transactions")}
                mainValue={<Number>{data.transactions.allTimeValue}</Number>}
                secondaryTitle={t("pages.statistics.information-cards.transactions")}
                data={data.transactions}
                defaultPeriod={data.defaultPeriod}
                periods={data.periods}
                formatValue={(v) => <Number>{v}</Number>}
            />

            <InformationCard
                id="all-time-fees-collected"
                mainTitle={t("pages.statistics.information-cards.all-time-fees-collected")}
                mainValue={networkCurrency(data.fees.allTimeValue, 8, true)}
                secondaryTitle={t("pages.statistics.information-cards.fees")}
                data={data.fees}
                defaultPeriod={data.defaultPeriod}
                periods={data.periods}
                formatValue={(v, aboveThreshold) => {
                    if (aboveThreshold) {
                        return <>{networkCurrency(v, 0, true)}</>;
                    }
                    return <>{networkCurrency(v, 8, true)}</>;
                }}
            />
        </div>
    );
}
