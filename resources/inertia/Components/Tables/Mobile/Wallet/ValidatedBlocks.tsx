import MobileTable from "@/Components/Tables/Mobile/Table";
import MobileTableRow from "@/Components/Tables/Mobile/Row";
import TableCell from "@/Components/Tables/Mobile/TableCell";
import { MobileValidatedBlocksSkeletonTable } from "@/Components/Tables/Mobile/Skeleton/Wallet/ValidatedBlocks";
import { IPaginatedResponse } from "@/types";
import { IBlock } from "@/types/generated";
import { useTranslation } from "react-i18next";
import Age from "@/Components/Model/Age";
import useSharedData from "@/hooks/use-shared-data";
import Height from "@/Components/Block/Height";
import Reward from "@/Components/Block/Reward";
import { usePageHandler } from "@/Providers/PageHandler/PageHandlerContext";
import { ValidatedBlocksHeaderActions } from "@/Components/Tables/Desktop/Wallet/ValidatedBlocks";
import { TableHeaderWrapper } from "@/Components/Tables/Desktop/Table";

export function ValidatedBlocksMobileTable({ blocks }: { blocks: IPaginatedResponse<IBlock> }) {
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

                            <div className="hidden leading-4.25 sm:flex sm:w-[142px] sm:justify-start">
                                {block.transactionCount + " " + t("tables.blocks.transactions")}
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
                    <TableCell
                        label={t("tables.blocks.transactions", {
                            currency: network?.currency,
                        })}
                        className="sm:hidden"
                    >
                        {block.transactionCount}
                    </TableCell>

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

export default function ValidatedBlocksMobileTableWrapper({
    blocks,
    rowCount = 10,
}: {
    blocks?: IPaginatedResponse<IBlock>;
    rowCount?: number;
}) {
    const { isLoading } = usePageHandler();

    if (!blocks || isLoading) {
        return (
            <div className="pt-6">
                <TableHeaderWrapper resultCount={0} breakpoint="md">
                    <ValidatedBlocksHeaderActions hasForgedBlocks={false} />
                </TableHeaderWrapper>
                <MobileValidatedBlocksSkeletonTable rowCount={rowCount} />
            </div>
        );
    }

    return (
        <div className="md:hidden">
            <ValidatedBlocksMobileTable blocks={blocks} />
        </div>
    );
}
