import MobileTableRow from "../../Row";
import LoadingText from "@/Components/Loading/Text";
import LoadingTableCell from "../TableCell";
import LoadingTable from "../Table";

export function MobileTransactionsSkeletonTable({ rowCount }: { rowCount: number }) {
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
                <div className="flex flex-col space-y-2 font-semibold leading-4.25">
                    <LoadingText />

                    <div className="flex flex-row space-x-2">
                        <LoadingText width="w-[39px]" />
                        <LoadingText />
                    </div>

                    <div className="flex flex-row space-x-2">
                        <LoadingText width="w-[39px]" />
                        <LoadingText />
                    </div>
                </div>

                <LoadingTableCell withLabel={true} />

                <LoadingTableCell withLabel={true} />
            </MobileTableRow>,
        );
    }

    return <LoadingTable>{rows}</LoadingTable>;
}
