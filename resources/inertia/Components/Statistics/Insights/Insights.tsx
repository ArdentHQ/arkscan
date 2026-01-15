import { useMemo, useState } from "react";
import { useTranslation } from "react-i18next";
import MobileDivider from "@/Components/General/MobileDivider";
import useSharedData from "@/hooks/use-shared-data";
import MobileDropdown from "./MobileDropdown";
import TransactionInsights from "./Transactions";
import MarketDataInsights from "./MarketData";
import ValidatorInsights from "./Validators";
import AddressInsights from "./Addresses";
import AnnualInsights from "./Annual";
import { StatisticsInsights } from "@/Pages/Statistics.contracts";

export default function Insights({ data }: { data: StatisticsInsights }) {
    const { t } = useTranslation();
    const { network } = useSharedData();
    const [activeTab, setActiveTab] = useState("transactions");

    const tabItems = useMemo(() => {
        const items = [{ value: "transactions", label: t("pages.statistics.insights.dropdown.transactions") }];

        if (network.canBeExchanged && data.marketData) {
            items.push({ value: "market_data", label: t("pages.statistics.insights.dropdown.market_data") });
        }

        items.push(
            { value: "validators", label: t("pages.statistics.insights.dropdown.validators") },
            { value: "addresses", label: t("pages.statistics.insights.dropdown.addresses") },
            { value: "annual", label: t("pages.statistics.insights.dropdown.annual") },
        );

        return items;
    }, [data.marketData, network.canBeExchanged, t]);

    return (
        <div>
            <MobileDivider />

            <div className="flex flex-col space-y-6 px-6 pb-4 pt-6 font-semibold md:mx-auto md:max-w-7xl md:px-10 md:pt-0">
                <div className="flex flex-col space-y-1.5">
                    <h1 className="mb-0 text-lg font-semibold leading-5.25 md:text-2xl md:leading-[1.8125rem]">
                        {t("pages.statistics.insights.title")}
                    </h1>

                    <span className="text-xs leading-3.75 text-theme-secondary-500 dark:text-theme-dark-200">
                        {t("pages.statistics.insights.subtitle")}
                    </span>
                </div>
            </div>

            <MobileDropdown items={tabItems} active={activeTab} onSelect={setActiveTab} />

            <TransactionInsights data={data.transactions} activeTab={activeTab} />

            {data.marketData && network.canBeExchanged && (
                <MarketDataInsights data={data.marketData} activeTab={activeTab} />
            )}

            <ValidatorInsights rows={data.validators} activeTab={activeTab} />

            <AddressInsights data={data.addresses} activeTab={activeTab} />

            <AnnualInsights years={data.annual} activeTab={activeTab} />
        </div>
    );
}
