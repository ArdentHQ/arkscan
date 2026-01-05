import MobileTable from "@/Components/Tables/Mobile/Table";
import MobileTableRow from "@/Components/Tables/Mobile/Row";
import TableCell from "@/Components/Tables/Mobile/TableCell";
import { MobileBlocksListSkeletonTable } from "@/Components/Tables/Mobile/Skeleton/Blocks/List";
import { IPaginatedResponse } from "@/types";
import { IBlock } from "@/types/generated";
import { useTranslation } from "react-i18next";
import Age from "@/Components/Model/Age";
import useSharedData from "@/hooks/use-shared-data";
import Height from "@/Components/Block/Height";
import Reward from "@/Components/Block/Reward";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";
import { BlocksListProps } from "@/Pages/Blocks.contracts";
import Address from "@/Components/Wallet/Address";

export function BlocksListMobileTable({ blocks }: { blocks: IPaginatedResponse<IBlock> }) {
    const { t } = useTranslation();
    const { network } = useSharedData();

    return (
        <MobileTable noResultsMessage={blocks.noResultsMessage} resultCount={blocks.total ?? 0}>
            {blocks.data.map((block: IBlock, index) => (
                <MobileTableRow
                    key={index}
                    header={
                        <>
                            <div className="sm:flex sm:flex-1">
                                <Height block={block} />
                            </div>

                            <div className="justify-end sm:flex sm:flex-1">
                                <Age
                                    timestamp={block.timestamp}
                                    className="text-theme-secondary-700 dark:text-theme-dark-200"
                                />
                            </div>
                        </>
                    }
                >
                    <TableCell label={t("tables.blocks.generated_by")}>
                        <Address wallet={{ address: block.proposer }} truncate />
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
            ))}
        </MobileTable>
    );
}

export default function BlocksListMobileTableWrapper({ rowCount = 10 }: { rowCount?: number }) {
    const { isLoading } = usePageHandler();
    const { blocks } = useSharedData<BlocksListProps>();

    if (!blocks || isLoading) {
        return (
            <div>
                <TableHeaderWrapper resultCount={0} />

                <MobileBlocksListSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <BlocksListMobileTable blocks={blocks} />
        </div>
    );
}
