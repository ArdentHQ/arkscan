import Card from "@/Components/General/Card";
import Detail from "@/Components/General/Detail";
import Number from "@/Components/General/Number";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import LoadingText from "@/Components/Loading/Text";
import { IStatistics } from "@/types";
import classNames from "classnames";
import { useTranslation } from "react-i18next";

export function HeaderStat({
    title,
    value,
    color,
    isLoading,
    ...props
}: {
    title: string;
    value?: string | number | null;
    color: string;
    isLoading?: boolean;
}) {
    return (
        <Detail title={title} className="flex items-center space-x-2" {...props}>
            <div
                className={classNames({
                    "h-3 w-3 rounded-full": true,
                    [color]: true,
                })}
            ></div>

            {isLoading ? <LoadingText width="w-[17px]" height="h-5" /> : <span>{value}</span>}
        </Detail>
    );
}

export default function HeaderStats({ height, statistics }: { height: number; statistics?: IStatistics }) {
    const { t } = useTranslation();

    const isLoading = !statistics;

    return (
        <div className="px-6 pb-6 md:mx-auto md:max-w-7xl md:px-10">
            <div id="statistics-list" className="grid w-full grid-cols-1 gap-2 sm:grid-cols-2 md:gap-3 xl:grid-cols-4">
                <Card className="flex items-center space-x-6">
                    <HeaderStat
                        title={t("pages.validator-monitor.stats.forging")}
                        value={statistics?.performances?.forging}
                        color="bg-theme-success-700 dark:bg-theme-success-500"
                        isLoading={isLoading || statistics?.performances?.forging === undefined}
                        data-testid="validator-monitor:forging-count"
                    />

                    <HeaderStat
                        title={t("pages.validator-monitor.stats.missed")}
                        value={statistics?.performances?.missed}
                        color="bg-theme-warning-700 dark:bg-theme-warning-400"
                        isLoading={isLoading || statistics?.performances?.missed === undefined}
                        data-testid="validator-monitor:missed-count"
                    />

                    <HeaderStat
                        title={t("pages.validator-monitor.stats.not_forging")}
                        value={statistics?.performances?.missing}
                        color="bg-theme-danger-600 dark:bg-theme-danger-400"
                        isLoading={isLoading || statistics?.performances?.missing === undefined}
                        data-testid="validator-monitor:not-forging-count"
                    />
                </Card>

                <Card>
                    <Detail
                        title={t("pages.validator-monitor.stats.current_height")}
                        isLoading={!height}
                        data-testid="validator-monitor:current-height"
                    >
                        <Number>{height}</Number>
                    </Detail>
                </Card>

                <Card>
                    <Detail
                        title={t("pages.validator-monitor.stats.current_round")}
                        isLoading={statistics?.blockCount === undefined}
                    >
                        {statistics?.blockCount || "N/A"}
                    </Detail>
                </Card>

                <Card>
                    <Detail title={t("pages.validator-monitor.stats.next_slot")} isLoading={isLoading}>
                        {!!statistics?.nextValidator && statistics?.nextValidator?.address ? (
                            <a href={`/addresses/${statistics?.nextValidator?.address}`} className="link">
                                {statistics?.nextValidator?.attributes?.username ? (
                                    <>{statistics?.nextValidator?.attributes?.username}</>
                                ) : (
                                    <>
                                        <TruncateMiddle>{statistics?.nextValidator?.address}</TruncateMiddle>
                                    </>
                                )}
                            </a>
                        ) : (
                            <span className="text-theme-secondary-500 dark:text-theme-dark-700">N/A</span>
                        )}
                    </Detail>
                </Card>
            </div>
        </div>
    );
}
