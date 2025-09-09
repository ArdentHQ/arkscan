import Card from "@/Components/General/Card";
import Detail from "@/Components/General/Detail";
import Number from "@/Components/General/Number";
import TruncateMiddle from "@/Components/General/TruncateMiddle";
import LoadingText from "@/Components/Loading/Text";
import { IStatistics } from "@/types";
import classNames from "@/utils/class-names";

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
        <Detail
            title={title}
            className="flex items-center space-x-2"
            {...props}
        >
            <div className={classNames({
                "rounded-full w-3 h-3": true,
                [color]: true,
            })}></div>

            {isLoading ? (
                <LoadingText
                    width="w-[17px]"
                    height="h-5"
                />
            ) : (
                <span>{value}</span>
            )}
        </Detail>
    );
}

export default function HeaderStats({ height, statistics }: {
    height: number;
    statistics?: IStatistics;
}) {
    const isLoading = !statistics;

    return (
        <div className="px-6 pb-6 md:px-10 md:mx-auto md:max-w-7xl">
            <div
                id="statistics-list"
                className="grid grid-cols-1 gap-2 w-full sm:grid-cols-2 md:gap-3 xl:grid-cols-4"
            >
                <Card className="flex items-center space-x-6">
                    <HeaderStat
                        title="Forging"
                        value={statistics?.performances?.forging}
                        color="bg-theme-success-700 dark:bg-theme-success-500"
                        isLoading={isLoading || statistics?.performances?.forging === undefined}
                        data-testid="forging-count"
                    />

                    <HeaderStat
                        title="Missed"
                        value={statistics?.performances?.missed}
                        color="bg-theme-warning-700 dark:bg-theme-warning-400"
                        isLoading={isLoading || statistics?.performances?.missed === undefined}
                        data-testid="missed-count"
                    />

                    <HeaderStat
                        title="Not Forging"
                        value={statistics?.performances?.missing}
                        color="bg-theme-danger-600 dark:bg-theme-danger-400"
                        isLoading={isLoading || statistics?.performances?.missing === undefined}
                        data-testid="not-forging-count"
                    />
                </Card>

                <Card>
                    <Detail
                        title="Current Height"
                        isLoading={!height}
                    >
                        <Number>{height}</Number>
                    </Detail>
                </Card>

                <Card>
                    <Detail
                        title="Current Round"
                        isLoading={statistics?.blockCount === undefined}
                    >
                        {statistics?.blockCount || 'N/A'}
                    </Detail>
                </Card>

                <Card>
                    <Detail
                        title="Next Slot"
                        isLoading={isLoading}
                    >
                        {!! statistics?.nextValidator && statistics?.nextValidator?.address ? (
                            <a
                                href={`/addresses/${statistics?.nextValidator?.address}`}
                                className="link"
                            >
                                {statistics?.nextValidator?.attributes?.username ? (<>
                                    {statistics?.nextValidator?.attributes?.username}
                                </>) : (<>
                                    <TruncateMiddle>{statistics?.nextValidator?.address}</TruncateMiddle>
                                </>)}
                            </a>
                        ) : (
                            <span className="text-theme-secondary-500 dark:text-theme-dark-700">
                                N/A
                            </span>
                        )}
                    </Detail>
                </Card>
            </div>
        </div>
    );
}
