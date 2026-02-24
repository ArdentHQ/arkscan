import MobileTableRow from "../../Row";
import LoadingText from "@/Components/Loading/Text";
import LoadingTableCell from "../TableCell";
import LoadingTable from "../Table";
import useSharedData from "@/hooks/use-shared-data";

export function MobileTokensSkeletonTable({ rowCount }: { rowCount: number }) {
    const { network } = useSharedData();

    const rows = [];
    for (let i = 0; i < rowCount; i++) {
        rows.push(
            <MobileTableRow
                key={i}
                header={
                    <>
                        <LoadingText />
                        <LoadingText />
                    </>
                }
            >
                <LoadingTableCell withLabel={true} />

                <LoadingTableCell withLabel={true} />

                {network.canBeExchanged && <LoadingTableCell withLabel={true} />}
            </MobileTableRow>,
        );
    }

    return <LoadingTable>{rows}</LoadingTable>;
}
