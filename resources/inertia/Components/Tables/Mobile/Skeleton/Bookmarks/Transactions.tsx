import MobileTableRow from "../../Row";
import LoadingText from "@/Components/Loading/Text";
import LoadingTableCell from "../TableCell";
import LoadingTable from "../Table";

export function MobileBookmarkTransactionsSkeletonTable({ rowCount }: { rowCount: number }) {
    const rows = [];
    for (let i = 0; i < rowCount; i++) {
        rows.push(
            <MobileTableRow
                key={i}
                header={
                    <>
                        <LoadingText />
                        <div className="flex items-center space-x-2">
                            <LoadingText width="w-[60px]" />
                            <LoadingText width="w-[16px]" />
                        </div>
                    </>
                }
            >
                <div className="flex flex-col space-y-2 leading-4.25 font-semibold">
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

                <LoadingTableCell withLabel />
                <LoadingTableCell withLabel />
            </MobileTableRow>,
        );
    }

    return <LoadingTable>{rows}</LoadingTable>;
}
