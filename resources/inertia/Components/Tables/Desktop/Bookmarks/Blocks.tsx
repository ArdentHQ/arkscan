import { useTranslation } from "react-i18next";
import useSharedData from "@/hooks/use-shared-data";
import { Table } from "../Table";
import TableHeader from "../TableHeader";
import TableCell from "../TableCell";
import { IPaginatedResponse } from "@/types";
import { IBlock } from "@/types/generated";
import Age from "@/Components/Model/Age";
import Height from "@/Components/Block/Height";
import Reward from "@/Components/Block/Reward";
import { Block } from "@/models/Block";
import Address from "@/Components/Wallet/Address";
import BookmarkButton from "@/Components/General/BookmarkButton";

function Row({ row }: { row: IBlock }) {
    const block = Block.from(row);

    return (
        <tr className="text-sm font-semibold">
            <TableCell>
                <Height block={block} />
            </TableCell>

            <TableCell breakpoint="md-lg" responsive>
                <Age timestamp={block.timestamp} />
            </TableCell>

            <TableCell>
                <Address wallet={block.proposer} truncate />
            </TableCell>

            <TableCell
                breakpoint="md-lg"
                responsive
                className="text-right text-theme-secondary-900 dark:text-theme-dark-50"
            >
                {block.transactionCount}
            </TableCell>

            <TableCell className="text-right">
                <Reward block={block} />
            </TableCell>

            <TableCell className="text-center">
                <BookmarkButton type="blocks" id={block.hash} />
            </TableCell>
        </tr>
    );
}

export default function BookmarkBlocksTable({ blocks }: { blocks?: IPaginatedResponse<IBlock> }) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    if (!blocks) {
        return null;
    }

    return (
        <Table
            withHeader
            withFooter
            paginator={blocks}
            rowComponent={Row}
            noResultsMessage={blocks.noResultsMessage}
            columns={
                <>
                    <TableHeader type="id" className="whitespace-nowrap">
                        {t("tables.blocks.height")}
                    </TableHeader>

                    <TableHeader breakpoint="md-lg" responsive>
                        {t("tables.blocks.age")}
                    </TableHeader>

                    <TableHeader>{t("tables.blocks.generated_by")}</TableHeader>

                    <TableHeader breakpoint="md-lg" responsive className="text-right">
                        {t("tables.blocks.transactions")}
                    </TableHeader>

                    <TableHeader className="text-right">
                        {t("tables.blocks.total_reward", {
                            currency: network!.currency,
                        })}
                    </TableHeader>

                    <TableHeader className="text-center">{""}</TableHeader>
                </>
            }
        />
    );
}
