import { useTranslation } from "react-i18next";
import MobileTable from "../Table";
import MobileTableRow from "../Row";
import TableCell from "../TableCell";
import { IPaginatedResponse } from "@/types";
import { IBlock } from "@/types/generated";
import useSharedData from "@/hooks/use-shared-data";
import { Block } from "@/models/Block";
import Age from "@/Components/Model/Age";
import Height from "@/Components/Block/Height";
import Reward from "@/Components/Block/Reward";
import Address from "@/Components/Wallet/Address";
import BookmarkButton from "@/Components/General/BookmarkButton";

function Row({ row }: { row: IBlock }) {
    const { t } = useTranslation();
    const { network } = useSharedData();
    const block = Block.from(row);

    return (
        <MobileTableRow
            header={
                <>
                    <div className="sm:flex sm:flex-1">
                        <Height block={block} />
                    </div>

                    <div className="flex items-center justify-end space-x-2 sm:flex-1">
                        <Age
                            className="text-theme-secondary-700 dark:text-theme-dark-200"
                            timestamp={block.timestamp}
                        />
                        <BookmarkButton type="blocks" id={block.hash} variant="inline" />
                    </div>
                </>
            }
        >
            <TableCell label={t("tables.blocks.generated_by")}>
                <Address wallet={block.proposer} truncate />
            </TableCell>

            <TableCell label={t("tables.blocks.transactions")}>{block.transactionCount}</TableCell>

            <TableCell
                label={t("tables.blocks.total_reward", {
                    currency: network?.currency,
                })}
            >
                <Reward block={block} withoutValue={!network?.canBeExchanged} />
            </TableCell>
        </MobileTableRow>
    );
}

export default function BookmarkBlocksMobileTable({ blocks }: { blocks: IPaginatedResponse<IBlock> }) {
    return (
        <div className="md:hidden">
            <MobileTable noResultsMessage={blocks.noResultsMessage} resultCount={blocks.total ?? 0}>
                {blocks.data.map((row: IBlock, index) => (
                    <Row key={index} row={row} />
                ))}
            </MobileTable>
        </div>
    );
}
