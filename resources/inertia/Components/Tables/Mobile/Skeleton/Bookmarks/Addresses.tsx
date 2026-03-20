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
                        <LoadingText width="w-[40px]" />
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
