import MobileTable from "../../Table";
import MobileTableRow from "../../Row";
import LoadingText from "@/Components/Loading/Text";
import LoadingTableCell from "../TableCell";

export function MonitorMobileHeaderSkeleton() {
    return (
        <div className="flex flex-1 min-w-0 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700">
            <div className="flex items-center pr-3">
                <div className="items-center">
                    <LoadingText width="w-[17px]" />
                </div>
            </div>

            <div className="flex flex-1 justify-between items-center pl-3 min-w-0">
                <div className="flex items-center">
                    <LoadingText />
                </div>

                <div className="flex items-center sm:space-x-3 h-[21px]">
                    <div className="flex items-center sm:hidden">
                        <LoadingText width="w-3" height="h-3" />
                    </div>

                    <div className="hidden sm:block">
                        <LoadingText width="w-[140px]" height="h-[21px]" />
                    </div>
                </div>
            </div>
        </div>
    );
}

export function MobileMonitorSkeletonTable({ rowCount }: { rowCount: number }) {
    const rows = [];
    for(let i = 0; i < rowCount; i++) {
        rows.push(
            <MobileTableRow
                key={i}
                expandable={true}
                header={<MonitorMobileHeaderSkeleton />}
                expandDisabled={true}
                expandClass="space-x-3 divide-x divide-theme-secondary-300 dark:divide-theme-dark-700"
            >
                <LoadingTableCell withLabel={true} />

                <LoadingTableCell withLabel={true} />
            </MobileTableRow>
        );
    }

    return (
        <MobileTable className="md:hidden">
            {rows}
        </MobileTable>
    );
}
