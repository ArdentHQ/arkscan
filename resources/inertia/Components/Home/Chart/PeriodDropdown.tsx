import Dropdown from "@/Components/General/Dropdown/Dropdown";
import DropdownItem from "@/Components/General/Dropdown/DropdownItem";
import DropdownProvider from "@/Providers/Dropdown/DropdownProvider";
import ChevronDownSmallIcon from "@ui/icons/arrows/chevron-down-small.svg?react";
import { useTranslation } from "react-i18next";
import { HomeChartPeriod } from "@/Pages/Home.contracts";

const PERIODS: HomeChartPeriod[] = ["all", "day", "week", "month", "year"];

export default function PeriodDropdown({
    period,
    onChange,
}: {
    period: HomeChartPeriod;
    onChange: (period: HomeChartPeriod) => void;
}) {
    const { t } = useTranslation();

    return (
        <DropdownProvider>
            <Dropdown
                dropdownClasses="w-50"
                buttonClass="justify-between py-1.5 px-3 text-sm button-secondary w-[116px] shadow-px shadow-theme-secondary-300"
                buttonClassExpanded="bg-white text-theme-secondary-700 dim:shadow-theme-dark-700 dark:text-theme-dark-50 dark:bg-theme-dark-900 dark:hover:bg-theme-secondary-800 hover:text-theme-secondary-700 hover:bg-theme-secondary-200"
                useDefaultButtonClasses={false}
                button={({ isOpen }) => (
                    <div className="flex w-full items-center justify-between">
                        <span>{t(`pages.home.charts.periods.${period}`)}</span>

                        <span className={isOpen ? "rotate-180 transition-default" : "transition-default"}>
                            <ChevronDownSmallIcon className="h-3 w-3" />
                        </span>
                    </div>
                )}
            >
                {PERIODS.map((option) => (
                    <DropdownItem key={option} selected={option === period} onClick={() => onChange(option)}>
                        {t(`pages.home.charts.periods.${option}`)}
                    </DropdownItem>
                ))}
            </Dropdown>
        </DropdownProvider>
    );
}
