import { useTranslation } from "react-i18next";
import classNames from "classnames";
import Tooltip from "@/Components/General/Tooltip";
import useSharedData from "@/hooks/use-shared-data";
import { GasTrackerData } from "@/Pages/Statistics.contracts";
import { gweiToArk } from "@/utils/UnitConverter";
import GasLowIcon from "@icons/gas/low.svg?react";
import GasAverageIcon from "@icons/gas/average.svg?react";
import GasHighIcon from "@icons/gas/high.svg?react";

const gasIcons = {
    low: GasLowIcon,
    average: GasAverageIcon,
    high: GasHighIcon,
};

function GasTrackerCard({
    title,
    fee,
    canBeExchanged,
    icon: Icon,
}: {
    title: string;
    fee: GasTrackerData["fees"]["low"];
    canBeExchanged: boolean;
    icon: React.FC<React.SVGProps<SVGSVGElement>>;
}) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    const tooltipValue = gweiToArk(fee.amount, network.currency);

    return (
        <div className="flex flex-1 flex-col rounded border border-white bg-white px-4 py-3 font-semibold dark:border-theme-dark-900 dark:bg-theme-dark-900 dark:text-theme-dark-200 md:rounded-lg">
            <div className="flex flex-1 items-center justify-between pb-3">
                <div className="mb-0 flex items-center space-x-1.5 text-sm text-theme-secondary-900 dark:text-theme-dark-50">
                    <Icon className="h-5 w-5" />

                    <span>{title}</span>
                </div>

                <span>{fee.durationLabel}</span>
            </div>

            <div
                className={classNames(
                    "-mx-4 -mb-3 flex rounded-b bg-theme-secondary-100 p-4 pt-3 text-sm dark:bg-theme-dark-950 md:rounded-b-lg",
                    {
                        "justify-between": canBeExchanged,
                        "justify-end": !canBeExchanged,
                    },
                )}
            >
                {canBeExchanged && fee.value && (
                    <span className="text-theme-secondary-900 dark:text-theme-dark-50">~ {fee.value}</span>
                )}

                <Tooltip content={tooltipValue}>
                    <span>
                        {fee.amount} {t("general.gwei")}
                    </span>
                </Tooltip>
            </div>
        </div>
    );
}

export default function GasTracker({ data }: { data: GasTrackerData }) {
    const { t } = useTranslation();

    return (
        <div className="bg-theme-secondary-200 p-6 dark:bg-theme-dark-950 md:rounded-xl md:p-[9px]">
            <h3 className="mb-[9px] text-sm font-semibold text-theme-secondary-700 dark:text-theme-dark-200 md:px-[15px]">
                {t("pages.statistics.gas-tracker.current_gas_prices")}
            </h3>

            <div className="flex flex-col space-y-2 sm:flex-row sm:space-x-3 sm:space-y-0">
                {(["low", "average", "high"] as const).map((level) => (
                    <GasTrackerCard
                        key={level}
                        title={t(`pages.statistics.gas-tracker.${level}`)}
                        fee={data.fees[level]}
                        canBeExchanged={data.canBeExchanged}
                        icon={gasIcons[level]}
                    />
                ))}
            </div>
        </div>
    );
}
