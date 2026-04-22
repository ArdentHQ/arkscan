import { useTranslation } from "react-i18next";
import InsightsContainer from "./Container";
import { StatisticsValidatorRow } from "@/Pages/Statistics.contracts";
import ValidatorRowMobile from "./ValidatorRowMobile";
import ValidatorRowDesktop from "./ValidatorRowDesktop";

export default function ValidatorInsights({ rows, activeTab }: { rows: StatisticsValidatorRow[]; activeTab: string }) {
    const { t } = useTranslation();

    return (
        <div className={activeTab !== "validators" ? "hidden md:block" : undefined}>
            <div className="text-theme-secondary-900 dark:text-theme-dark-50 hidden px-6 font-semibold md:mx-auto md:block md:max-w-7xl md:px-10">
                {t("pages.statistics.insights.validators.title")}
            </div>

            <div>
                <InsightsContainer fullWidth>
                    {rows.map((row) => (
                        <div key={row.key}>
                            <ValidatorRowMobile row={row} />
                            <ValidatorRowDesktop row={row} />
                        </div>
                    ))}
                </InsightsContainer>
            </div>
        </div>
    );
}
