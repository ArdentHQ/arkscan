import MobileTableRow from "../../Row";
import LoadingText from "@/Components/Loading/Text";
import LoadingTableCell from "../TableCell";
import LoadingTable from "../Table";

export function MobileBookmarkAddressesSkeletonTable({ rowCount }: { rowCount: number }) {
    const rows = [];

    for (let i = 0; i < rowCount; i++) {
        rows.push(
            <MobileTableRow
                key={i}
                header={
                    <>
                        <LoadingText />
                        <div className="flex items-center space-x-2">
                            <LoadingText width="w-[16px]" />
                            <LoadingText width="w-[16px]" />
                        </div>
                    </>
                }
            >
                <LoadingTableCell withLabel />
                <LoadingTableCell withLabel />
            </MobileTableRow>,
        );
    }

    return <LoadingTable>{rows}</LoadingTable>;
}
