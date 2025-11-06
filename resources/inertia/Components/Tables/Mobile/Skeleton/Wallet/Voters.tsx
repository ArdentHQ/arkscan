import MobileTable from "../../Table";
import MobileTableRow from "../../Row";
import LoadingText from "@/Components/Loading/Text";
import LoadingTableCell from "../TableCell";

export function MobileVotersSkeletonTable({ rowCount }: { rowCount: number }) {
    const rows = [];
    for (let i = 0; i < rowCount; i++) {
        rows.push(
            <MobileTableRow key={i} header={<LoadingText />}>
                <LoadingTableCell withLabel={true} />

                <LoadingTableCell withLabel={true} />
            </MobileTableRow>,
        );
    }

    return <MobileTable className="md:hidden">{rows}</MobileTable>;
}
