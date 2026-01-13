import MobileTableRow from "../../Row";
import LoadingText from "@/Components/Loading/Text";
import LoadingTableCell from "../TableCell";
import LoadingTable from "../Table";

export function MobileTopAccountsSkeletonTable({ rowCount }: { rowCount: number }) {
    const rows = [];

    for (let i = 0; i < rowCount; i++) {
        rows.push(
            <MobileTableRow
                key={i}
                header={
                    <div className="flex items-center space-x-3">
                        <LoadingText width="w-[20px]" />
                        <LoadingText />
                    </div>
                }
            >
                <LoadingTableCell withLabel={true} />
                <LoadingTableCell withLabel={true} />
                <LoadingTableCell withLabel={true} />
            </MobileTableRow>,
        );
    }

    return <LoadingTable>{rows}</LoadingTable>;
}
